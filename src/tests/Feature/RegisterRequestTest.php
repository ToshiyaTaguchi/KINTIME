<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase; // DBをテストごとにリセット

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

        $response->assertSessionHasErrors('name');
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

        $response->assertSessionHasErrors('email');
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

        $response->assertSessionHasErrors('password');
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

        $response->assertSessionHasErrors('password');
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

        $response->assertSessionHasErrors('password');
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

        // コントローラのリダイレクト先に合わせて修正
        $response->assertRedirect('/attendance/list');

        // DBに登録されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'Toshiya',
            'email' => 'test@example.com',
            'role_id' => 2,
        ]);
    }
}
