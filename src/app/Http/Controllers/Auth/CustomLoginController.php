<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class CustomLoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        // LoginRequest の rules() と messages() が自動適用される
        $request->authenticate();

        $request->session()->regenerate();

        // 一般ユーザーか確認
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->can('admin')) {
            Auth::logout();
            return back()->withErrors([
                'email' => '管理者アカウントは管理者用ログイン画面からログインしてください。',
            ]);
        }

        return redirect()->intended('/attendance/list');
    }
}