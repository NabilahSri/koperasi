<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'method',
        'url',
        'route_name',
        'status_code',
        'request_data',
        'action',
        'model_type',
        'model_id',
        'changes',
        'message',
    ];

    protected $casts = [
        'request_data' => 'array',
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
