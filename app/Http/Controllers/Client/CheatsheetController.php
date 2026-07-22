<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCheatsheet;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CheatsheetController extends Controller
{
    public function index(): View
    {
        $products = Product::with('cheatsheets')
            ->where('active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn (Product $product) => $product->cheatsheets->isNotEmpty())
            ->values();

        return view('client.cheatsheets.index', ['products' => $products]);
    }

    public function download(ProductCheatsheet $cheatsheet): StreamedResponse
    {
        return Storage::disk('public')->download(
            $cheatsheet->file,
            $cheatsheet->title . '.' . $cheatsheet->fileExtension()
        );
    }
}
