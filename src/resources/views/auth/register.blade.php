@extends('layouts.default')

@section('title', '会員登録')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('link')
{{-- ログイン画面へのリンク --}}
@endsection

@section('content')
<section class="register">

    {{-- タイトル --}}
    <h1 class="register__title">会員登録</h1>

    {{-- フォーム --}}
    <form class="register__form" method="POST" action="{{ route('register') }}">
        @csrf

        {{-- 名前 --}}
        <div class="register__group">
            <label for="name" class="register__label">名前</label>
            <input type="text" name="name" id="name" class="register__input" value="{{ old('name') }}">
            @error('name')
            <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- メール --}}
        <div class="register__group">
            <label for="email" class="register__label">メールアドレス</label>
            <input type="email" name="email" id="email" class="register__input" value="{{ old('email') }}">
            @error('email')
            <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="register__group">
            <label for="password" class="register__label">パスワード</label>
            <input type="password" name="password" id="password" class="register__input">
            @error('password')
            <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- パスワード確認 --}}
        <div class="register__group">
            <label for="password_confirmation" class="register__label">パスワード確認</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="register__input">
            @error('password_confirmation')
            <p class="register__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 送信ボタン --}}
        <button type="submit" class="register__button">登録する</button>

    </form>

    {{-- ログイン画面へのリンク --}}
    <div class="register__link-box">
        <a href="{{ route('login') }}" class="register__link">ログインはこちら</a>
    </div>

</section>
@endsection