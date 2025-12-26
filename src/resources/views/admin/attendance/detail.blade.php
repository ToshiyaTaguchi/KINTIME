@extends('layouts.default')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance/detail.css') }}">
@endsection

{{-- ================================
   ▼ 管理者用ヘッダー
================================ --}}
@section('link')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item">
            <a href="{{ route('admin.attendance.list') }}" class="header-nav__link header-nav__link--active">勤怠一覧</a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('admin.staff.list') }}" class="header-nav__link">スタッフ一覧</a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('admin.attendance_correction.list') }}" class="header-nav__link">申請一覧</a>
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

    {{-- 成功メッセージ --}}
    @if (session('success'))
    <p class="attendance-detail__success">
        {{ session('success') }}
    </p>
    @endif

    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST"
        class="attendance-detail__form">
        @csrf
        @method('PATCH')

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
                        <span>{{ $attendance->date->format('Y年') }}</span>
                        <span>{{ $attendance->date->format('n月j日') }}</span>
                    </div>
                </td>
            </tr>

            {{-- 出勤・退勤 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">出勤・退勤</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__time-range">
                        <div>
                            <input type="time" name="clock_in" class="attendance-detail__input"
                                value="{{ old('clock_in', optional($attendance->clock_in)?->format('H:i')) }}"
                                @if($hasPendingCorrection) readonly @endif>
                            @error('clock_in')
                            <p class="attendance-detail__error">{{ $message }}</p>
                            @enderror
                        </div>

                        〜

                        <div>
                            <input type="time" name="clock_out" class="attendance-detail__input"
                                value="{{ old('clock_out', optional($attendance->clock_out)?->format('H:i')) }}"
                                @if($hasPendingCorrection) readonly @endif>
                            @error('clock_out')
                            <p class="attendance-detail__error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </td>
            </tr>

            {{-- 休憩1・2 --}}
            @foreach ([0, 1] as $i)
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">休憩{{ $i + 1 }}</th>
                <td class="attendance-detail__data">
                    <div class="attendance-detail__time-range">
                        <div>
                            <input type="time" name="breaks[{{ $i }}][start]" class="attendance-detail__input"
                                value="{{ old("breaks.$i.start", optional($attendance->breaks[$i]->break_start ?? null)?->format('H:i')) }}"
                                @if($hasPendingCorrection) readonly @endif>
                            @error("breaks.$i.start")
                            <p class="attendance-detail__error">{{ $message }}</p>
                            @enderror
                        </div>

                        〜

                        <div>
                            <input type="time" name="breaks[{{ $i }}][end]" class="attendance-detail__input"
                                value="{{ old("breaks.$i.end", optional($attendance->breaks[$i]->break_end ?? null)?->format('H:i')) }}"
                                @if($hasPendingCorrection) readonly @endif>
                            @error("breaks.$i.end")
                            <p class="attendance-detail__error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach

            {{-- 備考 --}}
            <tr class="attendance-detail__row">
                <th class="attendance-detail__header">備考</th>
                <td class="attendance-detail__data">
                    <div style="width: 316px;">
                        <textarea name="notes" class="attendance-detail__textarea" @if($hasPendingCorrection) readonly
                            @endif>{{ old('notes', $attendance->notes) }}</textarea>
                        @error('notes')
                        <p class="attendance-detail__error">{{ $message }}</p>
                        @enderror
                    </div>
                </td>
            </tr>

        </table>

        <div class="attendance-detail__actions">
            @if ($hasPendingCorrection)
            <p class="attendance-detail__pending-message">
                承認待ちのため修正はできません。
            </p>
            @else
            <button type="submit" class="attendance-detail__submit">
                修正
            </button>
            @endif
        </div>

    </form>
</div>
@endsection