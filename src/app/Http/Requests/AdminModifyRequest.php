<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminModifyRequest extends FormRequest
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
            'clock_in_at' => ['required', 'date_format:H:i', 'before:clock_out_at'],
            'clock_out_at' => ['required', 'date_format:H:i', 'after:clock_in_at'],
            'breaks' => ['array'],
            'breaks.*.break_start_at' => [
                'date_format:H:i',
                'after_or_equal:clock_in_at',
                'before_or_equal:clock_out_at',
            ],
            'breaks.*.break_end_at' => [
                'nullable',
                'date_format:H:i',
                'before_or_equal:clock_out_at',
            ],
            'new_breaks.0.break_start_at' => [
                'nullable',
                'date_format:H:i',
                'after_or_equal:clock_in_at',
                'before_or_equal:clock_out_at',
            ],
            'new_breaks.0.break_end_at' => [
                'nullable',
                'date_format:H:i',
                'before_or_equal:clock_out_at',
            ],
            'reason' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'clock_in_at.required' => '出勤時間を入力してください',
            'clock_in_at.date_format' => '出勤時間は08:40のような形式で入力してください',
            'clock_in_at.before' => '出勤時間もしくは退勤時間が不適切な値です',

            'clock_out_at.required' => '退勤時間を入力してください',
            'clock_out_at.date_format' => '退勤時間は08:40のような形式で入力してください',
            'clock_out_at.after' => '出勤時間もしくは退勤時間が不適切な値です',

            'breaks.*.break_start_at.date_format' => '休憩開始時間は08:40のような形式で入力してください',
            'breaks.*.break_start_at.after_or_equal' => '休憩時間が不適切な値です',
            'breaks.*.break_start_at.before_or_equal' => '休憩時間が不適切な値です',

            'breaks.*.break_end_at.date_format' => '休憩終了時間は08:40のような形式で入力してください',
            'breaks.*.break_end_at.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',

            'new_breaks.0.break_start_at.date_format' => '休憩開始時間は08:40のような形式で入力してください',
            'new_breaks.0.break_start_at.after_or_equal' => '休憩時間が不適切な値です',
            'new_breaks.0.break_start_at.before_or_equal' => '休憩時間が不適切な値です',

            'new_breaks.0.break_end_at.date_format' => '休憩終了時間は08:40のような形式で入力してください',
            'new_breaks.0.break_end_at.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',

            'reason.required' => '備考を記入してください'
        ];
    }
}
