{{-- resources/views/admin/staff/list.blade.php --}}
@extends('layouts.default')

@section('title', 'スタッフ一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff/list.css') }}">
@endsection

@section('link')
<nav class="header-nav">
    <ul class="header-nav__list">
        <li class="header-nav__item">
            <a href="{{ route('admin.attendance.list') }}" class="header-nav__link">
                勤怠一覧
            </a>
        </li>
        <li class="header-nav__item">
            <a href="{{ route('admin.staff.list') }}" class="header-nav__link header-nav__link--active">
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

<section class="staff">
    {{-- タイトル --}}
    <h2 class="staff__title">スタッフ一覧</h2>

    {{-- スタッフ一覧テーブル --}}
    <table class="staff-table">
        <thead class="staff-table__head">
            <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>月次勤怠</th>
            </tr>
        </thead>

        <tbody class="staff-table__body">
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('admin.attendance.staff', $user->id) }}" class="staff-table__link">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</section>

@endsection