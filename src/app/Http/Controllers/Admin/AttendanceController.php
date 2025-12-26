<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceController extends Controller
{
    /**
     * 管理者用：日次勤怠一覧
     */
    public function list(Request $request)
    {
        // 対象日（指定がなければ今日）
        $targetDate = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        // 前日・翌日
        $prevDate = $targetDate->copy()->subDay();
        $nextDate = $targetDate->copy()->addDay();

        // その日の全ユーザー勤怠を取得
        $attendances = Attendance::with(['user', 'breaks'])
            ->whereDate('date', $targetDate)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.list', compact(
            'attendances',
            'targetDate',
            'prevDate',
            'nextDate'
        ));
    }

    /**
     * 管理者用：勤怠詳細
     */
    public function show($id)
    {
        $attendance = Attendance::with([
            'user',
            'breaks',
            'corrections',
        ])->findOrFail($id);

        // 承認待ち申請が存在するか（Blade互換用）
        $hasPendingCorrection = $attendance->corrections()
            ->where('approval_status_id', 1)
            ->exists();

        return view('admin.attendance.detail', compact(
            'attendance',
            'hasPendingCorrection'
        ));
    }

    /**
     * スタッフ別勤怠一覧
     */
    public function staffAttendance(Request $request, $userId)
    {
        $currentMonth = $request->month
            ? Carbon::parse($request->month . '-01')
            : Carbon::now()->startOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $start = $currentMonth->copy()->startOfMonth();
        $end   = $currentMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        return view('admin.attendance.staff', compact(
            'attendances',
            'currentMonth',
            'prevMonth',
            'nextMonth'
        ));
    }

    /**
     * スタッフ別勤怠 CSV 出力（文字化け対策あり）
     */
    public function exportStaffAttendanceCsv(Request $request, $userId)
    {
        $currentMonth = $request->month
            ? Carbon::parse($request->month . '-01')
            : Carbon::now()->startOfMonth();

        $start = $currentMonth->copy()->startOfMonth();
        $end   = $currentMonth->copy()->endOfMonth();

        $attendances = Attendance::with(['breaks', 'user'])
            ->where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(
            function () use ($attendances, $start, $end) {

                $handle = fopen('php://output', 'w');

                // ★ 文字化け防止（BOM）
                fwrite($handle, "\xEF\xBB\xBF");

                // ヘッダ
                fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

                $monthlyTotalMinutes = 0;

                for ($date = $start->copy(); $date <= $end; $date->addDay()) {

                    $attendance = $attendances[$date->format('Y-m-d')] ?? null;

                    // 勤務時間（分）を加算
                    if ($attendance && $attendance->clock_in && $attendance->clock_out) {
                        $monthlyTotalMinutes += $attendance->calculateTotalWorkingMinutes();
                    }

                    fputcsv($handle, [
                        $date->format('Y/m/d'),
                        $attendance?->formatted_clock_in ?? '',
                        $attendance?->formatted_clock_out ?? '',
                        $attendance?->total_break_time ?? '',
                        $attendance?->total_working_time ?? '',
                    ]);
                }

                // ==========================
                // ★ 月合計 行（最終行）
                // ==========================
                $hours = intdiv($monthlyTotalMinutes, 60);
                $minutes = $monthlyTotalMinutes % 60;

                fputcsv($handle, [
                    '月合計',
                    '',
                    '',
                    '',
                    sprintf('%02d：%02d', $hours, $minutes),
                ]);

                fclose($handle);
            }
        );

        $fileName = sprintf(
            'attendance_%d_%s.csv',
            $userId,
            $currentMonth->format('Ym')
        );

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename="' . $fileName . '"'
        );

        return $response;
    }



    public function update(
        AdminAttendanceUpdateRequest $request,
        Attendance $attendance
    ) {
        if ($attendance->hasPendingCorrection()) {
            return back()->withErrors('承認待ちのため修正できません');
        }

        DB::transaction(function () use ($request, $attendance) {

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'notes'     => $request->notes,
            ]);

            $attendance->breaks()->delete();

            foreach ($request->breaks ?? [] as $break) {
                if ($break['start'] && $break['end']) {
                    $attendance->breaks()->create([
                        'break_start' => $break['start'],
                        'break_end'   => $break['end'],
                    ]);
                }
            }
            // 休憩更新後に再保存
            $attendance->save();
        });

        return redirect()
            ->route('admin.attendance.detail', $attendance->id)
            ->with('success', '勤怠を修正しました');
    }
}
