<?php

namespace App\Models;
use App\Models\User;
use App\Traits\Auditable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory, Auditable;
    protected $table = 'pengajuan';
    protected $guarded = [];

     public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }
}
