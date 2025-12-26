<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // スタッフ一覧
    public function index()
    {
        // 全ユーザーを取得
        $users = User::orderBy('id')->get();

        return view('admin.staff.list', compact('users'));
    }
}