<?php

namespace Modules\Expense\app\Services;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplier;
use Modules\Expense\app\Models\ExpenseSupplierPayment;

class ExpenseSupplierService
{
    public function __construct(private ExpenseSupplier $expenseSupplier) {}

    public function all()
    {
        return $this->expenseSupplier->where('status', 1);
    }

    public function allSuppliers()
    {
        $suppliers = $this->expenseSupplier->query();
        $suppliers = $suppliers->with(['expenses' => function ($query) {
            if (request()->from_date || request()->to_date) {
                [$from_date, $to_date] = $this->getDateRangeFromRequest();
                if ($from_date) {
                    $query->where('date', '>=', $from_date);
                }
                if ($to_date) {
                    $query->where('date', '<=', $to_date);
                }
            }
        }, 'payments' => function ($query) {
            $query->where('is_paid', 1);

            [$from_date, $to_date] = $this->getDateRangeFromRequest();

            if ($from_date) {
                $query->where('payment_date', '>=', $from_date);
            }

            if ($to_date) {
                $query->where('payment_date', '<=', $to_date);
            }
        }]);

        if (request()->keyword) {
            $suppliers = $suppliers->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('company', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                    ->orWhere('address', 'like', '%' . request()->keyword . '%')
                    ->orWhere('email', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('area', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%');
                    });
            });
        }

        if (request()->order_by) {
            $suppliers = $suppliers->orderBy('name', request()->order_by);
        } else {
            $suppliers = $suppliers->orderBy('name', 'asc');
        }

        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $suppliers = $suppliers->with(['expenses', 'payments'])
                        ->where(function ($q) {
                            $q->whereHas('expenses', function ($query) {
                                $query->where('due_amount', '>', 0);
                            });
                        })
                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->total_due;
                        });
                    break;

                case 'paid':
                    $suppliers = $suppliers->with(['payments', 'expenses'])
                        ->whereHas('expenses')
                        ->get();
                    $suppliers = $suppliers->filter(function ($supplier) {
                        return $supplier->total_due <= 0;
                    });
                    $suppliers = $suppliers->$orderBy(function ($supplier) {
                        return $supplier->total_paid;
                    });
                    break;

                case 'total':
                    $suppliers = $suppliers->with(['expenses'])
                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->total_expense;
                        });
                    break;

                default:
                    break;
            }
        }

        return $suppliers;
    }

    private function getDateRangeFromRequest()
    {
        $from_date = request()->from_date ? now()->parse(request()->from_date) : null;
        $to_date = request()->to_date ? now()->parse(request()->to_date) : null;

        return [$from_date, $to_date];
    }

    public function find($id)
    {
        return $this->expenseSupplier->with('dueExpenses')->find($id);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth('admin')->id();
        return $this->expenseSupplier->create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth('admin')->id();
        return $this->expenseSupplier->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->expenseSupplier->where('id', $id)->delete();
    }

    public function duePay(Request $request, $id)
    {
        $supplier = $this->expenseSupplier->find($id);

        // Validate array lengths match
        if (count($request->expense_id) !== count($request->amount)) {
            throw new \Exception('Expense IDs and amounts array length mismatch.');
        }

        // Server-side validation: verify each payment amount and ownership
        $totalPayingAmount = 0;
        foreach ($request->expense_id as $index => $expenseId) {
            $payAmount = round((float) ($request->amount[$index] ?? 0), 2);
            if ($payAmount <= 0) continue;

            $expense = Expense::findOrFail($expenseId);

            // Verify expense belongs to this supplier
            if ((int) $expense->expense_supplier_id !== (int) $id) {
                throw new \Exception("Expense #{$expense->invoice} does not belong to this supplier.");
            }

            // Verify amount doesn't exceed due
            $rawDue = (float) \Illuminate\Support\Facades\DB::table('expenses')->where('id', $expenseId)->value('due_amount');
            if ($payAmount > $rawDue + 0.01) {
                throw new \Exception(
                    "Payment amount (" . number_format($payAmount, 2) . ") exceeds due amount ("
                    . number_format($rawDue, 2) . ") for expense: {$expense->invoice}"
                );
            }

            $totalPayingAmount += min($payAmount, $rawDue);
        }

        if ($totalPayingAmount <= 0) {
            throw new \Exception('No valid payment amounts provided.');
        }

        // Use server-calculated total
        $request->merge(['paying_amount' => round($totalPayingAmount, 2)]);

        $supplier->balance = $supplier->balance - $totalPayingAmount;
        $supplier->save();

        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }

        // Create Ledger
        $ledger = new Ledger();
        $ledger->expense_supplier_id = $id;
        $ledger->amount = $totalPayingAmount;
        $ledger->invoice_type = 'Expense Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber($request->payment_date);
        $ledger->note = $request->note;
        $ledger->due_amount = -$totalPayingAmount;
        $ledger->total_amount = 0;
        $ledger->date = now()->parse($request->payment_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.expense-suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // Create payment for each expense with non-zero amount
        foreach ($request->expense_id as $index => $expenseId) {
            $payAmount = round((float) ($request->amount[$index] ?? 0), 2);
            if ($payAmount <= 0) {
                continue;
            }

            $expense = Expense::findOrFail($expenseId);

            // Use DB-level arithmetic for safe updates
            $rawPaid = (float) \Illuminate\Support\Facades\DB::table('expenses')->where('id', $expenseId)->value('paid_amount');
            $rawDue = (float) \Illuminate\Support\Facades\DB::table('expenses')->where('id', $expenseId)->value('due_amount');
            $payAmount = min($payAmount, $rawDue);

            \Illuminate\Support\Facades\DB::table('expenses')->where('id', $expenseId)->update([
                'paid_amount' => round($rawPaid + $payAmount, 2),
                'due_amount'  => round(max(0, $rawDue - $payAmount), 2),
            ]);

            // Create payment data
            ExpenseSupplierPayment::create([
                'expense_id' => $expense->id,
                'expense_supplier_id' => $id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'memo' => $request->memo,
                'invoice' => generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $request->payment_date),
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            // Create ledger details
            $ledger->details()->create([
                'invoice' => $expense->invoice,
                'amount' => $request->amount[$index],
            ]);
        }
    }

    public function duePayHistory()
    {
        $list = ExpenseSupplierPayment::query();

        $list = $list->with('expense', 'expenseSupplier', 'createdBy')
            ->whereNotNull('expense_id')
            ->where('payment_type', 'due_pay');

        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $list = $list->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $list = $list->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)
                    ->orWhereHas('expenseSupplier', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            })
                ->orWhere('invoice', 'like', $keyword)
                ->orWhere('account_type', 'like', $keyword);
        }

        if (request()->order_by) {
            $list = $list->orderBy('payment_date', request()->order_by);
        } else {
            $list = $list->orderBy('payment_date', 'desc');
        }

        return $list;
    }

    public function duePayDelete($id)
    {
        $payment = ExpenseSupplierPayment::find($id);
        if (!$payment) {
            throw new \Exception('Payment not found.');
        }

        $paymentAmount = round((float) $payment->amount, 2);

        // Update expense paid/due amounts using DB-level arithmetic
        if ($payment->expense_id) {
            $rawPaid = (float) \Illuminate\Support\Facades\DB::table('expenses')->where('id', $payment->expense_id)->value('paid_amount');
            $rawDue = (float) \Illuminate\Support\Facades\DB::table('expenses')->where('id', $payment->expense_id)->value('due_amount');

            \Illuminate\Support\Facades\DB::table('expenses')->where('id', $payment->expense_id)->update([
                'paid_amount' => round(max(0, $rawPaid - $paymentAmount), 2),
                'due_amount'  => round($rawDue + $paymentAmount, 2),
            ]);
        }

        $ledger = $payment->ledger;

        if ($ledger && $ledger->id) {
            $otherPaymentsCount = ExpenseSupplierPayment::where('ledger_id', $ledger->id)
                ->where('id', '!=', $id)
                ->count();

            if ($otherPaymentsCount == 0) {
                $ledger->details()->delete();
                $ledger->delete();
            } else {
                $ledger->details()->where('invoice', $payment->expense ? $payment->expense->invoice : null)->delete();

                $rawLedgerAmount = (float) \Illuminate\Support\Facades\DB::table('ledgers')->where('id', $ledger->id)->value('amount');
                $rawLedgerDue = (float) \Illuminate\Support\Facades\DB::table('ledgers')->where('id', $ledger->id)->value('due_amount');

                \Illuminate\Support\Facades\DB::table('ledgers')->where('id', $ledger->id)->update([
                    'amount'     => round($rawLedgerAmount - $paymentAmount, 2),
                    'due_amount' => round($rawLedgerDue + $paymentAmount, 2),
                ]);
            }
        }

        return $payment->delete();
    }

    public function genInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(ExpenseSupplierPayment::class, 'invoice', 'ESP', [], $date);
    }

    public function advancePay(Request $request, $id)
    {
        // Validate refund doesn't exceed available advance
        if ($request->refund_amount) {
            $supplier = $this->expenseSupplier->find($id);
            $availableAdvance = $supplier ? $supplier->advance : 0;
            if ((float) $request->refund_amount > $availableAdvance + 0.01) {
                throw new \Exception(
                    "Refund amount (" . number_format($request->refund_amount, 2)
                    . ") exceeds available advance balance (" . number_format($availableAdvance, 2) . ")"
                );
            }
        }

        $account = $request->account_id;

        $ledger = new Ledger();
        $ledger->expense_supplier_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Expense Advance Payment' : 'Expense Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 0 : 1;
        $ledger->is_received = $request->refund_amount != null ? 1 : 0;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber($request->date);
        $ledger->note = $request->note;

        if ($request->refund_amount != null) {
            $ledger->due_amount += $request->refund_amount;
            $ledger->amount = -$request->refund_amount;
        } else {
            $ledger->due_amount = -$request->paying_amount;
            $ledger->amount = $request->paying_amount;
        }
        $ledger->date = now()->parse($request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
        $ledger->invoice_url = route('admin.expense-suppliers.ledger-details', $ledger->id);
        $ledger->save();

        $ledger->details()->create([
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount
        ]);

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }

        ExpenseSupplierPayment::create([
            'expense_supplier_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_pay',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'memo' => $request->memo,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber($request->date),
            'ledger_id' => $ledger->id
        ]);
    }

    public function genLedgerInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(Ledger::class, 'invoice_no', 'ESPL', ['invoice_type' => 'Expense Due Payment'], $date);
    }
}
