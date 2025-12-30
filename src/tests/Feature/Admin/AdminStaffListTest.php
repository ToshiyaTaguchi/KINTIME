<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser()
    {
        $adminRole = Role::factory()->create([
            'name' => 'admin',
        ]);

        return User::factory()->create([
            'role_id' => $adminRole->id,
        ]);
    }

    /**
     * 管理者がスタッフ一覧画面を表示できる
     */
    public function test_admin_can_view_staff_list_page()
    {
        $admin = $this->createAdminUser();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.staff.list'));

        $response->assertStatus(200);
        $response->assertSee('スタッフ一覧');
    }

    /**
     * 全一般ユーザーの氏名・メールアドレスが表示される
     */
    public function test_all_users_name_and_email_are_displayed()
    {
        $admin = $this->createAdminUser();

        $users = User::factory()->count(3)->create();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.staff.list'));

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    /**
     * 各ユーザーに勤怠詳細リンクが表示されている
     */
    public function test_staff_list_has_detail_links()
    {
        $admin = $this->createAdminUser();

        $user = User::factory()->create();

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.staff.list'));

        $response->assertSee(
            route('admin.attendance.staff', $user->id)
        );
    }
}
