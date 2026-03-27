<?php

namespace App\Console\Commands;

use App\Models\Ledger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class PurchaseReconcile extends Command
{
    protected $signature = 'purchase:reconcile
                            {--fix : Automatically fix inconsistencies}
                            {--supplier= : Only reconcile a specific supplier ID}';

    protected $description = 'Find and fix purchases and ledger entries where amounts do not match actual payments';

    public function handle()
    {
        $this->info('=== Purchase / Supplier Due Reconciliation ===');
        $this->newLine();

        $shouldFix = $this->option('fix');
        $supplierId = $this->option('supplier');

        // ─── 1. Find purchases where stored paid_amount ≠ sum of payments ───
        $this->info('1. Checking purchases.paid_amount vs supplier_payments...');
        $this->newLine();

        $query = Purchase::query();
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchases = $query->get();
        $mismatchCount = 0;
        $totalDiff = 0;

        foreach ($purchases as $purchase) {
            $paymentSum = (float) SupplierPayment::where('purchase_id', $purchase->id)
                ->whereIn('payment_type', ['purchase', 'due_pay'])
                ->sum('amount');

            $storedPaid = round((float) $purchase->paid_amount, 2);
            $computedPaid = round($paymentSum, 2);

            if (abs($storedPaid - $computedPaid) > 0.01) {
                $diff = round($computedPaid - $storedPaid, 2);
                $mismatchCount++;
                $totalDiff += $diff;

                $correctDue = round(max(0, (float) $purchase->total_amount - $computedPaid), 2);

                $this->warn(
                    "  MISMATCH: Purchase #{$purchase->invoice_number} (ID:{$purchase->id})"
                    . " | Total: " . number_format($purchase->total_amount, 2)
                    . " | Stored Paid: " . number_format($storedPaid, 2)
                    . " | Actual Paid: " . number_format($computedPaid, 2)
                    . " | Diff: " . number_format($diff, 2)
                    . " | Stored Due: " . number_format($purchase->due_amount, 2)
                    . " | Correct Due: " . number_format($correctDue, 2)
                );

                if ($shouldFix) {
                    DB::table('purchases')->where('id', $purchase->id)->update([
                        'paid_amount' => $computedPaid,
                        'due_amount' => $correctDue,
                        'payment_status' => $correctDue <= 0 ? 'paid' : 'due',
                    ]);
                    $this->info("    → FIXED: paid_amount={$computedPaid}, due_amount={$correctDue}, status=" . ($correctDue <= 0 ? 'paid' : 'due'));
                }
            }
        }

        if ($mismatchCount === 0) {
            $this->info('  ✓ All purchases paid_amount match supplier_payments. No mismatch found.');
        } else {
            $this->newLine();
            $this->error("  Found {$mismatchCount} mismatched purchase(s). Total diff: " . number_format($totalDiff, 2));
        }

        // ─── 2. Orphan ledger entries (purchase deleted but ledger remains) ───
        $this->newLine();
        $this->info('2. Checking for orphan ledger entries (deleted purchases)...');
        $this->newLine();

        $ledgerQuery = Ledger::where('invoice_type', 'purchase');
        if ($supplierId) {
            $ledgerQuery->where('supplier_id', $supplierId);
        }
        $purchaseLedgers = $ledgerQuery->get();
        $orphanCount = 0;

        foreach ($purchaseLedgers as $ledger) {
            $purchase = Purchase::where('invoice_number', $ledger->invoice_no)->first();
            $isOrphan = false;
            $reason = '';

            if (!$purchase) {
                $isOrphan = true;
                $reason = 'purchase no longer exists';
            } elseif ((int) $purchase->supplier_id !== (int) $ledger->supplier_id) {
                $isOrphan = true;
                $reason = "supplier mismatch: ledger has supplier_id={$ledger->supplier_id}, purchase has supplier_id={$purchase->supplier_id}";
            }

            if ($isOrphan) {
                $orphanCount++;
                $this->warn(
                    "  ORPHAN: Ledger #{$ledger->id} invoice={$ledger->invoice_no}"
                    . " | total_amount=" . number_format($ledger->total_amount, 2)
                    . " | due_amount=" . number_format($ledger->due_amount, 2)
                    . " — {$reason}"
                );

                if ($shouldFix) {
                    $ledger->details()->delete();
                    $ledger->delete();
                    $this->info("    → DELETED orphan ledger #{$ledger->id} and its details");
                }
            }
        }

        if ($orphanCount === 0) {
            $this->info('  ✓ No orphan ledger entries found.');
        } else {
            $this->error("  Found {$orphanCount} orphan ledger entry(ies).");
        }

        // ─── 3. Ledger purchase entries with wrong amounts ───
        $this->newLine();
        $this->info('3. Checking ledger purchase amounts vs actual purchases...');
        $this->newLine();

        $ledgerAmountIssues = 0;
        foreach ($purchaseLedgers as $ledger) {
            $purchase = Purchase::where('invoice_number', $ledger->invoice_no)->first();
            if (!$purchase) continue; // already handled as orphan

            $ledgerTotal = round((float) $ledger->total_amount, 2);
            $purchaseTotal = round((float) $purchase->total_amount, 2);

            // Check if the paid amount at time of purchase matches ledger
            $purchasePayments = SupplierPayment::where('purchase_id', $purchase->id)
                ->where('payment_type', 'purchase')
                ->sum('amount');
            $ledgerPaid = round((float) $ledger->amount, 2);
            $actualPaid = round((float) $purchasePayments, 2);
            $correctDue = round($purchaseTotal - $actualPaid, 2);

            $hasMismatch = false;
            if (abs($ledgerTotal - $purchaseTotal) > 0.01) {
                $hasMismatch = true;
            }
            if (abs($ledgerPaid - $actualPaid) > 0.01) {
                $hasMismatch = true;
            }

            if ($hasMismatch) {
                $ledgerAmountIssues++;
                $this->warn(
                    "  MISMATCH: Ledger #{$ledger->id} ({$ledger->invoice_no})"
                    . " | Ledger total={$ledgerTotal} vs Purchase total={$purchaseTotal}"
                    . " | Ledger paid={$ledgerPaid} vs Actual paid={$actualPaid}"
                );

                if ($shouldFix) {
                    DB::table('ledgers')->where('id', $ledger->id)->update([
                        'total_amount' => $purchaseTotal,
                        'amount' => $actualPaid,
                        'due_amount' => $correctDue,
                    ]);
                    $this->info("    → FIXED: total_amount={$purchaseTotal}, amount={$actualPaid}, due_amount={$correctDue}");
                }
            }
        }

        if ($ledgerAmountIssues === 0) {
            $this->info('  ✓ All ledger purchase amounts match actual purchases.');
        } else {
            $this->error("  Found {$ledgerAmountIssues} ledger amount mismatch(es).");
        }

        // ─── 4. Due payment ledger vs actual due payments ───
        $this->newLine();
        $this->info('4. Checking due payment ledger vs supplier_payments...');
        $this->newLine();

        $dueLedgerQuery = Ledger::where('invoice_type', 'Due Payment');
        if ($supplierId) {
            $dueLedgerQuery->where('supplier_id', $supplierId);
        }
        $dueLedgers = $dueLedgerQuery->get();

        $duePayQuery = SupplierPayment::where('payment_type', 'due_pay');
        if ($supplierId) {
            $duePayQuery->where('supplier_id', $supplierId);
        }
        $duePayments = $duePayQuery->get();

        $ledgerDueTotal = round($dueLedgers->sum('amount'), 2);
        $paymentDueTotal = round($duePayments->sum('amount'), 2);
        $dueDiff = round($paymentDueTotal - $ledgerDueTotal, 2);

        // Check each due payment ledger against its linked payments
        $duePayFixCount = 0;
        foreach ($dueLedgers as $ledger) {
            $linkedPaymentSum = round((float) SupplierPayment::where('ledger_id', $ledger->id)->sum('amount'), 2);
            $ledgerAmount = round((float) $ledger->amount, 2);

            if (abs($linkedPaymentSum - $ledgerAmount) > 0.01) {
                $duePayFixCount++;
                $this->warn(
                    "  MISMATCH: Ledger #{$ledger->id} ({$ledger->invoice_no})"
                    . " | ledger_amount=" . number_format($ledgerAmount, 2)
                    . " | payment_sum=" . number_format($linkedPaymentSum, 2)
                    . " | diff=" . number_format($linkedPaymentSum - $ledgerAmount, 2)
                );

                if ($shouldFix) {
                    DB::table('ledgers')->where('id', $ledger->id)->update([
                        'amount' => $linkedPaymentSum,
                        'due_amount' => -$linkedPaymentSum,
                    ]);
                    $this->info("    → FIXED: amount={$linkedPaymentSum}, due_amount=" . (-$linkedPaymentSum));
                }
            }
        }

        // Also check for payments without valid ledger — create missing ledger entries
        $orphanPayments = 0;
        foreach ($duePayments as $payment) {
            if (!$payment->ledger_id || !Ledger::find($payment->ledger_id)) {
                $orphanPayments++;
                $this->warn(
                    "    Payment #{$payment->id} amount=" . number_format($payment->amount, 2)
                    . " date=" . $payment->payment_date
                    . " supplier_id=" . $payment->supplier_id
                    . " — missing/invalid ledger_id={$payment->ledger_id}"
                );

                if ($shouldFix && $payment->supplier_id) {
                    $purchase = $payment->purchase;
                    $newLedger = Ledger::create([
                        'supplier_id' => $payment->supplier_id,
                        'amount' => $payment->amount,
                        'total_amount' => 0,
                        'due_amount' => -$payment->amount,
                        'invoice_type' => 'Due Payment',
                        'is_paid' => 1,
                        'invoice_no' => 'SDL-FIX-' . $payment->id,
                        'note' => 'Auto-created by reconciliation for payment #' . $payment->id,
                        'date' => $payment->payment_date,
                        'created_by' => $payment->created_by,
                    ]);
                    $newLedger->invoice_url = route('admin.suppliers.ledger-details', $newLedger->id);
                    $newLedger->save();

                    // Link payment to the new ledger
                    DB::table('supplier_payments')->where('id', $payment->id)->update(['ledger_id' => $newLedger->id]);

                    // Create ledger detail
                    if ($purchase) {
                        $newLedger->details()->create([
                            'invoice' => $purchase->invoice_number,
                            'amount' => $payment->amount,
                        ]);
                    }

                    $this->info("    → CREATED Ledger #{$newLedger->id} and linked payment #{$payment->id}");
                }
            }
        }

        if ($duePayFixCount === 0 && $orphanPayments === 0 && abs($dueDiff) < 0.01) {
            $this->info('  ✓ Due payment ledger matches supplier_payments.');
        } else {
            if ($duePayFixCount > 0) {
                $this->error("  Found {$duePayFixCount} due payment ledger mismatch(es).");
            }
            if ($orphanPayments > 0) {
                $this->warn("  {$orphanPayments} due payment(s) without valid ledger entries.");
            }
        }

        // ─── 5. Per-supplier summary ───
        $this->newLine();
        $this->info('5. Per-supplier due comparison (Supplier List vs Ledger vs Purchase Sum)...');
        $this->newLine();

        $supplierQuery = Supplier::query();
        if ($supplierId) {
            $supplierQuery->where('id', $supplierId);
        }
        $suppliers = $supplierQuery->with(['purchases.purchaseReturn', 'payments', 'purchaseReturn'])->get();

        foreach ($suppliers as $supplier) {
            $supplierTotalDue = $supplier->total_due;

            $purchaseDueSum = 0;
            foreach ($supplier->purchases as $p) {
                if ((float) $p->due_amount > 0) {
                    $returnAmount = $p->purchaseReturn ? $p->purchaseReturn->sum('return_amount') : 0;
                    $returnReceived = $p->purchaseReturn ? $p->purchaseReturn->sum('received_amount') : 0;
                    $effectiveDue = (float) $p->due_amount - $returnAmount + $returnReceived;
                    if ($effectiveDue > 0) {
                        $purchaseDueSum += $effectiveDue;
                    }
                }
            }

            $ledgerBalance = (float) Ledger::where('supplier_id', $supplier->id)->sum('due_amount');

            $hasIssue = abs($supplierTotalDue - $purchaseDueSum) > 0.01
                     || abs($supplierTotalDue - $ledgerBalance) > 0.01;

            if ($hasIssue) {
                $this->warn(
                    "  Supplier: {$supplier->name} (ID:{$supplier->id})"
                    . " | Model Due: " . number_format($supplierTotalDue, 2)
                    . " | Purchase Sum: " . number_format($purchaseDueSum, 2)
                    . " | Ledger Balance: " . number_format($ledgerBalance, 2)
                );
            }
        }

        // ─── Summary ───
        $this->newLine();
        $totalIssues = $mismatchCount + $orphanCount + $ledgerAmountIssues + (abs($dueDiff) > 0.01 ? 1 : 0);
        if ($totalIssues > 0 && !$shouldFix) {
            $this->info('Run with --fix to auto-repair:');
            $this->info('  php artisan purchase:reconcile --fix');
            if ($supplierId) {
                $this->info("  php artisan purchase:reconcile --fix --supplier={$supplierId}");
            }
        }

        $this->newLine();
        $this->info('=== Done ===');

        return 0;
    }
}
