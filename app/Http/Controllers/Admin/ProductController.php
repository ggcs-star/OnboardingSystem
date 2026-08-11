<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function index(): View
    {
        $products = Product::withCount('training', 'faqs', 'documentFields')
            ->latest()
            ->get();

        return view('admin.products.index', [
            'products' => $products,
            'stats' => $this->productService->getDashboardStats(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->createProduct(
            $request->safe()->except('image'),
            $request->file('image')
        );

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields'])
            ->with('success', 'Product created successfully.');
    }

    public function show(Request $request, Product $product): View
    {
        $tabs = [
            'product-details',
            'document-fields',
            'policies',
            'faqs',
            'renewal-settings',
        ];

        $activeTab = $request->query('tab', 'product-details');
        if (! in_array($activeTab, $tabs, true)) {
            $activeTab = 'product-details';
        }

        $product->loadCount('training', 'faqs', 'documentFields');
        $product->load(['documentGroups', 'faqs', 'policies', 'renewalSetting', 'subscriptionPlans']);

        return view('admin.products.show', [
            'product' => $product,
            'activeTab' => $activeTab,
            'projectsCount' => $product->projectsCount(),
            'clientsCount' => $product->clientsCount(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct(
            $product,
            $request->safe()->except('image'),
            $request->file('image')
        );

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'product-details'])
            ->with('success', 'Product details updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
