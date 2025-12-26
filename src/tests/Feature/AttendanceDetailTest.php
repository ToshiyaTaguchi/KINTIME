<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 勤怠詳細画面にログインユーザーの情報が正しく表示される
     */
    public function test_attendance_detail_displays_correct_user_data()
    {
        // -------------------------
        // ユーザー作成 & ログイン
        // -------------------------
        $user = User::factory()->create([
            'name' => '山田 太郎',
        ]);

        /** @var \App\Models\User $user */
        $this->actingAs($user);

        // -------------------------
        // 勤怠データ作成
        // -------------------------
        $date = Carbon::create(2025, 1, 15);

        $attendance = Attendance::factory()->create([
            'user_id'   => $user->id,
            'date'      => $date,
            'clock_in'  => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        // -------------------------
        // 休憩データ作成（Factoryは使わない）
        // -------------------------
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => Carbon::parse('2025-01-15 12:00:00'),
            'break_end'     => Carbon::parse('2025-01-15 13:00:00'),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => Carbon::parse('2025-01-15 15:00:00'),
            'break_end'     => Carbon::parse('2025-01-15 15:15:00'),
        ]);

        // -------------------------
        // 勤怠詳細ページへアクセス
        // -------------------------
        $response = $this->get(
            route('attendance.detail', $attendance->id)
        );

        // -------------------------
        // ステータス確認
        // -------------------------
        $response->assertStatus(200);

        // -------------------------
        // 名前がログインユーザーの氏名になっている
        // -------------------------
        $response->assertSee('山田 太郎');

        // -------------------------
        // 日付が選択した日付になっている
        // -------------------------
        $response->assertSee('2025年');
        $response->assertSee('1月15日');

        // -------------------------
        // 出勤・退勤時間が一致している
        // -------------------------
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);

        // -------------------------
        // 休憩時間が一致している
        // -------------------------
        $response->assertSee('value="12:00"', false);
        $response->assertSee('value="13:00"', false);
        $response->assertSee('value="15:00"', false);
        $response->assertSee('value="15:15"', false);
    }
}