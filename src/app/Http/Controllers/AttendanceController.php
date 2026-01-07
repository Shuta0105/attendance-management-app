<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Request as ModelsRequest;
use App\Models\RequestDetail;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->first();
        return view('staff.index', compact('attendance'));
    }

    public function clockIn()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->first();
        if (!$attendance) {
            Attendance::create([
                'user_id' => auth()->id(),
                'work_date' => now()->toDateString(),
                'clock_in_at' => now(),
            ]);
            return view('staff.attendance-in-work');
        }
        return view('staff.attendance-in-work');
    }

    public function clockOut()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->where('work_date', now()->toDateString())
            ->firstOrFail();
        if (!$attendance->clock_out_at) {
            $attendance->update([
                'clock_out_at' => now()
            ]);
            return view('staff.attendance-after-work');
        }
        return view('staff.attendance-after-work');
    }

    public function list(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m'));
        $attendances = Attendance::where('user_id', auth()->id())
            ->where('work_date', 'LIKE', '%' . $date . '%')
            ->orderBy('work_date', 'desc')
            ->get();
        return view('staff.list', compact('attendances', 'date'));
    }

    public function detail($id)
    {
        $attendance = Attendance::with('user', 'breakTimes')->find($id);
        $modelRequest = ModelsRequest::where('attendance_id', $attendance->id)
            ->where('status', '承認待ち')
            ->first();
        $requestDetail = $modelRequest ? RequestDetail::where('request_id', $modelRequest->id)->first() : null;
        return view('staff.detail', compact('attendance', 'modelRequest', 'requestDetail'));
    }
}
