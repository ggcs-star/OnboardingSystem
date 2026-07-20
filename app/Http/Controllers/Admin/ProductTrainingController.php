<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTrainingVideoRequest;
use App\Models\Product;
use App\Models\ProductTraining;
use Illuminate\Http\RedirectResponse;

class ProductTrainingController extends Controller
{
    public function store(StoreTrainingVideoRequest $request, Product $product): RedirectResponse
    {
        $product->training()->create($request->validated());

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'training-videos'])
            ->with('success', 'Training video added.');
    }

    public function destroy(Product $product, ProductTraining $training): RedirectResponse
    {
        abort_unless($training->product_id === $product->id, 404);

        $training->delete();

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'training-videos'])
            ->with('success', 'Training video removed.');
    }
}
