<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Request as ModelsRequest;
use App\Models\RequestDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function list(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = ModelsRequest::with('attendance', 'user');

        if ($status === 'pending') {
            $query->where('status', '承認待ち');
        } elseif ($status === 'approved') {
            $query->where('status', '承認済み');
        }

        $requests = $query->orderBy('requested_at', 'desc')->get();

        return view('admin.request-list', compact('requests', 'status'));
    }

    public function detail($request_id)
    {
        $request = ModelsRequest::with('attendance', 'user')->find($request_id);
        $requestDetail = RequestDetail::where('request_id', $request_id)->first();
        return view('admin.request-approve', compact('request', 'requestDetail'));
    }

    public function approve($request_id)
    {
        $request = ModelsRequest::find($request_id);
        $request->update([
            'admin_user_id' => auth()->id(),
            'status' => '承認済み',
            'approved_at' => now(),
        ]);

        $attendance = Attendance::find($request->attendance_id);
        $requestDetail = RequestDetail::where('request_id', $request_id)->first();
        $payload = $requestDetail->payload;
        $attendance->update([
            'clock_in_at' => Carbon::parse($attendance->clock_in_at)
                ->setTimeFromTimeString($payload['clock_in_at']),
            'clock_out_at' => Carbon::parse($attendance->clock_out_at)
                ->setTimeFromTimeString($payload['clock_out_at'])
        ]);

        foreach ($payload['breaks'] ?? [] as $breakPayload) {
            $break = BreakTime::find($breakPayload['id']);
            $break->update([
                'break_start_at' => $breakPayload['break_start_at'],
                'break_end_at' => $breakPayload['break_end_at'],
            ]);
        }

        if (
            $payload['new_breaks'][0]['break_start_at']
            && $payload['new_breaks'][0]['break_end_at']
        ) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start_at' => $payload['new_breaks'][0]['break_start_at'],
                'break_end_at' => $payload['new_breaks'][0]['break_end_at']
            ]);
        }

        return redirect('/stamp_correction_request/approve/' . $request_id);
    }
}
