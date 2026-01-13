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
        Schema::create('transaksi_tagihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_pengajuan');
            $table->unsignedBigInteger('id_kategori');
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->string('keterangan');
            $table->timestamps();
            $table->foreign('id_user')->references('id')->on('users')->onDelete('no action')->onUpdate('no action');
            $table->foreign('id_pengajuan')->references('id')->on('pengajuan')->onDelete('no action')->onUpdate('no action');
            $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('no action')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tagihan');
    }
};
