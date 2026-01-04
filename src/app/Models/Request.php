<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'admin_user_id',
        'status',
        'reason',
        'requested_at',
        'approved_at',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'approved_at' => 'date',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
