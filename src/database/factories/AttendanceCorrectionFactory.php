<?php

namespace Database\Factories;

use App\Models\AttendanceCorrection;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceCorrectionFactory extends Factory
{
    protected $model = AttendanceCorrection::class;

    public function definition()
    {
        return [
            // 👇 必ずテスト側で渡す前提
            'attendance_id' => null,
            'user_id'       => null,

            'corrected_clock_in'  => '09:00',
            'corrected_clock_out' => '18:00',

            'reason' => 'テスト用修正理由',

            // 1: 承認待ち / 2: 承認済み
            'approval_status_id' => 1,

            'corrected_breaks' => [
                [
                    'start' => '12:00',
                    'end'   => '13:00',
                ],
            ],
        ];
    }
}
