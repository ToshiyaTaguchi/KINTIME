<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 一般ユーザーは必ず勤怠一覧へ
        return redirect()->intended('/attendance/list');
    }
}