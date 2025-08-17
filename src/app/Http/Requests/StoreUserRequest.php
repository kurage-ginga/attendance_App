<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可は別途middleware等で管理
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            // confirmed => password_confirmation と一致する必要あり
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => '有効なメールアドレス形式で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'password.min' => 'パスワードは８文字以上で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.confirmed' => 'パスワードと一致しません。',
        ];
    }
}