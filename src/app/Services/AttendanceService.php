<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * 勤怠アクション実行
     */
    public function handle(Attendance $attendance, string $type): void
    {
        $now = Carbon::now();

        match ($type) {
            'clock_in'  => $this->clockIn($attendance, $now),
            'break_in'  => $this->breakIn($attendance, $now),
            'break_out' => $this->breakOut($attendance, $now),
            'clock_out' => $this->clockOut($attendance, $now),
            default     => null,
        };
    }

    /** =============================
     * 各アクション
     * ============================= */

    private function clockIn(Attendance $attendance, Carbon $now): void
    {
        if ($attendance->status !== Attendance::STATUS_OFF) {
            return;
        }

        $attendance->update([
            'clock_in' => $now->format('H:i:s'),
            'status'   => Attendance::STATUS_WORKING,
        ]);
    }

    private function breakIn(Attendance $attendance, Carbon $now): void
    {
        if ($attendance->status !== Attendance::STATUS_WORKING) {
            return;
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start'   => $now,
        ]);

        $attendance->update([
            'status' => Attendance::STATUS_BREAK,
        ]);
    }

    private function breakOut(Attendance $attendance, Carbon $now): void
    {
        if ($attendance->status !== Attendance::STATUS_BREAK) {
            return;
        }

        $latestBreak = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest('break_start')
            ->first();

        if (!$latestBreak) {
            return;
        }

        $latestBreak->update([
            'break_end' => $now,
        ]);

        $attendance->update([
            'status' => Attendance::STATUS_WORKING,
        ]);
    }

    private function clockOut(Attendance $attendance, Carbon $now): void
    {
        if ($attendance->status !== Attendance::STATUS_WORKING) {
            return;
        }

        $attendance->update([
            'clock_out' => $now->format('H:i:s'),
            'status'    => Attendance::STATUS_DONE,
        ]);
    }
}
