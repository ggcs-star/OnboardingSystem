<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInquiry extends Model
{
    protected $fillable = [

        'client_id',

        'product_id',

        'contact_person',

        'email',

        'phone',

        'company',

        'message',

        'status',

    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
