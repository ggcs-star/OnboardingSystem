<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class CheatsheetOverviewController extends Controller
{
    public function index(): View
    {
        $allProducts = Product::with('cheatsheets')
            ->orderBy('name')
            ->get();

        $products = $allProducts->filter(fn (Product $product) => $product->cheatsheets->isNotEmpty())->values();

        return view('admin.cheatsheets.index', ['products' => $products, 'allProducts' => $allProducts]);
    }
}
