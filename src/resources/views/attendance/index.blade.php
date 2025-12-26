@extends('layouts.default')

@php
use App\Models\Attendance;
$status = $attendance->status ?? Attendance::STATUS_OFF;
@endphp

@section('title', '出勤登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/index.css') }}">
@endsection

@section('link')
<nav class="header-nav">
    <ul class="header-nav__list">

        {{-- 退勤済の場合 --}}
        @if($status === Attendance::STATUS_DONE)
        <li class="header-nav__item">
            <a href="{{ route('attendance.list') }}" class="header-nav__link">
                今月の出勤一覧
            </a>
        </li>
        @else
        {{-- 通常表示 --}}
        <li class="header-nav__item">
            <a href="/attendance" class="header-nav__link header-nav__link--active">
                勤怠
            </a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('attendance.list') }}" class="header-nav__link">
                勤怠一覧
            </a>
        </li>
        @endif

        {{-- 申請は常に表示 --}}
        <li class="header-nav__item">
            <a href="{{ route('attendance_correction.list') }}" class="header-nav__link">
                申請
            </a>
        </li>

        {{-- ログアウト --}}
        <form method="POST" action="{{ route('logout') }}"
            onsubmit="this.querySelectorAll('button').forEach(b => b.disabled = true);">
            @csrf
            <button class="header-nav__link">ログアウト</button>
        </form>
    </ul>
</nav>
@endsection


@section('content')
<div class="attendance">

    {{-- ステータス --}}
    <div class="attendance__status">
        {{ $status }}
    </div>

    {{-- 日付 --}}
    <div class="attendance__date">
        {{ now()->isoFormat('YYYY年M月D日(ddd)') }}
    </div>

    {{-- 現在時刻 --}}
    <div class="attendance__time" id="current-time">
        {{ now()->format('H:i') }}
    </div>

    {{-- ボタン（退勤済は表示しない） --}}
    @if($status !== Attendance::STATUS_DONE)
    <div class="attendance__actions">

        {{-- 勤務外 --}}
        @if($status === Attendance::STATUS_OFF)
        <x-attendance-action-button type="clock_in" label="出勤" class="attendance__button attendance__button--dark" />
        @endif

        {{-- 出勤中 --}}
        @if($status === Attendance::STATUS_WORKING)
        <div class="attendance__actions-row">
            <x-attendance-action-button type="clock_out" label="退勤"
                class="attendance__button attendance__button--dark" />
            <x-attendance-action-button type="break_in" label="休憩入"
                class="attendance__button attendance__button--light" />
        </div>
        @endif


        {{-- 休憩中 --}}
        @if($status === Attendance::STATUS_BREAK)
        <x-attendance-action-button type="break_out" label="休憩戻" class="attendance__button attendance__button--light" />
        @endif

    </div>
    @endif


    {{-- 退勤後メッセージ --}}
    @if($status === Attendance::STATUS_DONE)
    <p class="attendance__message">
        お疲れさまでした。
    </p>
    @endif

</div>

{{-- 時刻更新用 --}}
<script>
function updateCurrentTime() {
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');

    const timeEl = document.getElementById('current-time');
    if (timeEl.textContent !== `${h}:${m}`) {
        timeEl.textContent = `${h}:${m}`;
    }
}

// 初回表示
updateCurrentTime();

// 1秒ごとに更新
setInterval(updateCurrentTime, 1000);
</script>
@endsection