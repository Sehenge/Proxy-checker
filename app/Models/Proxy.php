<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    use HasFactory;

    protected $casts = [
        'ip' => 'string',
        'port' => 'integer',
        'protocol' => 'string',
        'country' => 'string',
        'response_time' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'ip',
        'port',
        'protocol',
        'country',
        'response_time',
        'is_active',
    ];
}
