<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }

    /**
     * 認証処理
     */
    public function authenticate(): void
    {
        $credentials = $this->only('email', 'password');

        // まず通常の認証
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        }

        // 認証成功後に管理者かチェック
        $user = Auth::user();
        if ($user->role_id !== 1) { // role_id=1 を管理者とする例
            Auth::logout(); // 認証解除
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        }
    }
}
