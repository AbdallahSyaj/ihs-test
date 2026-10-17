<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class ProductOptionValue extends Model
{
    protected $fillable = [
        'product_option_id',
        'label',
        'additional_price',
        'sort_order',
        'catalog_managed',
        'price_locked',
    ];

    protected function casts(): array
    {
        return [
            'additional_price' => 'decimal:2',
            'sort_order' => 'integer',
            'catalog_managed' => 'boolean',
            'price_locked' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ProductOptionValue $value): void {
            if ($value->additional_price !== null && (float) $value->additional_price < 0) {
                throw ValidationException::withMessages([
                    'additional_price' => ['The additional price must be at least 0.'],
                ]);
            }
        });
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
