<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class LoginRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // role_id 2 の一般ユーザーを作成
        Role::factory()->create([
            'id' => 2,
            'name' => '一般ユーザー',
        ]);

        // 一般ユーザーを1人登録
        User::factory()->create([
            'name' => 'Toshiya',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2,
        ]);
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** @test */
    public function password_is_required()
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function valid_user_can_login()
    {
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/attendance/list');

        $user = User::where('email', 'user@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }
}