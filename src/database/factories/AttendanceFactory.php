<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        $date = Carbon::today();

        return [
            'user_id'    => 1,
            'date'       => $date,
            'clock_in'   => null,
            'clock_out'  => null,
            'status'     => Attendance::STATUS_OFF,
            'notes'      => $this->faker->sentence,
        ];
    }

    /** 勤務外 */
    public function off()
    {
        return $this->state(fn() => [
            'clock_in' => null,
            'clock_out' => null,
            'status' => Attendance::STATUS_OFF,
        ]);
    }

    /** 出勤中 */
    public function working()
    {
        $date = Carbon::today();
        return $this->state(fn() => [
            'clock_in' => $date->copy()->setTime(9, 0, 0)->format('H:i:s'),
            'clock_out' => null,
            'status' => Attendance::STATUS_WORKING,
        ]);
    }

    /** 休憩中 */
    public function break()
    {
        $date = Carbon::today();
        return $this->state(fn() => [
            'clock_in' => $date->copy()->setTime(9, 0, 0)->format('H:i:s'),
            'clock_out' => null,
            'status' => Attendance::STATUS_BREAK,
        ]);
    }

    /** 退勤済 */
    public function done()
    {
        $date = Carbon::today();
        return $this->state(fn() => [
            'clock_in'  => $date->copy()->setTime(9, 0, 0)->format('H:i:s'),
            'clock_out' => $date->copy()->setTime(18, 0, 0)->format('H:i:s'),
            'status'    => Attendance::STATUS_DONE,
        ]);
    }
}