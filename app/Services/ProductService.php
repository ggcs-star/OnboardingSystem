<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPolicy;
use App\Models\Project;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function __construct(private FileUploadService $fileUploadService)
    {
    }

    public function createProduct(array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $data['image'] = $this->fileUploadService->store($image, 'products');
        }

        $product = Product::create($data);

        foreach (ProductPolicy::TYPES as $type => $title) {
            $product->policies()->create([
                'type' => $type,
                'title' => $title,
                'content' => null,
            ]);
        }

        return $product;
    }

    public function updateProduct(Product $product, array $data, ?UploadedFile $image = null): Product
    {
        if ($image) {
            $this->fileUploadService->delete($product->image);
            $data['image'] = $this->fileUploadService->store($image, 'products');
        }

        $product->update($data);

        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $this->fileUploadService->delete($product->image);
        $product->delete();
    }

    public function getDashboardStats(): array
    {
        return [
            'total_products' => Product::count(),
            'active_products' => Product::where('active', true)->count(),
            'total_projects' => Project::count(),
            'total_clients' => Project::distinct('client_id')->count('client_id'),
        ];
    }
}
