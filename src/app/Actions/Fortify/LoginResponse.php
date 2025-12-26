<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class LoginResponse implements LoginResponseContract
{
    /**
     * Handle the response after the user is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request)
    {
        $user = $request->user();

        // デバッグ用ログ
        Log::info('LoginResponse: user_id=' . $user->id . ' role_id=' . $user->role_id);
        Log::info('Gate admin: ' . (Gate::allows('admin') ? 'true' : 'false'));

        // 管理者かどうかでリダイレクト先を切り替え
        if (Gate::forUser($user)->allows('admin')) {
            return redirect('/admin/attendance/list');
        }

        // 一般ユーザーは自分の勤怠一覧へ
        return redirect()->intended('/attendance/list');
    }
}