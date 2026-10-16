<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TermsAndCondition extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ===================================================================================================================
    // ============================================= Basic Section =======================================================
    // ===================================================================================================================
    protected $table = "terms_and_conditions";

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'status',
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
}
