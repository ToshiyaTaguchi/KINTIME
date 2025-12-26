<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /* ==========================
       勤怠ステータス定数
    ========================== */
    const STATUS_OFF     = '勤務外';
    const STATUS_WORKING = '出勤中';
    const STATUS_BREAK   = '休憩中';
    const STATUS_DONE    = '退勤済';

    /* ==========================
       保存可能カラム
    ========================== */
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'total_time',
        'status',
        'notes',
    ];

    /* ==========================
       キャスト
    ========================== */
    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
        'total_time' => 'datetime:H:i',
    ];

    /* ==========================
       リレーション
    ========================== */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    public function approvalStatus()
    {
        return $this->belongsTo(ApprovalStatus::class);
    }

    /* ==========================
       Model イベント
       - 保存時に total_time を自動計算
    ========================== */
    protected static function booted()
    {
        static::saving(function (Attendance $attendance) {

            // 出勤・退勤が揃っている場合のみ計算
            if ($attendance->clock_in && $attendance->clock_out) {

                $minutes = $attendance->calculateTotalWorkingMinutes();

                // time 型カラム用（H:i:s）
                $attendance->total_time = sprintf(
                    '%02d:%02d:00',
                    intdiv($minutes, 60),
                    $minutes % 60
                );

                // 自動的に退勤済みに
                $attendance->status = self::STATUS_DONE;
            }
        });
    }

    /* ==========================
       計算ロジック（保存用）
    ========================== */

    /**
     * 勤務合計（分）※保存用
     */
    public function calculateTotalWorkingMinutes(): int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 0;
        }

        $workMinutes = $this->clock_in->diffInMinutes($this->clock_out);

        $breakMinutes = $this->total_break_minutes;

        return max(0, $workMinutes - $breakMinutes);
    }

    /**
     * 休憩合計（分）
     */
    public function getTotalBreakMinutesAttribute(): int
    {
        return $this->breaks->sum(function ($break) {
            if ($break->break_start && $break->break_end) {
                return $break->break_start->diffInMinutes($break->break_end);
            }
            return 0;
        });
    }

    /* ==========================
       表示用 Accessor
    ========================== */

    /**
     * 休憩合計（H:i 表示）
     */
    public function getTotalBreakTimeAttribute(): string
    {
        $minutes = $this->total_break_minutes;

        return sprintf(
            '%02d：%02d',
            intdiv($minutes, 60),
            $minutes % 60
        );
    }

    /**
     * 勤務合計（H:i 表示）
     */
    public function getTotalWorkingTimeAttribute(): string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return '';
        }

        $totalMinutes = $this->calculateTotalWorkingMinutes();

        return sprintf(
            '%02d：%02d',
            intdiv($totalMinutes, 60),
            $totalMinutes % 60
        );
    }

    /**
     * 出勤時刻（表示用）
     */
    public function getFormattedClockInAttribute(): string
    {
        return $this->clock_in
            ? $this->clock_in->format('H：i')
            : '';
    }

    /**
     * 退勤時刻（表示用）
     */
    public function getFormattedClockOutAttribute(): string
    {
        return $this->clock_out
            ? $this->clock_out->format('H：i')
            : '';
    }

    /* ==========================
       業務ロジック系
    ========================== */

    /**
     * 承認待ちの修正申請があるか
     */
    public function hasPendingCorrection(): bool
    {
        return $this->corrections()
            ->where('approval_status_id', 1) // 承認待ち
            ->exists();
    }
}
