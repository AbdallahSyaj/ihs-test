<?php

namespace App\Services\Catalog;

use App\Contracts\Catalog\StorefrontCatalogProvider;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

final class DatabaseStorefrontCatalogProvider implements StorefrontCatalogProvider
{
    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    public function catalog(): array
    {
        if (! $this->catalogTablesReady()) {
            return ['categories' => [], 'products' => []];
        }

        $categories = $this->loadCategories();
        $products = $this->loadProducts();

        return [
            'categories' => $categories,
            'products' => $products,
        ];
    }

    public function findProduct(string $slug): ?array
    {
        if (! $this->catalogTablesReady()) {
            return null;
        }

        $product = Product::query()
            ->where('slug', $slug)
            ->where('status', Product::STATUS_ACTIVE)
            ->whereHas('category')
            ->with($this->productRelations())
            ->first();

        if ($product === null || $product->name === '') {
            return null;
        }

        return StorefrontCatalogArrayMapper::productToArray($product);
    }

    private function catalogTablesReady(): bool
    {
        return Schema::hasTable('categories')
            && Schema::hasTable('products')
            && Schema::hasColumn('categories', 'slug');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadCategories(): array
    {
        $categories = [];
        foreach (Category::query()->orderBy('name')->get() as $category) {
            $categories[] = StorefrontCatalogArrayMapper::categoryToArray($category);
        }

        return $categories;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function loadProducts(): array
    {
        $products = [];
        $models = Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->whereHas('category')
            ->with($this->productRelations())
            ->orderBy('id')
            ->get();

        foreach ($models as $product) {
            $products[] = StorefrontCatalogArrayMapper::productToArray($product);
        }

        return $products;
    }

    /**
     * @return array<string, mixed>
     */
    private function productRelations(): array
    {
        return [
            'category',
            'images',
            'specs' => fn ($query) => $query->orderBy('sort_order'),
            'accessories' => fn ($query) => $query->orderBy('sort_order'),
            'options' => fn ($query) => $query->orderBy('sort_order'),
            'options.values' => fn ($query) => $query->orderBy('sort_order'),
        ];
    }
}
