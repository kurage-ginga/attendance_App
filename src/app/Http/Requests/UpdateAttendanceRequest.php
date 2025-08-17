<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\AttendanceRecord;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $date = $this->route('date');
        $record = AttendanceRecord::where('user_id', $this->user()->id)
            ->where('date', $date)
            ->first();

        // 承認待ちの場合は修正不可
        if ($record && $record->status === 'pending_correction') {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'checkin_at' => ['required', 'date_format:H:i'],
            'checkout_at' => ['required', 'date_format:H:i', function($attribute, $value, $fail) {
                if (strtotime($this->checkin_at) > strtotime($value)) {
                    $fail('出勤時間が不適切な値です');
                }
            }],
            'breaks.*.start_at' => ['nullable', 'date_format:H:i', function($attribute, $value, $fail) {
                if (!empty($value) && strtotime($value) > strtotime($this->checkout_at)) {
                    $fail('休憩時間が不適切な値です');
                }
            }],
            'breaks.*.end_at' => ['nullable', 'date_format:H:i', function($attribute, $value, $fail) {
                if (!empty($value) && strtotime($value) > strtotime($this->checkout_at)) {
                    $fail('休憩時間もしくは退勤時間が不適切な値です');
                }
            }],
            'note' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'checkin_at.required' => '出勤時間が不適切な値です',
            'checkout_at.required' => '退勤時間は必須です',
            'breaks.*.start_at.date_format' => '休憩時間が不適切な値です',
            'breaks.*.end_at.date_format' => '休憩時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, '承認待ちのため修正はできません。');
    }
}