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
        Schema::table('lembaga', function (Blueprint $table) {
            $table->integer('tenggat_iuran_wajib')->nullable()->after('logo');
            $table->integer('tenggat_bayar_tagihan')->nullable()->after('tenggat_iuran_wajib');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lembaga', function (Blueprint $table) {
            $table->dropColumn(['tenggat_iuran_wajib', 'tenggat_bayar_tagihan']);
        });
    }
};
