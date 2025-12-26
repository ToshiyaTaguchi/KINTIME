@extends('layouts.default')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

@section('link')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item"><a href="#" class="header-nav__link--active">勤怠</a></li>
        <li class="header-nav__item"><a href="{{ route('attendance.list') }}" class="header-nav__link">勤怠一覧</a></li>
        <li class="header-nav__item"><a href="{{ route('attendance_correction.list') }}" class="header-nav__link">申請</a>
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

    <h1 class="attendance-detail__title">勤怠詳細</h1>

    <form action="{{ route('attendance_correction.store', $attendance->id) }}" method="POST"
        class="attendance-detail__form">
        @csrf

        <table class="attendance-detail__table">

            {{-- 名前 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">名前</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__text-wrap attendance-detail__text-wrap--left">
                        {{ $attendance->user->name }}
                    </div>
                </td>
            </tr>

            {{-- 日付 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">日付</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__date-wrap">
                        <span class="attendance-detail__date-year">{{ $attendance->date->format('Y') }}年</span>
                        <span class="attendance-detail__date-md">{{ $attendance->date->format('n月j日') }}</span>
                    </div>
                </td>
            </tr>

            {{-- 出勤・退勤 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">出勤・退勤</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__time-range">
                        <input type="time" name="clock_in" class="attendance-detail__input"
                            value="{{ old('clock_in', optional($attendance->clock_in)?->format('H:i')) }}">
                        〜
                        <input type="time" name="clock_out" class="attendance-detail__input"
                            value="{{ old('clock_out', optional($attendance->clock_out)?->format('H:i')) }}">
                    </div>
                    {{-- 出勤・退勤のエラーは最初の1件だけ表示 --}}
                    @if($errors->has('clock_in'))
                    <p class="attendance-detail__error">{{ $errors->first('clock_in') }}</p>
                    @elseif($errors->has('clock_out'))
                    <p class="attendance-detail__error">{{ $errors->first('clock_out') }}</p>
                    @endif
                </td>
            </tr>

            {{-- 休憩1 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">休憩</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__time-range">
                        <input type="time" name="breaks[0][start]" class="attendance-detail__input"
                            value="{{ old('breaks.0.start', optional($attendance->breaks[0]->break_start ?? null)?->format('H:i')) }}">
                        〜
                        <input type="time" name="breaks[0][end]" class="attendance-detail__input"
                            value="{{ old('breaks.0.end', optional($attendance->breaks[0]->break_end ?? null)?->format('H:i')) }}">
                    </div>
                    {{-- 休憩1のエラーは最初の1件だけ表示 --}}
                    @if($errors->has('breaks.0.start'))
                    <p class="attendance-detail__error">{{ $errors->first('breaks.0.start') }}</p>
                    @elseif($errors->has('breaks.0.end'))
                    <p class="attendance-detail__error">{{ $errors->first('breaks.0.end') }}</p>
                    @endif
                </td>
            </tr>

            {{-- 休憩2 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">休憩2</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__time-range">
                        <input type="time" name="breaks[1][start]" class="attendance-detail__input"
                            value="{{ old('breaks.1.start', optional($attendance->breaks[1]->break_start ?? null)?->format('H:i')) }}">
                        〜
                        <input type="time" name="breaks[1][end]" class="attendance-detail__input"
                            value="{{ old('breaks.1.end', optional($attendance->breaks[1]->break_end ?? null)?->format('H:i')) }}">
                    </div>
                    {{-- 休憩2のエラーは最初の1件だけ表示 --}}
                    @if($errors->has('breaks.1.start'))
                    <p class="attendance-detail__error">{{ $errors->first('breaks.1.start') }}</p>
                    @elseif($errors->has('breaks.1.end'))
                    <p class="attendance-detail__error">{{ $errors->first('breaks.1.end') }}</p>
                    @endif
                </td>
            </tr>

            {{-- 備考 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">備考</th>
                <td class="attendance-detail__data">
                    <textarea name="reason"
                        class="attendance-detail__textarea">{{ old('reason', $attendance->reason) }}</textarea>
                    {{-- 備考エラー --}}
                    @error('reason')
                    <p class="attendance-detail__error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>

        </table>

        <div class="attendance-detail__actions">
            @if ($hasPendingCorrection)
            <p class="attendance-detail__pending-message">
                *承認待ちのため修正はできません。
            </p>
            @else
            <button type="submit" class="attendance-detail__submit">修正</button>
            @endif
        </div>
    </form>
</div>
@endsection