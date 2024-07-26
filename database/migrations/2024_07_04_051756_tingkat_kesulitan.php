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
        Schema::create('tingkat_kesulitan', function (Blueprint $table) {
            $table->id();
            $table->string('tingkat_kesulitan');
            $table->boolean('bantuan_pengucapan')->default(true);
            $table->integer('delay_bantuan')->default(0);
            $table->integer('maks_salah')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tingkat_kesulitan');
    }
};
