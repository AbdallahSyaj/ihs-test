<?php

namespace App\Services\Backend;

use App\Models\Product;
use App\Models\ProductAccessory;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductSpec;
use App\Support\Backend\ProductImageStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductCatalogPersistence
{
    public function __construct(
        private readonly ProductImageStorage $imageStorage,
    ) {}

    /**
     * @param  array<string, mixed>  $productAttributes
     * @param  array<string, mixed>  $nested
     */
    public function create(array $productAttributes, array $nested): Product
    {
        return DB::transaction(function () use ($productAttributes, $nested) {
            $product = Product::query()->create($productAttributes);

            $this->syncNested($product, $nested, isCreate: true);

            return $product->fresh([
                'images',
                'specs',
                'accessories',
                'options.values',
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $productAttributes
     * @param  array<string, mixed>  $nested
     */
    public function update(Product $product, array $productAttributes, array $nested): Product
    {
        return DB::transaction(function () use ($product, $productAttributes, $nested) {
            $product->update($productAttributes);

            $this->syncNested($product, $nested, isCreate: false);

            return $product->fresh([
                'images',
                'specs',
                'accessories',
                'options.values',
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $nested
     */
    private function syncNested(Product $product, array $nested, bool $isCreate): void
    {
        $this->syncSpecs($product, Arr::get($nested, 'specs', []));
        $this->syncAccessories($product, Arr::get($nested, 'accessories', []));
        $this->syncOptions($product, Arr::get($nested, 'options', []));
        $this->syncImages(
            $product,
            Arr::get($nested, 'images', []),
            $isCreate
        );
    }

    /**
     * @param  list<array<string, mixed>>  $specs
     */
    private function syncSpecs(Product $product, array $specs): void
    {
        $product->specs()->delete();

        foreach (array_values($specs) as $index => $row) {
            if (! filled($row['label'] ?? null) && ! filled($row['value'] ?? null)) {
                continue;
            }

            $product->specs()->create([
                'label' => (string) $row['label'],
                'value' => (string) $row['value'],
                'sort_order' => (int) ($row['sort_order'] ?? $index),
                'catalog_managed' => true,
            ]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $accessories
     */
    private function syncAccessories(Product $product, array $accessories): void
    {
        $product->accessories()->delete();

        foreach (array_values($accessories) as $index => $row) {
            if (! filled($row['name'] ?? null)) {
                continue;
            }

            $product->accessories()->create([
                'name' => (string) $row['name'],
                'qty' => (string) ($row['qty'] ?? ''),
                'sort_order' => (int) ($row['sort_order'] ?? $index),
                'catalog_managed' => true,
            ]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $options
     */
    private function syncOptions(Product $product, array $options): void
    {
        $product->options()->each(function (ProductOption $option): void {
            $option->values()->delete();
        });
        $product->options()->delete();

        foreach (array_values($options) as $optionIndex => $optionRow) {
            if (! filled($optionRow['name'] ?? null)) {
                continue;
            }

            $option = $product->options()->create([
                'name' => (string) $optionRow['name'],
                'sort_order' => (int) ($optionRow['sort_order'] ?? $optionIndex),
                'catalog_managed' => true,
            ]);

            $values = is_array($optionRow['values'] ?? null) ? $optionRow['values'] : [];
            foreach (array_values($values) as $valueIndex => $valueRow) {
                if (! filled($valueRow['label'] ?? null)) {
                    continue;
                }

                $option->values()->create([
                    'label' => (string) $valueRow['label'],
                    'additional_price' => $valueRow['additional_price'] ?? '0.00',
                    'sort_order' => (int) ($valueRow['sort_order'] ?? $valueIndex),
                    'catalog_managed' => true,
                    'price_locked' => false,
                ]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $imagePayload
     */
    private function syncImages(Product $product, array $imagePayload, bool $isCreate): void
    {
        $removedIds = array_map('intval', Arr::get($imagePayload, 'remove_ids', []));
        if ($removedIds !== []) {
            $imagesToRemove = $product->images()->whereIn('id', $removedIds)->get();
            foreach ($imagesToRemove as $image) {
                $this->imageStorage->deleteLocalImageIfSafe($image);
                $image->delete();
            }
        }

        $uploadedPaths = [];
        $listingUpload = Arr::get($imagePayload, 'listing_upload');
        if ($listingUpload instanceof UploadedFile) {
            $product->images()->update(['is_listing' => false]);
            $path = (string) Arr::get($imagePayload, 'listing_path');
            $uploadedPaths[] = $path;
            $product->images()->create([
                'path' => $path,
                'gallery_order' => null,
                'is_listing' => true,
                'source' => ProductImage::SOURCE_UPLOAD,
                'catalog_managed' => true,
            ]);
        }

        $galleryUploads = Arr::get($imagePayload, 'gallery_uploads', []);
        $galleryOrders = Arr::get($imagePayload, 'gallery_orders', []);
        if (is_array($galleryUploads)) {
            foreach (array_values($galleryUploads) as $index => $uploadMeta) {
                if (! is_array($uploadMeta)) {
                    continue;
                }
                $file = $uploadMeta['file'] ?? null;
                $path = $uploadMeta['path'] ?? null;
                if (! $file instanceof UploadedFile || ! is_string($path)) {
                    continue;
                }
                $uploadedPaths[] = $path;
                $product->images()->create([
                    'path' => $path,
                    'gallery_order' => (int) ($galleryOrders[$index] ?? $index),
                    'is_listing' => false,
                    'source' => ProductImage::SOURCE_UPLOAD,
                    'catalog_managed' => true,
                ]);
            }
        }

        $existingOrders = Arr::get($imagePayload, 'existing_orders', []);
        if (is_array($existingOrders)) {
            foreach ($existingOrders as $imageId => $order) {
                $image = $product->images()->whereKey((int) $imageId)->first();
                if ($image) {
                    $image->update([
                        'gallery_order' => $order === '' || $order === null ? null : (int) $order,
                    ]);
                }
            }
        }

        $listingImageId = Arr::get($imagePayload, 'listing_image_id');
        if ($listingImageId !== null && $listingImageId !== '') {
            $product->images()->update(['is_listing' => false]);
            $listing = $product->images()->whereKey((int) $listingImageId)->first();
            if ($listing) {
                $listing->update([
                    'is_listing' => true,
                    'gallery_order' => null,
                ]);
            }
        } elseif ($isCreate && $listingUpload instanceof UploadedFile) {
            // listing flag already set on create row
        } elseif (! $isCreate && $product->images()->where('is_listing', true)->doesntExist()) {
            $first = $product->images()->orderBy('id')->first();
            if ($first) {
                $first->update(['is_listing' => true, 'gallery_order' => null]);
            }
        }
    }
}
