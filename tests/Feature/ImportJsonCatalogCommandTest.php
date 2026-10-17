<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpec;
use App\Services\Catalog\JsonCatalogImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ImportJsonCatalogCommandTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalogCategories(): void
    {
        foreach (JsonCatalogImporter::AUTHORITATIVE_CATEGORY_IDS as $slug) {
            Category::query()->create([
                'name' => str_replace('-', ' ', ucfirst($slug)),
                'slug' => $slug,
            ]);
        }
    }

    public function test_dry_run_makes_no_database_writes(): void
    {
        Artisan::call('catalog:import-json', ['--dry-run' => true]);

        $this->assertDatabaseCount('categories', 0);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_specs', 0);
        $this->assertStringContainsString('Categories to create: 6', Artisan::output());
    }

    public function test_command_without_apply_makes_no_database_writes(): void
    {
        Artisan::call('catalog:import-json');

        $this->assertDatabaseCount('categories', 0);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_apply_creates_missing_categories_with_exact_json_slugs(): void
    {
        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('categories', 6);

        $this->assertDatabaseHas('categories', [
            'slug' => 'prospecting',
            'name' => 'Prospecting Equipment',
        ]);
        $this->assertDatabaseHas('categories', [
            'slug' => 'batteries',
            'name' => 'Batteries & Chargers',
        ]);
        $this->assertDatabaseHas('categories', [
            'slug' => 'accessories',
            'name' => 'Detector Accessories',
        ]);
    }

    public function test_apply_creates_only_missing_categories_and_preserves_existing_ids(): void
    {
        $existing = Category::query()->create([
            'name' => 'Gold Detectors',
            'slug' => 'gold-detectors',
        ]);

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('categories', 6);
        $this->assertSame($existing->id, Category::query()->where('slug', 'gold-detectors')->value('id'));

        $product = Product::query()->where('slug', 'aurum-pro-x1')->firstOrFail();
        $this->assertSame($existing->id, $product->category_id);
    }

    public function test_apply_imports_products_with_correct_category_relationships(): void
    {
        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('products', 12);

        $metalCategoryId = Category::query()->where('slug', 'metal-detectors')->value('id');
        $product = Product::query()->where('slug', 'terra-vantage-v3')->firstOrFail();
        $this->assertSame($metalCategoryId, $product->category_id);
    }

    public function test_apply_imports_products_and_nested_data(): void
    {
        $this->seedCatalogCategories();

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(0, $exitCode);
        $this->assertDatabaseCount('products', 12);

        $product = Product::query()->where('slug', 'aurum-pro-x1')->firstOrFail();
        $this->assertSame(Product::ORIGIN_JSON_IMPORT, $product->catalog_origin);
        $this->assertTrue($product->import_sync_enabled);
        $this->assertSame('4280.00', (string) $product->base_price);
        $this->assertDatabaseHas('product_specs', [
            'product_id' => $product->id,
            'label' => 'Detection Technology',
            'catalog_managed' => true,
        ]);
        $this->assertDatabaseHas('product_accessories', [
            'product_id' => $product->id,
            'name' => 'Control Unit',
            'qty' => '1',
        ]);

        $listing = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('is_listing', true)
            ->firstOrFail();
        $this->assertSame(ProductImage::SOURCE_IMPORT, $listing->source);
        $this->assertNull($listing->gallery_order);

        $gallery = ProductImage::query()
            ->where('product_id', $product->id)
            ->where('is_listing', false)
            ->orderBy('gallery_order')
            ->get();
        $this->assertGreaterThan(0, $gallery->count());
        $this->assertSame(0, $gallery->first()->gallery_order);
    }

    public function test_repeated_import_does_not_duplicate_products_or_categories(): void
    {
        Artisan::call('catalog:import-json', ['--apply' => true]);
        Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertDatabaseCount('categories', 6);
        $this->assertDatabaseCount('products', 12);
    }

    public function test_ambiguous_category_label_blocks_import(): void
    {
        Category::query()->create([
            'name' => 'Prospecting Equipment',
            'slug' => 'prospecting-equipment',
        ]);

        foreach (array_diff(JsonCatalogImporter::AUTHORITATIVE_CATEGORY_IDS, ['prospecting']) as $slug) {
            Category::query()->create([
                'name' => $slug,
                'slug' => $slug,
            ]);
        }

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseCount('categories', 6);
        $this->assertDatabaseCount('products', 0);
        $this->assertStringContainsString('Ambiguous category mapping', Artisan::output());
        $this->assertDatabaseMissing('categories', ['slug' => 'prospecting']);
    }

    public function test_admin_owned_product_blocks_import(): void
    {
        $this->seedCatalogCategories();
        $category = Category::query()->where('slug', 'gold-detectors')->firstOrFail();

        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'aurum-pro-x1',
            'name' => 'Admin Product',
            'base_price' => '1.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertSame(1, Product::query()->count());
        $this->assertSame('Admin Product', Product::query()->first()->name);
    }

    public function test_manually_managed_child_blocks_import(): void
    {
        $this->seedCatalogCategories();
        $category = Category::query()->where('slug', 'gold-detectors')->firstOrFail();

        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'aurum-pro-x1',
            'name' => 'Imported',
            'base_price' => '1.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
        ]);

        ProductSpec::query()->create([
            'product_id' => $product->id,
            'label' => 'Manual',
            'value' => 'Value',
            'sort_order' => 0,
            'catalog_managed' => false,
        ]);

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseHas('product_specs', [
            'product_id' => $product->id,
            'label' => 'Manual',
            'catalog_managed' => false,
        ]);
    }

    public function test_trashed_only_category_mapping_blocks_import(): void
    {
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $category->delete();

        foreach (array_slice(JsonCatalogImporter::AUTHORITATIVE_CATEGORY_IDS, 1) as $slug) {
            Category::query()->create([
                'name' => $slug,
                'slug' => $slug,
            ]);
        }

        $exitCode = Artisan::call('catalog:import-json', ['--apply' => true]);

        $this->assertSame(1, $exitCode);
        $this->assertDatabaseCount('products', 0);
    }
}
