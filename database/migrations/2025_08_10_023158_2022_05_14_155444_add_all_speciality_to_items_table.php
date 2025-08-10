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
        Schema::table('items', function (Blueprint $table) {
            $table->text('disclaimer')->nullable();
            $table->boolean('special')->default(0)->nullable();
            $table->foreignId('origin_id')->nullable()->references('id')->on('origins');
            $table->foreignId('brand_id')->nullable()->references('id')->on('brands');
            $table->string('primary_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
