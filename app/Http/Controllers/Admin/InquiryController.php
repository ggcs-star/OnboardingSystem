<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductInquiry;
use App\Models\ClientProduct;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $inquiry = ProductInquiry::findOrFail($id);

        $inquiry->status = $request->status;
        $inquiry->save();

        // Agar approve hua to client ko product assign kar do — agar pehle se
        // assigned hai to ek aur brand slot de do (dusri baar interest dikhaya).
        if ($request->status === 'approved') {

            $clientProduct = ClientProduct::where('client_id', $inquiry->client_id)
                ->where('product_id', $inquiry->product_id)
                ->first();

            if ($clientProduct) {
                $clientProduct->increment('brand_slots');
            } else {
                ClientProduct::create([
                    'client_id'   => $inquiry->client_id,
                    'product_id'  => $inquiry->product_id,
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status'      => true,
                    'brand_slots' => 1,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }
}