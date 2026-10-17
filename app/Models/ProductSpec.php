<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpec extends Model
{
    protected $fillable = [
        'product_id',
        'label',
        'value',
        'sort_order',
        'catalog_managed',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'catalog_managed' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
