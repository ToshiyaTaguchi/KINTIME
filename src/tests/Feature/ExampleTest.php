<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // テストごとに DB をリセット

    /**
     * A basic feature test example.
     */
    public function test_example()
    {

        /** @var \App\Models\User $user */
        // Department や他テーブルに依存しないユーザーを作成
        $user = User::factory()->make([
            'department_id' => null, // Department 参照を切る
        ]);

        // 実際に DB に保存
        $user->save();

        // actingAs() で認証済みとしてアクセス
        $response = $this->actingAs($user)->get('/attendance/list');

        // 期待するステータスコードを確認（200 OK）
        $response->assertStatus(200);
    }
}
