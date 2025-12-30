<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\ApprovalStatus;
use Carbon\Carbon;

class AdminAttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();

        // ===== Role と Department を作成 =====
        $role = Role::factory()->create(['name' => 'admin']);
        $department = Department::factory()->create(['name' => '総務']);

        // ===== ApprovalStatus を作成 =====
        $this->pendingStatus = ApprovalStatus::factory()->create(['name' => '承認待ち']);
        $this->approvedStatus = ApprovalStatus::factory()->create(['name' => '承認済み']);

        // ===== 管理者ユーザーを作成 =====
        $this->admin = User::factory()->create([
            'role_id' => $role->id,
            'department_id' => $department->id,
            'email' => 'admin@example.com',
        ]);
    }

    /** @test */
    public function pending_corrections_are_displayed()
    {
        // 勤怠を作成
        $attendance = Attendance::factory()->create([
            'user_id' => $this->admin->id,
            'date' => Carbon::today(),
        ]);

        // 承認待ちの修正申請を作成
        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'approval_status_id' => $this->pendingStatus->id,
            'reason' => '承認待ち理由',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attendance_correction.list'));
        $response->assertStatus(200);
        $response->assertSee('承認待ち理由');
    }

    /** @test */
    public function approved_corrections_are_displayed()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->admin->id,
            'date' => Carbon::today(),
        ]);

        AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'approval_status_id' => $this->approvedStatus->id,
            'reason' => '承認済み理由',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.attendance_correction.list'));
        $response->assertStatus(200);
        $response->assertSee('承認済み理由');
    }

    /** @test */
    public function correction_detail_is_displayed_correctly()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->admin->id,
            'date' => Carbon::today(),
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'approval_status_id' => $this->pendingStatus->id,
            'reason' => '詳細確認理由',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.attendance_correction.show', $correction->id));

        $response->assertStatus(200);
        $response->assertSee('詳細確認理由');
    }

    /** @test */
    public function admin_can_approve_correction_and_update_attendance()
    {
        $attendance = Attendance::factory()->create([
            'user_id' => $this->admin->id,
            'date' => Carbon::today(),
        ]);

        $correction = AttendanceCorrection::factory()->create([
            'attendance_id' => $attendance->id,
            'approval_status_id' => $this->pendingStatus->id,
            'reason' => '承認処理理由',
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.attendance_correction.approve', $correction->id));

        $response->assertStatus(302); // リダイレクト
        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'approval_status_id' => $this->approvedStatus->id,
        ]);
    }
}
