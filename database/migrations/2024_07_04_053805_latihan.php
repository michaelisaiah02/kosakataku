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
        Schema::create('latihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_bahasa');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_tingkat_kesulitan');
            $table->integer('total_kata')->default(0);
            $table->integer('benar')->default(0);
            $table->json('list_kata')->nullable();
            $table->json('list_benar')->nullable();
            $table->boolean('selesai')->default(false);
            $table->timestamps();

            // Foreign key constraints with onDelete and onUpdate
            $table->foreign('id_user')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_bahasa')->references('id')->on('bahasa')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_kategori')->references('id')->on('kategori')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tingkat_kesulitan')->references('id')->on('tingkat_kesulitan')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->index('id_user');
            $table->index('id_bahasa');
            $table->index('id_kategori');
            $table->index('id_tingkat_kesulitan');
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
