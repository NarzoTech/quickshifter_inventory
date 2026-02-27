import re
from collections import defaultdict

with open('bikersfu_inventory.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Parse sales rows properly
def parse_values(block):
    rows = []
    current = ''
    in_quote = False
    escape = False
    depth = 0
    for ch in block:
        if escape:
            current += ch
            escape = False
            continue
        if ch == '\\':
            current += ch
            escape = True
            continue
        if ch == "'" and not escape:
            in_quote = not in_quote
            current += ch
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
            if depth == 0:
                rows.append(current.strip())
                current = ''
        elif depth > 0:
            current += ch
    return rows

def parse_row(row_text):
    row_text = row_text.strip()
    if row_text.startswith('('):
        row_text = row_text[1:]
    if row_text.endswith(')'):
        row_text = row_text[:-1]
    parts = []
    current = ''
    in_quote = False
    escape = False
    for ch in row_text:
        if escape:
            current += ch
            escape = False
            continue
        if ch == '\\':
            current += ch
            escape = True
            continue
        if ch == "'":
            in_quote = not in_quote
            current += ch
            continue
        if in_quote:
            current += ch
            continue
        if ch == ',':
            parts.append(current.strip())
            current = ''
        else:
            current += ch
    parts.append(current.strip())
    return parts

def unquote(val):
    val = val.strip()
    if val.upper() == 'NULL':
        return None
    if val.startswith("'") and val.endswith("'"):
        return val[1:-1]
    return val

# Extract sales
# Columns: id(0), user_id(1), customer_id(2), warehouse_id(3), quantity(4), total_price(5),
#           order_date(6), ..., invoice(22), ...
sales = []
for m in re.finditer(r'INSERT INTO `sales` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for row_text in parse_values(m.group(1)):
        parts = parse_row(row_text)
        if len(parts) >= 23:
            sale_id = unquote(parts[0])
            user_id = unquote(parts[1])
            customer_id = unquote(parts[2])
            order_date = unquote(parts[6])
            invoice = unquote(parts[22])
            sales.append({
                'id': sale_id,
                'user_id': user_id,
                'customer_id': customer_id,
                'order_date': order_date,
                'invoice': invoice,
            })

print(f"Total sales: {len(sales)}")

# Check: same customer_id + same invoice = dangerous
customer_invoice_map = defaultdict(list)
for s in sales:
    cid = s['customer_id']
    inv = s['invoice']
    customer_invoice_map[(cid, inv)].append(s['id'])

print("\n=== Same Customer + Same Invoice (dangerous duplicates) ===")
found = 0
for (cid, inv), sids in sorted(customer_invoice_map.items()):
    if len(sids) > 1:
        print(f"  Customer {cid}, Invoice {inv}: Sale IDs = {sids}")
        found += 1

if found == 0:
    print("  None found - each customer+invoice combination is unique!")
else:
    print(f"  Found {found} dangerous duplicates")

# Check customer_dues
# Columns: id(0), customer_id(1), due_date(2), invoice(3), ...
print("\n=== Customer Dues: same customer + same invoice ===")
cd_map = defaultdict(list)
for m in re.finditer(r'INSERT INTO `customer_dues` \([^)]+\) VALUES\s*\n(.*?);\n', content, re.DOTALL):
    for row_text in parse_values(m.group(1)):
        parts = parse_row(row_text)
        if len(parts) >= 4:
            cd_id = unquote(parts[0])
            cd_cust = unquote(parts[1])
            cd_inv = unquote(parts[3])
            cd_map[(cd_cust, cd_inv)].append(cd_id)

found2 = 0
for (cid, inv), ids in sorted(cd_map.items()):
    if len(ids) > 1:
        print(f"  Customer {cid}, Invoice {inv}: Due IDs = {ids}")
        found2 += 1

if found2 == 0:
    print("  None found - each customer+invoice due is unique!")

# Show how many duplicate invoices exist overall
print("\n=== Invoice duplication overview ===")
inv_map = defaultdict(list)
for s in sales:
    inv_map[s['invoice']].append(s)

dup_count = sum(1 for inv, rows in inv_map.items() if len(rows) > 1)
print(f"  Total unique invoices: {len(inv_map)}")
print(f"  Invoices appearing more than once: {dup_count}")

# Show a few examples
print("\n=== Sample duplicates (first 5) ===")
shown = 0
for inv, rows in sorted(inv_map.items(), key=lambda x: x[0]):
    if len(rows) > 1:
        print(f"  {inv}:")
        for r in rows:
            print(f"    Sale ID={r['id']}, Customer={r['customer_id']}, Date={r['order_date']}")
        shown += 1
        if shown >= 5:
            break
