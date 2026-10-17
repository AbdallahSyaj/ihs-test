<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductSpec;
use App\Services\Catalog\DatabaseStorefrontCatalogProvider;
use App\Services\Catalog\JsonStorefrontCatalogProvider;
use App\Services\Catalog\StorefrontCatalogArrayMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StorefrontCatalogDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private function importAuthoritativeJsonCatalog(): void
    {
        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(0, $exitCode);
    }

    /**
     * @return array{categories: list<array<string, mixed>>, products: list<array<string, mixed>>}
     */
    private function authoritativeJsonCatalog(): array
    {
        return app(JsonStorefrontCatalogProvider::class)->catalog();
    }

    private function authoritativeJsonProductCount(): int
    {
        return count($this->authoritativeJsonCatalog()['products']);
    }

    private function seedCategory(string $slug, string $name): Category
    {
        return Category::query()->create([
            'slug' => $slug,
            'name' => $name,
        ]);
    }

    private function seedShowcaseProduct(Category $category): Product
    {
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'aurum-pro-x1',
            'name' => 'Aurum Pro X1',
            'model' => 'APX-1100',
            'description' => 'Professional pulse-induction gold detector.',
            'long_description' => 'Long description text.',
            'compatibility' => 'Compatible with Aurum-series coils.',
            'base_price' => '4280.00',
            'badge' => 'New',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
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
            'path' => 'https://example.test/listing.jpg',
            'gallery_order' => 0,
            'is_listing' => false,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);
        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/gallery-1.jpg',
            'gallery_order' => 1,
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
        ProductOptionValue::query()->create([
            'product_option_id' => $option->id,
            'label' => '13-inch',
            'additional_price' => '0.00',
            'sort_order' => 1,
            'catalog_managed' => true,
            'price_locked' => false,
        ]);

        ProductSpec::query()->create([
            'product_id' => $product->id,
            'label' => 'Detection Technology',
            'value' => 'Pulse Induction',
            'sort_order' => 0,
            'catalog_managed' => true,
        ]);

        return $product;
    }

    public function test_database_driver_renders_listing_cards_and_category_filters(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');
        $this->seedCategory('metal-detectors', 'Metal Detectors');
        $this->seedShowcaseProduct($gold);

        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertSee('data-category="gold-detectors"', false);
        $response->assertSee('Gold Detectors', false);
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('$4,280', false);
        $response->assertSee('badge-gold', false);
        $response->assertSee('New', false);
        $response->assertSee(route('frontend.products.show', 'aurum-pro-x1'), false);
    }

    public function test_database_driver_product_detail_includes_gallery_and_variants(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');
        $this->seedShowcaseProduct($gold);

        $response = $this->get(route('frontend.products.show', 'aurum-pro-x1'));

        $response->assertOk();
        $response->assertSee('https://example.test/listing.jpg', false);
        $response->assertSee('https://example.test/gallery-1.jpg', false);
        $response->assertSee('Coil Size', false);
        $response->assertSee('11-inch', false);
        $response->assertSee('13-inch', false);
        $response->assertSee('Professional pulse-induction gold detector.', false);
    }

    public function test_database_driver_excludes_inactive_and_soft_deleted_products(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');
        $this->seedShowcaseProduct($gold);

        Product::query()->create([
            'category_id' => $gold->id,
            'slug' => 'inactive-product',
            'name' => 'Inactive Product',
            'model' => 'X',
            'base_price' => '10.00',
            'status' => Product::STATUS_INACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $deleted = Product::query()->create([
            'category_id' => $gold->id,
            'slug' => 'deleted-product',
            'name' => 'Deleted Product',
            'model' => 'Y',
            'base_price' => '20.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);
        $deleted->delete();

        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertDontSee('Inactive Product', false);
        $response->assertDontSee('Deleted Product', false);
        $response->assertSee('1 item', false);
    }

    public function test_database_driver_excludes_products_in_soft_deleted_categories(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');
        $this->seedShowcaseProduct($gold);

        $trashed = $this->seedCategory('metal-detectors', 'Metal Detectors');
        $trashed->delete();

        Product::query()->create([
            'category_id' => $trashed->id,
            'slug' => 'metal-only',
            'name' => 'Metal Only Product',
            'model' => 'M',
            'base_price' => '99.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertDontSee('Metal Only Product', false);
        $response->assertDontSee('data-category="metal-detectors"', false);
    }

    public function test_storefront_lists_products_by_database_id_order(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');

        Product::query()->create([
            'category_id' => $gold->id,
            'slug' => 'aurum-pro-x1',
            'name' => 'Pro First',
            'model' => 'X1',
            'description' => 'Pro',
            'base_price' => '200.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
        ]);

        Product::query()->create([
            'category_id' => $gold->id,
            'slug' => 'aurum-scout-s2',
            'name' => 'Scout Second',
            'model' => 'S2',
            'description' => 'Scout',
            'base_price' => '100.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
        ]);

        ProductImage::query()->create([
            'product_id' => Product::query()->where('slug', 'aurum-pro-x1')->value('id'),
            'path' => 'https://example.test/pro.jpg',
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);
        ProductImage::query()->create([
            'product_id' => Product::query()->where('slug', 'aurum-scout-s2')->value('id'),
            'path' => 'https://example.test/scout.jpg',
            'is_listing' => true,
            'source' => ProductImage::SOURCE_IMPORT,
            'catalog_managed' => true,
        ]);

        $response = $this->get(route('frontend.products'));
        $content = $response->getContent();

        $this->assertNotFalse($content);
        $this->assertLessThan(
            strpos($content, 'Scout Second'),
            strpos($content, 'Pro First'),
        );
    }

    public function test_mapper_builds_gallery_without_duplicate_listing(): void
    {
        $gold = $this->seedCategory('gold-detectors', 'Gold Detectors');
        $product = $this->seedShowcaseProduct($gold);
        $product->load(['category', 'images', 'specs', 'accessories', 'options.values']);

        $mapped = StorefrontCatalogArrayMapper::productToArray($product);

        $this->assertSame('$4,280', $mapped['price']);
        $this->assertSame([
            'https://example.test/listing.jpg',
            'https://example.test/gallery-1.jpg',
        ], $mapped['gallery']);
        $this->assertSame('gold-detectors', $mapped['category']);
        $this->assertSame('Gold Detectors', $mapped['categoryLabel']);
    }

    public function test_storefront_categories_render_in_name_order(): void
    {
        $this->seedCategory('zebra-cat', 'Zebra Category');
        $this->seedCategory('alpha-cat', 'Alpha Category');

        $response = $this->get(route('frontend.products'));
        $content = $response->getContent();

        $this->assertNotFalse($content);
        $this->assertLessThan(
            strpos($content, 'Zebra Category'),
            strpos($content, 'Alpha Category'),
        );
    }

    public function test_imported_catalog_products_listing_renders_over_http(): void
    {
        $this->importAuthoritativeJsonCatalog();

        $jsonCatalog = $this->authoritativeJsonCatalog();
        $productCount = count($jsonCatalog['products']);
        $this->assertGreaterThan(0, $productCount);

        $response = $this->get(route('frontend.products'));

        $response->assertOk();
        $response->assertSee('Explore Our Equipment', false);
        $response->assertSee('All Equipment', false);
        $response->assertSee('class="product-card"', false);

        foreach ($jsonCatalog['categories'] as $category) {
            if (! is_array($category) || ! isset($category['id'], $category['label'])) {
                continue;
            }
            $response->assertSee('data-category="'.$category['id'].'"', false);
            $response->assertSee(e((string) $category['label']), false);
        }

        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('$4,280', false);
        $response->assertSee('badge-gold', false);
        $response->assertSee('New', false);
        $response->assertSee(route('frontend.products.show', 'aurum-pro-x1'), false);

        $itemsLabel = $productCount === 1 ? '1 item' : $productCount.' items';
        $response->assertSee($itemsLabel, false);
        $this->assertSame($productCount, Product::query()->count());
    }

    public function test_imported_catalog_product_detail_renders_over_http(): void
    {
        $this->importAuthoritativeJsonCatalog();

        $jsonProduct = null;
        foreach ($this->authoritativeJsonCatalog()['products'] as $product) {
            if (is_array($product) && ($product['id'] ?? null) === 'aurum-pro-x1') {
                $jsonProduct = $product;
                break;
            }
        }

        $this->assertIsArray($jsonProduct);

        $response = $this->get(route('frontend.products.show', 'aurum-pro-x1'));

        $response->assertOk();
        $response->assertSee('Aurum Pro X1', false);
        $response->assertSee('$4,280', false);
        $response->assertSee(e((string) $jsonProduct['image']), false);
        $response->assertSee(
            e('https://images.pexels.com/photos/3930091/pexels-photo-3930091.jpeg?auto=compress&cs=tinysrgb&w=800&h=800&fit=crop'),
            false,
        );
        $response->assertSee('Coil Size', false);
        $response->assertSee('11-inch', false);
        $response->assertSee('15-inch', false);
    }

    public function test_imported_catalog_unknown_product_returns_not_found_over_http(): void
    {
        $this->importAuthoritativeJsonCatalog();

        $this->get(route('frontend.products.show', 'not-a-real-product'))
            ->assertNotFound();
    }

    public function test_imported_catalog_provider_returns_all_imported_products(): void
    {
        $this->importAuthoritativeJsonCatalog();

        $jsonCount = count($this->authoritativeJsonCatalog()['products']);
        $databaseIds = array_map(
            static fn (array $product): string => (string) $product['id'],
            app(DatabaseStorefrontCatalogProvider::class)->catalog()['products'],
        );

        $this->assertCount($jsonCount, $databaseIds);
        $this->assertContains('aurum-pro-x1', $databaseIds);
    }
}
