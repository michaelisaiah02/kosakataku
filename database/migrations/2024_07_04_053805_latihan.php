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
            $table->integer('jumlah_pengucapan_benar')->default(0);
            $table->integer('jumlah_artikata_benar')->default(0);
            $table->json('list_latihan_kosakata')->nullable()->default(null);
            $table->json('list_latihan_artikata')->nullable()->default(null);
            $table->enum('bantuan_suara', ['pria', 'wanita'])->default('wanita');
            $table->boolean('selesai')->default(false);
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_bahasa')->references('id')->on('bahasa')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_kategori')->references('id')->on('kategori')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->index('id_user');
            $table->index('id_bahasa');
            $table->index('id_kategori');
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
