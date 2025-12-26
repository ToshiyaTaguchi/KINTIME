@extends('layouts.default')

@section('title', '申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_correction/list.css') }}">
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
<div class="application-list">

    <h1 class="application-list__title">申請一覧</h1>

    {{-- タブ --}}
    <div class="application-list__tabs">
        <a href="{{ route('admin.attendance_correction.list', ['tab' => 'pending']) }}"
            class="application-list__tab {{ $tab === 'pending' ? 'is-active' : '' }}">
            承認待ち
        </a>

        <a href="{{ route('admin.attendance_correction.list', ['tab' => 'approved']) }}"
            class="application-list__tab {{ $tab === 'approved' ? 'is-active' : '' }}">
            承認済み
        </a>
    </div>

    {{-- 承認待ち --}}
    @if ($tab === 'pending')
    <section class="application-list__section">
        <table class="application-list__table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingCorrections as $correction)
                <tr>
                    <td class="status status--pending">承認待ち</td>
                    <td>{{ $correction->attendance->user->name }}</td>
                    <td>{{ $correction->attendance->date->format('Y/m/d') }}</td>
                    <td>{{ $correction->reason }}</td>
                    <td>{{ $correction->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a class="application-list__detail-link"
                            href="{{ route('admin.attendance_correction.show', $correction->id) }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="application-list__empty">
                        承認待ちの申請はありません
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>
    @endif

    {{-- 承認済み --}}
    @if ($tab === 'approved')
    <section class="application-list__section">
        <table class="application-list__table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($approvedCorrections as $correction)
                <tr>
                    <td class="status status--approved">承認済み</td>
                    <td>{{ $correction->attendance->user->name }}</td>
                    <td>{{ $correction->attendance->date->format('Y/m/d') }}</td>
                    <td>{{ $correction->reason }}</td>
                    <td>{{ $correction->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a class="application-list__detail-link"
                            href="{{ route('admin.attendance_correction.show', $correction->id) }}">
                            詳細
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="application-list__empty">
                        承認済みの申請はありません
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </section>
    @endif

</div>
@endsection