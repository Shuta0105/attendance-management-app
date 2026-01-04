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

    public function test_selected_data_displayed_in_detail_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now(),
            'clock_out_at' => now(),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get("/admin/attendance/{$attendance->id}");
        $response->assertSee($user->name);
    }

    public function test_clock_in_at_must_be_before_clock_out_at()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now(),
            'clock_out_at' => now(),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->post("/admin/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(15, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(14, 0)->format('H:i'),
            'reason' => 'test',
        ]);

        $response->assertSessionHasErrors([
            'clock_in_at' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_break_start_at_must_be_before_clock_out_at()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->post("/admin/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(17, 0)->format('H:i'),
            'breaks' => [
                $break->id => [
                    'break_start_at' => now()->setTime(18, 0)->format('H:i'),
                    'break_end_at' => now()->setTime(13, 0)->format('H:i'),
                ],
            ],
            'reason' => 'test',
        ]);

        $response->assertSessionHasErrors([
            "breaks.{$break->id}.break_start_at" => '休憩時間が不適切な値です'
        ]);
    }

    public function test_break_end_at_must_be_before_clock_out_at()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
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

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->post("/admin/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(17, 0)->format('H:i'),
            'breaks' => [
                $break->id => [
                    'break_start_at' => now()->setTime(13, 0)->format('H:i'),
                    'break_end_at' => now()->setTime(18, 0)->format('H:i'),
                ],
            ],
            'reason' => 'test',
        ]);

        $response->assertSessionHasErrors([
            "breaks.{$break->id}.break_end_at" => '休憩時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_admin_request_requires_reason()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now(),
            'clock_out_at' => now(),
        ]);

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->post("/admin/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => '',
        ]);

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください'
        ]);
    }
}
