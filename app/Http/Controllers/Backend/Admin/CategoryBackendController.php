<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Category\StoreCategoryFormRequest;
use App\Http\Requests\Backend\Category\UpdateCategoryFormRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryBackendController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('created_at', 'desc')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryFormRequest $request)
    {
        $created_data = [
            'name' => $request->name,
            'slug' => $request->slug,
        ];

        DB::transaction(function () use ($created_data) {
            Category::create($created_data);
        });

        return redirect()->route('super_admin.categories-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            return view('admin.categories.edit', compact('category'));
        }

        return redirect()->route('super_admin.categories-index')->with('danger', __('dashboard.shared.record_not_found'));
    }

    public function update($id, UpdateCategoryFormRequest $request)
    {
        $category = Category::find($id);
        if ($category) {
            $updated_data = [
                'name' => $request->name,
                'slug' => $request->slug,
            ];

            DB::transaction(function () use ($updated_data, $category) {
                $category->update($updated_data);
            });

            return redirect()->route('super_admin.categories-index')->with('success', __('dashboard.shared.updated_successfully'));
        }

        return redirect()->route('super_admin.categories-index')->with('danger', __('dashboard.shared.record_not_found'));
    }

    public function softDelete($id)
    {
        $category = Category::find($id);
        if ($category) {
            if ($category->products()->exists()) {
                return redirect()
                    ->route('super_admin.categories-index')
                    ->with('danger', __('dashboard.categories.delete_has_products'));
            }

            DB::transaction(function () use ($category) {
                $category->delete();
            });

            return redirect()->route('super_admin.categories-index')->with('success', __('dashboard.shared.deleted_successfully'));
        }

        return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
    }

    public function showSoftDelete(Request $request)
    {
        $categories = Category::onlyTrashed()->orderBy('created_at', 'asc')->get();

        return view('admin.categories.trashed', compact('categories'));
    }

    public function softDeleteRestore($id)
    {
        $category = Category::onlyTrashed()->find($id);
        if (! $category) {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }

        $nameTaken = Category::query()
            ->where('name', $category->name)
            ->exists();

        if ($nameTaken) {
            return redirect()
                ->route('super_admin.categories-showSoftDelete')
                ->with('danger', __('dashboard.categories.restore_name_conflict'));
        }

        $slugTaken = Category::query()
            ->where('slug', $category->slug)
            ->exists();

        if ($slugTaken) {
            return redirect()
                ->route('super_admin.categories-showSoftDelete')
                ->with('danger', __('dashboard.categories.restore_slug_conflict'));
        }

        DB::transaction(function () use ($category) {
            $category->restore();
        });

        return redirect()->route('super_admin.categories-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
    }
}
