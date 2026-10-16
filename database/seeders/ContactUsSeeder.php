<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        ContactUs::create([
            'email' => 'test@brws.com',
            'phone' => '1234567890',
            'address_ar' => 'هذا هو عنوان باللغة العربية',
            'address_en' => 'This is an address in English',
            'whatsapp' => '789456123',
            'facebook' => 'www.facebook.com',
            'twitter' => 'www.twitter.com',
            'instagram' => 'www.instagram.com',
            'linkedin' => 'www.linkedin.com',
            'youtube' => 'www.youtube.com',
            'snapchat' => 'www.snapchat.com',
            'tiktok' => 'www.tiktok.com',
            'telegram' => 'www.telegram.com',
        ]);
    }
}
