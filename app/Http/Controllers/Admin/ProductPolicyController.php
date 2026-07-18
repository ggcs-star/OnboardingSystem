<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePolicyRequest;
use App\Models\Product;
use App\Models\ProductPolicy;
use Illuminate\Http\RedirectResponse;

class ProductPolicyController extends Controller
{
    public function update(UpdatePolicyRequest $request, Product $product, ProductPolicy $policy): RedirectResponse
    {
        abort_unless($policy->product_id === $product->id, 404);

        $policy->update($request->validated());

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'policies'])
            ->with('success', 'Policy updated.');
    }
}
