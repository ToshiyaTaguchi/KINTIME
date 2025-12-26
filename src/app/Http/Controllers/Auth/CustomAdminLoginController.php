<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAdminLoginController extends Controller
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

            // 管理者権限を確認してリダイレクト
            if (Auth::user()->can('admin')) {
                return redirect()->intended('/admin/attendance/list');
            }

            // 万が一管理者用ログインで一般ユーザーがログインした場合
            Auth::logout();
            return back()->withErrors(['email' => '管理者アカウントでログインしてください。']);
        }

        return back()->withErrors([
            'email' => '認証に失敗しました。',
        ]);
    }
}
