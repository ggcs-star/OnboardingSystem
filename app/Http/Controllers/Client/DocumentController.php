<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $documents = Document::published()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('client.documents.index', [
            'documents' => $documents,
        ]);
    }
}
