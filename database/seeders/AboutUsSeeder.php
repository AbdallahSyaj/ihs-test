<?php

namespace Database\Seeders;

use App\Models\AboutUs;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AboutUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AboutUs::create([
            'title_ar' => 'عنوان باللغة العربية',
            'title_en' => 'Title in English',
            'description_ar' => 'وصف باللغة العربية',
            'description_en' => 'Description in English',
            'image' => 'public/style_files/logo/blueray_logo.jpg',
        ]);
    }
}
