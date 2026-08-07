<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductInquiry;
class ProductInquiryController extends Controller
{
       public function index(Request $request)
    {
        $query = ProductInquiry::with([
            'client.user',
            'product',
        ])->latest();

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");

            });
        }

        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }

        $inquiries = $query->paginate(10);

        $products = Product::orderBy('name')->get();

        return view(
            'admin.product-inquiries.index',
            compact('inquiries', 'products')
        );
    }
}
