<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate([
            'id' => 1,
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('Mcbf0476'),
            'role_id' => 1,
            'department_id' => 1,
        ]);

        // 残り29人をFactoryで作成
        User::factory()->count(29)->create();
    }
}