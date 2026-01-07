<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_break_button_work_properly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->post('/attendance/clock-in');

        $response->assertSee('休憩入');

        $response = $this->post('/attendance/break-start');

        $response->assertSee('休憩中');
    }

    public function test_user_can_break_more_than_one_time()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $this->post('/attendance/break-start');

        $response = $this->post('/attendance/break-end');

        $response->assertSee('休憩入');
    }

    public function test_break_end_button_work_properly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $response = $this->post('/attendance/break-start');
        $response->assertSee('休憩戻');

        $response = $this->post('/attendance/break-end');
        $response->assertSee('出勤中');
    }

    public function test_user_can_end_break_more_than_one_time()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $this->post('/attendance/break-start');

        $this->post('/attendance/break-end');

        $response = $this->post('/attendance/break-start');
        $response->assertSee('休憩戻');
    }

    public function test_user_can_see_break_time_in_attendance_list()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $this->post('/attendance/break-start');

        $this->post('/attendance/break-end');

        $response = $this->get('/attendance/list');

        $attendance = Attendance::where('user_id', auth()->id())->first();
        $breakMinutes = $attendance->totalBreakMinutes();
        $formattedBreakMinutes = sprintf('%02d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60);

        $response->assertSee($formattedBreakMinutes);
    }
}
