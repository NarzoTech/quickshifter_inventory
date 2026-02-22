# QuickShifter Inventory - Complete Testing Guide

> **Purpose:** Step-by-step testing guide covering every transaction flow from a user perspective.
> All variations, edge cases, and expected behaviors documented.

---

## Table of Contents

1. [Initial Setup & Login](#1-initial-setup--login)
2. [Master Data Setup](#2-master-data-setup)
3. [Product Management](#3-product-management)
4. [Customer Management](#4-customer-management)
5. [Supplier Management](#5-supplier-management)
6. [Account & Banking Setup](#6-account--banking-setup)
7. [Purchase Transactions](#7-purchase-transactions)
8. [Sales Transactions (Invoice)](#8-sales-transactions-invoice)
9. [POS (Point of Sale)](#9-pos-point-of-sale)
10. [Customer Due & Advance](#10-customer-due--advance)
11. [Supplier Due & Advance](#11-supplier-due--advance)
12. [Sales Return](#12-sales-return)
13. [Purchase Return](#13-purchase-return)
14. [Expense Management](#14-expense-management)
15. [Expense Supplier Due & Advance](#15-expense-supplier-due--advance)
16. [Quotation & Convert to Sale](#16-quotation--convert-to-sale)
17. [Stock Management](#17-stock-management)
18. [Balance Transfer](#18-balance-transfer)
19. [Employee & Salary](#19-employee--salary)
20. [Reports](#20-reports)
21. [Ledger Verification](#21-ledger-verification)
22. [End-to-End Scenarios](#22-end-to-end-scenarios)

---

## 1. Initial Setup & Login

### TC-1.1: Admin Login
| Step | Action | Expected |
|------|--------|----------|
| 1 | Go to `/admin/login` | Login form displayed |
| 2 | Enter valid email & password | Redirected to `/admin/dashboard` |
| 3 | Enter wrong credentials | Error message shown, stays on login |

### TC-1.2: Dashboard Verification
| Step | Action | Expected |
|------|--------|----------|
| 1 | After login, view dashboard | Shows summary cards: total sales, purchases, customers, stock value |
| 2 | Check sidebar navigation | All modules visible based on role permissions |

---

## 2. Master Data Setup

### TC-2.1: Warehouse
| Route | `/admin/warehouse` |
|-------|---------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create warehouse with name, address | Warehouse listed |
| 2 | Edit warehouse | Updated successfully |
| 3 | Delete warehouse (if no transactions linked) | Deleted successfully |

### TC-2.2: Product Category
| Route | `/admin/products/category` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create category with name | Category listed |
| 2 | Create sub-category under parent | Shows hierarchy |
| 3 | Edit category name | Updated |
| 4 | Delete category (no products linked) | Deleted |
| 5 | Delete category (has products) | Should show error/warning |

### TC-2.3: Product Brand
| Route | `/admin/products/brand` |
|-------|--------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create brand with name | Brand listed |
| 2 | Edit brand | Updated |
| 3 | Delete brand | Deleted |

### TC-2.4: Product Unit
| Route | `/admin/products/unit` |
|-------|-------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create unit type (e.g., PCS, KG, Box) | Unit listed |
| 2 | Edit unit | Updated |
| 3 | Delete unit | Deleted |

### TC-2.5: Product Attribute
| Route | `/admin/products/attribute` |
|-------|------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create attribute (e.g., Color, Size) | Attribute listed |
| 2 | Add attribute values (Red, Blue, S, M, L) | Values shown |
| 3 | Edit attribute name/values | Updated |
| 4 | Delete attribute | Deleted |

### TC-2.6: Customer Group
| Route | `/admin/customerGroup` |
|-------|--------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create group (e.g., Retail, Wholesale) | Group listed |
| 2 | Edit/Delete group | Works correctly |

### TC-2.7: Supplier Group
| Route | `/admin/supplierGroup` |
|-------|--------------------------|

### TC-2.8: Area
| Route | `/admin/area` |
|-------|----------------|

### TC-2.9: Vehicle
| Route | `/admin/vehicle` |
|-------|-------------------|

### TC-2.10: Tax
| Route | `/admin/tax` |
|-------|---------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create tax with name & percentage | Tax listed |
| 2 | Edit percentage | Updated |
| 3 | Delete tax | Deleted |

### TC-2.11: Expense Type
| Route | `/admin/expenseType` |
|-------|------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create type (e.g., Rent, Electricity) | Type listed |
| 2 | Edit/Delete | Works |

### TC-2.12: Bank
| Route | `/admin/bank` |
|-------|----------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create bank with name | Bank listed |

### TC-2.13: Currency
| Route | `/admin/currency` |
|-------|---------------------|

---

## 3. Product Management

### TC-3.1: Create Simple Product
| Route | `/admin/product/create` |
|-------|--------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Fill: Name, SKU, Category, Brand, Unit | Required fields validated |
| 2 | Set purchase price (cost), selling price | Prices saved |
| 3 | Set stock alert quantity | Alert threshold set |
| 4 | Upload product image | Image displayed |
| 5 | Save product | Product listed with stock = 0 |

### TC-3.2: Create Product with Variants
| Step | Action | Expected |
|------|--------|----------|
| 1 | Create base product first | Product created |
| 2 | Go to `/admin/product/related-variant/{id}` | Variant page shown |
| 3 | Add variant with attribute values (e.g., Color: Red, Size: M) | Variant created with own SKU |
| 4 | Set variant-specific prices | Each variant has own price |
| 5 | Verify variant appears in sales/purchase product search | Variant selectable |

### TC-3.3: Edit Product
| Route | `/admin/product/{id}/edit` |
|-------|------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Change name, prices, category | Updated |
| 2 | Change status (active/inactive) | Inactive products hidden from POS/Sales |
| 3 | Upload new image | Old image replaced |

### TC-3.4: Delete Product
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete product with no transactions | Deleted successfully |
| 2 | Delete product with transactions | Should handle gracefully |

### TC-3.5: Product Import (Bulk)
| Route | `/admin/product/import` |
|-------|--------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Download template | CSV/Excel template downloaded |
| 2 | Fill template with product data | - |
| 3 | Upload filled template | Products imported |

### TC-3.6: Barcode
| Route | `/admin/product/barcode` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select product(s) | Products listed |
| 2 | Set barcode quantity | Barcodes generated |
| 3 | Print barcodes | Printable barcode sheet |

### TC-3.7: Product Status Toggle
| Step | Action | Expected |
|------|--------|----------|
| 1 | Toggle product active/inactive | Status updated |
| 2 | Inactive product NOT shown in POS | Verified hidden |

---

## 4. Customer Management

### TC-4.1: Create Customer (No Opening Balance)
| Route | `/admin/customers/create` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Fill: Name, Phone, Email | Required fields |
| 2 | Select Group, Area, Vehicle | Optional fields |
| 3 | Leave wallet_balance = 0 | No opening due |
| 4 | Save | Customer created, total due = 0 |

### TC-4.2: Create Customer (With Opening Due)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Fill all fields | - |
| 2 | Set wallet_balance = 5000 (positive) | Opening due of 5000 |
| 3 | Save | Customer created |
| 4 | Check customer list | Due shown as 5000 |
| 5 | Check customer ledger | Opening balance = 5000 (debit) |

**Verify:** `wallet_balance` = 5000 means customer OWES you 5000 from previous system/opening.

### TC-4.3: Edit Customer
| Route | `/admin/customers/{id}/edit` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Change name, phone | Updated |
| 2 | Change wallet_balance | Opening balance adjusted |
| 3 | Change group/area | Updated |

### TC-4.4: Delete Customer
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete customer with no transactions | Deleted |
| 2 | Delete customer with sales/payments | Should warn or prevent |

---

## 5. Supplier Management

### TC-5.1: Create Supplier
| Route | `/admin/suppliers/create` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Fill: Name, Company, Phone, Email | Required fields |
| 2 | Select Group, Area | Optional |
| 3 | Save | Supplier created, due = 0 |

**Note:** Supplier has NO opening balance field (unlike customer). Advance is tracked via payments only.

### TC-5.2: Edit/Delete Supplier
| Step | Action | Expected |
|------|--------|----------|
| 1 | Edit supplier info | Updated |
| 2 | Delete supplier (no purchases) | Deleted |
| 3 | Delete supplier (has purchases) | Should handle gracefully |

---

## 6. Account & Banking Setup

### TC-6.1: Create Cash Account
| Route | `/admin/accounts/create` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select type = Cash | Minimal fields shown |
| 2 | Enter account holder name | - |
| 3 | Save | Cash account created |

### TC-6.2: Create Bank Account
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select type = Bank | Bank fields shown |
| 2 | Select bank from list | - |
| 3 | Enter account number, branch | - |
| 4 | Save | Bank account created |

### TC-6.3: Create Mobile Banking Account
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select type = Mobile Banking | Mobile fields shown |
| 2 | Select provider (bKash, Nagad, Rocket, etc.) | - |
| 3 | Enter mobile number | - |
| 4 | Save | Mobile banking account created |

### TC-6.4: Create Card Account
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select type = Card | Card fields shown |
| 2 | Enter card number, bank | - |
| 3 | Save | Card account created |

### TC-6.5: Opening Balance - Deposit
| Route | `/admin/opening-balance` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select account | Account selected |
| 2 | Enter amount = 50000 | - |
| 3 | Select type = Deposit | - |
| 4 | Set date | - |
| 5 | Save | Balance record created |
| 6 | Check account ledger | Opening deposit shown |

### TC-6.6: Opening Balance - Withdraw
| Step | Action | Expected |
|------|--------|----------|
| 1 | Same as above but type = Withdraw | - |
| 2 | Save | Withdrawal recorded |
| 3 | Check account balance | Reduced by withdraw amount |

---

## 7. Purchase Transactions

### TC-7.1: Create Purchase - Full Payment (Cash)
| Route | `/admin/purchase/create` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select supplier | Supplier selected |
| 2 | Invoice number auto-generated | Unique number shown |
| 3 | Enter memo no, reference (optional) | - |
| 4 | Set purchase date | Date set |
| 5 | Search & add Product A: qty=10, price=100 | Sub-total = 1000 |
| 6 | Search & add Product B: qty=5, price=200 | Sub-total = 1000 |
| 7 | Verify total = 2000 | Total amount correct |
| 8 | Payment type = Cash | Cash selected |
| 9 | Paid amount = 2000, Due = 0 | Full payment |
| 10 | Save | Purchase created |

**Verify After Save:**
- [ ] Purchase listed with status = "Paid"
- [ ] Product A stock increased by 10
- [ ] Product B stock increased by 5
- [ ] Product A cost updated to 100, selling price updated
- [ ] Product B cost updated to 200, selling price updated
- [ ] Supplier ledger shows: Debit = 2000, Credit = 2000, Balance = 0
- [ ] Cash account ledger shows debit of 2000
- [ ] Stock ledger shows "Purchase" entries for both products (in_quantity)

### TC-7.2: Create Purchase - Partial Payment (Due)
| Step | Action | Expected |
|------|--------|----------|
| 1-6 | Same as TC-7.1 | Total = 2000 |
| 7 | Payment type = Cash | - |
| 8 | Paid amount = 1200 | Due auto-calculated |
| 9 | Due amount = 800 | Shown in due field |
| 10 | Save | Purchase created |

**Verify After Save:**
- [ ] Purchase status = "Due"
- [ ] paid_amount = 1200, due_amount = 800
- [ ] Supplier due increased by 800
- [ ] Supplier ledger: Debit = 2000 (goods), Credit = 1200 (paid), Balance = 800
- [ ] Stock increased same as full payment

### TC-7.3: Create Purchase - No Payment (Full Due)
| Step | Action | Expected |
|------|--------|----------|
| 1-6 | Same as TC-7.1 | Total = 2000 |
| 7 | Paid amount = 0 | Due = 2000 |
| 8 | Save | Purchase created |

**Verify:**
- [ ] Purchase status = "Due"
- [ ] paid_amount = 0, due_amount = 2000
- [ ] Supplier total due = 2000
- [ ] No payment record created (amount = 0)

### TC-7.4: Create Purchase - Multiple Payment Methods
| Step | Action | Expected |
|------|--------|----------|
| 1-6 | Same as TC-7.1 | Total = 2000 |
| 7 | Add payment row 1: Cash = 1000 | First payment |
| 8 | Add payment row 2: Bank = 800 (select bank account) | Second payment |
| 9 | Due = 200 | Remaining |
| 10 | Save | Purchase created |

**Verify:**
- [ ] Two SupplierPayment records created
- [ ] Payment 1: Cash account, amount=1000
- [ ] Payment 2: Bank account, amount=800
- [ ] paid_amount = 1800, due_amount = 200
- [ ] Both accounts show debit in their ledgers

### TC-7.5: Edit Purchase
| Route | `/admin/purchase/{id}/edit` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open existing purchase for edit | Form pre-filled |
| 2 | Change Product A qty from 10 to 15 | Sub-total recalculated |
| 3 | Remove Product B | Total reduced |
| 4 | Adjust paid amount | Due recalculated |
| 5 | Save | Updated |

**Verify After Edit:**
- [ ] Old stock restored first (Product A -10, Product B -5)
- [ ] New stock applied (Product A +15)
- [ ] Product B stock back to original
- [ ] Old payment records deleted, new ones created
- [ ] Supplier ledger updated
- [ ] Stock records deleted and recreated

### TC-7.6: Delete Purchase
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete the purchase | Confirm dialog |
| 2 | Confirm | Deleted |

**Verify After Delete:**
- [ ] Product A stock decreased by qty that was purchased
- [ ] Product B stock decreased by qty that was purchased
- [ ] Supplier ledger entries removed
- [ ] Payment records deleted
- [ ] Stock records deleted
- [ ] Purchase removed from list

### TC-7.7: Print Purchase Invoice
| Route | `/admin/purchase/{id}/invoice` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click invoice/print button | Invoice page opens |
| 2 | Verify details | All amounts, products, supplier info correct |

---

## 8. Sales Transactions (Invoice)

### TC-8.1: Create Sale - Full Payment (Cash), Registered Customer
| Route | `/admin/sales/create` |
|-------|------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select customer from dropdown | Customer selected |
| 2 | Select warehouse | Warehouse set |
| 3 | Set sale date | Date set |
| 4 | Search & add Product A: qty=3, price=150, source=Stock | Sub-total = 450 |
| 5 | Search & add Product B: qty=2, price=250, source=Stock | Sub-total = 500 |
| 6 | Verify subtotal = 950 | Correct |
| 7 | Apply discount = 50 | Discount applied |
| 8 | Tax = 0 | No tax |
| 9 | Grand total = 900 | 950 - 50 = 900 |
| 10 | Payment type = Cash | - |
| 11 | Paying amount = 900 | Full payment |
| 12 | Receive amount = 1000 | Cash received |
| 13 | Return/change = 100 | Change to customer |
| 14 | Due = 0 | No due |
| 15 | Save | Sale created |

**Verify After Save:**
- [ ] Sale listed with invoice number, status paid
- [ ] paid_amount = 900, due_amount = 0
- [ ] Product A stock decreased by 3
- [ ] Product B stock decreased by 2
- [ ] CustomerPayment record: type=sale, is_received=1, amount=900
- [ ] Ledger entry: invoice_type=sale, amount=900, total=900, due=0
- [ ] Stock records: type=Sale, out_quantity for each product
- [ ] Stock profit calculated: (selling_price - purchase_price) x qty

### TC-8.2: Create Sale - Partial Payment (With Due)
| Step | Action | Expected |
|------|--------|----------|
| 1-9 | Same as TC-8.1 | Grand total = 900 |
| 10 | Payment type = Cash | - |
| 11 | Paying amount = 500 | Partial |
| 12 | Due = 400 | Auto-calculated |
| 13 | Set due date | - |
| 14 | Save | Sale created |

**Verify:**
- [ ] Sale: paid_amount=500, due_amount=400
- [ ] CustomerDue record created: due_amount=400, invoice linked
- [ ] Customer total due increased by 400
- [ ] Ledger: amount=500 (paid), due_amount=400, total=900
- [ ] Stock decreased same as full payment (stock doesn't depend on payment)

### TC-8.3: Create Sale - No Payment (Full Due)
| Step | Action | Expected |
|------|--------|----------|
| 1-9 | Same as TC-8.1 | Grand total = 900 |
| 10 | Paying amount = 0 | No payment |
| 11 | Due = 900 | Full due |
| 12 | Set due date | Required when due exists |
| 13 | Save | Sale created |

**Verify:**
- [ ] paid_amount = 0, due_amount = 900
- [ ] CustomerDue: 900
- [ ] No CustomerPayment record (amount = 0 skipped)
- [ ] Ledger: amount=0, due_amount=900

### TC-8.4: Create Sale - Multiple Payment Methods
| Step | Action | Expected |
|------|--------|----------|
| 1-9 | Same as TC-8.1 | Grand total = 900 |
| 10 | Add payment row 1: Cash = 500 | First payment |
| 11 | Add payment row 2: Mobile Banking (bKash) = 300 | Second payment |
| 12 | Due = 100 | Remaining |
| 13 | Save | Sale created |

**Verify:**
- [ ] Two CustomerPayment records
- [ ] Payment 1: account_type=cash, amount=500
- [ ] Payment 2: account_type=mobile_banking, amount=300
- [ ] paid_amount = 800, due_amount = 100
- [ ] Both accounts affected in their ledgers

### TC-8.5: Create Sale - Walk-in Customer (No Customer Selected)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select "Walk-in Customer" or leave customer blank | Walk-in mode |
| 2-9 | Add products, set amounts | Same as normal |
| 10 | Pay full amount | Must pay full (walk-in can't have due typically) |
| 11 | Save | Sale created |

**Verify:**
- [ ] Sale: customer_id = null
- [ ] CustomerPayment: customer_id=null, is_guest=1
- [ ] NO CustomerDue record created
- [ ] NO Ledger entry created (no customer to track)
- [ ] Stock still decreased normally

### TC-8.6: Create Sale - With Tax
| Step | Action | Expected |
|------|--------|----------|
| 1-6 | Add products, subtotal = 950 | - |
| 7 | Discount = 50 | After discount = 900 |
| 8 | Tax = 45 (5% of 900) | Tax applied |
| 9 | Grand total = 945 | 900 + 45 |
| 10 | Pay 945 | Full payment |
| 11 | Save | Sale created |

**Verify:**
- [ ] total_tax = 45
- [ ] grand_total = 945
- [ ] Amounts all correctly recorded

### TC-8.7: Create Sale - Product from Outside Stock (Source = 2)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Add product with source = "Outside" (2) | Product added |
| 2 | Complete sale | Saved |

**Verify:**
- [ ] Product stock NOT decreased (source=2 skips stock)
- [ ] NO Stock record created for this item
- [ ] Sale record still has the product in details
- [ ] ProductSale: source=2

### TC-8.8: Create Sale - With Service Item
| Step | Action | Expected |
|------|--------|----------|
| 1 | Add a service (not product) to cart | Service added |
| 2 | Set service price and quantity | - |
| 3 | Complete sale | Saved |

**Verify:**
- [ ] ProductSale: service_id filled, product_id=null
- [ ] No stock adjustment for services
- [ ] Service amount included in total

### TC-8.9: Edit Sale
| Route | `/admin/sales/{id}/edit` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open sale for editing | Form pre-filled with original data |
| 2 | Change product quantities | Totals recalculated |
| 3 | Change payment amounts | Due recalculated |
| 4 | Add/remove products | Items updated |
| 5 | Save | Updated |

**Verify After Edit:**
- [ ] OLD stock restored (old products get stock back)
- [ ] NEW stock applied (new quantities deducted)
- [ ] OLD payment records deleted
- [ ] NEW payment records created
- [ ] OLD CustomerDue deleted
- [ ] NEW CustomerDue created (if applicable)
- [ ] Ledger entry updated
- [ ] Stock records deleted and recreated

### TC-8.10: Delete Sale
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete sale | Confirm dialog |
| 2 | Confirm | Deleted |

**Verify After Delete:**
- [ ] Product stocks restored (increased back)
- [ ] All CustomerPayment records deleted
- [ ] CustomerDue record deleted
- [ ] All Ledger entries (and details) for this invoice deleted
- [ ] All Stock records deleted
- [ ] Sale removed from list

### TC-8.11: Print Sale Invoice
| Route | `/admin/sales/{id}/invoice` |
|-------|-------------------------------|

---

## 9. POS (Point of Sale)

### TC-9.1: POS - Basic Sale
| Route | `/admin/pos` |
|-------|---------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open POS page | Product grid/list displayed |
| 2 | Click product to add to cart | Product added, qty=1 |
| 3 | Click same product again | Quantity increases to 2 |
| 4 | Change quantity manually | Cart updates |
| 5 | Change price in cart | Sub-total recalculates |
| 6 | Select customer | Customer info shown |
| 7 | Apply discount | Grand total updated |
| 8 | Select payment type = Cash | - |
| 9 | Enter paying amount | Change calculated |
| 10 | Click Place Order | Sale created |

**Verify:**
- [ ] Same verifications as TC-8.1
- [ ] Cart cleared after order
- [ ] Invoice/receipt displayed

### TC-9.2: POS - Search Product by Barcode
| Step | Action | Expected |
|------|--------|----------|
| 1 | Scan/type barcode in search | Product found |
| 2 | Product added to cart | Correct product with correct price |

### TC-9.3: POS - Search Product by Name/SKU
| Step | Action | Expected |
|------|--------|----------|
| 1 | Type product name or SKU | Matching products shown |
| 2 | Select product | Added to cart |

### TC-9.4: POS - Filter by Category/Brand
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select category filter | Only category products shown |
| 2 | Select brand filter | Filtered by brand |
| 3 | Clear filters | All products shown |

### TC-9.5: POS - Remove Cart Item
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click remove/delete on cart item | Item removed |
| 2 | Totals recalculated | Correct amounts |

### TC-9.6: POS - Clear Cart
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click clear cart | All items removed |
| 2 | Cart empty | Totals = 0 |

### TC-9.7: POS - Create Quick Customer
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click "New Customer" in POS | Modal/form appears |
| 2 | Enter name, phone | - |
| 3 | Save | Customer created |
| 4 | Customer auto-selected in POS | Ready for sale |

### TC-9.8: POS - Hold Cart
| Step | Action | Expected |
|------|--------|----------|
| 1 | Add items to cart | Cart has items |
| 2 | Click "Hold" button | Note input shown |
| 3 | Enter note (e.g., "Customer will return") | - |
| 4 | Confirm hold | Cart saved to CartHold table |
| 5 | Cart cleared | Empty cart, ready for next customer |
| 6 | Check held carts list | Held cart visible with note |

### TC-9.9: POS - Resume Held Cart
| Step | Action | Expected |
|------|--------|----------|
| 1 | View held carts list | Shows all held carts |
| 2 | Click on a held cart | Cart items restored |
| 3 | All prices, quantities correct | Exact state restored |
| 4 | CartHold record deleted | No longer in held list |
| 5 | Complete the sale normally | Sale created |

### TC-9.10: POS - Delete Held Cart
| Step | Action | Expected |
|------|--------|----------|
| 1 | View held carts | List shown |
| 2 | Delete a held cart | Cart removed permanently |
| 3 | No stock impact | Held carts don't affect stock |

### TC-9.11: POS - Sale with Due
| Step | Action | Expected |
|------|--------|----------|
| 1 | Add products, select customer | - |
| 2 | Grand total = 1000 | - |
| 3 | Pay 600, due = 400 | - |
| 4 | Set due date | - |
| 5 | Place order | Sale with due created |

**Verify:** Same as TC-8.2

### TC-9.12: POS - Settings
| Route | `/admin/pos/settings` |
|-------|------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Configure POS display settings | Settings saved |

---

## 10. Customer Due & Advance

### TC-10.1: Customer Advance - Receive Payment
| Route | `/admin/customers/advance/{id}` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open advance page for customer | Shows previous advance balance |
| 2 | Enter paying amount = 5000 | - |
| 3 | Leave refund amount empty | - |
| 4 | Select account = Cash | - |
| 5 | Set date | - |
| 6 | Save | Advance stored |

**Verify:**
- [ ] CustomerPayment created: payment_type=advance_receive, is_received=1, amount=5000
- [ ] Ledger created: invoice_type=Advance Received, is_received=1, amount=5000, due_amount=-5000
- [ ] Customer advance balance = 5000
- [ ] Cash account balance increased
- [ ] Customer total due DECREASED (advance reduces due)

### TC-10.2: Customer Advance - Refund
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open advance page | Shows current advance = 5000 |
| 2 | Leave paying amount empty | - |
| 3 | Enter refund amount = 2000 | - |
| 4 | Select account | - |
| 5 | Save | Refund stored |

**Verify:**
- [ ] CustomerPayment: payment_type=advance_refund, is_paid=1, amount=2000
- [ ] Ledger: invoice_type=Payment Return, is_paid=1, amount=-2000, due_amount=+2000
- [ ] Customer advance = 5000 - 2000 = 3000
- [ ] Account balance decreased

### TC-10.3: Customer Due Receive - Single Invoice
| Route | `/admin/customers/due-receive-list` then select customer |
|-------|-----------------------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View due receive page for customer with dues | List of due invoices shown |
| 2 | Customer has Sale #INV001 with due = 400 | Invoice visible |
| 3 | Enter receiving amount = 400 (full payment) | - |
| 4 | Allocate 400 to INV001 | - |
| 5 | Select account = Cash | - |
| 6 | Save | Due received |

**Verify:**
- [ ] Sale #INV001: paid_amount increased by 400, due_amount = 0, status = "Paid"
- [ ] CustomerDue record: due reduced, paid increased
- [ ] CustomerPayment: type=due_receive, is_received=1, amount=400
- [ ] Ledger: type=Due Receive, amount=400
- [ ] LedgerDetails: invoice=INV001, amount=400
- [ ] Customer total due decreased by 400

### TC-10.4: Customer Due Receive - Partial Invoice Payment
| Step | Action | Expected |
|------|--------|----------|
| 1 | Customer has INV002 with due = 1000 | - |
| 2 | Enter receiving amount = 600 | Partial |
| 3 | Allocate 600 to INV002 | - |
| 4 | Save | Partial due received |

**Verify:**
- [ ] Sale #INV002: paid increased by 600, due reduced to 400
- [ ] Sale status still "Due" (due > 0)
- [ ] CustomerDue updated partially
- [ ] Remaining due = 400 still trackable

### TC-10.5: Customer Due Receive - Multiple Invoices at Once
| Step | Action | Expected |
|------|--------|----------|
| 1 | Customer has INV003 (due=300) and INV004 (due=500) | Two invoices |
| 2 | Enter total receiving amount = 800 | Covers both |
| 3 | Allocate 300 to INV003, 500 to INV004 | Split payment |
| 4 | Save | Both dues collected |

**Verify:**
- [ ] Both sales updated (due=0 each)
- [ ] One Ledger entry with amount=800
- [ ] Two LedgerDetails: INV003=300, INV004=500
- [ ] Separate CustomerPayment records for each sale

### TC-10.6: Customer Due Receive - Direct Balance (wallet_balance)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Customer has wallet_balance = 5000 (opening due) | Direct balance shown |
| 2 | Enter amount for direct balance = 2000 | - |
| 3 | Save | Direct due received |

**Verify:**
- [ ] Customer wallet_balance reduced: 5000 - 2000 = 3000
- [ ] CustomerPayment: type=direct_due_receive, sale_id=null
- [ ] LedgerDetails: invoice=DIRECT-BALANCE, amount=2000
- [ ] Customer total due decreased

### TC-10.7: Customer Due Receive - Mixed (Invoice + Direct Balance)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Customer has: wallet_balance=2000 + INV005 due=500 | Both types |
| 2 | Receive total = 2500 | - |
| 3 | Allocate 2000 to direct, 500 to INV005 | Split |
| 4 | Save | All dues collected |

**Verify:**
- [ ] wallet_balance = 0
- [ ] INV005 due = 0
- [ ] One Ledger with two LedgerDetails

### TC-10.8: Delete Due Receive Transaction
| Step | Action | Expected |
|------|--------|----------|
| 1 | Find the due receive transaction | In due receive list |
| 2 | Delete it | Confirm |

**Verify:**
- [ ] If invoice-based: Sale paid_amount and due_amount restored
- [ ] If direct-balance: wallet_balance restored
- [ ] LedgerDetails deleted
- [ ] If no other details: Entire Ledger deleted
- [ ] CustomerPayment deleted

---

## 11. Supplier Due & Advance

### TC-11.1: Supplier Advance - Pay
| Route | `/admin/suppliers/advance/{id}` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open advance page for supplier | Shows previous advance |
| 2 | Enter paying amount = 10000 | - |
| 3 | Select account = Cash | - |
| 4 | Save | Advance paid |

**Verify:**
- [ ] SupplierPayment: type=advance_pay, is_paid=1, amount=10000
- [ ] Ledger: type=Advance Payment, is_paid=1, due_amount=-10000
- [ ] LedgerDetails: amount=10000
- [ ] Supplier advance = 10000
- [ ] Cash account decreased
- [ ] Supplier total due DECREASED (advance reduces due)

### TC-11.2: Supplier Advance - Refund
| Step | Action | Expected |
|------|--------|----------|
| 1 | Open advance page | Advance = 10000 |
| 2 | Enter refund amount = 3000 | - |
| 3 | Save | Refund stored |

**Verify:**
- [ ] SupplierPayment: type=advance_refund, is_received=1, amount=3000
- [ ] Ledger: type=Payment Return, is_received=1, due_amount=+3000
- [ ] Supplier advance = 10000 - 3000 = 7000
- [ ] Account balance increased (money back)

### TC-11.3: Supplier Due Pay - Single Invoice
| Route | `/admin/suppliers/due-pay/{id}` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View due pay page | Purchases with status "Due" listed |
| 2 | Select purchase #PUR001 (due=800) | - |
| 3 | Enter paying amount = 800 | Full payment |
| 4 | Select account | - |
| 5 | Save | Due paid |

**Verify:**
- [ ] Purchase #PUR001: paid_amount increased, due_amount=0, status="Paid"
- [ ] SupplierPayment: type=due_pay, is_paid=1, amount=800
- [ ] Ledger: type=Due Payment, amount=800, due_amount=-800
- [ ] LedgerDetails: invoice=PUR001, amount=800
- [ ] Supplier total due decreased
- [ ] supplier.balance updated

### TC-11.4: Supplier Due Pay - Partial
| Step | Action | Expected |
|------|--------|----------|
| 1 | Purchase #PUR002 (due=2000) | - |
| 2 | Pay 1200 | Partial |
| 3 | Save | Partial payment |

**Verify:**
- [ ] Purchase: paid += 1200, due = 800, status still "Due"
- [ ] Supplier still has remaining due

### TC-11.5: Supplier Due Pay - Multiple Invoices
| Step | Action | Expected |
|------|--------|----------|
| 1 | PUR003 (due=500) + PUR004 (due=1000) | Two invoices |
| 2 | Pay total 1500 | Split across both |
| 3 | Save | Both paid |

**Verify:**
- [ ] Both purchases updated
- [ ] One Ledger with two LedgerDetails
- [ ] Separate SupplierPayments per invoice

### TC-11.6: Delete Supplier Due Payment
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete due payment | Confirm |

**Verify:**
- [ ] Purchase paid/due amounts restored
- [ ] Payment records deleted
- [ ] Ledger/details deleted or updated
- [ ] Supplier balance restored

---

## 12. Sales Return

### TC-12.1: Create Sales Return - Full Return, Full Refund
| Route | `/admin/sales/return/store` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select original sale (e.g., INV001) | Sale details loaded |
| 2 | Set return date | - |
| 3 | Enter return qty for Product A = 3 (all sold qty) | Return subtotal calculated |
| 4 | Enter return qty for Product B = 2 (all sold qty) | - |
| 5 | Return amount = 900 (full sale amount) | - |
| 6 | Paying amount = 900 (full refund) | - |
| 7 | Select refund account = Cash | - |
| 8 | Save | Return created |

**Verify:**
- [ ] SalesReturn record: return_amount=900, return_due=0
- [ ] Product A stock INCREASED by 3 (returned to inventory)
- [ ] Product B stock INCREASED by 2
- [ ] Stock records: type=Sale Return, in_quantity for each product
- [ ] CustomerPayment: type=sale return, is_paid=1, is_received=0, amount=900
- [ ] Ledger: type=Sale Return, is_paid=1, amount=900
- [ ] Customer total due DECREASED by return amount
- [ ] Cash account decreased (refund paid out)

### TC-12.2: Partial Return (Some Products)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select sale with 2 products | - |
| 2 | Return only Product A (qty=2 of 3) | Partial return |
| 3 | Return amount = 300 | For 2 units at 150 |
| 4 | Pay refund = 300 | - |
| 5 | Save | Partial return created |

**Verify:**
- [ ] Only Product A stock increased by 2
- [ ] Product B unchanged
- [ ] Return amount = 300 only

### TC-12.3: Sales Return - Partial Refund (Return Due)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Return amount = 500 | Total return value |
| 2 | Paying amount = 300 | Partial refund |
| 3 | Return due = 200 | Remaining to refund later |
| 4 | Save | Return with due |

**Verify:**
- [ ] SalesReturn: return_amount=500, return_due=200
- [ ] CustomerPayment: amount=300 only
- [ ] Customer due adjusted

### TC-12.4: Delete Sales Return
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete sales return | Confirm |

**Verify:**
- [ ] Stock DECREASED back (return reversed)
- [ ] Stock records deleted
- [ ] SalesReturnDetails deleted
- [ ] Ledger entries deleted
- [ ] CustomerPayment deleted
- [ ] Customer due recalculated

---

## 13. Purchase Return

### TC-13.1: Create Purchase Return - Full Return
| Route | `/admin/purchase/return/{id}` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select purchase | Details loaded |
| 2 | Select return type | Return type set |
| 3 | Set return date | - |
| 4 | Enter return qty for each product | Full qty |
| 5 | Enter received amount (refund from supplier) | Amount expected |
| 6 | Select payment account | - |
| 7 | Save | Return created |

**Verify:**
- [ ] PurchaseReturn record created
- [ ] Product stock DECREASED (returned to supplier)
- [ ] Stock records: type=Purchase Return, out_quantity
- [ ] SupplierPayment: type=purchase_receive, is_received=1
- [ ] Ledger: type=purchase return, is_received=1
- [ ] Supplier due DECREASED (they owe us for returned goods)

### TC-13.2: Partial Purchase Return
| Step | Action | Expected |
|------|--------|----------|
| 1 | Return only some products/partial qty | - |
| 2 | Only affected products stock decreased | - |
| 3 | Received amount matches returned value | - |

### TC-13.3: Edit Purchase Return
| Route | `/admin/purchase/return/{id}/update` |
|-------|---------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Edit return quantities | - |
| 2 | Old stock restored, new stock applied | - |
| 3 | Payment records updated | - |

### TC-13.4: Delete Purchase Return
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete return | Confirm |

**Verify:**
- [ ] Stock INCREASED back (products restored to inventory)
- [ ] Stock records deleted
- [ ] Payment records deleted
- [ ] Ledger entries deleted
- [ ] Supplier due recalculated

---

## 14. Expense Management

### TC-14.1: Create Expense - Direct (No Supplier), Full Payment
| Route | `/admin/expense/create` |
|-------|--------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select expense type (e.g., Rent) | - |
| 2 | Enter amount = 5000 | - |
| 3 | Set date | - |
| 4 | Leave expense supplier blank | Direct expense |
| 5 | Select payment type = Cash | - |
| 6 | Paid amount = 5000 | Full payment |
| 7 | Save | Expense created |

**Verify:**
- [ ] Expense: amount=5000, paid=5000, due=0
- [ ] Payment record created (type=direct_expense)
- [ ] Cash account balance decreased
- [ ] No supplier ledger entry (direct expense)

### TC-14.2: Create Expense - With Supplier, Full Payment
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select expense type | - |
| 2 | Select expense supplier | Supplier selected |
| 3 | Amount = 8000 | - |
| 4 | Paid = 8000 | Full |
| 5 | Save | Expense with supplier |

**Verify:**
- [ ] Expense: expense_supplier_id set, paid=8000, due=0
- [ ] Ledger: type=Expense, amount=8000, supplier tracked
- [ ] ExpenseSupplierPayment: type=expense
- [ ] Expense supplier total expense increased

### TC-14.3: Create Expense - With Supplier, Partial Payment (Due)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Amount = 8000 | - |
| 2 | Paid = 5000 | Partial |
| 3 | Due = 3000 | Auto-calculated |
| 4 | Save | Expense with due |

**Verify:**
- [ ] Expense: paid=5000, due=3000
- [ ] Ledger: amount=5000, due_amount=3000
- [ ] Expense supplier due increased by 3000

### TC-14.4: Edit Expense
| Route | `/admin/expense/{id}/edit` |
|-------|------------------------------|

### TC-14.5: Delete Expense
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete expense | All related records cleaned up |

---

## 15. Expense Supplier Due & Advance

### TC-15.1: Expense Supplier - Advance Payment
| Route | `/admin/expense-suppliers/{id}/advance` |
|-------|-------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Enter paying amount = 5000 | - |
| 2 | Select account | - |
| 3 | Save | Advance paid |

**Verify:**
- [ ] Ledger: type=Expense Advance Payment, is_paid=1, due_amount=-5000
- [ ] ExpenseSupplierPayment: type=advance_pay
- [ ] Supplier advance = 5000
- [ ] Account balance decreased

### TC-15.2: Expense Supplier - Advance Refund
| Step | Action | Expected |
|------|--------|----------|
| 1 | Enter refund amount = 2000 | - |
| 2 | Save | Refund stored |

**Verify:**
- [ ] Ledger: type=Expense Payment Return, is_received=1
- [ ] Payment: type=advance_refund
- [ ] Advance = 5000 - 2000 = 3000

### TC-15.3: Expense Supplier - Due Payment
| Route | `/admin/expense-suppliers/{id}/due-pay` |
|-------|-------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View pending expenses | Due expenses listed |
| 2 | Select expense(s) to pay | - |
| 3 | Enter amounts per expense | - |
| 4 | Save | Due paid |

**Verify:**
- [ ] Expenses updated: paid increased, due decreased
- [ ] Ledger: type=Expense Due Payment
- [ ] LedgerDetails per expense
- [ ] ExpenseSupplierPayment: type=due_pay

### TC-15.4: Expense Supplier Ledger
| Route | `/admin/expense-suppliers/{id}/ledger` |
|-------|------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View ledger | All transactions listed |
| 2 | Check: Expenses, Advance, Due Payments, Refunds | All visible |
| 3 | Verify balance calculation | Opening + transactions = closing |

---

## 16. Quotation & Convert to Sale

### TC-16.1: Create Quotation
| Route | `/admin/quotation/create` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select customer | - |
| 2 | Set date and expiry date | - |
| 3 | Add products with qty and price | Line items added |
| 4 | Apply discount | - |
| 5 | Apply VAT/tax | - |
| 6 | Set status = Draft | - |
| 7 | Save | Quotation created with auto-number |

**Verify:**
- [ ] Quotation record created
- [ ] QuotationDetails for each product
- [ ] NO stock impact (quotation doesn't affect inventory)
- [ ] NO payment impact (no money involved)

### TC-16.2: Edit Quotation
| Route | `/admin/quotation/{id}/edit` |
|-------|-------------------------------|

### TC-16.3: Convert Quotation to Sale
| Route | `/admin/quotation/{id}/convert-to-sale` |
|-------|-------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click "Convert to Sale" | - |
| 2 | Redirected to sales create form | Form pre-filled |
| 3 | Customer auto-selected | From quotation |
| 4 | Products auto-added | From quotation details |
| 5 | Amounts pre-filled | From quotation |
| 6 | Complete payment details | User fills payment info |
| 7 | Save sale | Normal sale created |

**Verify:**
- [ ] Sale created with all quotation data
- [ ] Stock adjusted (normal sale behavior)
- [ ] Payment recorded
- [ ] Quotation status changed (optional)

### TC-16.4: Cannot Convert Expired Quotation
| Step | Action | Expected |
|------|--------|----------|
| 1 | Try to convert expired quotation | Error/warning shown |
| 2 | Conversion blocked | Cannot proceed |

### TC-16.5: Delete Quotation
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete quotation | Deleted (no financial impact) |

---

## 17. Stock Management

### TC-17.1: View Stock List
| Route | `/admin/stock` |
|-------|-----------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View stock list | All products with current stock shown |
| 2 | Filter by category | Filtered list |
| 3 | Filter by brand | Filtered |
| 4 | Filter by stock status (in_stock / out_of_stock) | Filtered |
| 5 | Filter by date range | Stock within date range |
| 6 | Search by product name/SKU/barcode | Matching products |

### TC-17.2: View Stock Detail/Ledger (Per Product)
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click on a product in stock list | Detail page shown |
| 2 | View all transactions for this product | Chronological list |
| 3 | Verify each entry type: | |
| | - Purchase: in_quantity shown | Stock added |
| | - Sale: out_quantity shown | Stock removed |
| | - Sale Return: in_quantity shown | Stock added back |
| | - Purchase Return: out_quantity shown | Stock removed |
| 4 | Running balance matches current stock | Consistent |

### TC-17.3: Reset Single Product Stock
| Route | `/admin/stock/reset/{id}` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Click reset on a product | Confirm dialog |
| 2 | Confirm | Stock reset |

**Verify:**
- [ ] Product stock = 0
- [ ] stock_status = out_of_stock
- [ ] All Stock records for this product DELETED
- [ ] Opening stock record created (all zeros)
- [ ] **WARNING:** This is destructive - transaction history lost

---

## 18. Balance Transfer

### TC-18.1: Transfer Cash to Bank
| Route | `/admin/balance/transfer` |
|-------|----------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | From account type = Cash | - |
| 2 | To account type = Bank | Select bank account |
| 3 | Amount = 20000 | - |
| 4 | Set date | - |
| 5 | Add note (optional) | - |
| 6 | Save | Transfer recorded |

**Verify:**
- [ ] BalanceTransfer record created
- [ ] Cash account ledger: credit (money out)
- [ ] Bank account ledger: debit (money in)
- [ ] Cash flow report shows transfer

### TC-18.2: Transfer Bank to Mobile Banking
| Step | Action | Expected |
|------|--------|----------|
| 1 | From = Bank (select account) | - |
| 2 | To = Mobile Banking (select account) | - |
| 3 | Amount = 5000 | - |
| 4 | Save | Transfer recorded |

### TC-18.3: Edit Balance Transfer
| Route | `/admin/balance/transfer/update/{id}` |
|-------|----------------------------------------|

### TC-18.4: Delete Balance Transfer
| Route | `/admin/balance/transfer/destroy/{id}` |
|-------|------------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Delete transfer | Transfer reversed in both account ledgers |

---

## 19. Employee & Salary

### TC-19.1: Create Employee
| Route | `/admin/employee/create` |
|-------|---------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Fill: Name, Phone, Email, Address | - |
| 2 | Set joining date | - |
| 3 | Set salary amount | Base salary stored |
| 4 | Save | Employee created |

### TC-19.2: View Salary Calculation
| Route | `/admin/employee/{id}/salary-view` |
|-------|--------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select month/year | - |
| 2 | View calculation: | |
| | - Total days in month | Calendar days |
| | - Weekends (from setup) | Subtracted |
| | - Holidays (from setup) | Subtracted |
| | - Total working days | Calculated |
| | - Total attendance | From attendance records |
| | - Payable salary | Prorated if partial attendance |

**Salary Calculation:**
- Full attendance: payable = base salary
- Partial attendance: payable = (salary / total_days) x (working_days + attendance_days)

### TC-19.3: Pay Salary - Full Month
| Route | `/admin/employee/{id}/salary-pay` |
|-------|-------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select month/year | Payable calculated |
| 2 | Type = Salary | Full salary |
| 3 | Amount = payable salary | - |
| 4 | Select payment account | - |
| 5 | Save | Salary paid |

**Verify:**
- [ ] EmployeeSalary record: type=salary, amount matches
- [ ] Account balance decreased
- [ ] Shows in salary list/report

### TC-19.4: Pay Salary - Advance
| Step | Action | Expected |
|------|--------|----------|
| 1 | Type = Advance | Partial advance |
| 2 | Amount = partial amount | Less than payable |
| 3 | Save | Advance recorded |

**Verify:**
- [ ] EmployeeSalary: type=advance
- [ ] Due remaining = payable - advance paid

### TC-19.5: Edit/Delete Salary
| Step | Action | Expected |
|------|--------|----------|
| 1 | Edit salary record | Amount/date updated |
| 2 | Delete salary record | Record removed, account restored |

### TC-19.6: Attendance Management
| Route | `/admin/attendance` |
|-------|----------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select date | - |
| 2 | Mark each employee: Present/Absent/Late/Leave | Status saved |
| 3 | View attendance summary | Monthly summary |

### TC-19.7: Weekend Setup
| Route | `/admin/attendance/settings/weekdays` |
|-------|----------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Configure which days are weekends | Saved |
| 2 | Salary calculation reflects weekends | Correct proration |

### TC-19.8: Holiday Setup
| Route | `/admin/attendance/settings/holidays` |
|-------|----------------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Add holiday with date and name | Holiday saved |
| 2 | Salary calculation reflects holidays | Correct proration |

---

## 20. Reports

### TC-20.1: Sales Report
| Route | `/admin/report/details-sale` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Filter by date range | Sales in range shown |
| 2 | Verify totals: quantity, amount, paid, due | Match actual sales |
| 3 | Export to Excel/PDF | File downloaded |

### TC-20.2: Master Sales Report
| Route | `/admin/report/master-sale` |
|-------|-------------------------------|

### TC-20.3: Purchase Report
| Route | `/admin/report/purchase` |
|-------|---------------------------|

### TC-20.4: Profit/Loss Report
| Route | `/admin/report/profit-loss` |
|-------|-------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select date range | Report generated |
| 2 | Verify: Sales Revenue - Cost of Goods - Expenses = Profit | Calculation correct |

### TC-20.5: Customer Report
| Route | `/admin/report/customers` |
|-------|----------------------------|

### TC-20.6: Supplier Report
| Route | `/admin/report/supplier` |
|-------|---------------------------|

### TC-20.7: Receivable Report
| Route | `/admin/report/receivable` |
|-------|------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View all outstanding customer dues | Complete list |
| 2 | Filter by date | Filtered |

### TC-20.8: Expense Report
| Route | `/admin/report/expense` |
|-------|--------------------------|

### TC-20.9: Salary Report
| Route | `/admin/report/salary` |
|-------|--------------------------|

### TC-20.10: Product Sale Report
| Route | `/admin/report/product-sale-report` |
|-------|---------------------------------------|

### TC-20.11: Category Report
| Route | `/admin/report/categories` |
|-------|-------------------------------|

### TC-20.12: Barcode-wise Report
| Route | `/admin/report/barcode-wise-product` |
|-------|----------------------------------------|

### TC-20.13: Due Date Sale Report
| Route | `/admin/report/due-date-sale` |
|-------|----------------------------------|

### TC-20.14: Daily Transaction Summary (DTS)
| Route | `/admin/report/dts` |
|-------|----------------------|

### TC-20.15: Customer Summary
| Route | `/admin/other-summery/customer` |
|-------|-----------------------------------|

### TC-20.16: Supplier Summary
| Route | `/admin/other-summery/supplier` |
|-------|-----------------------------------|

---

## 21. Ledger Verification

### TC-21.1: Customer Ledger
| Route | `/admin/customers/ledger/{id}` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View customer ledger | All transactions listed |
| 2 | Verify entries present: | |
| | - Sale entries (debit = total, credit = paid) | |
| | - Due Receive entries (credit) | |
| | - Advance Received entries (credit) | |
| | - Advance Refund entries (debit) | |
| | - Sale Return entries (credit = refund amount) | |
| 3 | Opening balance = wallet_balance + prior transactions | Correct |
| 4 | Closing balance = opening + all transactions | Matches customer total due |
| 5 | Filter by date range | Filtered correctly |
| 6 | Export ledger | Downloaded |

### TC-21.2: Supplier Ledger
| Route | `/admin/suppliers/ledger/{id}` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View supplier ledger | All transactions listed |
| 2 | Verify entries: | |
| | - Purchase entries (debit = total, credit = paid) | |
| | - Due Payment entries (credit) | |
| | - Advance Payment entries (credit) | |
| | - Advance Refund entries (debit) | |
| | - Purchase Return entries (debit = received amount) | |
| 3 | Balance matches supplier total due | Correct |

### TC-21.3: Account Ledger
| Route | `/admin/accounts/{id}/ledger` |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | Select account (Cash/Bank/Mobile) | - |
| 2 | View all transactions through this account | Listed |
| 3 | Verify entries: | |
| | - Customer payments IN (debit) | |
| | - Supplier payments OUT (credit) | |
| | - Salary payments OUT (credit) | |
| | - Expense payments OUT (credit) | |
| | - Balance deposits IN (debit) | |
| | - Balance withdrawals OUT (credit) | |
| | - Transfers IN/OUT | |
| 4 | Opening + Debit - Credit = Closing balance | Correct |

### TC-21.4: Expense Supplier Ledger
| Route | `/admin/expense-suppliers/{id}/ledger` |
|-------|------------------------------------------|

### TC-21.5: Cash Flow Report
| Route | `/admin/accounts` → Cash Flow |
|-------|----------------------------------|
| Step | Action | Expected |
|------|--------|----------|
| 1 | View cash flow | All income vs expense categories |
| 2 | **Income categories verified:** | |
| | - Product Sales | CustomerPayment type=sale |
| | - Service Sales | Sales with service items |
| | - Customer Due Receipts | type=due_receive |
| | - Customer Advance | type=advance_receive |
| | - Balance Deposits | type=deposit |
| | - Supplier Advance Refund | type=advance_refund |
| | - Purchase Return Received | type=purchase_receive |
| 3 | **Expense categories verified:** | |
| | - Sale Returns | type=sale return |
| | - Balance Withdrawals | type=withdraw |
| | - Customer Advance Refund | type=advance_refund |
| | - Salary Payments | EmployeeSalary sum |
| | - Direct Expenses | Non-supplier expenses |
| | - Supplier Due Payments | type=due_pay |
| | - Supplier Advance Payments | type=advance_pay |
| | - Purchase Payments | SupplierPayment |
| | - Expense Supplier Payments | type=expense |
| 4 | Closing = Opening + Income - Expense | Correct |

---

## 22. End-to-End Scenarios

### Scenario A: Complete Purchase-to-Sale Cycle

```
Step 1: Create Supplier "ABC Traders"
Step 2: Create Customer "John Doe" (wallet_balance=0)
Step 3: Create Product "Widget" (stock=0, cost=100, price=150)
Step 4: Create Cash Account with opening balance 50000

Step 5: Purchase 20 Widgets from ABC Traders
        → Total = 2000, Pay 1500 cash, Due 500
        → Widget stock: 0 → 20
        → Cash: 50000 → 48500
        → Supplier due: 500

Step 6: Sell 5 Widgets to John Doe
        → Total = 750 (5 x 150), Pay 500, Due 250
        → Widget stock: 20 → 15
        → Cash: 48500 → 49000
        → Customer due: 250
        → Profit: (150-100) x 5 = 250

Step 7: John pays due 250
        → Cash: 49000 → 49250
        → Customer due: 250 → 0
        → Sale status: Due → Paid

Step 8: Pay supplier due 500
        → Cash: 49250 → 48750
        → Supplier due: 500 → 0
        → Purchase status: Due → Paid

Step 9: Verify all ledgers balance
```

### Scenario B: Advance Payment Cycle

```
Step 1: Customer "Jane" gives advance 10000
        → CustomerPayment: advance_receive
        → Customer advance: 10000
        → Customer total due: -10000 (credit)

Step 2: Create Sale for Jane, total = 8000, pay 8000
        → Customer total due: -10000 + 8000 = -2000 (still in credit)

Step 3: Create another Sale for Jane, total = 5000, pay 3000, due 2000
        → Customer total due: -2000 + 2000 = 0

Step 4: Verify Jane's ledger shows all transactions
Step 5: Verify Jane advance = 10000 (original)
Step 6: Verify Jane total due = 0 (balanced)
```

### Scenario C: Return Cycle

```
Step 1: Purchase 10 items @ 100 = 1000 (full payment)
        → Stock: +10
        → Cash: -1000

Step 2: Sell 8 items @ 150 = 1200 (full payment)
        → Stock: -8 (remaining: 2)
        → Cash: +1200

Step 3: Customer returns 3 items
        → Stock: +3 (now: 5)
        → Cash: -450 (refund: 3 x 150)

Step 4: Return 2 items to supplier
        → Stock: -2 (now: 3)
        → Cash: +200 (refund: 2 x 100)

Step 5: Final state:
        → Stock: 3 items
        → Cash impact: -1000 + 1200 - 450 + 200 = -50
        → Profit: (8-3) x 50 = 250 (5 net sold x 50 margin)
```

### Scenario D: Multi-Payment Method Sale

```
Step 1: Create sale, grand total = 5000
Step 2: Pay: Cash = 2000, Bank = 2000, bKash = 800
Step 3: Due = 200, set due date

Verify:
- 3 CustomerPayment records
- Cash account: +2000
- Bank account: +2000
- Mobile banking: +800
- Customer due: 200
- Sale status: Due

Step 4: Customer pays due 200 via cash
- Cash: +200
- Customer due: 0
- Sale status: Paid
```

### Scenario E: Expense Supplier Full Cycle

```
Step 1: Create Expense Supplier "Office Mart"
Step 2: Pay advance 5000 to Office Mart
        → Advance: 5000

Step 3: Create Expense: Rent = 8000 via Office Mart, pay 5000, due 3000
        → Paid: 5000, Due: 3000

Step 4: Create Expense: Supplies = 2000 via Office Mart, pay 0, due 2000
        → Total due: 3000 + 2000 = 5000

Step 5: Pay due 3000 (for rent invoice)
        → Rent due: 0
        → Remaining due: 2000

Step 6: Get advance refund 2000 (partial)
        → Advance: 5000 - 2000 = 3000

Step 7: Verify expense supplier ledger balances
```

### Scenario F: Quotation to Sale Conversion

```
Step 1: Create quotation for Customer X
        → Products: A x 5 @ 200 = 1000, B x 3 @ 300 = 900
        → Discount: 100
        → Total: 1800

Step 2: Convert to Sale
        → Redirected to sale form, pre-filled
        → Customer X selected
        → Products loaded

Step 3: Complete sale with payment
        → Normal sale created
        → Stock adjusted
        → Payment recorded
```

### Scenario G: Opening Balance Customer Due Collection

```
Step 1: Create Customer "Old Client" with wallet_balance = 15000
        → Customer total due: 15000

Step 2: Collect direct due 5000
        → wallet_balance: 15000 → 10000
        → Customer due: 10000

Step 3: Create sale for 3000, full payment
        → Customer due: 10000 (wallet unchanged, sale fully paid)

Step 4: Create sale for 2000, pay 0, due 2000
        → Customer due: 10000 + 2000 = 12000

Step 5: Collect due: 2000 (invoice) + 5000 (direct balance) = 7000
        → Invoice due cleared
        → wallet_balance: 10000 → 5000
        → Customer due: 12000 - 7000 = 5000

Step 6: Verify ledger shows all movements correctly
```

---

## Quick Reference: Payment Type Codes

### CustomerPayment.payment_type
| Code | Meaning | Money Direction |
|------|---------|-----------------|
| `sale` | Sale payment | Customer → Business (is_received=1) |
| `due_receive` | Invoice due collection | Customer → Business (is_received=1) |
| `direct_due_receive` | Opening balance collection | Customer → Business (is_received=1) |
| `advance_receive` | Advance deposit | Customer → Business (is_received=1) |
| `advance_refund` | Advance refund | Business → Customer (is_paid=1) |
| `sale return` | Sale return refund | Business → Customer (is_paid=1) |

### SupplierPayment.payment_type
| Code | Meaning | Money Direction |
|------|---------|-----------------|
| `purchase` | Purchase payment | Business → Supplier (is_paid=1) |
| `due_pay` | Invoice due payment | Business → Supplier (is_paid=1) |
| `advance_pay` | Advance to supplier | Business → Supplier (is_paid=1) |
| `advance_refund` | Advance refund | Supplier → Business (is_received=1) |
| `purchase_receive` | Purchase return refund | Supplier → Business (is_received=1) |

### Ledger.invoice_type Values
| Customer Side | Supplier Side | Expense Supplier Side |
|---------------|---------------|----------------------|
| `sale` | `purchase` | `Expense` |
| `Due Receive` | `Due Payment` | `Expense Due Payment` |
| `Advance Received` | `Advance Payment` | `Expense Advance Payment` |
| `Payment Return` | `Payment Return` | `Expense Payment Return` |
| `Sale Return` | `purchase return` | - |

### Stock.type Values
| Type | Direction | Triggered By |
|------|-----------|-------------|
| `Purchase` | in_quantity | New purchase |
| `Sale` | out_quantity | New sale |
| `Sale Return` | in_quantity | Customer returns product |
| `Purchase Return` | out_quantity | Return product to supplier |
| `Opening Stock` | in_quantity | Stock reset |

---

## Checklist Summary

Use this master checklist after each test:

- [ ] **Stock:** Current stock matches (purchases - sales - purchase returns + sale returns)
- [ ] **Customer Due:** total_sales - total_paid + advance_refunds - sale_returns + wallet_balance
- [ ] **Supplier Due:** total_purchases - total_paid - purchase_returns + advance_refunds
- [ ] **Account Balance:** opening + deposits + income - withdrawals - payments - expenses - salary
- [ ] **Ledger Balance:** Opening + sum(due_amount) = Closing
- [ ] **Cash Flow:** Opening + Total Income - Total Expense = Closing
- [ ] **Profit:** sum(sale_price - purchase_price) x quantity for each sold item
