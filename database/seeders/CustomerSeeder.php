<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'User Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'phone' => '081111111111',
            'role' => 'customer',
            'fcm_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}