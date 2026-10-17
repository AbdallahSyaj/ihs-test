<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public const SOURCE_IMPORT = 'import';

    public const SOURCE_UPLOAD = 'upload';

    protected $fillable = [
        'product_id',
        'path',
        'gallery_order',
        'is_listing',
        'source',
        'catalog_managed',
    ];

    protected function casts(): array
    {
        return [
            'gallery_order' => 'integer',
            'is_listing' => 'boolean',
            'catalog_managed' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
