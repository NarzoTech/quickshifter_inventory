<?php

namespace Modules\Product\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Product\app\Http\Requests\ProductRequest;
use Modules\Product\app\Services\AttributeService;
use Modules\Product\app\Services\BrandService;
use Modules\Product\app\Services\ProductCategoryService;
use Modules\Product\app\Services\ProductService;
use Modules\Product\app\Services\UnitTypeService;

class ProductController extends Controller
{
    use RedirectHelperTrait;
    private ProductService $productService;
    private ProductCategoryService $categoryService;
    private AttributeService $attributeService;
    private BrandService $brandService;
    public function __construct(ProductService $productService, ProductCategoryService $categoryService, AttributeService $attributeService, BrandService $brandService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->attributeService = $attributeService;
        $this->brandService = $brandService;
        $this->middleware('auth:admin');

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = $this->productService->getProducts()->paginate(20);
            return view('product::products.index', compact('products'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(UnitTypeService $unitService)
    {
        $categories = $this->categoryService->getAllProductCategoriesForSelect();
        $brands = $this->brandService->getActiveBrands();
        $units = $unitService->getActiveAll();
        return view('product::products.create', compact('categories', 'brands','units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $product = $this->productService->storeProduct($request);
            DB::commit();
            if ($product->id) {
                return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.product.create', [], [
                    'messege' => 'Product created successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.product.create', [], [
                    'messege' => 'Product creation failed',
                    'alert-type' => 'error',
                ]);
            }

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id,UnitTypeService $unitService)
    {
        try {
            $product = $this->productService->getProduct($id);
            $productCategories = $this->categoryService->getCategoriesIdsByProductId($id);

            $categories = $this->categoryService->getAllProductCategoriesForSelect();
            $brands = $this->brandService->getActiveBrands();
            $units = $unitService->getActiveAll();
            return view('product::products.edit', compact('categories', 'brands', 'product', 'productCategories','units'));

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            abort(500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $product = $this->productService->updateProduct($request, $product);
            DB::commit();
            if ($product->id) {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product.index', [], [
                    'messege' => 'Product updated successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product.index', [], [
                    'messege' => 'Product update failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $product = $this->productService->deleteProduct($product);
            if ($product) {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product.index', [], [
                    'messege' => 'Product deleted successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product.index', [], [
                    'messege' => 'Product deletion failed. Product has orders',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     *
     * related product view
     */
    public function related_product(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $relatedProducts = $this->productService->getRelatedProducts($product);
            $products = $this->productService->getProducts()->whereNot('id', $product->id)->paginate(20);
            return view('product::products.related_product', compact('product', 'relatedProducts', 'products'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     *
     * related product store
     */

    public function related_product_store(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $relatedProducts = $this->productService->storeRelatedProducts($request, $product);
            DB::commit();
            if ($relatedProducts) {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.related-products', [$product->id], [
                    'messege' => 'Related Products updated successfully',
                    'alert-type' => 'success',
                ]);
            } else {
                return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.related-products', [$product->id], [
                    'messege' => 'Related Products update failed',
                    'alert-type' => 'error',
                ]);
            }
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $variants = $this->productService->getProductVariants($product);
            return view('product::products.product_variant', compact('product', 'variants'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_create(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            return view('product::products.product_variant_create', compact('product', 'attributes'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_store(Request $request, string $id)
    {

        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->storeProductVariant($request, $product);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.product-variant', [$product->id], [
                'messege' => 'Product Variant created successfully',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_edit(string $variant_id)
    {
        try {
            $variant = $this->productService->getProductVariant($variant_id);
            if (!$variant) {
                return back()->with([
                    'messege' => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $attributes = $this->attributeService->getAllAttributesForSelect();
            $product = $variant->product;
            return view('product::products.product_variant_edit', compact('variant', 'attributes', 'product'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_update(Request $request, string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->productService->getProductVariant($variant_id);
            if (!$variant) {
                return back()->with([
                    'messege' => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->updateProductVariant($request, $variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product-variant', [$variant->product->id], [
                'messege' => 'Product Variant updated successfully',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_variant_delete(string $variant_id)
    {
        try {
            DB::beginTransaction();
            $variant = $this->productService->getProductVariant($variant_id);
            if (!$variant) {
                return back()->with([
                    'messege' => 'Product Variant not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->deleteProductVariant($variant);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.product-variant', [$variant->product->id], [
                'messege' => 'Product Variant deleted successfully',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    // bulk product import
    public function bulkImport()
    {
        return view('product::products.import');
    }

    // store bulk product
    public function bulkImportStore(Request $request)
    {
        

        
        try {
            $this->productService->bulkImport($request);
            return back()->with([
                'messege' => 'Products imported successfully',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_gallery(string $id)
    {
        try {
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            return view('product::products.gallery', compact('product'));
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }

    public function product_gallery_store(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $product = $this->productService->getProduct($id);
            if (!$product) {
                return back()->with([
                    'messege' => 'Product not found',
                    'alert-type' => 'error',
                ]);
            }
            $this->productService->storeProductGallery($request, $product);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.product-gallery', [$product->id], [
                'messege' => 'Product Gallery updated successfully',
                'alert-type' => 'success',
            ]);

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return back()->with([
                'messege' => 'Something Went Wrong',
                'alert-type' => 'error',
            ]);
        }
    }
}
