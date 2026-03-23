<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Ledger;
use App\Models\LedgerDetails;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class LedgerReconcile extends Command
{
    protected $signature = 'ledger:reconcile
                            {--fix : Automatically fix inconsistencies}
                            {--supplier= : Only reconcile a specific supplier ID}';

    protected $description = 'Check and fix supplier ledger inconsistencies where purchase ledger due_amount does not reflect current state';

    public function handle(): int
    {
        $this->info('=== Supplier Ledger Reconciliation ===');
        $this->newLine();

        $query = Supplier::with(['payments', 'purchases', 'purchaseReturn']);
        if ($supplierId = $this->option('supplier')) {
            $query->where('id', $supplierId);
        }
        $suppliers = $query->get();

        $totalIssues = 0;
        $totalFixed = 0;

        foreach ($suppliers as $supplier) {
            // Calculate expected ledger balance
            $ledgerBalance = (float) Ledger::where('supplier_id', $supplier->id)->sum('due_amount');
            $advGiven = (float) $supplier->payments->where('payment_type', 'advance_pay')->sum('amount');
            $advRefunded = (float) $supplier->payments->where('payment_type', 'advance_refund')->sum('amount');
            $expectedBalance = $supplier->total_due - ($advGiven - $advRefunded);

            if (abs($ledgerBalance - $expectedBalance) <= 0.01) {
                continue; // This supplier is fine
            }

            $diff = round($ledgerBalance - $expectedBalance, 2);
            $this->warn("{$supplier->name} ({$supplier->company}): ledger={$ledgerBalance}, expected={$expectedBalance}, diff={$diff}");
            $totalIssues++;

            // Find the problematic purchase ledger entries
            $purchases = Purchase::where('supplier_id', $supplier->id)->get();

            foreach ($purchases as $purchase) {
                $ledger = Ledger::where('supplier_id', $supplier->id)
                    ->where('invoice_type', 'purchase')
                    ->where('invoice_no', $purchase->invoice_number)
                    ->first();

                if (!$ledger) continue;

                // What the ledger due_amount SHOULD be:
                // = purchase.total_amount - cash paid at purchase time (non-advance)
                $cashPaidAtPurchase = SupplierPayment::where('purchase_id', $purchase->id)
                    ->where('payment_type', 'purchase')
                    ->sum('amount');

                $advancePaidAtPurchase = SupplierPayment::where('purchase_id', $purchase->id)
                    ->where('payment_type', 'advance_deduct')
                    ->sum('amount');

                $correctCashDue = $purchase->total_amount - $cashPaidAtPurchase;

                // The ledger amount should be cashPaid, due should be cashDue
                $correctAmount = $cashPaidAtPurchase;

                if (abs($ledger->due_amount - $correctCashDue) > 0.01 || abs($ledger->amount - $correctAmount) > 0.01) {
                    $this->line("  {$purchase->invoice_number}: ledger.due={$ledger->due_amount} should be {$correctCashDue}, ledger.amount={$ledger->amount} should be {$correctAmount}");

                    if ($this->option('fix')) {
                        $ledger->due_amount = $correctCashDue;
                        $ledger->amount = $correctAmount;
                        $ledger->save();
                        $totalFixed++;
                        $this->line("    <fg=green>FIXED</>");
                    }
                }

                // Also check advance deduct ledger entries
                $advDeductLedgers = Ledger::where('supplier_id', $supplier->id)
                    ->where('invoice_type', 'Advance Deduct')
                    ->where('invoice_no', $purchase->invoice_number)
                    ->get();

                foreach ($advDeductLedgers as $adl) {
                    // Advance deduct should have due_amount = -amount (reduces balance)
                    $correctAdvDue = -abs($adl->amount);
                    if ($adl->amount > 0 && abs($adl->due_amount - $correctAdvDue) > 0.01) {
                        $this->line("  {$purchase->invoice_number} (Advance Deduct): due_amount={$adl->due_amount} should be {$correctAdvDue}");

                        if ($this->option('fix')) {
                            $adl->due_amount = $correctAdvDue;
                            $adl->save();
                            $totalFixed++;
                            $this->line("    <fg=green>FIXED</>");
                        }
                    }
                }
            }
        }

        $this->newLine();
        if ($totalIssues === 0) {
            $this->info('All supplier ledgers are consistent!');
        } else {
            $this->warn("Found {$totalIssues} supplier(s) with ledger inconsistencies.");
            if ($this->option('fix')) {
                $this->info("Fixed {$totalFixed} ledger entries.");
            } else {
                $this->comment('Run with --fix to automatically repair these inconsistencies.');
            }
        }

        return self::SUCCESS;
    }
}
