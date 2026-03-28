<?php

namespace Modules\StockAdjustment\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\Request;
use Modules\Product\app\Models\Product;
use Modules\StockAdjustment\app\Http\Requests\StockAdjustmentRequest;
use Modules\StockAdjustment\app\Services\StockAdjustmentService;

class StockAdjustmentController extends Controller
{
    use RedirectHelperTrait;

    public function __construct(private StockAdjustmentService $service) {}

    /**
     * Display a listing of stock adjustments.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('stock.adjustment.view');

        $query = $this->service->all();

        // Calculate totals
        $data = [];
        $data['totalQuantity'] = 0;
        $data['totalLoss'] = 0;
        foreach ($query->get() as $item) {
            $data['totalQuantity'] += $item->quantity;
            $data['totalLoss'] += $item->total_loss;
        }

        // PDF Export
        if (checkAdminHasPermission('stock.adjustment.view')) {
            if (request('export_pdf')) {
                $lists = $query->get();
                return view('stockadjustment::pdf.index', compact('lists', 'data'));
            }
        }

        // Pagination
        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $lists = $query->get();
            } else {
                $lists = $query->paginate(request('par-page'));
                $lists->appends(request()->query());
            }
        } else {
            $lists = $query->paginate(20);
            $lists->appends(request()->query());
        }

        return view('stockadjustment::index', compact('lists', 'data'));
    }

    /**
     * Show the form for creating a new stock adjustment.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('stock.adjustment.create');

        $products = Product::where('status', 1)->get();
        $productsData = [];
        foreach ($products as $p) {
            $productsData[$p->id] = [
                'id' => $p->id,
                'stock' => (int) \Illuminate\Support\Facades\DB::table('products')->where('id', $p->id)->value('stock'),
                'cost' => (float) $p->cost,
            ];
        }
        return view('stockadjustment::create', compact('products', 'productsData'));
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function store(StockAdjustmentRequest $request)
    {
        checkAdminHasPermissionAndThrowException('stock.adjustment.create');

        try {
            $this->service->store($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.stock-adjustment.index', [], ['messege' => 'Stock adjustment created successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Display the specified stock adjustment.
     */
    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('stock.adjustment.view');

        $adjustment = $this->service->find($id);

        // Get current product stock (raw value)
        $currentStock = (int) \Illuminate\Support\Facades\DB::table('products')->where('id', $adjustment->product_id)->value('stock');

        // Reason-wise summary for this product
        $reasonSummary = \Modules\StockAdjustment\app\Models\StockAdjustment::where('product_id', $adjustment->product_id)
            ->selectRaw('reason, SUM(quantity) as total_qty, SUM(total_loss) as total_loss')
            ->groupBy('reason')
            ->get();

        return view('stockadjustment::show', compact('adjustment', 'currentStock', 'reasonSummary'));
    }

    /**
     * Remove the specified stock adjustment.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('stock.adjustment.delete');

        try {
            $this->service->destroy($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, '', [], ['messege' => 'Stock adjustment deleted successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Search products for AJAX select.
     */
    public function productSearch(Request $request)
    {
        $keyword = $request->keyword;

        if (empty($keyword)) {
            return response()->json([
                'status' => false,
                'message' => 'Keyword is required',
            ]);
        }

        $products = Product::where('status', 1)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%')
                    ->orWhere('barcode', 'like', '%' . $keyword . '%');
            })
            ->limit(20)
            ->get()
            ->map(function ($product) {
                $rawStock = (int) \Illuminate\Support\Facades\DB::table('products')->where('id', $product->id)->value('stock');
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'stock' => $rawStock,
                    'cost' => $product->cost,
                ];
            });

        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $products,
        ]);
    }
}
