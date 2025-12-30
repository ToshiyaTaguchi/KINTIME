<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Role;
use App\Models\Department;
use Carbon\Carbon;

class AdminAttendanceDetailUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        $role = Role::factory()->create();
        $department = Department::factory()->create();

        return User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $department->id,
        ]);
    }

    private function createAttendance(User $user): Attendance
    {
        return Attendance::factory()->create([
            'user_id'  => $user->id,
            'date'     => Carbon::today(),
            'clock_in' => '09:00',
            'clock_out'=> '18:00',
            'notes'    => '通常勤務',
        ]);
    }

    /** @test */
    public function admin_can_view_correct_attendance_detail()
    {
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($admin);

        $this->actingAs($admin);

        $response = $this->get(
            route('admin.attendance.detail', $attendance->id)
        );

        $response->assertStatus(200);
        $response->assertSee($attendance->user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('通常勤務');
    }

    /** @test */
    public function clock_in_after_clock_out_returns_error()
    {
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($admin);

        $this->actingAs($admin);

        $response = $this->patch(
            route('admin.attendance.update', $attendance->id),
            [
                'clock_in'  => '19:00',
                'clock_out' => '18:00',
                'notes'     => 'テスト',
            ]
        );

        $response->assertSessionHasErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function break_start_after_clock_out_returns_error()
    {
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($admin);

        $this->actingAs($admin);

        $response = $this->patch(
            route('admin.attendance.update', $attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'notes'     => 'テスト',
                'breaks' => [
                    [
                        'start' => '19:00',
                        'end'   => '19:30',
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.start' => '休憩時間が不適切な値です',
        ]);
    }

    /** @test */
    public function break_end_after_clock_out_returns_error()
    {
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($admin);

        $this->actingAs($admin);

        $response = $this->patch(
            route('admin.attendance.update', $attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'notes'     => 'テスト',
                'breaks' => [
                    [
                        'start' => '17:00',
                        'end'   => '19:00',
                    ],
                ],
            ]
        );

        $response->assertSessionHasErrors([
            'breaks.0.end' => '休憩時間もしくは退勤時間が不適切な値です',
        ]);
    }

    /** @test */
    public function notes_is_required_for_update()
    {
        $admin = $this->createAdminUser();
        $attendance = $this->createAttendance($admin);

        $this->actingAs($admin);

        $response = $this->patch(
            route('admin.attendance.update', $attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'notes'     => '',
            ]
        );

        $response->assertSessionHasErrors([
            'notes' => '備考を記入してください',
        ]);
    }
}
