<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modify extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'admin_user_id',
        'reason',
        'modified_at'
    ];
}
