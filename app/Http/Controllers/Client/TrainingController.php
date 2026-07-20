<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingController extends Controller
{
    public function index(Request $request): View
    {
        $client = $request->user()->client;

        if (! $client) {
            return view('client.training.index', ['products' => collect(), 'client' => null]);
        }

        $products = Product::with('training')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $client->load('trainingProgress');

        return view('client.training.index', ['products' => $products, 'client' => $client]);
    }
}
