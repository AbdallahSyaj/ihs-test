<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::create([
            'title_ar' => 'عنوان الشريحة باللغة العربية',
            'title_en' => 'Slider Title in English',
            'description_ar' => 'وصف الشريحة باللغة العربية',
            'description_en' => 'Slider Description in English',
            'image' => 'public/style_files/logo/blueray_logo.jpg',
        ]);
    }
}
