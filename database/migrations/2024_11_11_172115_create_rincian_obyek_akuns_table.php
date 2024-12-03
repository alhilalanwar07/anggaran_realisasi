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
        Schema::create('rincian_obyek_akuns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->text('nama');
            $table->unsignedBigInteger('obyek_akun_id')->nullable();
            $table->timestamps();

            $table->foreign('obyek_akun_id')->references('id')->on('obyek_akuns')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_obyek_akuns');
    }
};
