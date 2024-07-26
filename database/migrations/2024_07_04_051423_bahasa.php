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
        Schema::create('bahasa', function (Blueprint $table) {
            $table->id();
            $table->string('inggris');
            $table->string('indonesia');
            $table->string('kode_tts');
            $table->string('kode_stt');
            $table->string('suara_pria')->nullable();
            $table->string('suara_wanita')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahasa');
    }
};
