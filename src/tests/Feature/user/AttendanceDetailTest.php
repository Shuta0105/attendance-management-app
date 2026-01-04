<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_username_is_displayed_in_detail_page()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
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

        $response->assertSee('テストユーザー');
    }

    public function test_selected_date_is_displayed_in_detail_page()
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

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $year = $attendance->work_date->format('Y年');
        $selectedDate = $attendance->work_date->format('n月j日');

        $response->assertSee($year);
        $response->assertSee($selectedDate);
    }

    public function test_clock_in_and_out_at_display_correctly()
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

        $clock_in_at = $attendance->clock_in_at->format('H:i');
        $clock_out_at = $attendance->clock_out_at->format('H:i');

        $response->assertSee($clock_in_at);
        $response->assertSee($clock_out_at);
    }

    public function test_break_times_display_correctly()
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

        $break = BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start_at' => now()->setTime(12, 0),
            'break_end_at' => now()->setTime(13, 0),
        ]);

        $response = $this->get("/attendance/detail/{$attendance->id}");

        $break_start_at = $break->break_start_at->format('H:i');
        $break_end_at = $break->break_end_at->format('H:i');

        $response->assertSee($break_start_at);
        $response->assertSee($break_end_at);
    }
}
