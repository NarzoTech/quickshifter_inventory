#!/usr/bin/env python3
"""
Invoice Renumbering Script for bikersfu_inventory.sql

Reads the SQL dump, renumbers all invoices from old sequential format (INV-1, EXP-1, etc.)
to date-based format {PREFIX}{YYMM}{SEQ:3} (e.g. S2511004), and updates all cross-references.

KEY PRINCIPLE: Use sale/purchase PRIMARY KEY (ID) as the definitive link.
For customer_dues (no sale_id column), match by customer_id + old_invoice + created_at proximity.

Output: bikersfu_inventory_fixed.sql
"""

import re
import sys
from collections import defaultdict
from datetime import datetime


# ============================================================
# SQL Value Parser
# ============================================================

def parse_sql_values(values_str):
    """Parse a SQL VALUES row string like "(1, 'foo', NULL, 'bar')" into a list of raw value strings."""
    values_str = values_str.strip()
    if values_str.startswith('('):
        values_str = values_str[1:]
    if values_str.endswith(')'):
        values_str = values_str[:-1]
    if values_str.endswith('),'):
        values_str = values_str[:-2]

    parts = []
    current = ''
    in_quote = False
    escape_next = False
    depth = 0

    for ch in values_str:
        if escape_next:
            current += ch
            escape_next = False
            continue
        if ch == '\\':
            current += ch
            escape_next = True
            continue
        if ch == "'" and not in_quote:
            in_quote = True
            current += ch
            continue
        if ch == "'" and in_quote:
            current += ch
            in_quote = False
            continue
        if in_quote:
            current += ch
            continue
        if ch == '(':
            depth += 1
            current += ch
        elif ch == ')':
            depth -= 1
            current += ch
        elif ch == ',' and depth == 0:
            parts.append(current.strip())
            current = ''
        else:
            current += ch

    if current.strip():
        parts.append(current.strip())

    return parts


def unquote(val):
    """Remove surrounding quotes from a SQL value string. Return None for NULL."""
    if val is None:
        return None
    val = val.strip()
    if val.upper() == 'NULL':
        return None
    if val.startswith("'") and val.endswith("'"):
        return val[1:-1]
    return val


def quote(val):
    """Convert a Python value back to SQL representation."""
    if val is None:
        return 'NULL'
    escaped = str(val).replace("'", "\\'")
    return f"'{escaped}'"


def parse_datetime(dt_str):
    """Parse a datetime string like '2025-12-30 00:47:04' into a datetime object."""
    if not dt_str:
        return datetime(2000, 1, 1)
    try:
        return datetime.strptime(dt_str[:19], '%Y-%m-%d %H:%M:%S')
    except (ValueError, TypeError):
        try:
            return datetime.strptime(dt_str[:10], '%Y-%m-%d')
        except (ValueError, TypeError):
            return datetime(2000, 1, 1)


# ============================================================
# Row Extraction from INSERT Statements
# ============================================================

def extract_insert_blocks(content, table_name):
    """Find all INSERT INTO `table_name` blocks and extract individual rows."""
    pattern = rf"INSERT INTO `{re.escape(table_name)}` \(([^)]+)\) VALUES\s*\n"
    rows = []

    for match in re.finditer(pattern, content):
        columns_str = match.group(1)
        columns = [c.strip().strip('`') for c in columns_str.split(',')]
        start_pos = match.end()

        end_pos = content.find(';\n', start_pos)
        if end_pos == -1:
            end_pos = len(content)

        values_block = content[start_pos:end_pos]

        row_texts = []
        current_row = ''
        in_quote = False
        escape_next = False
        paren_depth = 0

        for ch in values_block:
            if escape_next:
                current_row += ch
                escape_next = False
                continue
            if ch == '\\':
                current_row += ch
                escape_next = True
                continue
            if ch == "'" and not escape_next:
                in_quote = not in_quote
                current_row += ch
                continue
            if in_quote:
                current_row += ch
                continue
            if ch == '(':
                paren_depth += 1
                current_row += ch
            elif ch == ')':
                paren_depth -= 1
                current_row += ch
                if paren_depth == 0:
                    row_texts.append(current_row.strip())
                    current_row = ''
            elif paren_depth > 0:
                current_row += ch

        for row_text in row_texts:
            vals = parse_sql_values(row_text)
            if len(vals) == len(columns):
                row_dict = {}
                for i, col in enumerate(columns):
                    row_dict[col] = vals[i]
                row_dict['_raw_values'] = vals
                row_dict['_columns'] = columns
                rows.append(row_dict)

    return rows


def reconstruct_row(row_dict):
    """Rebuild a row's values tuple from its column/value data."""
    vals = []
    for col in row_dict['_columns']:
        vals.append(row_dict[col])
    return '(' + ', '.join(vals) + ')'


# ============================================================
# Invoice Number Generation
# ============================================================

def generate_invoice_numbers(rows, date_col, prefix):
    """
    Sort rows by date ASC, id ASC, generate new invoice numbers.
    Format: {PREFIX}{YYMM}{SEQ:3} e.g. S2511001
    """
    def sort_key(r):
        date_val = unquote(r.get(date_col, 'NULL'))
        id_val = unquote(r.get('id', '0'))
        return (date_val or '9999-99-99', int(id_val or 0))

    sorted_rows = sorted(rows, key=sort_key)
    month_counters = defaultdict(int)
    assignments = []

    for row in sorted_rows:
        date_val = unquote(row.get(date_col, 'NULL'))
        if not date_val:
            date_val = '2025-01-01'

        try:
            year = date_val[2:4]
            month = date_val[5:7]
            yymm = year + month
        except (IndexError, TypeError):
            yymm = '2501'

        month_counters[yymm] += 1
        seq = month_counters[yymm]
        new_invoice = f"{prefix}{yymm}{seq:03d}"

        assignments.append((row, new_invoice))

    return sorted_rows, assignments


# ============================================================
# Timestamp-based Sale Finder
# ============================================================

def find_sale_by_timestamp(customer_id, old_invoice, due_created_at, sales_lookup):
    """
    For a customer_due record, find the correct sale using:
    1. customer_id + old_invoice to narrow candidates
    2. created_at timestamp proximity to disambiguate duplicates

    sales_lookup: dict of (customer_id, old_invoice) -> list of (sale_id, sale_created_at)
    """
    # Try exact customer_id match
    key = (customer_id, old_invoice)
    candidates = sales_lookup.get(key, [])

    if len(candidates) == 0:
        # Try with customer_id = '0' (walk-in customers)
        key0 = ('0', old_invoice)
        candidates = sales_lookup.get(key0, [])

    if len(candidates) == 0:
        # Fallback: try any sale with this invoice
        for k, v in sales_lookup.items():
            if k[1] == old_invoice:
                candidates = v
                break

    if len(candidates) == 0:
        return None

    if len(candidates) == 1:
        return candidates[0][0]  # sale_id

    # Multiple candidates - find closest by created_at
    due_dt = parse_datetime(due_created_at)
    best_sale_id = None
    best_diff = None
    for sale_id, sale_created_at in candidates:
        sale_dt = parse_datetime(sale_created_at)
        diff = abs((due_dt - sale_dt).total_seconds())
        if best_diff is None or diff < best_diff:
            best_diff = diff
            best_sale_id = sale_id

    return best_sale_id


def find_purchase_by_timestamp(supplier_id, old_invoice, ledger_created_at, purchases_lookup):
    """Same logic as find_sale_by_timestamp but for purchases."""
    key = (supplier_id, old_invoice)
    candidates = purchases_lookup.get(key, [])

    if len(candidates) == 0:
        for k, v in purchases_lookup.items():
            if k[1] == old_invoice:
                candidates = v
                break

    if len(candidates) == 0:
        return None

    if len(candidates) == 1:
        return candidates[0][0]

    dt = parse_datetime(ledger_created_at)
    best_id = None
    best_diff = None
    for pid, p_created_at in candidates:
        p_dt = parse_datetime(p_created_at)
        diff = abs((dt - p_dt).total_seconds())
        if best_diff is None or diff < best_diff:
            best_diff = diff
            best_id = pid

    return best_id


# ============================================================
# Main Processing
# ============================================================

def main():
    input_file = 'bikersfu_inventory.sql'
    output_file = 'bikersfu_inventory_fixed.sql'

    print(f"Reading {input_file}...")
    with open(input_file, 'r', encoding='utf-8') as f:
        content = f.read()

    print(f"File size: {len(content):,} bytes")

    # ============================================================
    # STEP 1: Extract all primary table rows
    # ============================================================
    print("\n=== Step 1: Extracting table data ===")

    sales_rows = extract_insert_blocks(content, 'sales')
    print(f"  sales: {len(sales_rows)} rows")

    purchases_rows = extract_insert_blocks(content, 'purchases')
    print(f"  purchases: {len(purchases_rows)} rows")

    expenses_rows = extract_insert_blocks(content, 'expenses')
    print(f"  expenses: {len(expenses_rows)} rows")

    esp_rows = extract_insert_blocks(content, 'expense_supplier_payments')
    print(f"  expense_supplier_payments: {len(esp_rows)} rows")

    customer_dues_rows = extract_insert_blocks(content, 'customer_dues')
    print(f"  customer_dues: {len(customer_dues_rows)} rows")

    supplier_payments_rows = extract_insert_blocks(content, 'supplier_payments')
    print(f"  supplier_payments: {len(supplier_payments_rows)} rows")

    customer_payments_rows = extract_insert_blocks(content, 'customer_payments')
    print(f"  customer_payments: {len(customer_payments_rows)} rows")

    ledgers_rows = extract_insert_blocks(content, 'ledgers')
    print(f"  ledgers: {len(ledgers_rows)} rows")

    ledger_details_rows = extract_insert_blocks(content, 'ledger_details')
    print(f"  ledger_details: {len(ledger_details_rows)} rows")

    # ============================================================
    # STEP 2: Generate new invoice numbers for primary tables
    # ============================================================
    print("\n=== Step 2: Generating new invoice numbers ===")

    # --- Save original invoices BEFORE renumbering ---
    # We need the original invoice for cross-reference matching later
    sales_original_invoice = {}  # sale_id -> old_invoice
    for row in sales_rows:
        sid = unquote(row['id'])
        sales_original_invoice[sid] = unquote(row['invoice'])

    purchases_original_invoice = {}  # purchase_id -> old_invoice
    for row in purchases_rows:
        pid = unquote(row['id'])
        purchases_original_invoice[pid] = unquote(row['invoice_number'])

    # --- Sales ---
    sales_sorted, sales_assignments = generate_invoice_numbers(sales_rows, 'order_date', 'S')
    sales_id_mapping = {}  # sale_id -> new_invoice
    for row, new_inv in sales_assignments:
        sale_id = unquote(row['id'])
        sales_id_mapping[sale_id] = new_inv
        row['invoice'] = quote(new_inv)

    print(f"  Sales: {len(sales_assignments)} invoices renumbered")

    # --- Build sales lookup for timestamp-based matching ---
    # (customer_id, old_invoice) -> [(sale_id, created_at), ...]
    sales_lookup = defaultdict(list)
    for row in sales_rows:
        sid = unquote(row['id'])
        cid = unquote(row['customer_id']) or '0'
        uid = unquote(row.get('user_id'))
        old_inv = sales_original_invoice[sid]
        created_at = unquote(row.get('created_at'))

        if old_inv:
            sales_lookup[(cid, old_inv)].append((sid, created_at))
            # Also index by user_id if different from customer_id
            if uid and uid != cid:
                sales_lookup[(uid, old_inv)].append((sid, created_at))

    # --- Purchases ---
    purchases_sorted, purchases_assignments = generate_invoice_numbers(purchases_rows, 'purchase_date', 'P')
    purchases_id_mapping = {}
    for row, new_inv in purchases_assignments:
        purchase_id = unquote(row['id'])
        purchases_id_mapping[purchase_id] = new_inv
        row['invoice_number'] = quote(new_inv)

    print(f"  Purchases: {len(purchases_assignments)} invoices renumbered")

    # --- Build purchases lookup for timestamp-based matching ---
    purchases_lookup = defaultdict(list)
    for row in purchases_rows:
        pid = unquote(row['id'])
        supplier_id = unquote(row['supplier_id'])
        old_inv = purchases_original_invoice[pid]
        created_at = unquote(row.get('created_at'))

        if old_inv:
            purchases_lookup[(supplier_id, old_inv)].append((pid, created_at))

    # --- Expenses ---
    expenses_sorted, expenses_assignments = generate_invoice_numbers(expenses_rows, 'date', 'EXP')
    expenses_mapping = {}
    expenses_id_mapping = {}
    for row, new_inv in expenses_assignments:
        old_inv = unquote(row['invoice'])
        expense_id = unquote(row['id'])
        if old_inv:
            expenses_mapping[old_inv] = new_inv
        expenses_id_mapping[expense_id] = new_inv
        row['invoice'] = quote(new_inv)

    print(f"  Expenses: {len(expenses_assignments)} invoices renumbered")

    # --- Expense Supplier Payments ---
    esp_with_invoice = [r for r in esp_rows if unquote(r.get('invoice')) is not None]
    esp_without_invoice = [r for r in esp_rows if unquote(r.get('invoice')) is None]

    esp_sorted, esp_assignments = generate_invoice_numbers(esp_with_invoice, 'payment_date', 'ESP')
    esp_mapping = {}
    for row, new_inv in esp_assignments:
        old_inv = unquote(row['invoice'])
        if old_inv:
            esp_mapping[old_inv] = new_inv
        row['invoice'] = quote(new_inv)

    print(f"  Expense Supplier Payments: {len(esp_assignments)} invoices renumbered ({len(esp_without_invoice)} NULL invoices unchanged)")

    # ============================================================
    # STEP 3: Update cross-reference tables using SALE ID
    # ============================================================
    print("\n=== Step 3: Updating cross-references (using sale PK ID) ===")

    # --- Customer Dues ---
    # customer_dues has NO sale_id column, only (customer_id, invoice)
    # Use timestamp-based matching to find the correct sale
    cd_updated = 0
    cd_ambiguous = 0
    for row in customer_dues_rows:
        old_inv = unquote(row['invoice'])
        customer_id = unquote(row['customer_id'])
        due_created_at = unquote(row.get('created_at'))
        if not old_inv:
            continue

        # Find the correct sale using timestamp proximity
        sale_id = find_sale_by_timestamp(customer_id, old_inv, due_created_at, sales_lookup)

        if sale_id and sale_id in sales_id_mapping:
            new_inv = sales_id_mapping[sale_id]
            row['invoice'] = quote(new_inv)
            cd_updated += 1

            # Check if this was an ambiguous match
            key = (customer_id, old_inv)
            if len(sales_lookup.get(key, [])) > 1:
                cd_ambiguous += 1
        else:
            print(f"    WARN: customer_due id={unquote(row['id'])} customer={customer_id} invoice={old_inv} - no matching sale found")

    print(f"  customer_dues: {cd_updated} invoices updated ({cd_ambiguous} resolved by timestamp)")

    # --- Supplier Payments ---
    # supplier_payments HAS purchase_id column - direct lookup!
    sp_updated = 0
    for row in supplier_payments_rows:
        old_inv = unquote(row['invoice'])
        purchase_id = unquote(row.get('purchase_id'))
        if not old_inv:
            continue

        if purchase_id and purchase_id in purchases_id_mapping:
            row['invoice'] = quote(purchases_id_mapping[purchase_id])
            sp_updated += 1
        else:
            # Fallback: timestamp-based matching
            supplier_id = unquote(row.get('supplier_id'))
            created_at = unquote(row.get('created_at'))
            pid = find_purchase_by_timestamp(supplier_id, old_inv, created_at, purchases_lookup)
            if pid and pid in purchases_id_mapping:
                row['invoice'] = quote(purchases_id_mapping[pid])
                sp_updated += 1

    print(f"  supplier_payments: {sp_updated} invoices updated")

    # --- Customer Payments ---
    # customer_payments HAS sale_id column - direct lookup!
    cp_updated = 0
    cp_month_counters = defaultdict(int)
    cp_standalone = []
    for row in customer_payments_rows:
        old_inv = unquote(row['invoice'])
        if not old_inv:
            continue
        sale_id = unquote(row.get('sale_id'))
        if sale_id and sale_id in sales_id_mapping:
            row['invoice'] = quote(sales_id_mapping[sale_id])
            cp_updated += 1
        else:
            cp_standalone.append(row)

    cp_standalone.sort(key=lambda r: (unquote(r.get('payment_date', 'NULL')) or '9999-99-99', int(unquote(r.get('id', '0')) or 0)))
    for row in cp_standalone:
        date_val = unquote(row.get('payment_date')) or '2025-01-01'
        try:
            yymm = date_val[2:4] + date_val[5:7]
        except (IndexError, TypeError):
            yymm = '2501'
        cp_month_counters[yymm] += 1
        new_inv = f"CP{yymm}{cp_month_counters[yymm]:03d}"
        row['invoice'] = quote(new_inv)
        cp_updated += 1

    print(f"  customer_payments: {cp_updated} invoices updated")

    # --- Ledgers ---
    ledger_type_counters = defaultdict(lambda: defaultdict(int))

    def gen_ledger_invoice(prefix, date_val):
        try:
            year = date_val[2:4]
            month = date_val[5:7]
            yymm = year + month
        except (IndexError, TypeError):
            yymm = '2501'
        ledger_type_counters[prefix][yymm] += 1
        seq = ledger_type_counters[prefix][yymm]
        return f"{prefix}{yymm}{seq:03d}"

    ledger_updated = 0
    ledger_id_invoice_map = {}  # ledger_id -> {old_invoice_no, new_invoice_no, invoice_type, matched_sale_id/purchase_id}

    for row in ledgers_rows:
        old_inv = unquote(row['invoice_no'])
        inv_type = unquote(row['invoice_type'])
        customer_id = unquote(row.get('customer_id'))
        supplier_id = unquote(row.get('supplier_id'))
        expense_supplier_id = unquote(row.get('expense_supplier_id'))
        date_val = unquote(row.get('date')) or '2025-01-01'
        ledger_id = unquote(row['id'])
        created_at = unquote(row.get('created_at'))

        if not old_inv:
            continue

        new_inv = None
        matched_id = None

        if inv_type == 'sale' and customer_id:
            # Use timestamp-based sale finder
            sale_id = find_sale_by_timestamp(customer_id, old_inv, created_at, sales_lookup)
            if sale_id and sale_id in sales_id_mapping:
                new_inv = sales_id_mapping[sale_id]
                matched_id = sale_id

        elif inv_type == 'purchase' and supplier_id:
            purchase_id = find_purchase_by_timestamp(supplier_id, old_inv, created_at, purchases_lookup)
            if purchase_id and purchase_id in purchases_id_mapping:
                new_inv = purchases_id_mapping[purchase_id]
                matched_id = purchase_id

        elif inv_type == 'Due Receive' and customer_id:
            new_inv = gen_ledger_invoice('DRL', date_val)

        elif inv_type == 'Advance Received' and customer_id:
            new_inv = gen_ledger_invoice('CAL', date_val)

        elif inv_type == 'Payment Return' and customer_id:
            new_inv = gen_ledger_invoice('CARL', date_val)

        elif inv_type == 'Due Payment' and supplier_id:
            new_inv = gen_ledger_invoice('SDL', date_val)

        elif inv_type == 'Advance Payment' and supplier_id:
            new_inv = gen_ledger_invoice('SAL', date_val)

        elif inv_type == 'Payment Return' and supplier_id:
            new_inv = gen_ledger_invoice('SARL', date_val)

        elif inv_type == 'Expense' and expense_supplier_id:
            if old_inv in expenses_mapping:
                new_inv = expenses_mapping[old_inv]
            else:
                m = re.match(r'EXP-(\d+)', old_inv)
                if m:
                    exp_id = m.group(1)
                    if exp_id in expenses_id_mapping:
                        new_inv = expenses_id_mapping[exp_id]

        elif inv_type == 'Expense Due Payment' and expense_supplier_id:
            new_inv = gen_ledger_invoice('ESPL', date_val)

        if new_inv:
            ledger_id_invoice_map[ledger_id] = {
                'old_invoice_no': old_inv,
                'new_invoice_no': new_inv,
                'invoice_type': inv_type,
                'customer_id': customer_id,
                'supplier_id': supplier_id,
                'matched_id': matched_id,
                'created_at': created_at,
            }
            row['invoice_no'] = quote(new_inv)
            ledger_updated += 1

    print(f"  ledgers: {ledger_updated} invoice_no values updated")

    # --- Ledger Details ---
    # Use parent ledger's matched_id to find the correct sale/purchase
    ld_updated = 0
    for row in ledger_details_rows:
        old_inv = unquote(row['invoice'])
        ledger_id = unquote(row['ledger_id'])
        if not old_inv:
            continue

        new_inv = None

        if ledger_id in ledger_id_invoice_map:
            parent_info = ledger_id_invoice_map[ledger_id]
            inv_type = parent_info['invoice_type']
            customer_id = parent_info.get('customer_id')
            supplier_id = parent_info.get('supplier_id')
            matched_id = parent_info.get('matched_id')
            created_at = parent_info.get('created_at')

            if inv_type == 'sale' and matched_id:
                # The parent ledger matched a specific sale - but the detail may reference
                # a different sale invoice (e.g. due receive for multiple invoices)
                # Try to find THIS detail's specific sale by old_inv
                detail_sale_id = find_sale_by_timestamp(customer_id, old_inv, created_at, sales_lookup)
                if detail_sale_id and detail_sale_id in sales_id_mapping:
                    new_inv = sales_id_mapping[detail_sale_id]
                elif matched_id in sales_id_mapping:
                    new_inv = sales_id_mapping[matched_id]

            elif inv_type == 'purchase' and matched_id:
                detail_pur_id = find_purchase_by_timestamp(supplier_id, old_inv, created_at, purchases_lookup)
                if detail_pur_id and detail_pur_id in purchases_id_mapping:
                    new_inv = purchases_id_mapping[detail_pur_id]
                elif matched_id in purchases_id_mapping:
                    new_inv = purchases_id_mapping[matched_id]

            elif inv_type in ('Due Receive', 'Advance Received', 'Payment Return') and customer_id:
                # Detail references a sale invoice
                detail_sale_id = find_sale_by_timestamp(customer_id, old_inv, created_at, sales_lookup)
                if detail_sale_id and detail_sale_id in sales_id_mapping:
                    new_inv = sales_id_mapping[detail_sale_id]

            elif inv_type in ('Due Payment', 'Advance Payment', 'Payment Return') and supplier_id:
                # Detail references a purchase invoice
                detail_pur_id = find_purchase_by_timestamp(supplier_id, old_inv, created_at, purchases_lookup)
                if detail_pur_id and detail_pur_id in purchases_id_mapping:
                    new_inv = purchases_id_mapping[detail_pur_id]

            elif inv_type in ('Expense', 'Expense Due Payment'):
                if old_inv in expenses_mapping:
                    new_inv = expenses_mapping[old_inv]
                else:
                    m = re.match(r'EXP-(\d+)', old_inv)
                    if m:
                        exp_id = m.group(1)
                        if exp_id in expenses_id_mapping:
                            new_inv = expenses_id_mapping[exp_id]

        # Fallback if parent ledger didn't help
        if not new_inv and old_inv:
            if old_inv.startswith('EXP-'):
                if old_inv in expenses_mapping:
                    new_inv = expenses_mapping[old_inv]
                else:
                    m = re.match(r'EXP-(\d+)', old_inv)
                    if m:
                        exp_id = m.group(1)
                        if exp_id in expenses_id_mapping:
                            new_inv = expenses_id_mapping[exp_id]
            elif old_inv == 'DIRECT-BALANCE':
                pass  # Leave as-is, not an invoice reference

        if new_inv:
            row['invoice'] = quote(new_inv)
            ld_updated += 1

    print(f"  ledger_details: {ld_updated} invoices updated")

    # ============================================================
    # STEP 4: Rebuild the SQL file
    # ============================================================
    print("\n=== Step 4: Rebuilding SQL file ===")

    output = content

    tables_to_replace = [
        ('sales', sales_rows),
        ('purchases', purchases_rows),
        ('expenses', expenses_rows),
        ('expense_supplier_payments', esp_with_invoice + esp_without_invoice),
        ('customer_dues', customer_dues_rows),
        ('supplier_payments', supplier_payments_rows),
        ('customer_payments', customer_payments_rows),
        ('ledgers', ledgers_rows),
        ('ledger_details', ledger_details_rows),
    ]

    for table_name, rows in tables_to_replace:
        if not rows:
            continue

        search_pattern = rf"INSERT INTO `{re.escape(table_name)}` \([^)]+\) VALUES\s*\n"
        blocks_to_replace = []

        for m in re.finditer(search_pattern, output):
            header = m.group(0)
            start_of_values = m.end()

            end_pos = output.find(';\n', start_of_values)
            if end_pos == -1:
                end_pos = len(output) - 1

            blocks_to_replace.append({
                'start': m.start(),
                'end': end_pos + 2,
                'header': header,
                'values_start': start_of_values,
                'values_end': end_pos,
            })

        if blocks_to_replace:
            block_row_counts = []
            for block in blocks_to_replace:
                old_values = output[block['values_start']:block['values_end']]
                count = 0
                in_q = False
                esc = False
                pd = 0
                for ch in old_values:
                    if esc:
                        esc = False
                        continue
                    if ch == '\\':
                        esc = True
                        continue
                    if ch == "'" and not esc:
                        in_q = not in_q
                        continue
                    if in_q:
                        continue
                    if ch == '(':
                        pd += 1
                    elif ch == ')':
                        pd -= 1
                        if pd == 0:
                            count += 1
                block_row_counts.append(count)

            row_idx = 0
            replacements = []

            for block_idx, block in enumerate(blocks_to_replace):
                block_size = block_row_counts[block_idx]
                block_rows = rows[row_idx:row_idx + block_size]
                row_idx += block_size

                if not block_rows:
                    continue

                row_texts = []
                for r in block_rows:
                    row_texts.append(reconstruct_row(r))

                new_values = ',\n'.join(row_texts)
                new_block = block['header'] + new_values + ';\n'

                replacements.append((block['start'], block['end'], new_block))

            for start, end, new_text in reversed(replacements):
                output = output[:start] + new_text + output[end:]

    # ============================================================
    # STEP 5: Validation
    # ============================================================
    print("\n=== Step 5: Validation ===")

    remaining_old = 0
    for m in re.finditer(r"INSERT INTO `sales` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        remaining_old += len(re.findall(r"'INV-\d+'", m.group(1)))
    print(f"  Remaining INV- in sales: {remaining_old}")

    remaining_old_purchases = 0
    for m in re.finditer(r"INSERT INTO `purchases` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        remaining_old_purchases += len(re.findall(r"'INV-\d+'", m.group(1)))
    print(f"  Remaining INV- in purchases: {remaining_old_purchases}")

    remaining_old_exp = 0
    for m in re.finditer(r"INSERT INTO `expenses` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        remaining_old_exp += len(re.findall(r"'EXP-\d+'", m.group(1)))
    print(f"  Remaining old EXP- in expenses: {remaining_old_exp}")

    remaining_old_cd = 0
    for m in re.finditer(r"INSERT INTO `customer_dues` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        remaining_old_cd += len(re.findall(r"'INV-\d+'", m.group(1)))
    print(f"  Remaining INV- in customer_dues: {remaining_old_cd}")

    remaining_old_ld = 0
    for m in re.finditer(r"INSERT INTO `ledger_details` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        remaining_old_ld += len(re.findall(r"'INV-\d+'", m.group(1)))
        remaining_old_ld += len(re.findall(r"'EXP-\d+'", m.group(1)))
    print(f"  Remaining old format in ledger_details: {remaining_old_ld}")

    new_sales_invoices = []
    for m in re.finditer(r"INSERT INTO `sales` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        new_sales_invoices.extend(re.findall(r"'(S\d{7})'", m.group(1)))

    dup_sales = [inv for inv in set(new_sales_invoices) if new_sales_invoices.count(inv) > 1]
    print(f"  Duplicate invoices in sales: {len(dup_sales)}")

    new_purchase_invoices = []
    for m in re.finditer(r"INSERT INTO `purchases` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        new_purchase_invoices.extend(re.findall(r"'(P\d{7})'", m.group(1)))

    dup_purchases = [inv for inv in set(new_purchase_invoices) if new_purchase_invoices.count(inv) > 1]
    print(f"  Duplicate invoices in purchases: {len(dup_purchases)}")

    # Verify customer_dues → sales cross-reference
    sales_inv_set = set(new_sales_invoices)
    cd_invoices = []
    for m in re.finditer(r"INSERT INTO `customer_dues` \([^)]+\) VALUES\s*\n(.*?);\n", output, re.DOTALL):
        cd_invoices.extend(re.findall(r"'(S\d+)'", m.group(1)))
    cd_missing = [inv for inv in cd_invoices if inv not in sales_inv_set]
    print(f"  Customer dues referencing non-existent sales: {len(cd_missing)}")

    # Write output
    print(f"\nWriting {output_file}...")
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(output)

    print(f"Done! Output written to {output_file}")
    print(f"Output size: {len(output):,} bytes")

    # Print summary
    print("\n=== Summary ===")
    print(f"  Sales:                    {len(sales_assignments):>4} invoices renumbered (prefix: S)")
    print(f"  Purchases:                {len(purchases_assignments):>4} invoices renumbered (prefix: P)")
    print(f"  Expenses:                 {len(expenses_assignments):>4} invoices renumbered (prefix: EXP)")
    print(f"  Expense Supplier Payments:{len(esp_assignments):>4} invoices renumbered (prefix: ESP)")
    print(f"  Customer Dues:            {cd_updated:>4} cross-refs updated ({cd_ambiguous} by timestamp)")
    print(f"  Supplier Payments:        {sp_updated:>4} cross-refs updated")
    print(f"  Customer Payments:        {cp_updated:>4} cross-refs updated")
    print(f"  Ledgers:                  {ledger_updated:>4} invoice_no values updated")
    print(f"  Ledger Details:           {ld_updated:>4} invoices updated")

    # Print sample
    print("\n=== Sample New Invoices ===")
    print("  Sales (first 5):")
    for row, new_inv in sales_assignments[:5]:
        sid = unquote(row['id'])
        print(f"    Sale ID={sid} -> {new_inv}")
    print("  Purchases (first 5):")
    for row, new_inv in purchases_assignments[:5]:
        pid = unquote(row['id'])
        print(f"    Purchase ID={pid} -> {new_inv}")
    print("  Expenses (first 5):")
    for row, new_inv in expenses_assignments[:5]:
        eid = unquote(row['id'])
        print(f"    Expense ID={eid} -> {new_inv}")


if __name__ == '__main__':
    main()
