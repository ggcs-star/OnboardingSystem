<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTrainingVideoRequest;
use App\Models\Product;
use App\Models\ProductTraining;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->input('order') as $index => $trainingId) {
            ProductTraining::where('id', $trainingId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
