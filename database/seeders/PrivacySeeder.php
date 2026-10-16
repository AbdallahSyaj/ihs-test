<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrivacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PrivacyPolicy::create([
            'title_ar' => 'عنوان سياسة الخصوصية باللغة العربية',
            'title_en' => 'Privacy Policy Title in English',
            'description_ar' => 'وصف سياسة الخصوصية باللغة العربية',
            'description_en' => 'Privacy Policy Description in English',
        ]);
    }
}
