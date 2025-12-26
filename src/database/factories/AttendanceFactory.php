<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = \App\Models\Attendance::class;

    public function definition()
    {
        // Faker で日付を生成（Carbon に変換）
        $date = Carbon::instance($this->faker->dateTimeBetween('-2 months', 'now'));

        // 出勤時間 9:00〜10:59
        $clock_in = $date->copy()->setTime(rand(9, 10), rand(0, 59), 0);

        // 退勤時間 17:00〜18:59
        $clock_out = $date->copy()->setTime(rand(17, 18), rand(0, 59), 0);

        // 休憩 1時間固定
        $break_time = 60;

        // 合計労働時間 = 退勤 - 出勤 - 休憩
        $total_time = $clock_out->diffInMinutes($clock_in) - $break_time;

        return [
            'user_id'    => 1, // ダミーユーザーID
            'work_date' => $this->faker->dateTimeBetween('first day of this month', 'last day of this month'),
            'clock_in'   => $clock_in->format('Y-m-d H:i:s'),   // datetime 型に対応
            'clock_out'  => $clock_out->format('Y-m-d H:i:s'),  // datetime 型に対応
            'break_time' => $break_time,
            'total_time' => $total_time,
            'status'     => $this->faker->randomElement(['勤務外', '出勤中', '休憩中', '退勤済']),
            'notes'      => $this->faker->sentence(),
        ];
    }
}
