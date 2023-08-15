<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sw_shops', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('shop_id');
            $table->string('shop_url', 255);
            $table->string('shop_secret', 255);
            $table->string('api_key', 255)->nullable();
            $table->string('secret_key', 255)->nullable();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sw_shops');
    }
};
