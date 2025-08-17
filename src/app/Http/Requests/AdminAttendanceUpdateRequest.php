<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // 管理者のみ許可
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'checkin_at' => ['required', 'date_format:H:i'],
            'checkout_at' => ['required', 'date_format:H:i', function ($attribute, $value, $fail) {
                if (request('checkin_at') && $value <= request('checkin_at')) {
                    $fail('出勤時間が不適切な値です');
                }
            }],
            'breaks.*.start_at' => ['nullable', 'date_format:H:i', function ($attribute, $value, $fail) {
                if (request('checkout_at') && $value > request('checkout_at')) {
                    $fail('休憩時間が不適切な値です');
                }
            }],
            'breaks.*.end_at' => ['nullable', 'date_format:H:i', function ($attribute, $value, $fail) {
                if (request('checkout_at') && $value > request('checkout_at')) {
                    $fail('休憩時間もしくは退勤時間が不適切な値です');
                }
            }],
            'note' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => '備考を記入してください',
        ];
    }
}