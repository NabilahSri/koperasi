<?php

namespace App\Models;

use App\Models\Kategori;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory, Auditable;
    protected $table = 'jenis';
    protected $guarded = [];

    public function kategori()
    {
        return $this->hasMany(Kategori::class, 'id');
    }
}
