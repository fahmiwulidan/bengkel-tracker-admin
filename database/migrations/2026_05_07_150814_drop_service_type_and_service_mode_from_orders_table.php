<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'service_type')) {
                $table->dropColumn('service_type');
            }

            if (Schema::hasColumn('orders', 'service_mode')) {
                $table->dropColumn('service_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('service_type', 50)->nullable();
            $table->string('service_mode', 20)->nullable();
        });
    }
};