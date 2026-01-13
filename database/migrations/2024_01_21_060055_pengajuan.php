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
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->integer('nominal_pinjaman');
            $table->integer('nominal_bagihasil');
            $table->date('tanggal_pengajuan');
            $table->string('keterangan');
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('no action')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
