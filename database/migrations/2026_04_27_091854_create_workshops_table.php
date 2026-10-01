<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan ekstensi PostGIS aktif di PostgreSQL
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        // 2. Buat tabel workshops menggunakan Raw SQL murni
        DB::statement("
            CREATE TABLE workshops (
                id BIGSERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                address VARCHAR(100) NOT NULL,
                rating DECIMAL(2, 1) DEFAULT 0.0,
                is_open BOOLEAN DEFAULT TRUE,
                geom GEOGRAPHY(Point, 4326),
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NULL,
                updated_at TIMESTAMP(0) WITHOUT TIME ZONE NULL
            );
        ");

        // 3. Tambahkan indeks GIST spasial agar pencarian koordinat GIS nanti super cepat
        DB::statement('CREATE INDEX workshops_geom_idx ON workshops USING GIST (geom);');
    }

    public function down(): void
    {
        // Menghapus tabel jika dilakukan rollback
        Schema::dropIfExists('workshops');
    }
};
