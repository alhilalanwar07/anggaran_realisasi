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
        Schema::create('skpds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->text('nama');
            $table->unsignedBigInteger('urusan_pelaksana_id')->nullable();
            $table->timestamps();

            $table->foreign('urusan_pelaksana_id')->references('id')->on('urusan_pelaksanas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skpds');
    }
};
