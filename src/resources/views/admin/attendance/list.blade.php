{{-- resources/views/admin/attendance/list.blade.php --}}

@extends('layouts.default')

@section('title', '管理者用勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/list.css') }}">
@endsection

@section('link')

<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a href="{{ route('admin.attendance.list') }}"
                class="header-nav__link header-nav__link--active">勤怠一覧</a></li>
        <li class="header-nav__item"><a href="{{ route('admin.staff.list') }}" class="header-nav__link">スタッフ一覧</a></li>
        <li class="header-nav__item"><a href="{{ route('admin.attendance_correction.list') }}"
                class="header-nav__link">申請一覧</a>
        </li>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="header-nav__link">ログアウト</button>
        </form>
    </ul>
</nav>
@endsection

@section('content')

<section class="attendance">
    <h2 class="attendance__title">{{ $targetDate->format('Y年n月j日') }}の勤怠</h2>

    {{-- 日付切り替え --}}
    <div class="attendance__month-selector">
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate->format('Y-m-d')]) }}"
            class="attendance__month-btn attendance__month-btn--prev">
            ← 前日
        </a>

        <div class="attendance__month-display">
            <span class="attendance__month-icon">📅</span>
            {{ $targetDate->format('Y/m/d') }}
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => $nextDate->format('Y-m-d')]) }}"
            class="attendance__month-btn attendance__month-btn--next">
            翌日 →
        </a>
    </div>

    {{-- 勤怠テーブル --}}
    <table class="attendance-table">
        <thead class="attendance-table__head">
            <tr>
                <th>名前</th>
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
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->formatted_clock_in ?? '' }}</td>
                <td>{{ $attendance->formatted_clock_out ?? '' }}</td>
                <td>{{ $attendance->total_break_time ?? '' }}</td>
                <td>{{ $attendance->total_working_time ?? '' }}</td>
                <td>
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}"
                        class="attendance-table__detail-link">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

</section>

@endsection