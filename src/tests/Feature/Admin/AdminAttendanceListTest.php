<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Attendance;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_today_attendance_list()
    {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $department = Department::factory()->create();

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'department_id' => $department->id,
        ]);

        Attendance::factory()->create([
            'user_id' => $admin->id,
            'date' => Carbon::today(),
        ]);

        /** @var \App\Models\User $admin */
        $this->actingAs($admin);

        $response = $this->get(route('admin.attendance.list'));

        $response->assertStatus(200);
        $response->assertSee(
            Carbon::today()->format('Y年m月d日')
        );
    }

    /** @test */
    public function admin_can_view_previous_day_attendance()
    {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $department = Department::factory()->create();

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'department_id' => $department->id,
        ]);

        $yesterday = Carbon::yesterday();

        Attendance::factory()->create([
            'user_id' => $admin->id,
            'date' => $yesterday,
        ]);

        /** @var \App\Models\User $admin */
        $this->actingAs($admin);

        $response = $this->get(
            route('admin.attendance.list', [
                'date' => $yesterday->format('Y-m-d')
            ])
        );

        $response->assertStatus(200);
        $response->assertSee(
            $yesterday->format('Y年m月d日')
        );
    }

    /** @test */
    public function admin_can_view_next_day_attendance()
    {
        $adminRole = Role::factory()->create(['name' => 'admin']);
        $department = Department::factory()->create();

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'department_id' => $department->id,
        ]);

        $tomorrow = Carbon::tomorrow();

        Attendance::factory()->create([
            'user_id' => $admin->id,
            'date' => $tomorrow,
        ]);


        /** @var \App\Models\User $admin */
        $this->actingAs($admin);

        $response = $this->get(
            route('admin.attendance.list', [
                'date' => $tomorrow->format('Y-m-d')
            ])
        );

        $response->assertStatus(200);
        $response->assertSee(
            $tomorrow->format('Y年m月d日')
        );
    }

    /** @test */
    public function guest_is_redirected_to_login()
    {
        $this->get(route('admin.attendance.list'))
            ->assertRedirect('/login');
    }

    /** @test */
    public function general_user_can_access_admin_attendance_list()
    {
        $userRole = Role::factory()->create(['name' => 'user']);
        $department = Department::factory()->create();

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'department_id' => $department->id,
        ]);


        /** @var \App\Models\User $user */
        $this->actingAs($user);

        $this->get(route('admin.attendance.list'))
            ->assertStatus(200);
    }
}