<?php

namespace Database\Seeders;

use App\Models\ContactUsRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactUsRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactUsRequest::create([
            'name' => 'John Doe',
            'email' => 'johnDoe@Brws.com',
            'phone' => '1234567890',
            'message' => 'This is a test message.',
            'subject' => 'Test Subject',
        ]);
    }
}
