<?php

namespace App\Services\Catalog;

use App\Contracts\Catalog\StorefrontCatalogProvider;

final class JsonStorefrontCatalogProvider implements StorefrontCatalogProvider
{
    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    public function catalog(): array
    {
        $empty = ['categories' => [], 'products' => []];
        $path = public_path(JsonCatalogImporter::JSON_PATH);
        if (! is_file($path)) {
            return $empty;
        }

        $catalog = json_decode((string) file_get_contents($path), true);
        if (! is_array($catalog)) {
            return $empty;
        }

        $categories = $catalog['categories'] ?? null;
        $products = $catalog['products'] ?? null;
        if (! is_array($categories) || ! is_array($products)) {
            return $empty;
        }

        return [
            'categories' => array_values($categories),
            'products' => array_values($products),
        ];
    }

    public function findProduct(string $slug): ?array
    {
        foreach ($this->catalog()['products'] as $product) {
            if (! is_array($product) || ($product['id'] ?? null) !== $slug) {
                continue;
            }

            if (! is_string($product['name'] ?? null) || $product['name'] === '') {
                return null;
            }

            return $product;
        }

        return null;
    }
}
