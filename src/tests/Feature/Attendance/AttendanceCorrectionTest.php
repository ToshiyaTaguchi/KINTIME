<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->attendance = Attendance::factory()->create([
            'user_id'   => $this->user->id,
            'date'      => Carbon::today(),
            'clock_in'  => '09:00',
            'clock_out' => '18:00',
            'status'    => Attendance::STATUS_DONE,
        ]);
    }

    /** @test */
    public function clock_in_after_clock_out_is_updated_without_error()
    {
        $this->actingAs($this->user);

        $response = $this->patch(
            route('admin.attendance.update', $this->attendance->id),
            [
                'clock_in'  => '20:00',
                'clock_out' => '18:00',
                'notes'     => '修正',
            ]
        );

        // エラーにならずリダイレクトされていることだけ確認
        $response->assertStatus(302);
    }

    /** @test */
    public function break_time_after_clock_out_is_updated_without_error()
    {
        $this->actingAs($this->user);

        $response = $this->patch(
            route('admin.attendance.update', $this->attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'breaks' => [
                    ['start' => '19:00', 'end' => '19:30'],
                ],
                'notes' => '修正',
            ]
        );

        $response->assertRedirect('/admin/attendance/' . $this->attendance->id);
    }

    /** @test */
    public function notes_is_required()
    {
        $this->actingAs($this->user);

        $response = $this->patch(
            route('admin.attendance.update', $this->attendance->id),
            [
                'clock_in'  => '09:00',
                'clock_out' => '18:00',
                'notes'     => '',
            ]
        );

        $response->assertSessionHasErrors('notes');
    }

    /** @test */
    public function attendance_is_updated_successfully()
    {
        $this->actingAs($this->user);

        $response = $this->patch(
            route('admin.attendance.update', $this->attendance->id),
            [
                'clock_in'  => '10:00',
                'clock_out' => '19:00',
                'notes'     => '管理者修正',
            ]
        );

        $response->assertRedirect('/admin/attendance/' . $this->attendance->id);

        $this->assertDatabaseHas('attendances', [
            'id'        => $this->attendance->id,
            'clock_in'  => '10:00',
            'clock_out' => '19:00',
            'notes'     => '管理者修正',
        ]);
    }
}