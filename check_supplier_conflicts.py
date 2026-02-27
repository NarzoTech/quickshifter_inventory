import re
from collections import defaultdict

with open('bikersfu_inventory.sql', 'r', encoding='utf-8') as f:
    content = f.read()


def parse_row_simple(line):
    """Parse a SQL row into parts, handling quotes."""
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
# 1. Check purchases: same supplier + same invoice
# ============================================================
# Columns: id(0), supplier_id(1), warehouse_id(2), invoice_number(3),
#           memo_no(4), reference_no(5), purchase_date(6), ...
print("=== PURCHASES: Same Supplier + Same Invoice ===")
purchases = []
for m in re.finditer(r'INSERT INTO `purchases` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 7:
            purchases.append({
                'id': unq(parts[0].lstrip('(')),
                'supplier_id': unq(parts[1]),
                'invoice': unq(parts[3]),
                'purchase_date': unq(parts[6]),
            })

print(f"Total purchases: {len(purchases)}")

supplier_invoice_map = defaultdict(list)
for p in purchases:
    if p['invoice']:
        supplier_invoice_map[(p['supplier_id'], p['invoice'])].append(p)

found = 0
for (sid, inv), rows in sorted(supplier_invoice_map.items()):
    if len(rows) > 1:
        print(f"  Supplier {sid}, Invoice {inv}:")
        for r in rows:
            print(f"    Purchase ID={r['id']}, Date={r['purchase_date']}")
        found += 1

if found == 0:
    print("  None found - each supplier+invoice is unique!")
else:
    print(f"\n  Total: {found} dangerous supplier duplicates")


# ============================================================
# 2. Check supplier_payments: same supplier + same invoice
# ============================================================
# Columns: id(0), purchase_id(1), supplier_id(2), ledger_id(3), account_id(4),
#           purchase_return_id(5), invoice(6), ...
print("\n=== SUPPLIER PAYMENTS: Same Supplier + Same Invoice ===")
sp_rows = []
for m in re.finditer(r'INSERT INTO `supplier_payments` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 7:
            sp_rows.append({
                'id': unq(parts[0].lstrip('(')),
                'purchase_id': unq(parts[1]),
                'supplier_id': unq(parts[2]),
                'invoice': unq(parts[6]),
            })

print(f"Total supplier_payments: {len(sp_rows)}")

sp_map = defaultdict(list)
for sp in sp_rows:
    if sp['invoice']:
        sp_map[(sp['supplier_id'], sp['invoice'])].append(sp)

found2 = 0
for (sid, inv), rows in sorted(sp_map.items()):
    if len(rows) > 1:
        print(f"  Supplier {sid}, Invoice {inv}: Payment IDs = {[r['id'] for r in rows]}, Purchase IDs = {[r['purchase_id'] for r in rows]}")
        found2 += 1

if found2 == 0:
    print("  None found!")
else:
    print(f"\n  Total: {found2} duplicates")


# ============================================================
# 3. Check ledgers for supplier: same supplier + same invoice_no
# ============================================================
print("\n=== LEDGERS (supplier): Same Supplier + Same Invoice ===")
# Columns: id(0), customer_id(1), supplier_id(2), expense_supplier_id(3), sale_return_id(4),
#           amount(5), total_amount(6), is_paid(7), is_received(8), due_amount(9),
#           invoice_type(10), invoice_url(11), invoice_no(12), ...
ledger_rows = []
for m in re.finditer(r'INSERT INTO `ledgers` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for line in m.group(1).split('\n'):
        line = line.strip()
        if not line.startswith('('):
            continue
        parts = parse_row_simple(line)
        if len(parts) >= 13:
            supplier_id = unq(parts[2])
            if supplier_id:
                ledger_rows.append({
                    'id': unq(parts[0].lstrip('(')),
                    'supplier_id': supplier_id,
                    'invoice_type': unq(parts[10]),
                    'invoice_no': unq(parts[12]),
                    'created_at': unq(parts[-2]) if len(parts) >= 18 else None,
                })

print(f"Total supplier ledgers: {len(ledger_rows)}")

led_map = defaultdict(list)
for l in ledger_rows:
    if l['invoice_no'] and l['invoice_type'] == 'purchase':
        led_map[(l['supplier_id'], l['invoice_no'])].append(l)

found3 = 0
for (sid, inv), rows in sorted(led_map.items()):
    if len(rows) > 1:
        print(f"  Supplier {sid}, Invoice {inv}, Type=purchase: Ledger IDs = {[r['id'] for r in rows]}")
        found3 += 1

if found3 == 0:
    print("  None found!")
else:
    print(f"\n  Total: {found3} duplicates")
