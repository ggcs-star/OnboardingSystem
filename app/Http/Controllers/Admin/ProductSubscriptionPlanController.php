<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionPlanRequest;
use App\Http\Requests\Admin\UpdateSubscriptionPlanRequest;
use App\Models\Product;
use App\Models\ProductSubscriptionPlan;
use Illuminate\Http\RedirectResponse;

class ProductSubscriptionPlanController extends Controller
{
    public function store(StoreSubscriptionPlanRequest $request, Product $product): RedirectResponse
    {
        $product->subscriptionPlans()->create($request->validated());

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'renewal-settings'])
            ->with('success', 'Subscription plan added.');
    }

    public function update(UpdateSubscriptionPlanRequest $request, Product $product, ProductSubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        abort_unless($subscriptionPlan->product_id === $product->id, 404);

        $subscriptionPlan->update($request->validated());

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'renewal-settings'])
            ->with('success', 'Subscription plan updated.');
    }

    public function destroy(Product $product, ProductSubscriptionPlan $subscriptionPlan): RedirectResponse
    {
        abort_unless($subscriptionPlan->product_id === $product->id, 404);

        $subscriptionPlan->delete();

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'renewal-settings'])
            ->with('success', 'Subscription plan removed.');
    }
}
