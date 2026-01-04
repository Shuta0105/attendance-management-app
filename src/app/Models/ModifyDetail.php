<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModifyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'modify_id',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
