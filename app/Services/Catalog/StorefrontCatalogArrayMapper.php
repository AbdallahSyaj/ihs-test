<?php

namespace App\Services\Catalog;

use App\Models\Category;
use App\Models\Product;

final class StorefrontCatalogArrayMapper
{
    /**
     * @return array{id: string, label: string}
     */
    public static function categoryToArray(Category $category): array
    {
        return [
            'id' => (string) $category->slug,
            'label' => (string) $category->name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function productToArray(Product $product): array
    {
        $category = $product->category;
        $listingPath = self::listingImagePath($product);
        $gallery = self::galleryPaths($product, $listingPath);

        return [
            'id' => (string) $product->slug,
            'name' => (string) $product->name,
            'model' => (string) ($product->model ?? ''),
            'category' => (string) ($category?->slug ?? ''),
            'categoryLabel' => (string) ($category?->name ?? ''),
            'description' => (string) ($product->description ?? ''),
            'longDescription' => (string) ($product->long_description ?? ''),
            'price' => self::formatPrice($product->base_price),
            'badge' => filled($product->badge) ? (string) $product->badge : null,
            'image' => $listingPath,
            'gallery' => $gallery,
            'specs' => self::mapSpecs($product),
            'variants' => self::mapVariants($product),
            'accessories' => self::mapAccessories($product),
            'compatibility' => (string) ($product->compatibility ?? ''),
        ];
    }

    public static function formatPrice(mixed $basePrice): string
    {
        $amount = is_numeric($basePrice) ? (float) $basePrice : 0.0;

        return '$'.number_format($amount, 0, '.', ',');
    }

    public static function publicImageUrl(string $path): string
    {
        if ($path === '') {
            return '';
        }

        $normalized = strtolower($path);
        if (str_starts_with($normalized, 'http://') || str_starts_with($normalized, 'https://')) {
            return $path;
        }

        return asset($path);
    }

    private static function listingImagePath(Product $product): string
    {
        $listing = $product->images->firstWhere('is_listing', true);
        if ($listing === null) {
            return '';
        }

        return self::publicImageUrl((string) $listing->path);
    }

    /**
     * @return list<string>
     */
    private static function galleryPaths(Product $product, string $listingPath): array
    {
        $gallery = [];
        if ($listingPath !== '') {
            $gallery[] = $listingPath;
        }

        $galleryImages = $product->images
            ->where('is_listing', false)
            ->sortBy(fn ($image) => $image->gallery_order ?? PHP_INT_MAX)
            ->values();

        foreach ($galleryImages as $image) {
            $storedPath = (string) $image->path;
            if ($storedPath === '') {
                continue;
            }

            $publicUrl = self::publicImageUrl($storedPath);
            if ($publicUrl === '' || $publicUrl === $listingPath) {
                continue;
            }
            $gallery[] = $publicUrl;
        }

        return $gallery;
    }

    /**
     * @return list<array{label: string, value: string}>
     */
    private static function mapSpecs(Product $product): array
    {
        $rows = [];
        foreach ($product->specs->sortBy('sort_order')->values() as $spec) {
            $rows[] = [
                'label' => (string) $spec->label,
                'value' => (string) $spec->value,
            ];
        }

        return $rows;
    }

    /**
     * @return list<array{name: string, options: list<string>}>
     */
    private static function mapVariants(Product $product): array
    {
        $variants = [];
        foreach ($product->options->sortBy('sort_order')->values() as $option) {
            $options = [];
            foreach ($option->values->sortBy('sort_order')->values() as $value) {
                $options[] = (string) $value->label;
            }
            $variants[] = [
                'name' => (string) $option->name,
                'options' => $options,
            ];
        }

        return $variants;
    }

    /**
     * @return list<array{name: string, qty: string}>
     */
    private static function mapAccessories(Product $product): array
    {
        $rows = [];
        foreach ($product->accessories->sortBy('sort_order')->values() as $accessory) {
            $rows[] = [
                'name' => (string) $accessory->name,
                'qty' => (string) ($accessory->qty ?? ''),
            ];
        }

        return $rows;
    }
}
