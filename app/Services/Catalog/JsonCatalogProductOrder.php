<?php

namespace App\Services\Catalog;

final class JsonCatalogProductOrder
{
    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        $path = public_path(JsonCatalogImporter::JSON_PATH);
        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (! is_array($decoded) || ! is_array($decoded['products'] ?? null)) {
            return [];
        }

        $slugs = [];
        foreach ($decoded['products'] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $id = $row['id'] ?? null;
            if (is_string($id) && $id !== '') {
                $slugs[] = $id;
            }
        }

        return $slugs;
    }

    /**
     * @param  iterable<\App\Models\Product>  $products
     * @return list<\App\Models\Product>
     */
    public static function sortProducts(iterable $products): array
    {
        $order = array_flip(self::slugs());
        $sorted = [];

        foreach ($products as $product) {
            $sorted[] = $product;
        }

        usort($sorted, function ($left, $right) use ($order): int {
            $leftIndex = $order[$left->slug] ?? PHP_INT_MAX;
            $rightIndex = $order[$right->slug] ?? PHP_INT_MAX;

            if ($leftIndex === $rightIndex) {
                return strcmp($left->slug, $right->slug);
            }

            return $leftIndex <=> $rightIndex;
        });

        return $sorted;
    }
}
