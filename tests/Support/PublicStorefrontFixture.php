<?php

namespace Tests\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;

trait PublicStorefrontFixture
{
    private function createStorefrontCategory(string $slug, string $name): Category
    {
        return Category::query()->create([
            'slug' => $slug,
            'name' => $name,
        ]);
    }

    private function createStorefrontProductWithVariants(Category $category, string $slug = 'aurum-pro-x1'): Product
    {
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => $slug,
            'name' => 'Aurum Pro X1',
            'model' => 'APX-1100',
            'description' => 'Professional pulse-induction gold detector.',
            'long_description' => 'Long description text.',
            'compatibility' => 'Compatible with Aurum-series coils.',
            'base_price' => '4280.00',
            'badge' => 'New',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/listing.jpg',
            'gallery_order' => null,
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/gallery-1.jpg',
            'gallery_order' => 0,
            'is_listing' => false,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);

        $option = ProductOption::query()->create([
            'product_id' => $product->id,
            'name' => 'Coil Size',
            'sort_order' => 0,
            'catalog_managed' => true,
        ]);
        ProductOptionValue::query()->create([
            'product_option_id' => $option->id,
            'label' => '11-inch',
            'additional_price' => '0.00',
            'sort_order' => 0,
            'catalog_managed' => true,
            'price_locked' => false,
        ]);

        return $product;
    }

    private function createStorefrontProductWithoutVariants(Category $category): Product
    {
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'aurum-battery-pack',
            'name' => 'Aurum Battery Pack',
            'model' => 'ABP-900',
            'description' => 'Replacement battery pack.',
            'base_price' => '120.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/battery.jpg',
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);

        return $product;
    }
}
