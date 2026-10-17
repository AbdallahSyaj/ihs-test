<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductBackendTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validProductPayload(Category $category, string $slug = 'aurum-pro-x1'): array
    {
        return [
            'category_id' => $category->id,
            'name' => 'Aurum Pro X1',
            'slug' => $slug,
            'model' => 'APX-1100',
            'description' => 'Short',
            'long_description' => 'Long',
            'compatibility' => 'Compatible',
            'base_price' => '4280.00',
            'badge' => 'New',
            'status' => Product::STATUS_ACTIVE,
            'specs' => [
                ['label' => 'Weight', 'value' => '1.4 kg', 'sort_order' => 0],
            ],
            'accessories' => [
                ['name' => 'Battery', 'qty' => '1', 'sort_order' => 0],
            ],
            'options' => [
                [
                    'name' => 'Coil Size',
                    'sort_order' => 0,
                    'values' => [
                        ['label' => '11-inch', 'additional_price' => '0.00', 'sort_order' => 0],
                    ],
                ],
            ],
        ];
    }

    public function test_guest_cannot_access_products_index(): void
    {
        $this->get(route('super_admin.products-index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_create_product_with_nested_data(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.products-store'), $this->validProductPayload($category))
            ->assertRedirect(route('super_admin.products-index'));

        $product = Product::query()->firstOrFail();
        $this->assertSame(Product::ORIGIN_ADMIN, $product->catalog_origin);
        $this->assertFalse($product->import_sync_enabled);
        $this->assertDatabaseHas('product_specs', ['product_id' => $product->id, 'label' => 'Weight']);
        $this->assertDatabaseHas('product_accessories', ['product_id' => $product->id, 'name' => 'Battery', 'qty' => '1']);
        $this->assertDatabaseHas('product_options', ['product_id' => $product->id, 'name' => 'Coil Size']);
    }

    public function test_create_rejects_duplicate_active_slug(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'taken-slug',
            'name' => 'Existing',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.products-create'))
            ->post(route('super_admin.products-store'), $this->validProductPayload($category, 'taken-slug'))
            ->assertRedirect(route('super_admin.products-create'))
            ->assertSessionHasErrors('slug');

        $this->assertSame(1, Product::query()->count());
    }

    public function test_create_rejects_trashed_category(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Archived', 'slug' => 'archived']);
        $category->delete();

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.products-create'))
            ->post(route('super_admin.products-store'), $this->validProductPayload($category))
            ->assertRedirect(route('super_admin.products-create'))
            ->assertSessionHasErrors('category_id');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'toggle-me',
            'name' => 'Toggle',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => true,
        ]);

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.products-activeInactiveSingle', $product->id))
            ->assertRedirect();

        $this->assertSame(Product::STATUS_INACTIVE, $product->fresh()->status);
        $this->assertFalse($product->fresh()->import_sync_enabled);
    }

    public function test_create_rejects_negative_option_surcharge(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $payload = $this->validProductPayload($category);
        $payload['options'][0]['values'][0]['additional_price'] = '-5.00';

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.products-create'))
            ->post(route('super_admin.products-store'), $payload)
            ->assertRedirect(route('super_admin.products-create'))
            ->assertSessionHasErrors('options.0.values.0.additional_price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_validation_failure_does_not_persist_product(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $payload = $this->validProductPayload($category);
        unset($payload['name']);

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.products-store'), $payload)
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_specs', 0);
    }

    public function test_admin_can_soft_delete_product(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'delete-me',
            'name' => 'Delete',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.products-softDelete', $product->id))
            ->assertRedirect(route('super_admin.products-index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_listing_image_upload_is_stored_with_upload_source(): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            $this->markTestSkipped('GD extension is required for image upload tests.');
        }

        Storage::fake('public');
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $payload = $this->validProductPayload($category, 'image-product');
        $payload['listing_image'] = UploadedFile::fake()->image('listing.jpg', 1600, 900);

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.products-store'), $payload)
            ->assertRedirect(route('super_admin.products-index'));

        $product = Product::query()->where('slug', 'image-product')->firstOrFail();
        $image = ProductImage::query()->where('product_id', $product->id)->where('is_listing', true)->first();
        $this->assertNotNull($image);
        $this->assertSame(ProductImage::SOURCE_UPLOAD, $image->source);
        $this->assertStringStartsWith('images/products/', $image->path);
    }

    public function test_update_persists_reassigned_category_id(): void
    {
        $admin = Admin::factory()->create();
        $originalCategory = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $newCategory = Category::query()->create(['name' => 'Metal', 'slug' => 'metal-detectors']);
        $product = Product::query()->create([
            'category_id' => $originalCategory->id,
            'slug' => 'reassign-me',
            'name' => 'Reassign Me',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_ADMIN,
            'import_sync_enabled' => false,
        ]);

        $payload = $this->validProductPayload($newCategory, 'reassign-me');
        $payload['name'] = 'Reassign Me';

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.products-update', $product->id), $payload)
            ->assertRedirect(route('super_admin.products-index'));

        $this->assertSame($newCategory->id, $product->fresh()->category_id);
    }

    public function test_update_sets_import_sync_disabled(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Gold', 'slug' => 'gold-detectors']);
        $product = Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'sync-off',
            'name' => 'Sync',
            'base_price' => '10.00',
            'status' => Product::STATUS_ACTIVE,
            'catalog_origin' => Product::ORIGIN_JSON_IMPORT,
            'import_sync_enabled' => true,
        ]);

        $payload = $this->validProductPayload($category, 'sync-off');
        $payload['name'] = 'Sync Updated';

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.products-update', $product->id), $payload)
            ->assertRedirect(route('super_admin.products-index'));

        $product->refresh();
        $this->assertFalse($product->import_sync_enabled);
        $this->assertSame(Product::ORIGIN_JSON_IMPORT, $product->catalog_origin);
    }
}
