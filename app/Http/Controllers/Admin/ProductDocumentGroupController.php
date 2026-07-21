<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDocumentGroupRequest;
use App\Models\Product;
use App\Models\ProductDocumentGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductDocumentGroupController extends Controller
{
    public function store(StoreDocumentGroupRequest $request, Product $product): RedirectResponse
    {
        $baseSlug = Str::slug($request->string('name'), '_');
        $slug = $baseSlug;
        $suffix = 1;

        while ($product->documentGroups()->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '_' . $suffix;
        }

        $product->documentGroups()->create([
            'name' => $request->string('name'),
            'slug' => $slug,
            'is_mandatory' => $request->boolean('is_mandatory'),
        ]);

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields'])
            ->with('success', 'Document group added.');
    }

    public function destroy(Product $product, ProductDocumentGroup $documentGroup): RedirectResponse
    {
        abort_unless($documentGroup->product_id === $product->id, 404);

        $documentGroup->delete();

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields'])
            ->with('success', 'Document group removed.');
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->input('order') as $index => $groupId) {
            ProductDocumentGroup::where('id', $groupId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
