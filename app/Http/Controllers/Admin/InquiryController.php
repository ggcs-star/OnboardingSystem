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

        // Agar approve hua to client ko product assign kar do
        if ($request->status === 'approved') {

            ClientProduct::firstOrCreate(
                [
                    'client_id'  => $inquiry->client_id,
                    'product_id' => $inquiry->product_id,
                ],
                [
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status'      => true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }
}