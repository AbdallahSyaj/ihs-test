<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Catalog\StorefrontCatalogArrayMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontCatalogArrayMapperTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_image_url_passes_through_external_paths(): void
    {
        $url = 'https://cdn.example.test/photo.jpg';

        $this->assertSame($url, StorefrontCatalogArrayMapper::publicImageUrl($url));
    }

    public function test_public_image_url_wraps_local_admin_upload_paths_with_asset(): void
    {
        $mapped = StorefrontCatalogArrayMapper::publicImageUrl('images/products/listing.jpg');

        $this->assertSame(asset('images/products/listing.jpg'), $mapped);
        $this->assertStringStartsWith('http', $mapped);
    }

    public function test_product_mapper_exposes_public_urls_for_local_upload_images(): void
    {
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'mapped-product',
            'name' => 'Mapped Product',
            'model' => 'M1',
            'description' => 'Desc',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'images/products/listing.jpg',
            'is_listing' => true,
            'source' => ProductImage::SOURCE_UPLOAD,
            'catalog_managed' => true,
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'images/products/gallery.jpg',
            'gallery_order' => 0,
            'is_listing' => false,
            'source' => ProductImage::SOURCE_UPLOAD,
            'catalog_managed' => true,
        ]);

        $product->load(['category', 'images', 'specs', 'accessories', 'options.values']);
        $array = StorefrontCatalogArrayMapper::productToArray($product);

        $expectedListing = asset('images/products/listing.jpg');
        $this->assertSame($expectedListing, $array['image']);
        $this->assertSame([$expectedListing, asset('images/products/gallery.jpg')], $array['gallery']);
    }
}
