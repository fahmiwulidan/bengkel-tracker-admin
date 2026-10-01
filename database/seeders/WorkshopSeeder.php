<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkshopSeeder extends Seeder
{
   public function run(): void
    {
        // 1. Kosongkan tabel workshops dan reset auto-increment ID
        DB::statement('TRUNCATE TABLE workshops RESTART IDENTITY CASCADE');

        // 2. Tembak langsung menggunakan Query SQL Mentah agar binding PostGIS tidak bergeser
        DB::statement("
            INSERT INTO workshops (id, name, address, rating, is_open, geom, created_at, updated_at)
            VALUES
            (
                1,
                'Bengkel Pak Slamet',
                'Jl. Basuki Rahmat No. 12, Lamongan',
                4.9,
                TRUE,
                ST_SetSRID(ST_MakePoint(112.4173, -7.1132), 4326)::geography,
                NOW(),
                NOW()
            ),
            (
                2,
                'Auto Karya Motor',
                'Jl. Veteran No. 45, Lamongan',
                4.8,
                TRUE,
                ST_SetSRID(ST_MakePoint(112.4215, -7.1187), 4326)::geography,
                NOW(),
                NOW()
            ),
            (
                3,
                'Bengkel Sumber Rejeki',
                'Jl. Panglima Sudirman, Lamongan',
                4.5,
                FALSE,
                ST_SetSRID(ST_MakePoint(112.4097, -7.1064), 4326)::geography,
                NOW(),
                NOW()
            ),
            (
                4,
                'Bengkel Berkah Teknik',
                'Jl. Lamongrejo, Lamongan',
                4.7,
                TRUE,
                ST_SetSRID(ST_MakePoint(112.4291, -7.1242), 4326)::geography,
                NOW(),
                NOW()
            );
        ");
    }
}
