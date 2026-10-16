<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $password = Hash::make('Adm!nD@sh2025#Secure');

        // ============================================================================
        // ============================================================================
        // ============================================================================
        // =========================== Test Users =====================================
        // ============================================================================
        // ============================================================================
        // ============================================================================
        Admin::create([
            'name' => 'admin',
            'email' => 'admin@dashboard.com',
            'password' => $password,
        ]);
    }
}
