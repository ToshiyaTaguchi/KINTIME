<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in'  => ['required'],
            'clock_out' => ['required', 'after:clock_in'],
            'breaks.*.start' => ['nullable'],
            'breaks.*.end'   => ['nullable', 'after:breaks.*.start'],
            'notes' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.start.*' => '休憩時間が不適切な値です',
            'breaks.*.end.after' => '休憩時間もしくは退勤時間が不適切な値です',
            'notes.required' => '備考を記入してください',
        ];
    }

}
