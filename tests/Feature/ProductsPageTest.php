<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\PublicStorefrontFixture;
use Tests\TestCase;

class ProductsPageTest extends TestCase
{
    use PublicStorefrontFixture;
    use RefreshDatabase;

    public function test_empty_listing_does_not_render_json_demo_products(): void
    {
        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertSee('id="site-menu-toggle"', false);
        $response->assertSee('aria-controls="site-nav"', false);
        $response->assertSee('id="site-nav-backdrop"', false);
        $response->assertSee('Explore Our Equipment', false);
        $response->assertSee('0 items', false);
        $response->assertDontSee('Aurum Pro X1', false);
        $response->assertDontSee('frontend/data/products.json', false);
    }

    public function test_listing_renders_database_products_and_categories(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $this->createStorefrontCategory('metal-detectors', 'Metal Detectors');
        $this->createStorefrontProductWithVariants($gold);

        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertSee('1 item', false);
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('$4,280', false);
        $response->assertSee('data-category="gold-detectors"', false);
        $response->assertSee('Gold Detectors', false);
        $response->assertSee('class="product-card"', false);
        $response->assertSee(route('frontend.products.show', 'aurum-pro-x1'), false);
    }

    public function test_product_cards_use_category_slugs_matching_filter_chips(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $metal = $this->createStorefrontCategory('metal-detectors', 'Metal Detectors');
        $this->createStorefrontProductWithVariants($gold);

        Product::query()->create([
            'category_id' => $metal->id,
            'slug' => 'terra-vantage-v3',
            'name' => 'Terra Vantage V3',
            'model' => 'TVV-3300',
            'description' => 'Metal detector.',
            'base_price' => '1890.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);
        ProductImage::query()->create([
            'product_id' => Product::query()->where('slug', 'terra-vantage-v3')->value('id'),
            'path' => 'https://example.test/metal.jpg',
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);

        $response = $this->get(route('frontend.products'));
        $content = $response->getContent();

        $this->assertNotFalse($content);
        $this->assertMatchesRegularExpression(
            '/class="products-filters__chip"[^>]*data-category="gold-detectors"/',
            $content,
        );
        $this->assertMatchesRegularExpression(
            '/class="product-card"[^>]*data-category="gold-detectors"/',
            $content,
        );
        $this->assertMatchesRegularExpression(
            '/class="product-card"[^>]*data-category="metal-detectors"/',
            $content,
        );
        $this->assertStringContainsString('id="filter-chips"', $content);
        $this->assertStringContainsString('frontend/js/products-page.js', $content);
    }

    public function test_product_detail_route_still_resolves_for_catalog_slug(): void
    {
        $gold = $this->createStorefrontCategory('gold-detectors', 'Gold Detectors');
        $this->createStorefrontProductWithVariants($gold);

        $this->get(route('frontend.products.show', 'aurum-pro-x1'))
            ->assertOk()
            ->assertSee('Aurum Pro X1', false);
    }

    public function test_unknown_product_slug_still_returns_not_found(): void
    {
        $this->get(route('frontend.products.show', 'not-a-real-product'))
            ->assertNotFound();
    }
}
