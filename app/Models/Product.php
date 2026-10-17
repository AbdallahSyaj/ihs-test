<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const ORIGIN_JSON_IMPORT = 'json_import';

    public const ORIGIN_ADMIN = 'admin';

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'model',
        'description',
        'long_description',
        'compatibility',
        'base_price',
        'badge',
        'status',
        'catalog_origin',
        'import_sync_enabled',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'import_sync_enabled' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class);
    }

    public function accessories(): HasMany
    {
        return $this->hasMany(ProductAccessory::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    public function isActiveSlugTakenByAnotherProduct(): bool
    {
        if (! filled($this->slug)) {
            return false;
        }

        return static::query()
            ->where('slug', $this->slug)
            ->whereKeyNot($this->getKey())
            ->exists();
    }

    /**
     * Restore only when no other active row holds the same slug.
     */
    public function restoreSafely(): bool
    {
        if ($this->isActiveSlugTakenByAnotherProduct()) {
            return false;
        }

        try {
            $this->restore();
        } catch (UniqueConstraintViolationException) {
            return false;
        } catch (QueryException $exception) {
            if ($this->isUniqueConstraintQueryException($exception)) {
                return false;
            }

            throw $exception;
        }

        return true;
    }

    private function isUniqueConstraintQueryException(QueryException $exception): bool
    {
        if ($exception instanceof UniqueConstraintViolationException) {
            return true;
        }

        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;

        if ($sqlState === '23000') {
            return true;
        }

        if (in_array($driverCode, [1062, 19], true)) {
            return true;
        }

        return str_contains(strtolower($exception->getMessage()), 'unique');
    }
}
