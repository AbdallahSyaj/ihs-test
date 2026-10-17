<?php

namespace App\Support\Backend;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;

final class ProductImageStorage
{
    public const UPLOAD_DIRECTORY = 'images/products/';

    public function isExternalPath(string $path): bool
    {
        return str_starts_with(strtolower($path), 'http://')
            || str_starts_with(strtolower($path), 'https://');
    }

    public function isLocalProductUploadPath(string $path): bool
    {
        if ($this->isExternalPath($path)) {
            return false;
        }

        return str_starts_with($path, self::UPLOAD_DIRECTORY);
    }

    public function deleteLocalFileIfExists(string $path): void
    {
        if (! $this->isLocalProductUploadPath($path)) {
            return;
        }

        $fullPath = public_path($path);
        if (is_file($fullPath)) {
            File::delete($fullPath);
        }
    }

    public function deleteLocalImageIfSafe(ProductImage $image): void
    {
        if ($image->source !== ProductImage::SOURCE_UPLOAD) {
            return;
        }

        if (! $this->isLocalProductUploadPath($image->path)) {
            return;
        }

        $stillReferenced = ProductImage::query()
            ->where('path', $image->path)
            ->whereKeyNot($image->getKey())
            ->exists();

        if ($stillReferenced) {
            return;
        }

        $this->deleteLocalFileIfExists($image->path);
    }

    public function productOwnsLocalUpload(Product $product, string $path): bool
    {
        if (! $this->isLocalProductUploadPath($path)) {
            return false;
        }

        return $product->images()->where('path', $path)->exists();
    }
}
