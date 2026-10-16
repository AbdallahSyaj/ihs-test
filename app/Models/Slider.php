<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slider extends Model
{
    use HasFactory;
    use SoftDeletes;


    // ===================================================================================================================
    // ============================================= Basic Section =======================================================
    // ===================================================================================================================
    protected $table = "sliders";

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'image',
        'status',
        'type',
        'video',
    ];



    // ===================================================================================================================
    // ============================================= Accessors Section ===================================================
    // ===================================================================================================================
    // status
    public function getStatusAttribute($value)
    {
        if ($value == 1) {
            return 'Active';
        } elseif ($value == 2) {
            return 'Inactive';
        }
    }

    // type
    public function getTypeAttribute($value)
    {
        if ($value == 1) {
            return 'Image';
        } elseif ($value == 2) {
            return 'Video';
        }
    }
}
