<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in'  => ['nullable'],
            'clock_out' => ['nullable'],
            'notes'     => ['required', 'string'],

            'breaks'            => ['array'],
            'breaks.*.start'    => ['nullable'],
            'breaks.*.end'      => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required' => '備考を記入してください',
        ];
    }

    /**
     * 相関バリデーション
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->clock_in ? Carbon::parse($this->clock_in) : null;
            $clockOut = $this->clock_out ? Carbon::parse($this->clock_out) : null;

            // 出勤 > 退勤
            if ($clockIn && $clockOut && $clockIn->gt($clockOut)) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->breaks ?? [] as $index => $break) {

                if (!$clockOut) {
                    continue;
                }

                $breakStart = $break['start'] ? Carbon::parse($break['start']) : null;
                $breakEnd   = $break['end']   ? Carbon::parse($break['end'])   : null;

                // 休憩開始 > 退勤
                if ($breakStart && $breakStart->gt($clockOut)) {
                    $validator->errors()->add(
                        "breaks.$index.start",
                        '休憩時間が不適切な値です'
                    );
                }

                // 休憩終了 > 退勤
                if ($breakEnd && $breakEnd->gt($clockOut)) {
                    $validator->errors()->add(
                        "breaks.$index.end",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
