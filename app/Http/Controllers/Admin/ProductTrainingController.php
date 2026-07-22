<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTrainingVideoRequest;
use App\Models\Product;
use App\Models\ProductTraining;
use Illuminate\Http\RedirectResponse;

class ProductTrainingController extends Controller
{
    public function store(StoreTrainingVideoRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));

        $product->training()->create($request->safe()->except('product_id'));

        return redirect()
            ->route('admin.training.index')
            ->with('success', 'Training video added.');
    }

    public function destroy(ProductTraining $training): RedirectResponse
    {
        $training->delete();

        return redirect()
            ->route('admin.training.index')
            ->with('success', 'Training video removed.');
    }
}
