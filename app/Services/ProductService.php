<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPolicy;
use App\Models\Project;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public const DEFAULT_PLAN_NAME = 'Yearly';
    public const DEFAULT_PLAN_DURATION_MONTHS = 12;
    public const DEFAULT_PLAN_AMOUNT = 90000;

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

        $product->subscriptionPlans()->create([
            'name' => self::DEFAULT_PLAN_NAME,
            'duration_months' => self::DEFAULT_PLAN_DURATION_MONTHS,
            'amount' => self::DEFAULT_PLAN_AMOUNT,
        ]);

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
