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
        Schema::create('lcologs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('done')->default(0);        // Jumlah aktivasi selesai hari itu
            $table->integer('target')->default(0);      // Target hari itu
            $table->float('jam')->default(0);
            $table->integer('mb20')->default(0);
            $table->integer('mb50')->default(0);       // Jam kerja hari itu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lcologs');
    }
};
