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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->String('no_user');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('alamat');
            $table->string('nohp');
            $table->string('password');
            $table->string('ktp')->nullable();
            $table->string('foto')->nullable();
            $table->integer('iuran_wajib')->nullable();
            $table->integer('iuran_pokok')->nullable();
            $table->enum('role',['admin','anggota']);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
