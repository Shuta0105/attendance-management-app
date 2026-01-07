<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_out_button_work_properly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->post('/attendance/clock-in');

        $response->assertSee('退勤');

        $response = $this->post('/attendance/clock-out');
        $response->assertSee('退勤済');
    }

    public function test_user_can_see_clock_out_time_in_attendance_list()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $this->post('/attendance/clock-out');

        $response = $this->get('/attendance/list');

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response->assertSee($attendance->clock_out_at->format('H:i'));
    }
}
