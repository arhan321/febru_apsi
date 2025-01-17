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
        Schema::create('fkilkoms', function (Blueprint $table) {
            $table->id();
            $table->string('nidn');
            $table->string('nama_dosen');
            $table->string('kode_matakuliah');
            $table->string('matakuliah');
            $table->integer('sks');
            $table->string('jurusan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fkilkoms');
    }
};
