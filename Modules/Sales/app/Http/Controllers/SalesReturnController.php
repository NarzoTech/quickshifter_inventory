<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Sales\app\Models\SalesReturnDetails;

class SalesReturnController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private AccountsService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function returnList()
    {
        $lists = SalesReturn::orderBy('id', 'desc')->paginate(20);

        return view('sales::return.index', compact('lists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $sale = Sale::find($id);
        $accounts = $this->service->all()->get();
        return view('sales::return.create', compact('sale', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validation

        $request->validate([
            'sale_id' => 'required',
            'order_date' => 'required',
            'return_date' => 'required',
            'return_amount' => 'required',
            'paying_amount' => 'required',
            'payment_type' => 'required',
            'return_subtotal' => 'required|array',
            'return_subtotal.*' => 'required',
            'return_quantity' => 'required|array',
            'return_quantity.*' => 'required',
            'price' => 'required|array',
            'price.*' => 'required',
        ]);

        DB::beginTransaction();
        // create a new sale return
        try {

            $due = $request->return_amount - $request->paying_amount;
            $return = SalesReturn::create([
                'sale_id' => $request->sale_id,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'return_date' => date($request->return_date),
                'return_amount' => $request->return_amount,
                'return_due' => $due > 0 ? $due : 0,
                'note'  => $request->note,
                'status' => 1,
            ]);

            // create a return details

            foreach ($request->product_id as $key => $prod_id) {
                $details = SalesReturnDetails::create(
                    [
                        'sale_return_id' => $return->id,
                        'product_id' => $prod_id,
                        'quantity' => $request->return_quantity[$key],
                        'price' => $request->price[$key],
                        'sub_total' => $request->return_subtotal[$key],
                    ]
                );


                // update stock
                $stock = $details->product->stock;
                $stock = $stock + $request->return_quantity[$key];
                $details->product->update([
                    'stock' => $stock
                ]);
            }


            if ($request->paying_amount) {
                // create a payment
                $account = Account::where('account_type', $request->payment_type);
                if ($request->payment_type == 'cash') {
                    $account = $account->first();
                } else {
                    $account = $account->where('id', $request->account_id)->first();
                }
                $data = [
                    'payment_type' => 'sale return',
                    'sale_return_id' => $return->id,
                    'is_paid' => 1,
                    'account_id' => $account->id,
                    'amount' => $request->paying_amount,
                    'payment_date' => now(),
                    'created_by' => auth('admin')->user()->id,
                ];
                Payment::create($data);
            }


            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.sales.index', [], ['messege' => 'Sales return created successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('sales::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('sales::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
