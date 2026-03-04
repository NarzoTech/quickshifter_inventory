#!/usr/bin/env python3
"""
Verify invoice migration: compare original vs fixed SQL dump.
Checks cross-references and amounts between tables.
"""

import re
import sys
from collections import defaultdict


def parse_sql_values(values_str):
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
    if val is None:
        return None
    val = val.strip()
    if val.upper() == 'NULL':
        return None
    if val.startswith("'") and val.endswith("'"):
        return val[1:-1]
    return val


def extract_insert_blocks(content, table_name):
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
                rows.append(row_dict)
    return rows


def build_index(rows, key_col):
    idx = {}
    for r in rows:
        k = unquote(r.get(key_col))
        if k:
            idx[k] = r
    return idx


def compare_amount(a, b):
    try:
        va = float(a) if a else 0
        vb = float(b) if b else 0
        return abs(va - vb) < 0.01
    except (ValueError, TypeError):
        return a == b


def main():
    old_file = '../bikersfu_updated_inventory.sql'
    new_file = '../bikersfu_updated_inventory_fixed.sql'

    print("Loading original SQL...")
    with open(old_file, 'r', encoding='utf-8') as f:
        old_content = f.read()
    print("Loading fixed SQL...")
    with open(new_file, 'r', encoding='utf-8') as f:
        new_content = f.read()

    errors = []
    warnings = []

    # ================================================================
    # Extract all tables from BOTH files
    # ================================================================
    print("\n=== Extracting tables ===")

    tables = {
        'sales':                     {'invoice_col': 'invoice',        'id_col': 'id'},
        'purchases':                 {'invoice_col': 'invoice_number', 'id_col': 'id'},
        'expenses':                  {'invoice_col': 'invoice',        'id_col': 'id'},
        'expense_supplier_payments': {'invoice_col': 'invoice',        'id_col': 'id'},
        'customer_dues':             {'invoice_col': 'invoice',        'id_col': 'id'},
        'supplier_payments':         {'invoice_col': 'invoice',        'id_col': 'id'},
        'customer_payments':         {'invoice_col': 'invoice',        'id_col': 'id'},
        'ledgers':                   {'invoice_col': 'invoice_no',     'id_col': 'id'},
        'ledger_details':            {'invoice_col': 'invoice',        'id_col': 'id'},
    }

    old_data = {}
    new_data = {}
    for tbl in tables:
        old_data[tbl] = extract_insert_blocks(old_content, tbl)
        new_data[tbl] = extract_insert_blocks(new_content, tbl)
        count_match = len(old_data[tbl]) == len(new_data[tbl])
        status = "OK" if count_match else "*** ROW COUNT MISMATCH ***"
        print(f"  {tbl}: old={len(old_data[tbl])} new={len(new_data[tbl])}  {status}")
        if not count_match:
            errors.append(f"Row count mismatch in {tbl}: old={len(old_data[tbl])}, new={len(new_data[tbl])}")

    # ================================================================
    # CHECK 1: Data integrity (amounts, dates, FKs unchanged per row)
    # ================================================================
    print("\n=== Check 1: Data integrity (amounts, dates, FKs unchanged) ===")

    amount_cols = {
        'sales':        ['total_price', 'grand_total', 'paid_amount', 'due_amount', 'order_discount', 'total_tax'],
        'purchases':    ['total_amount', 'paid_amount', 'due_amount'],
        'expenses':     ['amount', 'paid_amount', 'due_amount'],
        'expense_supplier_payments': ['amount'],
        'customer_dues': ['amount', 'paid_amount'],
        'supplier_payments': ['amount'],
        'customer_payments': ['amount'],
        'ledgers':      ['amount', 'total_amount', 'due_amount'],
        'ledger_details': ['amount'],
    }

    date_cols = {
        'sales':        ['order_date'],
        'purchases':    ['purchase_date'],
        'expenses':     ['date'],
        'expense_supplier_payments': ['payment_date'],
        'customer_dues': ['due_date', 'created_at'],
        'supplier_payments': ['payment_date'],
        'customer_payments': ['payment_date'],
        'ledgers':      ['date'],
        'ledger_details': ['created_at'],
    }

    fk_cols = {
        'sales':        ['customer_id', 'user_id'],
        'purchases':    ['supplier_id'],
        'expenses':     ['expense_type_id'],
        'expense_supplier_payments': ['expense_id', 'expense_supplier_id'],
        'customer_dues': ['customer_id'],
        'supplier_payments': ['supplier_id', 'purchase_id'],
        'customer_payments': ['customer_id', 'sale_id'],
        'ledgers':      ['customer_id', 'supplier_id', 'expense_supplier_id', 'sale_return_id'],
        'ledger_details': ['ledger_id'],
    }

    for tbl in tables:
        old_by_id = build_index(old_data[tbl], 'id')
        new_by_id = build_index(new_data[tbl], 'id')
        tbl_errors = 0

        for row_id, old_row in old_by_id.items():
            new_row = new_by_id.get(row_id)
            if not new_row:
                errors.append(f"{tbl} id={row_id}: MISSING in new file")
                tbl_errors += 1
                continue

            for col in amount_cols.get(tbl, []):
                old_val = unquote(old_row.get(col))
                new_val = unquote(new_row.get(col))
                if not compare_amount(old_val, new_val):
                    errors.append(f"{tbl} id={row_id}: {col} changed {old_val} -> {new_val}")
                    tbl_errors += 1

            for col in date_cols.get(tbl, []):
                old_val = unquote(old_row.get(col))
                new_val = unquote(new_row.get(col))
                if old_val != new_val:
                    errors.append(f"{tbl} id={row_id}: {col} changed '{old_val}' -> '{new_val}'")
                    tbl_errors += 1

            for col in fk_cols.get(tbl, []):
                old_val = unquote(old_row.get(col))
                new_val = unquote(new_row.get(col))
                if old_val != new_val:
                    errors.append(f"{tbl} id={row_id}: FK {col} changed {old_val} -> {new_val}")
                    tbl_errors += 1

        if tbl_errors == 0:
            print(f"  {tbl}: ALL amounts, dates, FKs intact  OK")
        else:
            print(f"  {tbl}: {tbl_errors} ERRORS")

    # ================================================================
    # CHECK 2: No duplicate invoices in primary tables
    # ================================================================
    print("\n=== Check 2: Invoice uniqueness ===")

    for tbl in ['sales', 'purchases', 'expenses', 'expense_supplier_payments']:
        inv_col = tables[tbl]['invoice_col']
        seen = defaultdict(list)
        for r in new_data[tbl]:
            inv = unquote(r.get(inv_col))
            if inv:
                seen[inv].append(unquote(r.get('id')))

        dups = {k: v for k, v in seen.items() if len(v) > 1}
        if dups:
            for inv, ids in dups.items():
                errors.append(f"{tbl}: DUPLICATE invoice '{inv}' in rows {ids}")
            print(f"  {tbl}: {len(dups)} DUPLICATES found")
        else:
            print(f"  {tbl}: all {len(seen)} invoices unique  OK")

    # ================================================================
    # CHECK 3: Cross-reference integrity
    # ================================================================
    print("\n=== Check 3: Cross-reference integrity ===")

    # Build lookup maps from new data
    new_sales_by_id = {}
    new_sales_inv_set = set()
    for r in new_data['sales']:
        inv = unquote(r.get('invoice'))
        sid = unquote(r.get('id'))
        if inv:
            new_sales_by_id[sid] = inv
            new_sales_inv_set.add(inv)

    new_purchases_by_id = {}
    new_purchase_inv_set = set()
    for r in new_data['purchases']:
        inv = unquote(r.get('invoice_number'))
        pid = unquote(r.get('id'))
        if inv:
            new_purchases_by_id[pid] = inv
            new_purchase_inv_set.add(inv)

    new_expenses_inv_set = set()
    for r in new_data['expenses']:
        inv = unquote(r.get('invoice'))
        if inv:
            new_expenses_inv_set.add(inv)

    # --- customer_dues.invoice -> sales.invoice ---
    cd_ok = cd_fail = 0
    for r in new_data['customer_dues']:
        inv = unquote(r.get('invoice'))
        if not inv:
            continue
        if inv in new_sales_inv_set:
            cd_ok += 1
        else:
            cd_fail += 1
            warnings.append(f"customer_dues id={unquote(r.get('id'))}: invoice '{inv}' not in sales")
    print(f"  customer_dues -> sales: {cd_ok} OK, {cd_fail} missing")

    # --- supplier_payments: if has purchase_id, invoice should match ---
    sp_ok = sp_fail = sp_standalone = 0
    for r in new_data['supplier_payments']:
        inv = unquote(r.get('invoice'))
        purchase_id = unquote(r.get('purchase_id'))
        if not inv:
            sp_standalone += 1
            continue
        if purchase_id and purchase_id in new_purchases_by_id:
            expected = new_purchases_by_id[purchase_id]
            if inv == expected:
                sp_ok += 1
            else:
                sp_fail += 1
                errors.append(f"supplier_payments id={unquote(r.get('id'))}: invoice='{inv}' but purchase {purchase_id} has '{expected}'")
        elif inv in new_purchase_inv_set:
            sp_ok += 1
        else:
            sp_standalone += 1
    print(f"  supplier_payments -> purchases: {sp_ok} OK, {sp_fail} MISMATCH, {sp_standalone} standalone/no-inv")

    # --- customer_payments: if has sale_id, check consistency ---
    cp_with = cp_null = 0
    for r in new_data['customer_payments']:
        inv = unquote(r.get('invoice'))
        if inv:
            cp_with += 1
        else:
            cp_null += 1
    print(f"  customer_payments: {cp_with} with invoice, {cp_null} NULL")

    # --- ledgers type=sale -> sales.invoice ---
    ls_ok = ls_fail = lp_ok = lp_fail = le_ok = le_fail = l_other = 0
    for r in new_data['ledgers']:
        inv_no = unquote(r.get('invoice_no'))
        inv_type = unquote(r.get('invoice_type'))
        if not inv_no:
            continue
        if inv_type == 'sale':
            if inv_no in new_sales_inv_set:
                ls_ok += 1
            else:
                ls_fail += 1
                errors.append(f"ledger id={unquote(r.get('id'))}: type=sale, invoice_no='{inv_no}' NOT in sales")
        elif inv_type == 'purchase':
            if inv_no in new_purchase_inv_set:
                lp_ok += 1
            else:
                lp_fail += 1
                errors.append(f"ledger id={unquote(r.get('id'))}: type=purchase, invoice_no='{inv_no}' NOT in purchases")
        elif inv_type == 'Expense':
            if inv_no in new_expenses_inv_set:
                le_ok += 1
            else:
                le_fail += 1
                errors.append(f"ledger id={unquote(r.get('id'))}: type=Expense, invoice_no='{inv_no}' NOT in expenses")
        else:
            l_other += 1

    print(f"  ledgers(sale) -> sales: {ls_ok} OK, {ls_fail} MISMATCH")
    print(f"  ledgers(purchase) -> purchases: {lp_ok} OK, {lp_fail} MISMATCH")
    print(f"  ledgers(Expense) -> expenses: {le_ok} OK, {le_fail} MISMATCH")
    print(f"  ledgers(other types): {l_other} (self-generated DRL/SDL/CAL/SAL/etc)")

    # --- ledger_details.invoice -> any primary table ---
    all_known = new_sales_inv_set | new_purchase_inv_set | new_expenses_inv_set
    ld_ok = ld_fail = ld_null = 0
    for r in new_data['ledger_details']:
        inv = unquote(r.get('invoice'))
        if not inv:
            ld_null += 1
            continue
        if inv in all_known or inv == 'DIRECT-BALANCE':
            ld_ok += 1
        else:
            ld_fail += 1
            warnings.append(f"ledger_details id={unquote(r.get('id'))} (ledger={unquote(r.get('ledger_id'))}): invoice '{inv}' not in any primary table")
    print(f"  ledger_details -> primary tables: {ld_ok} OK, {ld_fail} unmatched, {ld_null} NULL")

    # ================================================================
    # CHECK 4: Amount totals match between old and new
    # ================================================================
    print("\n=== Check 4: Amount totals (old vs new) ===")

    for tbl, cols in amount_cols.items():
        for col in cols:
            old_total = 0.0
            new_total = 0.0
            for r in old_data[tbl]:
                v = unquote(r.get(col))
                try:
                    old_total += float(v) if v else 0
                except (ValueError, TypeError):
                    pass
            for r in new_data[tbl]:
                v = unquote(r.get(col))
                try:
                    new_total += float(v) if v else 0
                except (ValueError, TypeError):
                    pass
            diff = abs(old_total - new_total)
            status = "OK" if diff < 0.01 else f"*** MISMATCH diff={diff:.2f} ***"
            if diff >= 0.01:
                errors.append(f"{tbl}.{col}: old={old_total:.2f}, new={new_total:.2f}, diff={diff:.2f}")
            print(f"  {tbl}.{col}: old={old_total:>14,.2f}  new={new_total:>14,.2f}  {status}")

    # ================================================================
    # CHECK 5: Invoice mapping preserved across tables
    # ================================================================
    print("\n=== Check 5: Invoice mapping preserved across tables ===")

    # Build old->new mapping from primary tables
    old_sales_by_id = build_index(old_data['sales'], 'id')
    new_sales_by_id_full = build_index(new_data['sales'], 'id')
    sale_inv_map = {}
    for sid in old_sales_by_id:
        old_inv = unquote(old_sales_by_id[sid].get('invoice'))
        new_inv = unquote(new_sales_by_id_full[sid].get('invoice')) if sid in new_sales_by_id_full else None
        if old_inv and new_inv:
            sale_inv_map[old_inv] = new_inv

    old_purchases_by_id = build_index(old_data['purchases'], 'id')
    new_purchases_by_id_full = build_index(new_data['purchases'], 'id')
    purchase_inv_map = {}
    for pid in old_purchases_by_id:
        old_inv = unquote(old_purchases_by_id[pid].get('invoice_number'))
        new_inv = unquote(new_purchases_by_id_full[pid].get('invoice_number')) if pid in new_purchases_by_id_full else None
        if old_inv and new_inv:
            purchase_inv_map[old_inv] = new_inv

    # Verify customer_dues used the correct mapping
    old_cd_by_id = build_index(old_data['customer_dues'], 'id')
    new_cd_by_id = build_index(new_data['customer_dues'], 'id')
    cd_map_ok = cd_map_fail = cd_map_skip = 0
    for cid in old_cd_by_id:
        old_inv = unquote(old_cd_by_id[cid].get('invoice'))
        new_inv = unquote(new_cd_by_id[cid].get('invoice')) if cid in new_cd_by_id else None
        if not old_inv or not new_inv:
            cd_map_skip += 1
            continue
        expected = sale_inv_map.get(old_inv)
        if expected and new_inv == expected:
            cd_map_ok += 1
        elif expected and new_inv != expected:
            cd_map_fail += 1
            errors.append(f"customer_dues id={cid}: old='{old_inv}' expected->'{expected}' but got '{new_inv}'")
        elif old_inv == new_inv:
            cd_map_skip += 1
        else:
            cd_map_skip += 1
    print(f"  customer_dues mapping: {cd_map_ok} correct, {cd_map_fail} wrong, {cd_map_skip} skipped/unmapped")

    # Verify supplier_payments used the correct mapping
    old_sp_by_id = build_index(old_data['supplier_payments'], 'id')
    new_sp_by_id = build_index(new_data['supplier_payments'], 'id')
    sp_map_ok = sp_map_fail = sp_map_skip = 0
    for spid in old_sp_by_id:
        old_inv = unquote(old_sp_by_id[spid].get('invoice'))
        new_inv = unquote(new_sp_by_id[spid].get('invoice')) if spid in new_sp_by_id else None
        if not old_inv or not new_inv:
            sp_map_skip += 1
            continue
        expected = purchase_inv_map.get(old_inv)
        if expected and new_inv == expected:
            sp_map_ok += 1
        elif expected and new_inv != expected:
            sp_map_fail += 1
            errors.append(f"supplier_payments id={spid}: old='{old_inv}' expected->'{expected}' but got '{new_inv}'")
        elif old_inv == new_inv:
            sp_map_skip += 1
        else:
            sp_map_skip += 1
    print(f"  supplier_payments mapping: {sp_map_ok} correct, {sp_map_fail} wrong, {sp_map_skip} skipped/unmapped")

    # ================================================================
    # SUMMARY
    # ================================================================
    print("\n" + "=" * 60)
    print(f"  ERRORS:   {len(errors)}")
    print(f"  WARNINGS: {len(warnings)}")
    print("=" * 60)

    if errors:
        print("\n--- ERRORS ---")
        for e in errors[:40]:
            print(f"  {e}")
        if len(errors) > 40:
            print(f"  ... and {len(errors) - 40} more")

    if warnings:
        print("\n--- WARNINGS ---")
        for w in warnings[:20]:
            print(f"  {w}")
        if len(warnings) > 20:
            print(f"  ... and {len(warnings) - 20} more")

    if not errors:
        print("\n  ALL CHECKS PASSED!")

    return 1 if errors else 0


if __name__ == '__main__':
    sys.exit(main())
