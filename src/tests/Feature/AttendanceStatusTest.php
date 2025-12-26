<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider statusProvider
     */
    public function testAttendanceStatusDisplay($factoryMethod, $expectedText)
    {
        $user = User::factory()->create();

        // Attendance 作成
        $attendance = Attendance::factory()
            ->$factoryMethod()
            ->for($user)
            ->create();

        /** @var \App\Models\User $user */
        $response = $this->actingAs($user)
            ->get(route('attendance.index'));

        // ステータス表示が正しいことを確認
        $response->assertSee($expectedText);
    }

    /**
     * ステータスのデータプロバイダー
     */
    public function statusProvider()
    {
        return [
            ['off', Attendance::STATUS_OFF],
            ['working', Attendance::STATUS_WORKING],
            ['break', Attendance::STATUS_BREAK],
            ['done', Attendance::STATUS_DONE],
        ];
    }
}