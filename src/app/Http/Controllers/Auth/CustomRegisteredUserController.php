<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class CustomRegisteredUserController extends Controller
{
    /**
     * 登録処理
     */
    public function store(RegisterRequest $request)
    {
        // バリデーション済みデータを取得
        $data = $request->validated();

        // ユーザー作成
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => 2, // 一般ユーザー
        ]);

        // メール認証を送る場合
        event(new Registered($user));

        // 自動ログインする場合
        auth()->login($user);

        // メール認証が必要なら /email/verify へ
        return redirect()->intended('/attendance/list');
    }
}
