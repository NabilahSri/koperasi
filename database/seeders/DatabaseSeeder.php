<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Lembaga;
use App\Models\TransaksiS;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::firstOrCreate(
            ['email' => 'admintest@gmail.com'],
            [
                'no_user' => '000',
                'name' => 'Admin Test',
                'alamat' => 'Tasikmalaya',
                'nohp' => '081234567890',
                'password' => bcrypt('12341234'),
                'role' => 'admin',
            ]
        );

        $jenisSimpanan = Jenis::firstOrCreate(['nama' => 'Simpanan']);
        $jenisTagihan = Jenis::firstOrCreate(['nama' => 'Tagihan']);

        Kategori::firstOrCreate(['id_jenis' => $jenisSimpanan->id, 'nama' => 'Iuran Pokok']);
        Kategori::firstOrCreate(['id_jenis' => $jenisSimpanan->id, 'nama' => 'Iuran Wajib']);
        Kategori::firstOrCreate(['id_jenis' => $jenisSimpanan->id, 'nama' => 'Niaga']);
        Kategori::firstOrCreate(['id_jenis' => $jenisSimpanan->id, 'nama' => 'Manasuka']);
        Kategori::firstOrCreate(['id_jenis' => $jenisSimpanan->id, 'nama' => 'Lebaran']);

        Kategori::firstOrCreate(['id_jenis' => $jenisTagihan->id, 'nama' => 'Pinjaman']);
        Kategori::firstOrCreate(['id_jenis' => $jenisTagihan->id, 'nama' => 'Bagihasil']);

        if (Lembaga::count() === 0) {
            Lembaga::create([
                'nama' => 'Kopotren Al-Falah',
                'nohp' => '081xxxx',
                'alamat' => 'Isi sendiri',
                'pimpinan' => 'Gak tau',
                'logo' => ''
            ]);
        }
    }
}
