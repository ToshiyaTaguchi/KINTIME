<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterRequestTest extends TestCase
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
    }

    /** @test */
    public function name_is_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    /** @test */
    public function email_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Toshiya',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /** @test */
    public function password_is_required()
    {
        $response = $this->post('/register', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /** @test */
    public function password_must_be_min_8_characters()
    {
        $response = $this->post('/register', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    /** @test */
    public function password_confirmation_must_match()
    {
        $response = $this->post('/register', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    /** @test */
    public function valid_data_can_be_saved()
    {
        $response = $this->post('/register', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 正常にリダイレクトされることを確認
        $response->assertRedirect('/attendance/list');

        // DBに保存されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'role_id' => 2,
        ]);

        // 登録されたユーザーでログイン済みか確認
        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user);

        // パスワードはハッシュ化されていることを確認
        $this->assertTrue(Hash::check('password123', $user->password));
    }
}