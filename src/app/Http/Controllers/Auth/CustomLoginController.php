<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 認証
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 権限によって振り分けも可能
            if (Auth::user()->can('admin')) {
                // 管理者が一般ユーザー用ログイン画面からログインした場合は弾く
                Auth::logout();
                return back()->withErrors(['email' => '管理者アカウントは管理者用ログイン画面からログインしてください。']);
            }

            return redirect()->intended('/attendance/list');
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ]);
    }
}
