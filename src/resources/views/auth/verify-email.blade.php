@extends('layouts.default')

@section('title', 'メール認証')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify">

    <p class="verify__text">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>

    {{-- 認証はこちらから --}}
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="verify__button">
            認証はこちらから
        </button>
    </form>

    {{-- 再送エリア（ボタン or メッセージ） --}}
    <div class="verify__resend">
        @if (session('message'))
        <p class="verify__message">{{ session('message') }}</p>
        @else
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify__resend-button">
                認証メールを再送する
            </button>
        </form>
        @endif
    </div>

</div>
@endsection