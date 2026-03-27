<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Sales\app\Models\Sale;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Customer\app\Models\CustomerDue;
use App\Models\User;

class SalesReconcile extends Command
{
    protected $signature = 'sales:reconcile
                            {--fix : Automatically fix inconsistencies}
                            {--customer= : Only reconcile a specific customer ID}';

    protected $description = 'Find and fix sales where paid_amount/due_amount does not match customer_payments';

    public function handle()
    {
        $this->info('=== Sales / Customer Due Reconciliation ===');
        $this->newLine();

        $shouldFix = $this->option('fix');
        $customerId = $this->option('customer');

        // ─── 1. Find sales where stored paid_amount ≠ sum of payments ───
        $this->info('1. Checking sales.paid_amount vs customer_payments...');
        $this->newLine();

        $query = Sale::query()->whereNotNull('customer_id');
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        $sales = $query->get();
        $mismatchCount = 0;
        $totalDiff = 0;
        $mismatches = [];

        foreach ($sales as $sale) {
            // Sum payments that count toward this sale's paid_amount
            $paymentSum = (float) CustomerPayment::where('sale_id', $sale->id)
                ->whereIn('payment_type', ['sale', 'due_receive', 'advance_deduct'])
                ->sum('amount');

            $storedPaid = round((float) $sale->paid_amount, 2);
            $computedPaid = round($paymentSum, 2);

            if (abs($storedPaid - $computedPaid) > 0.01) {
                $diff = round($computedPaid - $storedPaid, 2);
                $mismatchCount++;
                $totalDiff += $diff;

                $correctDue = round(max(0, (float) $sale->grand_total - $computedPaid), 2);

                $mismatches[] = [
                    'sale_id' => $sale->id,
                    'invoice' => $sale->invoice,
                    'customer_id' => $sale->customer_id,
                    'grand_total' => $sale->grand_total,
                    'stored_paid' => $storedPaid,
                    'actual_paid' => $computedPaid,
                    'diff' => $diff,
                    'stored_due' => $sale->due_amount,
                    'correct_due' => $correctDue,
                ];

                $this->warn(
                    "  MISMATCH: Sale #{$sale->invoice} (ID:{$sale->id})"
                    . " | Grand Total: " . number_format($sale->grand_total, 2)
                    . " | Stored Paid: " . number_format($storedPaid, 2)
                    . " | Actual Paid (from payments): " . number_format($computedPaid, 2)
                    . " | Diff: " . number_format($diff, 2)
                    . " | Stored Due: " . number_format($sale->due_amount, 2)
                    . " | Correct Due: " . number_format($correctDue, 2)
                );

                if ($shouldFix) {
                    DB::table('sales')->where('id', $sale->id)->update([
                        'paid_amount' => $computedPaid,
                        'due_amount' => $correctDue,
                        'payment_status' => $correctDue <= 0 ? 'paid' : 'due',
                    ]);
                    $this->info("    → FIXED: paid_amount={$computedPaid}, due_amount={$correctDue}");
                }
            }
        }

        if ($mismatchCount === 0) {
            $this->info('  ✓ All sales paid_amount match customer_payments. No mismatch found.');
        } else {
            $this->newLine();
            $this->error("  Found {$mismatchCount} mismatched sale(s). Total diff: " . number_format($totalDiff, 2));
        }

        // ─── 2. Find customer_dues that don't match sales ───
        $this->newLine();
        $this->info('2. Checking customer_dues vs sales...');
        $this->newLine();

        $dueQuery = CustomerDue::query();
        if ($customerId) {
            $dueQuery->where('customer_id', $customerId);
        }
        $dues = $dueQuery->get();
        $dueMismatchCount = 0;

        foreach ($dues as $due) {
            $sale = Sale::where('invoice', $due->invoice)->first();
            if (!$sale) {
                $this->warn("  ORPHAN: CustomerDue #{$due->id} invoice={$due->invoice} — no matching sale");
                $dueMismatchCount++;
                continue;
            }

            $saleDue = round((float) $sale->due_amount, 2);
            $customerDue = round((float) $due->due_amount, 2);

            if (abs($saleDue - $customerDue) > 0.01) {
                $dueMismatchCount++;
                $this->warn(
                    "  MISMATCH: CustomerDue #{$due->id} invoice={$due->invoice}"
                    . " | Sale.due_amount: " . number_format($saleDue, 2)
                    . " | CustomerDue.due_amount: " . number_format($customerDue, 2)
                    . " | Diff: " . number_format($saleDue - $customerDue, 2)
                );

                if ($shouldFix) {
                    DB::table('customer_dues')->where('id', $due->id)->update([
                        'due_amount' => $saleDue,
                        'paid_amount' => round(max(0, (float) $sale->grand_total - $saleDue), 2),
                    ]);
                    $this->info("    → FIXED: due_amount={$saleDue}");
                }
            }
        }

        if ($dueMismatchCount === 0) {
            $this->info('  ✓ All customer_dues match sales. No mismatch found.');
        } else {
            $this->error("  Found {$dueMismatchCount} mismatched customer_due(s).");
        }

        // ─── 3. Summary per customer ───
        $this->newLine();
        $this->info('3. Per-customer due comparison (Customer List vs Sales List)...');
        $this->newLine();

        $userQuery = User::query();
        if ($customerId) {
            $userQuery->where('id', $customerId);
        }
        $users = $userQuery->with(['sales.saleReturns', 'payment', 'saleReturn'])->get();

        $grandCustomerDue = 0;
        $grandSalesDue = 0;

        foreach ($users as $user) {
            // Customer list formula
            $customerTotalDue = $user->total_due;
            $customerAdvance = $user->advances();
            $customerOffset = min(max(0, $customerTotalDue), max(0, $customerAdvance));
            $customerEffectiveDue = $customerTotalDue - $customerOffset;

            // Sales list formula
            $salesDue = 0;
            $advanceRemaining = $customerAdvance;
            foreach ($user->sales->sortBy('id') as $sale) {
                $returnDue = $sale->saleReturns->sum('return_due');
                $offset = min(max(0, (float) $sale->due_amount), max(0, $advanceRemaining));
                $advanceRemaining -= $offset;
                $salesDue += (float) $sale->due_amount - $offset - $returnDue;
            }

            $diff = round($customerEffectiveDue - $salesDue, 2);

            if (abs($diff) > 0.01) {
                $this->warn(
                    "  Customer: {$user->name} (ID:{$user->id})"
                    . " | wallet_balance: " . number_format($user->wallet_balance ?? 0, 2)
                    . " | Customer List Due: " . number_format($customerEffectiveDue, 2)
                    . " | Sales List Due: " . number_format($salesDue, 2)
                    . " | DIFF: " . number_format($diff, 2)
                );
            }

            $grandCustomerDue += $customerEffectiveDue;
            $grandSalesDue += $salesDue;
        }

        $this->newLine();
        $this->info("  Grand Total — Customer List Due: " . number_format($grandCustomerDue, 2));
        $this->info("  Grand Total — Sales List Due:    " . number_format($grandSalesDue, 2));
        $this->info("  Grand Total — Difference:        " . number_format($grandCustomerDue - $grandSalesDue, 2));

        // ─── 4. Payments without ledger_id ───
        $this->newLine();
        $this->info('4. Checking for due_receive payments without ledger_id...');

        $orphanedPayments = CustomerPayment::whereIn('payment_type', ['due_receive', 'direct_due_receive'])
            ->whereNull('ledger_id')
            ->count();

        if ($orphanedPayments > 0) {
            $this->warn("  Found {$orphanedPayments} due_receive payment(s) without ledger_id (created before the fix).");
        } else {
            $this->info('  ✓ All due_receive payments have ledger_id.');
        }

        $this->newLine();
        if ($mismatchCount > 0 && !$shouldFix) {
            $this->info('Run with --fix to auto-repair the mismatches:');
            $this->info('  php artisan sales:reconcile --fix');
        }

        $this->newLine();
        $this->info('=== Done ===');

        return 0;
    }
}
