<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ContactUsRequest extends Model
{
    use HasFactory;


    // ===================================================================================================================
    // ============================================= Basic Section =======================================================
    // ===================================================================================================================
    protected $table = "contact_us_requests";

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
    ];
}
