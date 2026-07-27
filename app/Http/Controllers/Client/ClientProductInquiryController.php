<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductInquiry;
class ClientProductInquiryController extends Controller
{
   public function store(Request $request)
{
    $request->validate([

        'product_id'=>'required|exists:products,id',

        'contact_person'=>'required',

        'email'=>'required|email',

        'phone'=>'nullable',

        'company'=>'nullable',

        'message'=>'nullable',

    ]);

    ProductInquiry::create([

        'client_id'=>auth()->user()->client->id,

        'product_id'=>$request->product_id,

        'contact_person'=>$request->contact_person,

        'email'=>$request->email,

        'phone'=>$request->phone,

        'company'=>$request->company,

        'message'=>$request->message,

    ]);

    return back()->with('success','Inquiry submitted successfully.');
}
}
