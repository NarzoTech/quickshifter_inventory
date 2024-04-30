<?php

namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;

use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Http\Requests\ProductCategoryRequest;
use Modules\Product\app\Services\ProductCategoryService;

class ProductCategoryController extends Controller
{
    use RedirectHelperTrait;
    protected $category;
    public function __construct(ProductCategoryService $category)
    {
        $this->category = $category;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->category->getAllProductCategories();
        return view('product::products.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = $this->category->getAllProductCategoriesForSelect();
        return view('product::products.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request)
    {
        DB::beginTransaction();
        try {
            $category = $this->category->storeProductCategory($request);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.category.index', ['category' => $category->id, 'code' => getSessionLanguage()]);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cat = $this->category->getProductCategory($id);
        $categories = $this->category->getAllProductCategoriesForSelect();
        return view('product::products.category.edit', compact('categories', 'cat'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $this->category->updateProductCategory($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.category.index');

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = $this->category->deleteProductCategory($id);
            if (!$category) {
                return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index', [], [
                    'messege' => 'Category has products',
                    'alert-type' => RedirectType::ERROR->value,
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.category.index');
            }

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.category.index');
        }
    }
}
