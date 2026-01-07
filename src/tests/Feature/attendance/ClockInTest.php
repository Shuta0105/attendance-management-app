<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    public function test_clock_in_button_work_properly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        $response = $this->post('/attendance/clock-in');
        $response->assertSee('出勤中');
    }

    public function test_user_after_work_does_not_see_clock_in_button()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $this->post('/attendance/clock-out');

        $response = $this->get('/attendance');

        $response->assertDontSee('出勤');
    }

    public function test_user_in_work_can_see_clock_in_time_in_list_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $response = $this->get('/attendance/list');

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response->assertSee($attendance->clock_in_at->format('H:i'));
    }
}
