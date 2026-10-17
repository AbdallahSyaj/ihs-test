<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Product\StoreProductFormRequest;
use App\Http\Requests\Backend\Product\UpdateProductFormRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Backend\ProductCatalogPersistence;
use App\Support\Backend\ProductImageStorage;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBackendController extends Controller
{
    use UploadImageTrait;

    public function __construct(
        private readonly ProductCatalogPersistence $catalogPersistence,
        private readonly ProductImageStorage $imageStorage,
    ) {}

    public function index(Request $request)
    {
        $query = Product::query()->with('category')->orderByDesc('created_at');

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->filled('status') && in_array($request->input('status'), [Product::STATUS_ACTIVE, Product::STATUS_INACTIVE], true)) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('name', 'like', $term)
                    ->orWhere('slug', 'like', $term)
                    ->orWhere('model', 'like', $term);
            });
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductFormRequest $request)
    {
        $attributes = $this->productAttributesFromRequest($request, isCreate: true);
        $nested = $this->nestedPayloadFromRequest($request, isCreate: true);

        $this->catalogPersistence->create($attributes, $nested);

        return redirect()->route('super_admin.products-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images', 'specs', 'accessories', 'options.values'])->find($id);
        if (! $product) {
            return redirect()->route('super_admin.products-index')->with('danger', __('dashboard.shared.record_not_found'));
        }

        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with(['images', 'specs', 'accessories', 'options.values'])->find($id);
        if (! $product) {
            return redirect()->route('super_admin.products-index')->with('danger', __('dashboard.shared.record_not_found'));
        }

        $categories = Category::query()->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update($id, UpdateProductFormRequest $request)
    {
        $product = Product::find($id);
        if (! $product) {
            return redirect()->route('super_admin.products-index')->with('danger', __('dashboard.shared.record_not_found'));
        }

        $attributes = $this->productAttributesFromRequest($request, isCreate: false);
        $nested = $this->nestedPayloadFromRequest($request, isCreate: false);

        $this->catalogPersistence->update($product, $attributes, $nested);

        return redirect()->route('super_admin.products-index')->with('success', __('dashboard.shared.updated_successfully'));
    }

    public function softDelete($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }

        DB::transaction(function () use ($product): void {
            $product->delete();
        });

        return redirect()->route('super_admin.products-index')->with('success', __('dashboard.shared.deleted_successfully'));
    }

    public function showSoftDelete(Request $request)
    {
        $products = Product::onlyTrashed()->with('category')->orderBy('created_at')->get();

        return view('admin.products.trashed', compact('products'));
    }

    public function softDeleteRestore($id)
    {
        $product = Product::onlyTrashed()->find($id);
        if (! $product) {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }

        if (! $product->restoreSafely()) {
            return redirect()
                ->route('super_admin.products-showSoftDelete')
                ->with('danger', __('dashboard.products.restore_slug_conflict'));
        }

        return redirect()->route('super_admin.products-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
    }

    public function activeInactiveSingle($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }

        $product->status = $product->status === Product::STATUS_ACTIVE
            ? Product::STATUS_INACTIVE
            : Product::STATUS_ACTIVE;
        $product->import_sync_enabled = false;
        $product->save();

        return redirect()->back()->with('success', __('dashboard.shared.process_successful'));
    }

    /**
     * @return array<string, mixed>
     */
    private function productAttributesFromRequest(StoreProductFormRequest|UpdateProductFormRequest $request, bool $isCreate): array
    {
        $attributes = [
            'category_id' => (int) $request->input('category_id'),
            'slug' => $request->input('slug'),
            'name' => $request->input('name'),
            'model' => $request->input('model'),
            'description' => $request->input('description'),
            'long_description' => $request->input('long_description'),
            'compatibility' => $request->input('compatibility'),
            'base_price' => $request->input('base_price'),
            'badge' => $request->input('badge'),
            'status' => $request->input('status'),
            'import_sync_enabled' => false,
        ];

        if ($isCreate) {
            $attributes['catalog_origin'] = Product::ORIGIN_ADMIN;
        }

        return $attributes;
    }

    /**
     * @return array<string, mixed>
     */
    private function nestedPayloadFromRequest(StoreProductFormRequest|UpdateProductFormRequest $request, bool $isCreate): array
    {
        $galleryUploads = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $index => $file) {
                if (! $file) {
                    continue;
                }
                $galleryUploads[] = [
                    'file' => $file,
                    'path' => $this->saveFile(
                        $file,
                        ProductImageStorage::UPLOAD_DIRECTORY,
                        1400,
                        0
                    ),
                ];
            }
        }

        $listingUpload = $request->file('listing_image');
        $listingPath = null;
        if ($listingUpload) {
            $listingPath = $this->saveFile(
                $listingUpload,
                ProductImageStorage::UPLOAD_DIRECTORY,
                1400,
                0
            );
        }

        return [
            'specs' => $request->input('specs', []),
            'accessories' => $request->input('accessories', []),
            'options' => $request->input('options', []),
            'images' => [
                'listing_upload' => $listingUpload,
                'listing_path' => $listingPath,
                'gallery_uploads' => $galleryUploads,
                'gallery_orders' => $request->input('gallery_orders', []),
                'remove_ids' => $request->input('remove_image_ids', []),
                'existing_orders' => $request->input('existing_gallery_orders', []),
                'listing_image_id' => $request->input('listing_image_id'),
            ],
        ];
    }
}
