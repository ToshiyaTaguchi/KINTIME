@extends('layouts.default')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/attendance/staff.css') }}">
@endsection

@section('link')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item">
            <a href="{{ route('admin.attendance.list') }}" class="header-nav__link header-nav__link">
                勤怠一覧
            </a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('admin.staff.list') }}" class="header-nav__link">
                スタッフ一覧
            </a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('admin.attendance_correction.list') }}" class="header-nav__link">
                申請一覧
            </a>
        </li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header-nav__link">ログアウト</button>
        </form>
    </ul>
</nav>
@endsection

@section('content')

<section class="attendance attendance-staff">

    {{-- タイトル --}}
    <h1 class="attendance__title">
        {{ $attendances->first()?->user->name }} さんの勤怠
    </h1>

    {{-- 月切り替え（一般ユーザーと完全共通） --}}
    <div class="attendance__month-selector">
        <a href="{{ route('admin.attendance.staff', [
            'id' => request()->route('id'),
            'month' => $prevMonth
        ]) }}" class="attendance__month-btn attendance__month-btn--prev">
            ← 前月
        </a>

        <div class="attendance__month-display">
            <span class="attendance__month-icon">📅</span>
            {{ $currentMonth->format('Y/m') }}
        </div>

        <a href="{{ route('admin.attendance.staff', [
            'id' => request()->route('id'),
            'month' => $nextMonth
        ]) }}" class="attendance__month-btn attendance__month-btn--next">
            翌月 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="attendance-table">
        <thead class="attendance-table__head">
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody class="attendance-table__body">
            @php
            $attendanceMap = $attendances->keyBy(
            fn ($a) => $a->date->format('Y-m-d')
            );
            $start = $currentMonth->copy()->startOfMonth();
            $end = $currentMonth->copy()->endOfMonth();
            @endphp

            @for ($date = $start->copy(); $date <= $end; $date->addDay())
                @php
                $attendance = $attendanceMap[$date->format('Y-m-d')] ?? null;
                @endphp
                <tr>
                    {{-- 日付（曜日付き・日本語） --}}
                    <td>
                        {{ $date->locale('ja')->translatedFormat('n/j(D)') }}
                    </td>

                    {{-- 出勤 --}}
                    <td>
                        {{ toZenkaku($attendance?->formatted_clock_in ?? '') }}
                    </td>

                    {{-- 退勤 --}}
                    <td>
                        {{ toZenkaku($attendance?->formatted_clock_out ?? '') }}
                    </td>

                    {{-- 休憩 --}}
                    <td>
                        {{ toZenkaku($attendance?->total_break_time ?? '') }}
                    </td>

                    {{-- 合計 --}}
                    <td>
                        {{ toZenkaku($attendance?->total_working_time ?? '') }}
                    </td>

                    {{-- 詳細 --}}
                    <td>
                        @if ($attendance)
                        <a href="{{ route('admin.attendance.detail', $attendance->id) }}"
                            class="attendance-table__detail-link">
                            詳細
                        </a>
                        @endif
                    </td>
                </tr>
                @endfor
        </tbody>
    </table>

    {{-- CSV出力 --}}
    <div class="attendance-staff__csv">
        <form method="GET" action="{{ route('admin.attendance.staff.csv', request()->route('id')) }}">
            <input type="hidden" name="month" value="{{ $currentMonth->format('Y-m') }}">
            <button type="submit" class="attendance-staff__csv-button">
                CSV出力
            </button>
        </form>
    </div>

</section>

@endsection