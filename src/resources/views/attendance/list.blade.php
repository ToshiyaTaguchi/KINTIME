@extends('layouts.default')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('link')

<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a href="/attendance" class="header-nav__link">勤怠</a></li>
        <li class="header-nav__item"><a href="{{ route('attendance.list') }}"
                class="header-nav__link header-nav__link--active">勤怠一覧</a></li>
        <li class="header-nav__item"><a href="{{ route('attendance_correction.list') }}"
                class="header-nav__link header-nav__link">申請</a></li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header-nav__link">ログアウト</button>
        </form>
    </ul>
</nav>
@endsection

@section('content')

<section class="attendance">
    <h1 class="attendance__title">勤怠一覧</h1>

    {{-- 月切り替え --}}
    <div class="attendance__month-selector">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}"
            class="attendance__month-btn attendance__month-btn--prev">
            ← 前月
        </a>

        <div class="attendance__month-display">
            <span class="attendance__month-icon">📅</span>
            {{ $currentMonth->format('Y/m') }}
        </div>

        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}"
            class="attendance__month-btn attendance__month-btn--next">
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
            @foreach($attendances as $attendance)
            <tr>
                {{-- 日付（0埋めなし → 12/8(月)） --}}
                <td>{{ \Carbon\Carbon::parse($attendance->date)->locale('ja')->translatedFormat('n/j(D)') }}</td>

                {{-- 出勤 --}}
                <td>{{ toZenkaku($attendance->formatted_clock_in) }}</td>

                {{-- 退勤 --}}
                <td>{{ toZenkaku($attendance->formatted_clock_out) }}</td>

                {{-- 休憩（breaks テーブル由来） --}}
                <td>{{ toZenkaku($attendance->total_break_time) }}</td>

                {{-- 合計（8:00 のように表示） --}}
                <td>{{ toZenkaku($attendance->total_working_time) }}</td>

                {{-- 詳細 --}}
                <td>
                    <a href="{{ route('attendance.detail', $attendance->id) }}" class="attendance-table__detail-link">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

</section>

@endsection