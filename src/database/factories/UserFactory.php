<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use App\Models\Role;

class UserFactory extends Factory
{
    protected $model = \App\Models\User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password123'), // 固定パスワード
            'role_id' => Role::factory(),             // Role を Factory で作成
            'department_id' => Department::factory(), // Department も Factory
            'remember_token' => Str::random(10),
        ];
    }
}