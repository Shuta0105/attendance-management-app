<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;

class BreakTimeController extends Controller
{
    public function breakStart()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->firstOrFail();
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start_at' => now(),
        ]);
        return view('staff.attendance-during-break');
    }

    public function breakEnd()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->firstOrFail();
        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end_at')
            ->latest('break_start_at')
            ->firstOrFail();
        $break->update([
            'break_end_at' => now(),
        ]);
        return view('staff.attendance-in-work');
    }
}
