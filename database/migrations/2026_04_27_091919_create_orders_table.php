<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 100)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('workshop_id')->nullable()->constrained('workshops')->nullOnDelete();
            $table->foreignId('mechanic_id')->nullable()->constrained('mechanics')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->text('problem')->nullable();
            $table->integer('basic_cost')->default(25000);
            $table->integer('total_cost')->default(25000);
            $table->integer('eta')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE orders ADD COLUMN user_location geography(Point, 4326)');
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};