<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\Support\RestorableProduct;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function productAttributes(Category $category, string $slug = 'aurum-pro-x1'): array
    {
        return [
            'category_id' => $category->id,
            'slug' => $slug,
            'name' => 'Aurum Pro X1',
            'model' => 'APX-1100',
            'description' => 'Short description',
            'long_description' => 'Long description',
            'compatibility' => 'Compatible accessories',
            'base_price' => '4280.00',
            'badge' => 'New',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ];
    }

    public function test_two_active_products_cannot_share_the_same_slug(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        Product::query()->create($this->productAttributes($category, 'shared-slug'));

        $this->expectException(UniqueConstraintViolationException::class);

        Product::query()->create($this->productAttributes($category, 'shared-slug'));
    }

    public function test_soft_deleted_product_slug_can_be_reused_by_new_active_product(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $original = Product::query()->create($this->productAttributes($category, 'reusable-slug'));
        $original->delete();

        $replacement = Product::query()->create($this->productAttributes($category, 'reusable-slug'));
        $replacement->name = 'Replacement Product';

        $this->assertDatabaseHas('products', [
            'id' => $replacement->id,
            'slug' => 'reusable-slug',
            'deleted_at' => null,
        ]);
        $this->assertSoftDeleted('products', ['id' => $original->id]);
    }

    public function test_restore_fails_safely_when_active_slug_is_taken(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $archived = Product::query()->create($this->productAttributes($category, 'shared-slug'));
        $archived->delete();

        Product::query()->create($this->productAttributes($category, 'shared-slug'));

        $archived = Product::onlyTrashed()->findOrFail($archived->id);

        $this->assertFalse($archived->restoreSafely());
        $this->assertSoftDeleted('products', ['id' => $archived->id]);
    }

    public function test_restore_safely_returns_false_when_database_unique_conflict_occurs_after_precheck(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $archived = RestorableProduct::query()->create($this->productAttributes($category, 'race-slug'));
        $archived->delete();
        Product::query()->create($this->productAttributes($category, 'race-slug'));

        $archived = RestorableProduct::onlyTrashed()->findOrFail($archived->id);
        $archived->skipActiveSlugPrecheck = true;

        $this->assertFalse($archived->restoreSafely());
        $this->assertSoftDeleted('products', ['id' => $archived->id]);
    }

    public function test_failed_restore_leaves_product_soft_deleted(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $archived = Product::query()->create($this->productAttributes($category, 'held-slug'));
        $archived->delete();

        Product::query()->create($this->productAttributes($category, 'held-slug'));

        $archived->refresh();
        $archived->restoreSafely();

        $this->assertSoftDeleted('products', ['id' => $archived->id]);
        $this->assertSame(1, Product::query()->count());
    }

    public function test_product_relationships_load_expected_children(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $product = Product::query()->create($this->productAttributes($category));
        $image = ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/listing.jpg',
            'gallery_order' => null,
            'is_listing' => true,
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

        $product->load(['category', 'images', 'options.values']);

        $this->assertTrue($product->category->is($category));
        $this->assertCount(1, $product->images);
        $this->assertTrue($product->images->first()->is($image));
        $this->assertCount(1, $product->options);
        $this->assertCount(1, $product->options->first()->values);
    }

    public function test_category_hard_delete_is_restricted_when_products_reference_it(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        Product::query()->create($this->productAttributes($category));

        $this->expectException(QueryException::class);

        $category->forceDelete();
    }

    public function test_product_hard_delete_cascades_to_child_rows(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $product = Product::query()->create($this->productAttributes($category));
        $image = ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'https://example.test/gallery.jpg',
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

        $product->forceDelete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
        $this->assertDatabaseMissing('product_options', ['id' => $option->id]);
    }

    public function test_option_values_reject_negative_additional_price_at_application_layer(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $product = Product::query()->create($this->productAttributes($category));
        $option = ProductOption::query()->create([
            'product_id' => $product->id,
            'name' => 'Configuration',
            'sort_order' => 0,
            'catalog_managed' => true,
        ]);

        $this->expectException(ValidationException::class);

        ProductOptionValue::query()->create([
            'product_option_id' => $option->id,
            'label' => 'Advanced Kit',
            'additional_price' => '-1.00',
            'sort_order' => 0,
            'catalog_managed' => true,
            'price_locked' => false,
        ]);
    }

    public function test_option_values_reject_negative_additional_price_on_direct_database_write(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $product = Product::query()->create($this->productAttributes($category));
        $option = ProductOption::query()->create([
            'product_id' => $product->id,
            'name' => 'Configuration',
            'sort_order' => 0,
            'catalog_managed' => true,
        ]);
        $now = now();

        $this->expectException(QueryException::class);

        DB::table('product_option_values')->insert([
            'product_option_id' => $option->id,
            'label' => 'Bypassed Kit',
            'additional_price' => '-1.00',
            'sort_order' => 0,
            'catalog_managed' => true,
            'price_locked' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function test_product_catalog_migration_rolls_back_on_sqlite(): void
    {
        $category = Category::query()->create(['name' => 'Gold Detectors', 'slug' => 'gold-detectors']);
        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasColumn('products', 'active_slug'));
        $this->assertContains('products_active_slug_unique', $this->sqliteIndexNames('products'));

        Artisan::call('migrate:rollback', [
            '--path' => 'database/migrations/2026_10_17_140000_create_product_catalog_tables.php',
        ]);

        $this->assertFalse(Schema::hasTable('products'));
        $this->assertFalse(Schema::hasTable('product_images'));

        Artisan::call('migrate', [
            '--path' => 'database/migrations/2026_10_17_140000_create_product_catalog_tables.php',
        ]);

        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasColumn('products', 'active_slug'));
        $this->assertContains('products_active_slug_unique', $this->sqliteIndexNames('products'));

        Product::query()->create($this->productAttributes($category, 'post-remigrate-slug'));

        try {
            Product::query()->create($this->productAttributes($category, 'post-remigrate-slug'));
            $this->fail('Expected duplicate active slug to be rejected after re-migration.');
        } catch (UniqueConstraintViolationException) {
            // expected
        }

        $original = Product::query()->where('slug', 'post-remigrate-slug')->whereNull('deleted_at')->firstOrFail();
        $original->delete();
        $replacement = Product::query()->create($this->productAttributes($category, 'post-remigrate-slug'));
        $replacement->update(['name' => 'Replacement After Remigrate']);

        $this->assertDatabaseHas('products', [
            'id' => $replacement->id,
            'slug' => 'post-remigrate-slug',
            'deleted_at' => null,
        ]);
        $this->assertSoftDeleted('products', ['id' => $original->id]);
    }

    /**
     * @return list<string>
     */
    private function sqliteIndexNames(string $table): array
    {
        $indexes = DB::select('PRAGMA index_list('.$table.')');

        return array_map(static fn (object $index): string => (string) $index->name, $indexes);
    }
}
