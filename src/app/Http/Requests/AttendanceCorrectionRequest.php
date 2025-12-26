<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AttendanceCorrectionRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['required'],
            'clock_out' => ['required'],
            'breaks' => ['array'],
            'breaks.*.start' => [],
            'breaks.*.end' => [],
            'reason' => ['required'],
        ];
    }

    /**
     * エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'reason.required' => '備考を記入してください',
        ];
    }

    /**
     * 独自のバリデーション処理
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            // 出勤・退勤チェック
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
            }

            // 休憩チェック
            $breaks = $this->input('breaks', []);
            foreach ($breaks as $index => $break) {
                $start = $break['start'] ?? null;
                $end = $break['end'] ?? null;

                if ($start && ($start < $clockIn || $start > $clockOut)) {
                    $validator->errors()->add("breaks.$index.start", '休憩時間が不適切な値です');
                }

                if ($end && $end > $clockOut) {
                    $validator->errors()->add("breaks.$index.end", '休憩時間もしくは退勤時間が不適切な値です');
                }
            }
        });
    }
}
