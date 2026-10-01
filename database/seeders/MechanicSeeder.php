<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MechanicSeeder extends Seeder
{
    public function run(): void
    {
        $userId1 = DB::table('users')->insertGetId([
            'name' => 'Pak Slamet Riyadi',
            'email' => 'slamet@example.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'role' => 'mechanic',
            'fcm_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $userId2 = DB::table('users')->insertGetId([
            'name' => 'Mas Budi Mekanik',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'phone' => '081998877665',
            'role' => 'mechanic',
            'fcm_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('mechanics')->insert([
            [
                'user_id' => $userId1,
                'workshop_id' => 1,
                'current_position' => DB::raw("ST_SetSRID(ST_MakePoint(112.4190, -7.1150), 4326)::geography"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $userId2,
                'workshop_id' => 2,
                'current_position' => DB::raw("ST_SetSRID(ST_MakePoint(112.4120, -7.1105), 4326)::geography"),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}