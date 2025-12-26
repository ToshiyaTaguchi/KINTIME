<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    protected $casts = [
        'break_start' => 'datetime:H:i',
        'break_end'   => 'datetime:H:i',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    protected static function booted()
    {
        // 休憩が保存されたら勤怠を再保存
        static::saved(function (BreakTime $break) {
            if ($break->attendance) {
                $break->attendance->save();
            }
        });

        // 休憩が削除されたら勤怠を再保存
        static::deleted(function (BreakTime $break) {
            if ($break->attendance) {
                $break->attendance->save();
            }
        });
    }
}
