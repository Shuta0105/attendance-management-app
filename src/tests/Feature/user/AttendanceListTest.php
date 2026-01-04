<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_all_attendances_are_displayed()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now(),
            'clock_out_at' => now(),
        ]);

        $response = $this->get('/attendance/list');

        $date = $attendance->work_date->locale('ja')->isoFormat('MM/DD(ddd)');

        $response->assertSee($date);
    }

    public function test_current_month_shows_in_list_display()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $month = now()->format('m');

        $response->assertSee($month);
    }

    public function test_attendances_shows_in_last_month()
    {
        $now = Carbon::create(2026, 2, 5);
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $previous = now()->copy()->subMonth();

        $year = $previous->format('Y');
        $month = $previous->format('m');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $previous->toDateString(),
            'clock_in_at' => $previous->copy()->setTime(9, 0),
            'clock_out_at' => $previous->copy()->setTime(18, 0),
        ]);

        $response = $this->get("/attendance/list?date={$year}-{$month}");

        $expectDate = Carbon::parse($attendance->work_date)->locale('ja')->isoFormat('MM/DD(ddd)');

        $response->assertSee($expectDate);
    }

    public function test_attendances_shows_in_next_month()
    {
        $now = Carbon::create(2026, 2, 5);
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $next = now()->copy()->addMonth();

        $year = $next->format('Y');
        $month = $next->format('m');

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $next->toDateString(),
            'clock_in_at' => $next->copy()->setTime(9, 0),
            'clock_out_at' => $next->copy()->setTime(18, 0),
        ]);

        $response = $this->get("/attendance/list?date={$year}-{$month}");

        $expectDate = Carbon::parse($attendance->work_date)->locale('ja')->isoFormat('MM/DD(ddd)');

        $response->assertSee($expectDate);
    }

    public function test_detail_button_goes_to_attendance_detail()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now(),
            'clock_out_at' => now(),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
    }
}
