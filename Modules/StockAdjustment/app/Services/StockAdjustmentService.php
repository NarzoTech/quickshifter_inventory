<?php

namespace Modules\StockAdjustment\app\Services;

use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseType;
use Modules\Product\app\Models\Product;
use Modules\StockAdjustment\app\Models\StockAdjustment;

class StockAdjustmentService
{
    public function all()
    {
        $query = StockAdjustment::with(['product', 'createdBy']);

        if (request('keyword')) {
            $keyword = request('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice', 'like', '%' . $keyword . '%')
                    ->orWhereHas('product', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if (request('reason')) {
            $query->where('reason', request('reason'));
        }

        if (request('product_id')) {
            $query->where('product_id', request('product_id'));
        }

        if (request('from_date') && request('to_date')) {
            $query->whereBetween('date', [
                now()->parse(request('from_date')),
                now()->parse(request('to_date')),
            ]);
        }

        $orderBy = request('order_by', 'desc');
        $orderType = request('order_type', 'id');

        return $query->orderBy($orderType, $orderBy);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->product_id);
            $qty = (int) $request->quantity;

            // Get raw stock value (bypass number_format accessor)
            $currentStock = (int) DB::table('products')->where('id', $product->id)->value('stock');
            if ($qty > $currentStock) {
                throw new Exception("Insufficient stock ({$currentStock}) to adjust {$qty} units.");
            }

            // Get unit cost (last purchase price or product cost)
            $unitCost = (float) $product->cost;
            $totalLoss = $qty * $unitCost;

            // Generate invoice number
            $invoice = generateInvoiceNumber(StockAdjustment::class, 'invoice', 'SA', [], $request->date);

            // Reduce product stock
            Product::where('id', $product->id)->update([
                'stock' => DB::raw("CASE WHEN stock >= {$qty} THEN stock - {$qty} ELSE 0 END"),
                'stock_status' => DB::raw("CASE WHEN stock - {$qty} <= 0 THEN 'out_of_stock' ELSE 'in_stock' END"),
            ]);

            // Create stock record
            $stockRecord = Stock::create([
                'product_id' => $product->id,
                'date' => Carbon::createFromFormat('d-m-Y', $request->date),
                'type' => 'Stock Adjustment',
                'out_quantity' => $qty,
                'rate' => $unitCost,
                'sku' => $product->sku,
                'created_by' => auth('admin')->user()->id,
            ]);

            // Find or create "Inventory Loss" expense type
            $expenseType = ExpenseType::firstOrCreate(
                ['name' => 'Inventory Loss', 'parent_id' => null]
            );

            // Create expense record (non-cash write-off)
            $reason = ucfirst($request->reason);
            $expense = Expense::create([
                'invoice' => generateInvoiceNumber(Expense::class, 'invoice', 'EXP', [], $request->date),
                'date' => Carbon::createFromFormat('d-m-Y', $request->date),
                'amount' => $totalLoss,
                'paid_amount' => $totalLoss,
                'due_amount' => 0,
                'expense_type_id' => $expenseType->id,
                'payment_type' => 'cash',
                'account_id' => null,
                'note' => "Stock Adjustment [{$invoice}]: {$qty} x {$product->name} - Reason: {$reason}",
                'created_by' => auth('admin')->id(),
            ]);

            // Create stock adjustment record
            $adjustment = StockAdjustment::create([
                'invoice' => $invoice,
                'product_id' => $product->id,
                'quantity' => $qty,
                'reason' => $request->reason,
                'date' => Carbon::createFromFormat('d-m-Y', $request->date),
                'note' => $request->note,
                'unit_cost' => $unitCost,
                'total_loss' => $totalLoss,
                'expense_id' => $expense->id,
                'created_by' => auth('admin')->id(),
            ]);

            // Link stock record to adjustment
            $stockRecord->update(['stock_adjustment_id' => $adjustment->id]);

            DB::commit();
            return $adjustment;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            throw $ex;
        }
    }

    public function find($id)
    {
        return StockAdjustment::with(['product', 'expense', 'stockRecord', 'createdBy'])->findOrFail($id);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::findOrFail($id);
            $qty = (int) $adjustment->quantity;

            // Restore product stock
            Product::where('id', $adjustment->product_id)->update([
                'stock' => DB::raw("stock + {$qty}"),
                'stock_status' => DB::raw("CASE WHEN stock + {$qty} > 0 THEN 'in_stock' ELSE 'out_of_stock' END"),
            ]);

            // Delete linked stock record
            Stock::where('stock_adjustment_id', $adjustment->id)->delete();

            // Delete linked expense record
            if ($adjustment->expense_id) {
                Expense::where('id', $adjustment->expense_id)->delete();
            }

            // Delete the adjustment
            $adjustment->delete();

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            throw $ex;
        }
    }
}
