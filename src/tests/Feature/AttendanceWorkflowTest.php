<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function full_attendance_workflow()
    {
        $user = User::factory()->create();

        // ----------------------------
        // 1. 勤務外（初期状態）
        // ----------------------------
        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertSeeText('勤務外');
        $response->assertSee('<input type="hidden" name="type" value="clock_in">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="clock_out">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="break_in">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="break_out">', false);

        // ----------------------------
        // 2. 出勤
        // ----------------------------
        $this->post(route('attendance.store'), [
            'type' => 'clock_in',
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertSeeText('出勤中');
        $response->assertSee('<input type="hidden" name="type" value="clock_out">', false);
        $response->assertSee('<input type="hidden" name="type" value="break_in">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="clock_in">', false);

        // ----------------------------
        // 3. 休憩入
        // ----------------------------
        $this->post(route('attendance.store'), [
            'type' => 'break_in',
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertSeeText('休憩中');
        $response->assertSee('<input type="hidden" name="type" value="break_out">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="clock_out">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="break_in">', false);

        // ----------------------------
        // 4. 休憩戻
        // ----------------------------
        $this->post(route('attendance.store'), [
            'type' => 'break_out',
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertSeeText('出勤中');
        $response->assertSee('<input type="hidden" name="type" value="clock_out">', false);
        $response->assertSee('<input type="hidden" name="type" value="break_in">', false);

        // ----------------------------
        // 5. 退勤
        // ----------------------------
        $this->post(route('attendance.store'), [
            'type' => 'clock_out',
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        $response->assertSeeText('退勤済');
        $response->assertSeeText('お疲れさまでした。');
        $response->assertDontSee('<input type="hidden" name="type" value="clock_in">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="clock_out">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="break_in">', false);
        $response->assertDontSee('<input type="hidden" name="type" value="break_out">', false);
    }
}