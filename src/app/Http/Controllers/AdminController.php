<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminModifyRequest;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Modify;
use App\Models\ModifyDetail;
use App\Models\Request as ModelsRequest;
use App\Models\RequestDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $attendances = Attendance::where('work_date', $date)->with('user')->get();
        return view('admin.attendance-list', compact('attendances', 'date'));
    }

    public function detail($id)
    {
        $attendance = Attendance::with('user', 'breakTimes')->find($id);
        $modelRequest = ModelsRequest::where('attendance_id', $id)
            ->where('status', '承認待ち')
            ->first();
        $requestDetail = $modelRequest ? RequestDetail::where('request_id', $modelRequest->id)->first() : null;
        if ($modelRequest) {
            return view('staff.detail', compact('attendance', 'modelRequest', 'requestDetail'));
        }
        return view('admin.attendance-detail', compact('attendance'));
    }

    public function staffList()
    {
        $staffs = User::where('role', 'user')->get();
        return view('admin.staff-list', compact('staffs'));
    }

    public function staffDetail(Request $request, $id)
    {
        $date = $request->input('date', now()->format('Y-m'));
        $user = User::find($id);
        $attendances = Attendance::where('user_id', $id)
            ->where('work_date', 'LIKE', '%' . $date . '%')
            ->orderBy('work_date', 'desc')
            ->get();
        return view('admin.staff-detail', compact('user', 'attendances', 'date'));
    }

    public function modify(AdminModifyRequest $request, $id)
    {
        $attendance = Attendance::find($id);

        $modify = Modify::create([
            'attendance_id' => $id,
            'user_id' => $attendance->user->id,
            'admin_user_id' => auth()->id(),
            'reason' => $request->reason,
            'modified_at' => now(),
        ]);

        ModifyDetail::firstOrCreate(
            ['modify_id' => $modify->id],
            [
                'payload' => [
                    'clock_in_at' => $request->clock_in_at,
                    'clock_out_at' => $request->clock_out_at,
                    'breaks' => $request->breaks,
                    'new_breaks' => $request->new_breaks,
                ]
            ]
        );

        $attendance->update([
            'clock_in_at' => Carbon::parse($attendance->clock_in_at)
                ->setTimeFromTimeString($request->clock_in_at),
            'clock_out_at' => Carbon::parse($attendance->clock_out_at)
                ->setTimeFromTimeString($request->clock_out_at)
        ]);

        if ($request->filled('breaks')) {
            foreach ($request->input('breaks') as $breakRequest) {
                $break = BreakTime::find($breakRequest['id']);
                $break->update([
                    'break_start_at' => Carbon::parse($break->break_start_at)
                        ->setTimeFromTimeString($breakRequest['break_start_at']),
                    'break_end_at' => Carbon::parse($break->break_end_at)
                        ->setTimeFromTimeString($breakRequest['break_end_at']),
                ]);
            }
        }

        if (
            $request->filled('new_breaks.0.break_start_at')
            && $request->filled('new_breaks.0.break_end_at')
        ) {
            BreakTime::create([
                'attendance_id' => $id,
                'break_start_at' => Carbon::parse($attendance->clock_in_at)
                    ->setTimeFromTimeString($request->new_breaks[0]['break_start_at']),
                'break_end_at' => Carbon::parse($attendance->clock_in_at)
                    ->setTimeFromTimeString($request->new_breaks[0]['break_end_at']),
            ]);
        }

        return redirect('/admin/attendance/' . $id);
    }

    public function exportCsv(Request $request, $id): StreamedResponse
    {
        $date = $request->input('date', now()->format('Y-m'));
        $user = User::find($id);
        $attendances = Attendance::where('user_id', $id)
            ->where('work_date', 'LIKE', '%' . $date . '%')
            ->orderBy('work_date', 'desc')
            ->get();

        $fileName = "atttendance_{$user->name}_{$date}.csv";

        return response()->streamDownload(function () use ($attendances) {
            $stream = fopen('php://output', 'w');

            fputcsv($stream, ['日付', '出勤', '退勤', '休憩', '合計']);

            foreach ($attendances as $attendance) {
                $breakMinutes = $attendance->totalBreakMinutes();
                $workMinutes = $attendance->totalWorkMinutes();

                fputcsv($stream, [
                    $attendance->work_date->locale('ja')->isoFormat('MM/DD(ddd)'),
                    $attendance->clock_in_at->format('H:i'),
                    optional($attendance->clock_out_at)->format('H:i'),
                    sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60),
                    sprintf('%02d:%02d', intdiv($workMinutes, 60), $workMinutes % 60)
                ]);
            }

            fclose($stream);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
