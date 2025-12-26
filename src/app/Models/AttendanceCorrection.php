<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'corrected_clock_in',
        'corrected_clock_out',
        'corrected_breaks',
        'reason',
        'approval_status_id',
    ];

    protected $casts = [
        'corrected_clock_in'  => 'datetime',
        'corrected_clock_out' => 'datetime',
        'corrected_breaks'    => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approvalStatus()
    {
        return $this->belongsTo(ApprovalStatus::class);
    }
}
