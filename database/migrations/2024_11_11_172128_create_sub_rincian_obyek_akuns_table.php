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
        Schema::create('sub_rincian_obyek_akuns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->string('nama', 255);
            $table->unsignedBigInteger('rincian_obyek_akun_id')->nullable();
            $table->timestamps();

            $table->foreign('rincian_obyek_akun_id')->references('id')->on('rincian_obyek_akuns')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_rincian_obyek_akuns');
    }
};
