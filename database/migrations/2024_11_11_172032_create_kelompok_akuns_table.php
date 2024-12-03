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
        Schema::create('kelompok_akuns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->text('nama');
            $table->unsignedBigInteger('akun_id')->nullable();
            $table->timestamps();

            $table->foreign('akun_id')->references('id')->on('akuns')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_akuns');
    }
};
