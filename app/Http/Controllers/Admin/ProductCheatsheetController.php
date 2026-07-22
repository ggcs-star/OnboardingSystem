<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCheatsheetRequest;
use App\Models\Product;
use App\Models\ProductCheatsheet;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductCheatsheetController extends Controller
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function store(StoreCheatsheetRequest $request): RedirectResponse
    {
        $product = Product::findOrFail($request->validated('product_id'));

        $product->cheatsheets()->create([
            'title' => $request->validated('title'),
            'description' => $request->validated('description'),
            'file' => $this->fileUploadService->store($request->file('file'), 'cheatsheets'),
        ]);

        return redirect()
            ->route('admin.cheatsheets.index')
            ->with('success', 'Cheatsheet added.');
    }

    public function destroy(ProductCheatsheet $cheatsheet): RedirectResponse
    {
        $this->fileUploadService->delete($cheatsheet->file);
        $cheatsheet->delete();

        return redirect()
            ->route('admin.cheatsheets.index')
            ->with('success', 'Cheatsheet removed.');
    }

    public function download(ProductCheatsheet $cheatsheet): StreamedResponse
    {
        return Storage::disk('public')->download(
            $cheatsheet->file,
            $cheatsheet->title . '.' . $cheatsheet->fileExtension()
        );
    }

    public function reorder(Request $request, Product $product): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->input('order') as $index => $cheatsheetId) {
            ProductCheatsheet::where('id', $cheatsheetId)
                ->where('product_id', $product->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
