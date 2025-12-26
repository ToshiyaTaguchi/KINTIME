@extends('layouts.default')

@section('title', '修正申請承認')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_correction/approve.css') }}">
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
<div class="attendance-detail">

    <h1 class="attendance-detail__title">修正申請承認</h1>

    <table class="attendance-detail__table">

        {{-- 名前 --}}
        <tr class="attendance-detail__row">
            <th class="attendance-detail__header">名前</th>
            <td class="attendance-detail__data">
                <div class="attendance-detail__text-wrap attendance-detail__text-wrap--left">
                    {{ $attendanceCorrection->user->name }}
                </div>
            </td>
        </tr>

        {{-- 日付 --}}
        <tr class="attendance-detail__row">
            <th class="attendance-detail__header">日付</th>
            <td class="attendance-detail__data">
                <div class="attendance-detail__date-wrap">
                    <span>{{ $attendanceCorrection->attendance->date->format('Y年') }}</span>
                    <span>{{ $attendanceCorrection->attendance->date->format('n月j日') }}</span>
                </div>
            </td>
        </tr>

        {{-- 出勤・退勤 --}}
        <tr class="attendance-detail__row">
            <th class="attendance-detail__header">出勤・退勤</th>
            <td class="attendance-detail__data">
                <div class="attendance-detail__time-range">
                    <input type="time" class="attendance-detail__input"
                        value="{{ optional($attendanceCorrection->corrected_clock_in)?->format('H:i') }}" readonly>
                    〜
                    <input type="time" class="attendance-detail__input"
                        value="{{ optional($attendanceCorrection->corrected_clock_out)?->format('H:i') }}" readonly>
                </div>
            </td>
        </tr>

        {{-- 休憩 --}}
        @foreach ($attendanceCorrection->corrected_breaks ?? [] as $index => $break)
        <tr class="attendance-detail__row">
            <th class="attendance-detail__header">休憩{{ $index + 1 }}</th>
            <td class="attendance-detail__data">
                <div class="attendance-detail__time-range">
                    <input type="time" class="attendance-detail__input"
                        value="{{ $break['start'] ?? '' }}" readonly>
                    〜
                    <input type="time" class="attendance-detail__input"
                        value="{{ $break['end'] ?? '' }}" readonly>
                </div>
            </td>
        </tr>
        @endforeach

        {{-- 備考 --}}
        <tr class="attendance-detail__row">
            <th class="attendance-detail__header">備考</th>
            <td class="attendance-detail__data">
                <textarea class="attendance-detail__textarea" readonly>{{ $attendanceCorrection->reason }}</textarea>
            </td>
        </tr>

    </table>

    <div class="attendance-detail__actions">
        @if ($attendanceCorrection->approval_status_id === 2)
            <button class="attendance-detail__approve attendance-detail__approve--done" disabled>
                承認済み
            </button>
        @else
            <form method="POST" action="{{ route('admin.attendance_correction.approve', $attendanceCorrection->id) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="attendance-detail__approve">
                    承認
                </button>
            </form>
        @endif
    </div>

</div>
@endsection