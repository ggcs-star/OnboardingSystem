<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRenewalSettingRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

class ProductRenewalSettingController extends Controller
{
    public function update(UpdateRenewalSettingRequest $request, Product $product): RedirectResponse
    {
        $product->renewalSetting()->updateOrCreate(
            ['product_id' => $product->id],
            [
                ...$request->validated(),
                'auto_renew_reminder' => $request->boolean('auto_renew_reminder'),
            ]
        );

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'renewal-settings'])
            ->with('success', 'Renewal settings updated.');
    }
}
