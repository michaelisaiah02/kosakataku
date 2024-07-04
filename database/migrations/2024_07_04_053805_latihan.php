<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('latihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users');
            $table->unsignedBigInteger('id_bahasa');
            $table->foreign('id_bahasa')->references('id')->on('bahasa');
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id')->on('kategori');
            $table->unsignedBigInteger('id_tingkat_kesulitan');
            $table->foreign('id_tingkat_kesulitan')->references('id')->on('tingkat_kesulitan');
            $table->integer('total_kata');
            $table->integer('benar');
            $table->json('list_kata');
            $table->json('list_benar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latihan');
    }
};
