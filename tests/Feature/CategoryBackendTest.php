<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_category_index(): void
    {
        $this->get(route('super_admin.categories-index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_empty_category_index(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.categories-index'))
            ->assertOk()
            ->assertSee(__('dashboard.shared.modules.categories'), false);
    }

    public function test_admin_can_create_category(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.categories-store'), ['name' => 'Gold Detectors'])
            ->assertRedirect(route('super_admin.categories-index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Gold Detectors',
            'deleted_at' => null,
        ]);
    }

    public function test_create_rejects_missing_blank_and_overlong_names(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.categories-create'))
            ->post(route('super_admin.categories-store'), [])
            ->assertRedirect(route('super_admin.categories-create'))
            ->assertSessionHasErrors('name');

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.categories-create'))
            ->post(route('super_admin.categories-store'), ['name' => '   '])
            ->assertRedirect(route('super_admin.categories-create'))
            ->assertSessionHasErrors('name');

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.categories-create'))
            ->post(route('super_admin.categories-store'), ['name' => str_repeat('a', 256)])
            ->assertRedirect(route('super_admin.categories-create'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_create_rejects_duplicate_active_names(): void
    {
        $admin = Admin::factory()->create();
        Category::query()->create(['name' => 'Metal Detectors']);

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.categories-create'))
            ->post(route('super_admin.categories-store'), ['name' => 'Metal Detectors'])
            ->assertRedirect(route('super_admin.categories-create'))
            ->assertSessionHasErrors('name');

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Prospecting']);

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.categories-update', $category->id), ['name' => 'Prospecting Equipment'])
            ->assertRedirect(route('super_admin.categories-index'));

        $this->assertSame('Prospecting Equipment', $category->fresh()->name);
    }

    public function test_update_rejects_duplicate_active_name(): void
    {
        $admin = Admin::factory()->create();
        Category::query()->create(['name' => 'Batteries']);
        $second = Category::query()->create(['name' => 'Accessories']);

        $this->actingAs($admin, 'super_admin')
            ->from(route('super_admin.categories-edit', $second->id))
            ->post(route('super_admin.categories-update', $second->id), ['name' => 'Batteries'])
            ->assertRedirect(route('super_admin.categories-edit', $second->id))
            ->assertSessionHasErrors('name');

        $this->assertSame('Accessories', $second->fresh()->name);
    }

    public function test_soft_deleted_category_disappears_from_active_index(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Search Coils']);

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.categories-softDelete', $category->id))
            ->assertRedirect(route('super_admin.categories-index'));

        $this->assertSoftDeleted($category);
        $this->assertSame(0, Category::query()->count());
    }

    public function test_archived_categories_appear_in_archive(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Archived Category']);
        $category->delete();

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.categories-showSoftDelete'))
            ->assertOk()
            ->assertSee('Archived Category', false);
    }

    public function test_archived_category_can_be_restored_when_name_is_available(): void
    {
        $admin = Admin::factory()->create();
        $category = Category::query()->create(['name' => 'Restore Me']);
        $category->delete();

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.categories-softDeleteRestore', $category->id))
            ->assertRedirect(route('super_admin.categories-showSoftDelete'));

        $this->assertNull($category->fresh()->deleted_at);
        $this->assertSame(1, Category::query()->count());
    }

    public function test_restore_is_blocked_when_active_name_conflicts(): void
    {
        $admin = Admin::factory()->create();
        $archived = Category::query()->create(['name' => 'Shared Name']);
        $archived->delete();
        Category::query()->create(['name' => 'Shared Name']);

        $this->actingAs($admin, 'super_admin')
            ->get(route('super_admin.categories-softDeleteRestore', $archived->id))
            ->assertRedirect(route('super_admin.categories-showSoftDelete'))
            ->assertSessionHas('danger', __('dashboard.categories.restore_name_conflict'));

        $this->assertSoftDeleted($archived);
        $this->assertSame(1, Category::query()->count());
    }

    public function test_deleted_category_name_can_be_reused(): void
    {
        $admin = Admin::factory()->create();
        $original = Category::query()->create(['name' => 'Reusable']);
        $original->delete();

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.categories-store'), ['name' => 'Reusable'])
            ->assertRedirect(route('super_admin.categories-index'));

        $this->assertSame(1, Category::query()->count());
        $this->assertDatabaseHas('categories', [
            'name' => 'Reusable',
            'deleted_at' => null,
        ]);
        $this->assertSoftDeleted('categories', ['id' => $original->id]);
    }

    public function test_create_trims_whitespace_from_name(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'super_admin')
            ->post(route('super_admin.categories-store'), ['name' => '  Trimmed Name  '])
            ->assertRedirect(route('super_admin.categories-index'));

        $this->assertDatabaseHas('categories', ['name' => 'Trimmed Name']);
    }
}
