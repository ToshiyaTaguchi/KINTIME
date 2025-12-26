<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    /**
     * 申請一覧（承認待ち / 承認済み）
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending'); // デフォルトは承認待ち

        $pendingCorrections = AttendanceCorrection::with('attendance')
            ->where('user_id', auth()->id())
            ->where('approval_status_id', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedCorrections = AttendanceCorrection::with('attendance')
            ->where('user_id', auth()->id())
            ->where('approval_status_id', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance_correction.list', compact(
            'tab',
            'pendingCorrections',
            'approvedCorrections'
        ));
    }

    /**
     * 修正申請の保存
     */
    public function store(AttendanceCorrectionRequest $request, Attendance $attendance)
    {
        // ✅ フォームリクエストでバリデーション済み
        $validated = $request->validated();
        $date = $attendance->date->format('Y-m-d');

        AttendanceCorrection::create([
            'attendance_id'      => $attendance->id,
            'user_id'            => Auth::id(),
            'corrected_clock_in' => $validated['clock_in']
                ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['clock_in'])
                : null,
            'corrected_clock_out' => $validated['clock_out']
                ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['clock_out'])
                : null,
            'corrected_breaks'   => $validated['breaks'] ?? [],
            'reason'             => $validated['reason'],
            'approval_status_id' => 1, // 承認待ち
        ]);

        return redirect()
            ->route('attendance.detail', $attendance->id)
            ->with('success', '勤怠修正申請を送信しました');
    }

    /**
     * 承認処理（管理者用）
     */
    public function approve($attendance_correct_request_id)
    {
        $attendanceCorrection = AttendanceCorrection::with(['attendance', 'attendance.breaks'])
            ->findOrFail($attendance_correct_request_id);

        if ($attendanceCorrection->approval_status_id !== 1) {
            return redirect()
                ->back()
                ->with('success', 'この申請はすでに処理済みです。');
        }

        DB::transaction(function () use ($attendanceCorrection) {
            $attendance = $attendanceCorrection->attendance;

            // 勤怠データ更新
            $attendance->update([
                'clock_in'  => $attendanceCorrection->corrected_clock_in,
                'clock_out' => $attendanceCorrection->corrected_clock_out,
            ]);

            // 休憩データ反映（全削除後に作成）
            $attendance->breaks()->delete();

            foreach ($attendanceCorrection->corrected_breaks ?? [] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $break['start'],
                        'break_end'   => $break['end'],
                    ]);
                }
            }

            // 承認ステータス更新
            $attendanceCorrection->update([
                'approval_status_id' => 2, // 承認済み
            ]);
        });

        return redirect()
            ->route('admin.attendance_correction.list')
            ->with('success', '修正申請を承認しました。');
    }
}
