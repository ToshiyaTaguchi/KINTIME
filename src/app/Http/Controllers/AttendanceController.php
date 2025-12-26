<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{

    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        return view('attendance.index', compact('attendance'));
    }

    /**
     * 勤怠登録（出勤・休憩・退勤）
     */

    public function store(Request $request, AttendanceService $service)
    {
        $request->validate([
            'type' => ['required', 'in:clock_in,break_in,break_out,clock_out'],
        ]);

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'date'    => Carbon::today(),
            ],
            [
                'status' => Attendance::STATUS_OFF,
            ]
        );

        DB::transaction(function () use ($service, $attendance, $request) {
            $service->handle($attendance, $request->type);
        });

        return redirect()->route('attendance.index');
    }



    /**
     * 勤怠詳細表示
     */
    public function show($id)
    {
        $attendance = Attendance::with([
            'breaks',
            'corrections'
        ])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // 承認待ち申請が存在するか
        $hasPendingCorrection = $attendance->corrections()
            ->where('approval_status_id', 1)
            ->exists();

        return view('attendance.detail', compact(
            'attendance',
            'hasPendingCorrection'
        ));
    }

    /**
     * 勤怠更新（修正申請前の編集）
     */
    public function update(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $attendance = Attendance::with('breaks')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $date = Carbon::parse($attendance->date)->format('Y-m-d');

            /** -------------------------
             * 出勤・退勤時刻
             * ------------------------ */
            $clockIn  = $request->clock_in
                ? Carbon::parse("$date {$request->clock_in}")
                : null;

            $clockOut = $request->clock_out
                ? Carbon::parse("$date {$request->clock_out}")
                : null;

            /** -------------------------
             * 休憩を全削除 → 再登録
             * ------------------------ */
            $attendance->breaks()->delete();

            $totalBreakMinutes = 0;

            foreach ($request->breaks ?? [] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {

                    $start = Carbon::parse("$date {$break['start']}");
                    $end   = Carbon::parse("$date {$break['end']}");

                    $totalBreakMinutes += $start->diffInMinutes($end);

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start'   => $start,
                        'break_end'     => $end,
                    ]);
                }
            }

            /** -------------------------
             * 合計勤務時間計算
             * ------------------------ */
            $totalWorkMinutes = null;

            if ($clockIn && $clockOut) {
                $totalWorkMinutes =
                    $clockIn->diffInMinutes($clockOut) - $totalBreakMinutes;
            }

            /** -------------------------
             * status 自動判定
             * ------------------------ */

            $status = Attendance::STATUS_OFF;

            if ($clockIn && !$clockOut) {
                $status = Attendance::STATUS_WORKING;
            }

            if ($clockIn && $clockOut) {
                $status = Attendance::STATUS_DONE;
            }


            /** -------------------------
             * 勤怠更新
             * ------------------------ */
            $attendance->update([
                'clock_in'   => $clockIn?->format('H:i:s'),
                'clock_out'  => $clockOut?->format('H:i:s'),
                'break_time' => $totalBreakMinutes
                    ? gmdate('H:i:s', $totalBreakMinutes * 60)
                    : null,
                'total_time' => $totalWorkMinutes
                    ? gmdate('H:i:s', $totalWorkMinutes * 60)
                    : null,
                'notes'      => $request->notes,
                'status'     => $status,
            ]);
        });

        return redirect()
            ->route('attendance.detail', $id)
            ->with('success', '勤怠情報を更新しました');
    }

    /**
     * 勤怠一覧
     */
    public function list(Request $request)
    {
        $currentMonth = $request->month
            ? Carbon::parse($request->month . '-01')
            : Carbon::now()->startOfMonth();

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $start = $currentMonth->copy()->startOfMonth();
        $end   = $currentMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        return view('attendance.list', compact(
            'attendances',
            'currentMonth',
            'prevMonth',
            'nextMonth'
        ));
    }
}