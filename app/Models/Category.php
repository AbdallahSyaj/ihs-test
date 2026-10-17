<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    public static function slugFromName(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'category';
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Category $category): void {
            if (! filled($category->slug) && filled($category->name)) {
                $category->slug = self::slugFromName($category->name);
            }
        });
    }
}
