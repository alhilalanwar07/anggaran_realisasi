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
        Schema::create('realisasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anggaran_id')->nullable();
            $table->string('kode', 255)->nullable();
            $table->decimal('nilai_realisasi', 20, 2)->default(0);
            $table->string('tahun', 4)->nullable();
            $table->foreign('anggaran_id')->references('id')->on('anggarans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realisasis');
    }
};
