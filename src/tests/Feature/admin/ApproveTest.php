<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Request;
use App\Models\RequestDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApproveTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_requests_displayed_in_request_list_page()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin'
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

        /** @var User $admin */
        $this->actingAs($admin);

        $response = $this->get('/stamp_correction_request/list');

        $response->assertSee($user->name);
    }

    public function test_approved_request_displayed_in_approved_page()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin'
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

        /** @var User $admin */
        $this->actingAs($admin);

        $request = Request::where('attendance_id', $attendance->id)->first();
        $this->post("/stamp_correction_request/approve/{$request->id}");

        $response = $this->get('/stamp_correction_request/list?status=approved');
        $response->assertSee('test');
    }

    public function test_request_detail_has_necessary_information()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin'
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

        /** @var User $admin */
        $this->actingAs($admin);

        $request = Request::where('attendance_id', $attendance->id)->first();
        $requestDetail = RequestDetail::where('request_id', $request->id)->first();
        $this->post("/stamp_correction_request/approve/{$request->id}");

        $response = $this->get("/stamp_correction_request/approve/{$request->id}");

        $response->assertSee($user->name);
        $response->assertSee($attendance->work_date->format('Y年'));
        $response->assertSee($attendance->work_date->format('n月j日'));
        $response->assertSee($requestDetail->clock_in_at);
        $response->assertSee($requestDetail->clock_out_at);
        $response->assertSee($request->reason);
    }

    public function test_admin_can_approve_request_correctly()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' => 'admin'
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

        /** @var User $admin */
        $this->actingAs($admin);

        $request = Request::where('attendance_id', $attendance->id)->first();
        $this->post("/stamp_correction_request/approve/{$request->id}");

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in_at' => now()->setTime(9, 0),
            'clock_out_at' => now()->setTime(16, 0)
        ]);
    }
}
