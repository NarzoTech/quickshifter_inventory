<?php

namespace Modules\Supplier\app\Services;

use App\Imports\SuppliersImport;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(private Supplier $supplier) {}

    public function all()
    {
        return $this->supplier->where('status', 1)->with('purchaseReturn');
    }

    public function allSupplier()
    {
        $suppliers = $this->supplier->query();
        $hasDateFilter = request()->from_date || request()->to_date;

        $suppliers = $suppliers->with([
            'purchaseReturn' => function ($query) use ($hasDateFilter) {
                if ($hasDateFilter) {
                    [$from_date, $to_date] = $this->getDateRangeFromRequest();
                    if ($from_date) {
                        $query->where('return_date', '>=', $from_date);
                    }
                    if ($to_date) {
                        $query->where('return_date', '<=', $to_date);
                    }
                }
            },
            'purchases' => function ($query) use ($hasDateFilter) {
                if ($hasDateFilter) {
                    [$from_date, $to_date] = $this->getDateRangeFromRequest();
                    if ($from_date) {
                        $query->where('purchase_date', '>=', $from_date);
                    }
                    if ($to_date) {
                        $query->where('purchase_date', '<=', $to_date);
                    }
                }
            },
            'payments' => function ($query) use ($hasDateFilter) {
                $query->where(function ($q) {
                    $q->where('is_paid', 1)
                        ->orWhereIn('payment_type', ['advance_refund']);
                });

                if ($hasDateFilter) {
                    [$from_date, $to_date] = $this->getDateRangeFromRequest();

                    if ($from_date) {
                        $query->where('payment_date', '>=', $from_date);
                    }

                    if ($to_date) {
                        $query->where('payment_date', '<=', $to_date);
                    }
                }
            },
        ]);


        if (request()->keyword) {
            $suppliers = $suppliers->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('company', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                    ->orWhere('address', 'like', '%' . request()->keyword . '%')
                    ->orWhere('email', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('area', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%');
                    })
                ;
            });
        }

        if (request()->order_by) {
            $suppliers = $suppliers->orderBy('company', request()->order_by);
        } else {
            $suppliers = $suppliers->orderBy('company', 'asc');
        }

        if (request()->from_date && request()->to_date) {

            $suppliers = $suppliers->whereBetween('date', [now()->parse(request()->from_date), now()->parse(request()->to_date)]);
        }

        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $suppliers = $suppliers->with(['purchases', 'payments', 'purchaseReturn'])
                        ->where(function ($q) {
                            // check if supplier has due
                            $q->whereHas('purchases', function ($query) {
                                $query->where('due_amount', '>', 0);
                            });
                        })
                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->total_due;
                        });
                    break;

                case 'paid':
                    $suppliers = $suppliers->with(['payments', 'purchases', 'purchaseReturn'])
                        ->whereHas('purchases')
                        ->get();
                    $suppliers = $suppliers->filter(function ($supplier) {
                        return $supplier->total_due <= 0;
                    });

                    $suppliers = $suppliers->$orderBy(function ($supplier) {
                        return $supplier->total_paid;
                    });
                    break;

                case 'total':
                    $suppliers = $suppliers->with(['purchases'])

                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->purchases->sum('total_amount');
                        });
                    break;

                default:
                    // Default sorting logic
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
        $supplier = $this->supplier->with('duePurchase')->find($id);

        return $supplier;
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->create($data);
    }

    public function updateSupplier(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->where('id', $id)->update($data);
    }

    public function deleteSupplier($id)
    {
        return $this->supplier->where('id', $id)->delete();
    }

    public function duePay(Request $request, $id)
    {
        $supplier = $this->supplier->find($id);

        // Validate array lengths match
        if (count($request->purchase_id) !== count($request->amount)) {
            throw new \Exception('Purchase IDs and amounts array length mismatch.');
        }

        // Server-side validation: verify each payment, ownership, and amount limits
        $totalPayingAmount = 0;
        foreach ($request->purchase_id as $index => $purchaseId) {
            $payAmount = round((float) ($request->amount[$index] ?? 0), 2);

            // Skip zero amounts
            if ($payAmount <= 0) continue;

            $purchase = Purchase::findOrFail($purchaseId);

            // Verify purchase belongs to this supplier
            if ((int) $purchase->supplier_id !== (int) $id) {
                throw new \Exception(
                    "Purchase #{$purchase->invoice_number} does not belong to this supplier."
                );
            }

            // Get raw due_amount from DB to bypass any accessor issues
            $rawDue = (float) \DB::table('purchases')->where('id', $purchaseId)->value('due_amount');

            if ($payAmount > $rawDue + 0.01) {
                throw new \Exception(
                    "Payment amount (" . number_format($payAmount, 2)
                    . ") exceeds due amount (" . number_format($rawDue, 2)
                    . ") for invoice: {$purchase->invoice_number}"
                );
            }

            // Cap payment at actual due (prevent tiny overpayment from float)
            $request->merge([
                'amount' => array_replace($request->amount, [$index => min($payAmount, $rawDue)]),
            ]);

            $totalPayingAmount += min($payAmount, $rawDue);
        }

        if ($totalPayingAmount <= 0) {
            throw new \Exception('No valid payment amounts provided.');
        }

        // Use server-calculated total — never trust client's paying_amount
        $request->merge(['paying_amount' => round($totalPayingAmount, 2)]);

        // account information

        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }


        // create Ledger
        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $totalPayingAmount;
        $ledger->invoice_type = 'Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber('Due Payment', $request->payment_date);
        $ledger->note = $request->note;
        $ledger->due_amount = -$totalPayingAmount;
        $ledger->total_amount = 0;
        $ledger->date = now()->parse($request->payment_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // create payment for each purchase with non-zero amount
        foreach ($request->purchase_id as $index => $purchaseId) {
            $payAmount = round((float) ($request->amount[$index] ?? 0), 2);

            if ($payAmount <= 0) {
                continue;
            }
            $purchase = Purchase::findOrFail($purchaseId);

            // Use DB-level arithmetic to avoid number_format accessor issues
            $rawPaid = (float) \DB::table('purchases')->where('id', $purchaseId)->value('paid_amount');
            $rawDue = (float) \DB::table('purchases')->where('id', $purchaseId)->value('due_amount');

            $newPaid = round($rawPaid + $payAmount, 2);
            $newDue = round(max(0, $rawDue - $payAmount), 2);

            \DB::table('purchases')->where('id', $purchaseId)->update([
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $newDue == 0 ? 'paid' : 'due',
            ]);

            // create payment data
            SupplierPayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            // create ledger details
            $ledger->details()->create([
                'invoice' => $purchase->invoice_number,
                'amount' => $request->amount[$index],
            ]);
        }
    }

    public function advanceList()
    {
        $list = SupplierPayment::query();

        $list = $list->with('supplier', 'createdBy')
            ->whereIn('payment_type', ['advance_pay', 'advance_refund'])
            ->where('amount', '>', 0);

        // Date filtering
        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $list = $list->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        // Keyword search
        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $list = $list->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)
                    ->orWhere('invoice', 'like', $keyword)
                    ->orWhere('account_type', 'like', $keyword)
                    ->orWhereHas('supplier', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            });
        }

        // Filter by payment type
        if (request()->filled('payment_type')) {
            $list = $list->where('payment_type', request()->payment_type);
        }

        if (request()->order_by) {
            $list = $list->orderBy('payment_date', request()->order_by)->orderBy('id', request()->order_by);
        } else {
            $list = $list->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }

        return $list;
    }

    public function duePayHistory()
    {
        $list = SupplierPayment::query();

        $list = $list->with('purchase', 'supplier', 'createdBy')
            ->whereNotNull('purchase_id')
            ->where('payment_type', 'due_pay');

        // Date filtering
        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $list = $list->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        // Keyword search
        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $list = $list->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)
                    ->orWhere('invoice', 'like', $keyword)
                    ->orWhere('account_type', 'like', $keyword)
                    ->orWhereHas('supplier', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            });
        }

        // Supplier filter
        if (request()->filled('supplier')) {
            $list = $list->where('supplier_id', request()->supplier);
        }

        if (request()->order_by) {
            $list = $list->orderBy('payment_date', request()->order_by)->orderBy('id', request()->order_by);
        } else {
            $list = $list->orderBy('payment_date', 'desc')->orderBy('id', 'desc');
        }

        return $list;
    }

    public function dueReceiveDelete($id)
    {
        $payment = SupplierPayment::find($id);
        if (!$payment) {
            throw new \Exception('Payment not found.');
        }

        $paymentAmount = round((float) $payment->amount, 2);

        // Update purchase paid/due amounts using DB-level arithmetic
        if ($payment->purchase_id) {
            $rawPaid = (float) \DB::table('purchases')->where('id', $payment->purchase_id)->value('paid_amount');
            $rawDue = (float) \DB::table('purchases')->where('id', $payment->purchase_id)->value('due_amount');

            $newPaid = round(max(0, $rawPaid - $paymentAmount), 2);
            $newDue = round($rawDue + $paymentAmount, 2);

            \DB::table('purchases')->where('id', $payment->purchase_id)->update([
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $newDue == 0 ? 'paid' : 'due',
            ]);
        }

        $ledger = $payment->ledger;

        if ($ledger && $ledger->id) {
            // Check if other payments reference this ledger
            $otherPaymentsCount = SupplierPayment::where('ledger_id', $ledger->id)
                ->where('id', '!=', $id)
                ->count();

            if ($otherPaymentsCount == 0) {
                // No other payments use this ledger, safe to delete
                $ledger->details()->delete();
                $ledger->delete();
            } else {
                // Other payments exist, only delete the specific ledger detail for this payment
                $invoiceNo = $payment->purchase?->invoice_number;
                if ($invoiceNo) {
                    $ledger->details()->where('invoice', $invoiceNo)->delete();
                }

                // Update ledger amount using raw DB values
                $rawLedgerAmount = (float) \DB::table('ledgers')->where('id', $ledger->id)->value('amount');
                $rawLedgerDue = (float) \DB::table('ledgers')->where('id', $ledger->id)->value('due_amount');

                \DB::table('ledgers')->where('id', $ledger->id)->update([
                    'amount'     => round($rawLedgerAmount - $paymentAmount, 2),
                    'due_amount' => round($rawLedgerDue + $paymentAmount, 2),
                ]);
            }
        }

        return $payment->delete();
    }

    public function genInvoiceNumber($date = null)
    {
        return generateInvoiceNumber(SupplierPayment::class, 'invoice', 'SP', [], $date);
    }


    public function advancePay(Request $request, $id)
    {
        // Validate refund doesn't exceed available advance
        if ($request->refund_amount) {
            $supplier = $this->supplier->find($id);
            $availableAdvance = $supplier ? $supplier->advance : 0;
            if ((float) $request->refund_amount > $availableAdvance + 0.01) {
                throw new \Exception(
                    "Refund amount (" . number_format($request->refund_amount, 2)
                    . ") exceeds available advance balance (" . number_format($availableAdvance, 2) . ")"
                );
            }
        }

        $account = $request->account_id;

        // create ledger

        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Payment' : 'Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 0 : 1;
        $ledger->is_received = $request->refund_amount != null ? 1 : 0;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber($ledger->invoice_type, $request->date);
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
        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // create ledger details
        $ledger->details()->create([
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount
        ]);

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::where('account_type', $account)?->first();
        } else {
            $account = Account::find($account);
        }
        // create payment data
        SupplierPayment::create([
            'supplier_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_pay',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber($request->date),
            'ledger_id' => $ledger->id
        ]);
    }


    public function genLedgerInvoiceNumber($type = 'Due Payment', $date = null)
    {
        $prefixMap = [
            'Due Payment'      => 'SDL',
            'Advance Payment'  => 'SAL',
            'Payment Return'   => 'SARL',
        ];
        $prefix = $prefixMap[$type] ?? 'SL';
        return generateInvoiceNumber(Ledger::class, 'invoice_no', $prefix, ['invoice_type' => $type], $date);
    }


    public function offsetDueWithAdvance($supplierId)
    {
        $supplier = $this->supplier->findOrFail($supplierId);
        $advanceBalance = $supplier->advance;
        $totalDue = $supplier->total_due;

        if ($advanceBalance <= 0 || $totalDue <= 0) {
            throw new \Exception('No advance or due to offset');
        }

        $offsetAmount = min($advanceBalance, $totalDue);

        $account = Account::where('account_type', 'advance')->first();
        if (!$account) {
            $account = Account::create(['account_type' => 'advance']);
        }

        // Create Due Payment ledger
        $ledger = new Ledger();
        $ledger->supplier_id = $supplierId;
        $ledger->amount = $offsetAmount;
        $ledger->invoice_type = 'Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber('Due Payment');
        $ledger->note = 'Auto offset due with advance balance';
        $ledger->due_amount = -$offsetAmount;
        $ledger->total_amount = 0;
        $ledger->date = now();
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // Apply to due purchases (oldest first)
        $remaining = $offsetAmount;
        $duePurchases = Purchase::where('supplier_id', $supplierId)
            ->where('due_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($duePurchases as $purchase) {
            if ($remaining <= 0) break;

            $payAmount = min($remaining, $purchase->due_amount);
            $purchase->paid_amount += $payAmount;
            $purchase->due_amount -= $payAmount;
            $purchase->payment_status = $purchase->due_amount == 0 ? 'paid' : 'due';
            $purchase->save();

            SupplierPayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $supplierId,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $payAmount,
                'payment_date' => now(),
                'note' => 'Auto offset with advance',
                'created_by' => auth('admin')->user()->id,
                'ledger_id' => $ledger->id,
            ]);

            $ledger->details()->create([
                'invoice' => $purchase->invoice_number,
                'amount' => $payAmount,
            ]);

            $remaining -= $payAmount;
        }

        $actualOffset = $offsetAmount - $remaining;

        // Create Advance Deduct ledger FIRST (records advance consumption, no due impact — due already reduced by Due Payment entry)
        $advLedger = new Ledger();
        $advLedger->supplier_id = $supplierId;
        $advLedger->amount = $actualOffset;
        $advLedger->total_amount = 0;
        $advLedger->due_amount = 0;
        $advLedger->invoice_type = 'Advance Deduct';
        $advLedger->is_paid = 1;
        $advLedger->invoice_no = $ledger->invoice_no;
        $advLedger->date = now();
        $advLedger->created_by = auth('admin')->user()->id;
        $advLedger->save();

        // Create Advance Deduct payment (linked to ledger)
        SupplierPayment::create([
            'supplier_id' => $supplierId,
            'account_id' => $account->id,
            'payment_type' => 'advance_deduct',
            'is_paid' => 1,
            'amount' => $actualOffset,
            'payment_date' => now(),
            'note' => 'Auto offset due with advance',
            'created_by' => auth('admin')->user()->id,
            'invoice' => $ledger->invoice_no,
            'ledger_id' => $advLedger->id,
        ]);

        return $actualOffset;
    }

    public function bulkImport(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new SuppliersImport, $file);
    }
}
