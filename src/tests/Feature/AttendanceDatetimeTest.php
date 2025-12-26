<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceDatetimeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // role_id 2 の一般ユーザーを作成
        Role::factory()->create([
            'id' => 2,
            'name' => '一般ユーザー',
        ]);
    }

    /** @test */
    public function attendance_page_displays_current_datetime_correctly()
    {
        // テスト用に日時を固定
        $now = Carbon::create(2025, 12, 27, 10, 30);
        Carbon::setTestNow($now);

        // ユーザー作成
        $user = User::factory()->create([
            'role_id' => 2,
        ]);

        // ログイン
        /** @var \App\Models\User $user */
        $this->actingAs($user);

        // 勤怠打刻画面へアクセス
        $response = $this->get('/attendance');

        $response->assertStatus(200);

        // 日付と時刻を HTML に含むか確認
        $response->assertSee($now->isoFormat('YYYY年M月D日(ddd)'));
        $response->assertSee($now->format('H:i'));
    }
}