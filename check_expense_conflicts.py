import re
from collections import defaultdict

with open('bikersfu_inventory.sql', 'r', encoding='utf-8') as f:
    content = f.read()


def parse_row_simple(line):
    parts = []
    curr = ''
    in_q = False
    esc = False
    for ch in line:
        if esc:
            curr += ch
            esc = False
            continue
        if ch == '\\':
            curr += ch
            esc = True
            continue
        if ch == "'":
            in_q = not in_q
            curr += ch
            continue
        if in_q:
            curr += ch
            continue
        if ch == ',' and not in_q:
            parts.append(curr.strip())
            curr = ''
        else:
            curr += ch
    parts.append(curr.strip().rstrip('),'))
    return parts


def unq(val):
    val = val.strip()
    if val.upper() == 'NULL':
        return None
    return val.strip("'")


# ============================================================
# 1. Expenses: duplicate invoices
# ============================================================
# Columns: id(0), invoice(1), date(2), payment_type(3), account_id(4),
#           ..., expense_supplier_id(14), ...
print("=== EXPENSES: Duplicate Invoices ===")
expenses = []
for m in re.finditer(r'INSERT INTO `expenses` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 15:
            expenses.append({
                'id': unq(parts[0].lstrip('(')),
                'invoice': unq(parts[1]),
                'date': unq(parts[2]),
                'expense_supplier_id': unq(parts[14]),
            })

print(f"Total expenses: {len(expenses)}")

inv_map = defaultdict(list)
for e in expenses:
    if e['invoice']:
        inv_map[e['invoice']].append(e)

dup_count = 0
for inv, rows in sorted(inv_map.items()):
    if len(rows) > 1:
        print(f"  Invoice {inv}:")
        for r in rows:
            print(f"    Expense ID={r['id']}, Date={r['date']}, Supplier={r['expense_supplier_id']}")
        dup_count += 1

if dup_count == 0:
    print("  None found - all expense invoices are unique!")
else:
    print(f"\n  Total: {dup_count} duplicate expense invoices")


# ============================================================
# 2. Expenses: same supplier + same invoice
# ============================================================
print("\n=== EXPENSES: Same Supplier + Same Invoice ===")
sup_inv_map = defaultdict(list)
for e in expenses:
    if e['invoice'] and e['expense_supplier_id']:
        sup_inv_map[(e['expense_supplier_id'], e['invoice'])].append(e)

found = 0
for (sid, inv), rows in sorted(sup_inv_map.items()):
    if len(rows) > 1:
        print(f"  Supplier {sid}, Invoice {inv}:")
        for r in rows:
            print(f"    Expense ID={r['id']}, Date={r['date']}")
        found += 1

if found == 0:
    print("  None found!")
else:
    print(f"\n  Total: {found} duplicates")


# ============================================================
# 3. Expense Supplier Payments: duplicate invoices
# ============================================================
# Columns: id(0), expense_id(1), expense_supplier_id(2), account_id(3),
#           ledger_id(4), invoice(5), ...
print("\n=== EXPENSE SUPPLIER PAYMENTS: Duplicate Invoices ===")
esp_rows = []
for m in re.finditer(r'INSERT INTO `expense_supplier_payments` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 6:
            esp_rows.append({
                'id': unq(parts[0].lstrip('(')),
                'expense_id': unq(parts[1]),
                'expense_supplier_id': unq(parts[2]),
                'invoice': unq(parts[5]),
                'payment_date': unq(parts[11]) if len(parts) >= 12 else None,
            })

print(f"Total expense_supplier_payments: {len(esp_rows)}")

esp_inv_map = defaultdict(list)
for esp in esp_rows:
    if esp['invoice']:
        esp_inv_map[esp['invoice']].append(esp)

dup2 = 0
for inv, rows in sorted(esp_inv_map.items()):
    if len(rows) > 1:
        print(f"  Invoice {inv}:")
        for r in rows:
            print(f"    Payment ID={r['id']}, Expense ID={r['expense_id']}, Supplier={r['expense_supplier_id']}")
        dup2 += 1

if dup2 == 0:
    print("  None found - all ESP invoices are unique!")
else:
    print(f"\n  Total: {dup2} duplicate ESP invoices")


# ============================================================
# 4. Ledgers (expense type): same expense_supplier + same invoice_no
# ============================================================
print("\n=== LEDGERS (expense types): Same Expense Supplier + Same Invoice ===")
ledger_rows = []
for m in re.finditer(r'INSERT INTO `ledgers` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 13:
            esid = unq(parts[3])
            if esid:
                ledger_rows.append({
                    'id': unq(parts[0].lstrip('(')),
                    'expense_supplier_id': esid,
                    'invoice_type': unq(parts[10]),
                    'invoice_no': unq(parts[12]),
                })

print(f"Total expense supplier ledgers: {len(ledger_rows)}")

# Group by type
for inv_type in ['Expense', 'Expense Due Payment']:
    led_map = defaultdict(list)
    for l in ledger_rows:
        if l['invoice_no'] and l['invoice_type'] == inv_type:
            led_map[(l['expense_supplier_id'], l['invoice_no'])].append(l)

    found3 = 0
    for (sid, inv), rows in sorted(led_map.items()):
        if len(rows) > 1:
            print(f"  Supplier {sid}, Invoice {inv}, Type={inv_type}: Ledger IDs = {[r['id'] for r in rows]}")
            found3 += 1

    if found3 == 0:
        print(f"  Type '{inv_type}': None found!")
    else:
        print(f"  Type '{inv_type}': {found3} duplicates")


# ============================================================
# 5. Ledger Details referencing EXP-: any issues?
# ============================================================
print("\n=== LEDGER DETAILS: EXP- references ===")
ld_exp = defaultdict(list)
for m in re.finditer(r'INSERT INTO `ledger_details` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 3:
            ld_id = unq(parts[0].lstrip('('))
            ledger_id = unq(parts[1])
            invoice = unq(parts[2])
            if invoice and invoice.startswith('EXP-'):
                ld_exp[invoice].append({'id': ld_id, 'ledger_id': ledger_id})

dup4 = 0
for inv, rows in sorted(ld_exp.items()):
    if len(rows) > 1:
        print(f"  {inv}: Detail IDs = {[r['id'] for r in rows]}, Ledger IDs = {[r['ledger_id'] for r in rows]}")
        dup4 += 1

if dup4 == 0:
    print("  None found - all EXP- references are unique!")
else:
    print(f"\n  Total: {dup4} duplicate EXP- references")
