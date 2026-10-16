<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
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
        User::create([
            'first_name' => 'User',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'user@dashboard.com',
            'password' => $password,
        ]);
    }
}
