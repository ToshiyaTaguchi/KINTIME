<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1; // ダミーユーザーID

        // 今月＋過去2ヶ月
        $months = [
            Carbon::now()->subMonths(2),
            Carbon::now()->subMonths(1),
            Carbon::now(),
        ];

        foreach ($months as $month) {
            $daysInMonth = $month->daysInMonth;

            for ($i = 1; $i <= $daysInMonth; $i++) {
                $date = $month->copy()->startOfMonth()->addDays($i - 1);

                // 土日ならスキップ
                if ($date->isWeekend()) {
                    continue;
                }

                // 出勤時間（09:00〜09:45）
                $clockIn = Carbon::parse("09:00")
                    ->addMinutes(rand(0, 45))
                    ->format("H:i");

                // 退勤時間（17:00〜20:00）
                $clockOut = Carbon::parse("17:00")
                    ->addMinutes(rand(0, 180)) // 17:00〜20:00 の範囲
                    ->format("H:i");

                Attendance::create([
                    'user_id'   => $userId,
                    'date'      => $date->format('Y-m-d'),
                    'clock_in'  => $clockIn,
                    'clock_out' => $clockOut,
                    'status'    => '退勤済',
                    'notes'     => null,
                ]);
            }
        }
    }
}