<?php

namespace App\Models;
use App\Models\Jenis;
use App\Models\TransaksiS;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory, Auditable;
    protected $table = 'kategori';
    protected $guarded = [];

    public function jenis(){
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }

    public function transaksi_simpanan() {
        return $this->hasMany(TransaksiS::class, 'id');
    }
}
