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

        \App\Models\User::factory()->create([
            'no_user' => '001',
            'name' => 'Admin Test',
            'email' => 'admintest@gmail.com',
            'alamat' => 'Tasikmalaya',
            'nohp' => '081234567890',
            'password' => bcrypt('12341234'),
            'iuran_wajib' => '50000',
            'iuran_pokok' => '50000',
            'role' => 'admin',
        ]);

        \App\Models\User::factory()->create([
            'no_user' => '002',
            'name' => 'Nabilah Sri Mulyani',
            'email' => 'nabilah@gmail.com',
            'alamat' => 'Tasikmalaya',
            'nohp' => '081234567890',
            'password' => bcrypt('12341234'),
            'iuran_wajib' => '70000',
            'iuran_pokok' => '50000',
            'role' => 'anggota',
        ]);

        Jenis::create([
            'nama' => 'Simpanan'
        ]);

        Jenis::create([
            'nama' => 'Tagihan'
        ]);

        Kategori::create([
            'id_jenis' => '1',
            'nama' => 'Iuran Pokok'
        ]);

        Kategori::create([
            'id_jenis' => '1',
            'nama' => 'Iuran Wajib'
        ]);

        Kategori::create([
            'id_jenis' => '2',
            'nama' => 'Pinjaman'
        ]);

        Kategori::create([
            'id_jenis' => '2',
            'nama' => 'Bagihasil'
        ]);

        Lembaga::create([
            'nama' => 'Kopotren Al-Falah',
            'nohp' => '081xxxx',
            'alamat' => 'Isi sendiri',
            'pimpinan' => 'Gak tau',
            'logo' => ''
        ]);
    }
}
