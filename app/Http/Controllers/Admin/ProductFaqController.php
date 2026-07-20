<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFaqRequest;
use App\Models\Product;
use App\Models\ProductFaq;
use Illuminate\Http\RedirectResponse;

class ProductFaqController extends Controller
{
    public function store(StoreFaqRequest $request, Product $product): RedirectResponse
    {
        $product->faqs()->create($request->validated());

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'faqs'])
            ->with('success', 'FAQ added.');
    }

    public function destroy(Product $product, ProductFaq $faq): RedirectResponse
    {
        abort_unless($faq->product_id === $product->id, 404);

        $faq->delete();

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'faqs'])
            ->with('success', 'FAQ removed.');
    }
}
