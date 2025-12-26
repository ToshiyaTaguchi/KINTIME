@extends('layouts.default')

@section('title', 'ユーザーログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
<section class="login">
    <h1 class="login__title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}" class="login__form">
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

        {{-- 共通エラー（認証失敗） --}}
        @if(session('status'))
        <p class="login__error">{{ session('status') }}</p>
        @endif

        <button type="submit" class="login__button">ログインする</button>

        <div class="login__link-box">
            <a href="{{ route('register') }}" class="login__link">会員登録はこちら</a>
        </div>
    </form>
</section>
@endsection