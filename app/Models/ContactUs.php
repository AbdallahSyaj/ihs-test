<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class ContactUs extends Model
{
    use HasFactory;


    // ===================================================================================================================
    // ============================================= Basic Section =======================================================
    // ===================================================================================================================
    protected $table = "contact_us";

    protected $fillable = [
        'email',
        'phone',
        'address_ar',
        'address_en',
        'whatsapp',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'youtube',
        'snapchat',
        'tiktok',
        'telegram',
        'hours',
        'page_title_en',
        'page_intro_en',
    ];
}
