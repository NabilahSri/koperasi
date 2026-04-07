<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','anggota','operator') NOT NULL");
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','anggota') NOT NULL");
    }
};

