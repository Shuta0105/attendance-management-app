<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequestRequest;
use App\Models\Attendance;
use App\Models\Request as ModelsRequest;
use App\Models\RequestDetail;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function list(Request $request)
    {
        $status = $request->query('status', 'pending');

        $query = ModelsRequest::with('attendance', 'user');

        if ($status === 'pending') {
            $query->where('user_id', auth()->id())
                ->where('status', '承認待ち');
        } elseif ($status === 'approved') {
            $query->where('user_id', auth()->id())
                ->where('status', '承認済み');
        }

        $requests = $query->orderBy('requested_at', 'desc')->get();

        return view('staff.request-list', compact('requests', 'status'));
    }

    public function request(UserRequestRequest $request)
    {
        Attendance::find($request->id);
        $modelRequest = ModelsRequest::create(
            [
                'attendance_id' => $request->id,
                'user_id' => auth()->id(),
                'reason' => $request->reason,
                'requested_at' => now(),
            ]
        );
        RequestDetail::firstOrCreate(
            ['request_id' => $modelRequest->id],
            [
                'payload' => [
                    'clock_in_at' => $request->clock_in_at,
                    'clock_out_at' => $request->clock_out_at,
                    'breaks' => $request->breaks,
                    'new_breaks' => $request->new_breaks,
                ],
            ]
        );
        return redirect('/attendance/detail/' . $request->id);
    }
}
