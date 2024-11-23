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
        Schema::create('urusan_pelaksanas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode', 50);
            $table->string('nama', 255);
            $table->unsignedBigInteger('urusan_id')->nullable();
            $table->timestamps();

            $table->foreign('urusan_id')->references('id')->on('urusans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urusan_pelaksanas');
    }
};
