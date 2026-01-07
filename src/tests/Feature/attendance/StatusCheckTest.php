<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_show_correctly_when_out_of_work()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test_display_show_correctly_when_in_work()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->post('/attendance/clock-in');

        $response->assertSee('出勤中');
    }

    public function test_display_show_correctly_when_during_break()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $response = $this->post('/attendance/break-start');

        $response->assertSee('休憩中');
    }

    public function test_display_show_correctly_when_after_work()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $this->post('/attendance/clock-in');

        $response = $this->post('/attendance/clock-out');

        $response->assertSee('退勤済');
    }
}
