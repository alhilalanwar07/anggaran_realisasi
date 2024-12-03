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
        Schema::create('sub_skpds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->text('nama');
            $table->unsignedBigInteger('skpd_id')->nullable();
            $table->timestamps();

            $table->foreign('skpd_id')->references('id')->on('skpds')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_skpds');
    }
};
