@extends('layouts.default')

@section('title', '管理者用ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<section class="login">
    <h1 class="login__title">管理者用ログイン</h1>

    <form method="POST" action="{{ route('admin.login') }}" class="login__form">
        @csrf

        {{-- メールアドレス --}}
        <div class="login__group">
            <label for="email" class="login__label">メールアドレス</label>
            <input id="email" type="email" name="email" class="login__input" value="{{ old('email') }}">
            @error('email')
            <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="login__group">
            <label for="password" class="login__label">パスワード</label>
            <input id="password" type="password" name="password" class="login__input">
            @error('password')
            <p class="login__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 認証エラー --}}
        @if(session('status'))
        <p class="login__error">{{ session('status') }}</p>
        @endif

        <button type="submit" class="login__button">
            管理者ログインする
        </button>
    </form>
</section>
@endsection