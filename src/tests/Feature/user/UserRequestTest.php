<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_in_at_must_be_before_clock_out_at()
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

        $response = $this->post("/attendance/modify/{$attendance->id}", [
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

        $response = $this->post("/attendance/modify/{$attendance->id}", [
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

        $response = $this->post("/attendance/modify/{$attendance->id}", [
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

    public function test_user_request_requires_reason()
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

        $response = $this->post("/attendance/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => '',
        ]);

        $response->assertSessionHasErrors([
            'reason' => '備考を記入してください'
        ]);
    }

    public function test_user_request_excuted_correctly()
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

        $response = $this->post("/attendance/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => 'test',
        ]);
        $response->assertStatus(302);

        $admin = User::factory()->create([
            'name' => '管理者',
            'role' => 'admin',
        ]);
        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee('test');

        $request = Request::where('attendance_id', $attendance->id)->first();
        $this->assertNotNull($request);
        $response = $this->get("/stamp_correction_request/approve/{$request->id}");
        $response->assertStatus(200);
    }

    public function test_all_requests_displayed_in_requests_page()
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

        $this->post("/attendance/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => 'test',
        ]);

        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee('test');
    }

    public function test_approved_request_displayed_in_approved_page()
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

        $this->post("/attendance/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => 'test',
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);
        /** @var User $admin */
        $this->actingAs($admin);

        $request = Request::where('attendance_id', $attendance->id)->first();
        $this->post("/stamp_correction_request/approve/{$request->id}");

        $response = $this->get('/stamp_correction_request/list?status=approved');
        $response->assertSee('test');
    }

    public function test_request_detail_link_to_attendance_detail()
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

        $this->post("/attendance/modify/{$attendance->id}", [
            'clock_in_at' => now()->setTime(9, 0)->format('H:i'),
            'clock_out_at' => now()->setTime(16, 0)->format('H:i'),
            'reason' => 'test',
        ]);

        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee('test');

        $response = $this->get("/attendance/detail/{$attendance->id}");
        $response->assertStatus(200);
    }
}
