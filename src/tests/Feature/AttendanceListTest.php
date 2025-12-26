<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 自分の勤怠情報のみ表示される
     */
    public function test_user_can_see_only_their_attendances()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::create(2025, 12, 2),
        ]);

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::create(2025, 12, 3),
        ]);

        Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'date' => Carbon::create(2025, 12, 4),
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(route('attendance.list'));

        $response->assertStatus(200);
        $response->assertSee('12/2');
        $response->assertSee('12/3');
        $response->assertDontSee('12/4');
    }

    /**
     * 勤怠一覧画面に現在の月が表示される
     */
    public function test_current_month_is_displayed()
    {
        $user = User::factory()->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(route('attendance.list'));

        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->format('Y/m'));
    }

    /**
     * 「前月」を押下すると前月の勤怠情報が表示される
     */
    public function test_previous_month_attendances_are_displayed()
    {
        $user = User::factory()->create();
        $prevMonth = Carbon::now()->subMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $prevMonth->copy()->startOfMonth()->addDay(),
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(
            route('attendance.list', ['month' => $prevMonth->format('Y-m')])
        );

        $response->assertStatus(200);
        $response->assertSee($prevMonth->format('Y/m'));
    }

    /**
     * 「翌月」を押下すると翌月の勤怠情報が表示される
     */
    public function test_next_month_attendances_are_displayed()
    {
        $user = User::factory()->create();
        $nextMonth = Carbon::now()->addMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => $nextMonth->copy()->startOfMonth()->addDay(),
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(
            route('attendance.list', ['month' => $nextMonth->format('Y-m')])
        );

        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y/m'));
    }

    /**
     * 「詳細」を押下すると勤怠詳細画面に遷移できる
     */
    public function test_detail_link_navigates_to_attendance_detail()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::now(),
        ]);

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)->get(route('attendance.list'));

        $response->assertStatus(200);
        $response->assertSee(
            route('attendance.detail', $attendance->id)
        );
    }
}