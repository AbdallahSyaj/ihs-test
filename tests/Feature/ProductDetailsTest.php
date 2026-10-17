<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PublicStorefrontFixture;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
{
    use PublicStorefrontFixture;
    use RefreshDatabase;

    public function test_valid_product_renders_full_details_in_html(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $this->createStorefrontProductWithVariants($gold);

        $response = $this->get(route('frontend.products.show', 'aurum-pro-x1'));

        $response->assertOk();
        $response->assertSee('class="pd__title"', false);
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('data-variant="Coil Size"', false);
        $response->assertSee('11-inch', false);
        $response->assertSee('https://example.test/listing.jpg', false);
        $response->assertSee('badge-gold', false);
        $response->assertSee(route('frontend.contact'), false);
        $response->assertSee(route('frontend.products'), false);
        $response->assertSee('id="pd-slider"', false);
        $response->assertSee('data-pd-gallery-multi', false);
        $response->assertSee('id="pd-nav-prev"', false);
        $response->assertSee('id="pd-thumbs"', false);
        $response->assertSee('class="pd__slider-track"', false);
    }

    public function test_product_without_variants_omits_variant_interface(): void
    {
        $category = $this->createStorefrontCategory('batteries', 'Batteries & Chargers');
        $this->createStorefrontProductWithoutVariants($category);

        $response = $this->get(route('frontend.products.show', 'aurum-battery-pack'));

        $response->assertOk();
        $response->assertSee('Aurum Battery Pack', false);
        $response->assertDontSee('id="pd-variants"', false);
        $response->assertDontSee('id="pd-nav-prev"', false);
        $response->assertDontSee('id="pd-thumbs"', false);
        $response->assertDontSee('data-pd-gallery-multi', false);
    }

    public function test_inactive_product_returns_not_found(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = $this->createStorefrontProductWithVariants($gold);
        $product->update(['status' => Product::STATUS_INACTIVE]);

        $this->get(route('frontend.products.show', 'aurum-pro-x1'))
            ->assertNotFound();
    }

    public function test_invalid_product_slug_returns_not_found(): void
    {
        $this->get(route('frontend.products.show', 'not-a-real-product'))
            ->assertNotFound();
    }

    public function test_local_upload_image_paths_render_on_detail_page(): void
    {
        $category = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'local-images',
            'name' => 'Local Images Product',
            'model' => 'LOC-1',
            'description' => 'Description',
            'base_price' => '99.00',
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

        $listingUrl = asset('images/products/listing.jpg');
        $galleryUrl = asset('images/products/gallery.jpg');

        $this->get(route('frontend.products.show', 'local-images'))
            ->assertOk()
            ->assertSee(e($listingUrl), false)
            ->assertSee(e($galleryUrl), false);
    }
}
