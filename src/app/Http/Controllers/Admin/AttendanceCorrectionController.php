<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceCorrectionController extends Controller
{
    /**
     * 管理者用：申請一覧
     */
    public function index(Request $request)
    {
        // タブ判定（デフォルト：承認待ち）
        $tab = $request->query('tab', 'pending');

        // 承認待ち（approval_status_id = 1）
        $pendingCorrections = AttendanceCorrection::with([
            'attendance.user'
        ])
            ->where('approval_status_id', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        // 承認済み（approval_status_id = 2）
        $approvedCorrections = AttendanceCorrection::with([
            'attendance.user'
        ])
            ->where('approval_status_id', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.attendance_correction.list', compact(
            'tab',
            'pendingCorrections',
            'approvedCorrections'
        ));
    }
    /**
     * 修正申請詳細（承認画面）
     * GET
     */
    public function show($attendance_correct_request_id)
    {
        $attendanceCorrection = AttendanceCorrection::with([
            'attendance.user',
        ])->findOrFail($attendance_correct_request_id);

        return view('admin.attendance_correction.approve', compact(
            'attendanceCorrection'
        ));
    }

    /**
     * 修正申請 承認処理
     * PATCH
     */
    public function approve($attendance_correct_request_id)
    {
        $attendanceCorrection = AttendanceCorrection::with([
            'attendance.breaks',
        ])->findOrFail($attendance_correct_request_id);

        // すでに承認済みの場合は何もしない
        if ($attendanceCorrection->approval_status_id === 2) {
            return back();
        }

        DB::transaction(function () use ($attendanceCorrection) {

            $attendance = $attendanceCorrection->attendance;

            /* ==========================
               勤怠本体を修正内容で更新
               → Attendance::saving が走る
            ========================== */
            $attendance->update([
                'clock_in'  => $attendanceCorrection->corrected_clock_in,
                'clock_out' => $attendanceCorrection->corrected_clock_out,
                'notes'     => $attendanceCorrection->reason,
            ]);

            /* ==========================
               休憩を差し替え
               → BreakTime::saved / deleted が
                 Attendance::save() を自動実行
            ========================== */
            $attendance->breaks()->delete();

            foreach ($attendanceCorrection->corrected_breaks ?? [] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $break['start'],
                        'break_end'   => $break['end'],
                    ]);
                }
            }

            /* ==========================
               申請ステータスを承認済みに
            ========================== */
            $attendanceCorrection->update([
                'approval_status_id' => 2, // 承認済み
            ]);
        });

        return redirect()
            ->route('admin.attendance_correction.show', $attendanceCorrection->id)
            ->with('success', '修正申請を承認しました');
    }
}
