<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kategori;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiS extends Model
{
    use HasFactory, Auditable;
    protected $table = 'transaksi_simpanan';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}
