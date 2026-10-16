<?php

namespace Database\Seeders;

use App\Models\TermsAndCondition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TermsAndCondition::create([
            'title_ar' => 'عنوان الشروط والأحكام باللغة العربية',
            'title_en' => 'Terms and Conditions Title in English',
            'description_ar' => 'وصف الشروط والأحكام باللغة العربية',
            'description_en' => 'Terms and Conditions Description in English',
        ]);
    }
}
