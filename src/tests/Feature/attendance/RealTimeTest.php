<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_real_time_in_attendace_display()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /** @var User $user */
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('setInterval(updateDate, 1000)');
    }
}
