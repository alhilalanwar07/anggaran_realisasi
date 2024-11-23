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
        Schema::create('anggarans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sub_kegiatan_id');
            $table->unsignedBigInteger('sub_rincian_obyek_akun_id');
            $table->decimal('nilai_anggaran', 20, 2)->default(0);
            $table->decimal('nilai_realisasi', 20, 2)->default(0);
            $table->string('tahun', 4)->nullable();
            $table->timestamps();

            $table->foreign('sub_kegiatan_id')->references('id')->on('sub_kegiatans')->onDelete('cascade');
            $table->foreign('sub_rincian_obyek_akun_id')->references('id')->on('sub_rincian_obyek_akuns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggarans');
    }
};
