<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function totalBreakMinutes(): int
    {
        return $this->breakTimes
            ->whereNotNull('break_end_at')
            ->sum(
                fn($break) =>
                Carbon::parse($break->break_end_at)
                    ->diffInMinutes(Carbon::parse($break->break_start_at))
            );
    }

    public function totalWorkMinutes(): int
    {
        $minutes = Carbon::parse($this->clock_out_at)
            ->diffInMinutes(Carbon::parse($this->clock_in_at));
        $breakMinutes = $this->totalBreakMinutes();

        return $minutes - $breakMinutes;
    }
}
