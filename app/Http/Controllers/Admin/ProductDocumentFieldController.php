<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDocumentFieldRequest;
use App\Models\Product;
use App\Models\ProductDocumentField;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductDocumentFieldController extends Controller
{
    public function store(StoreDocumentFieldRequest $request, Product $product): RedirectResponse
    {
        $product->documentFields()->create([
            ...$request->validated(),
            'required' => $request->boolean('required', true),
            'field_key' => Str::slug($request->string('label'), '_'),
        ]);

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields'])
            ->with('success', 'Document field added.');
    }

    public function update(Request $request, Product $product, ProductDocumentField $documentField): RedirectResponse
    {
        abort_unless($documentField->product_id === $product->id, 404);

        $documentField->update(['required' => ! $documentField->required]);

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields']);
    }

    public function destroy(Product $product, ProductDocumentField $documentField): RedirectResponse
    {
        abort_unless($documentField->product_id === $product->id, 404);

        $documentField->delete();

        return redirect()
            ->route('admin.products.show', ['product' => $product, 'tab' => 'document-fields'])
            ->with('success', 'Document field removed.');
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->input('order') as $index => $fieldId) {
            ProductDocumentField::where('id', $fieldId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
