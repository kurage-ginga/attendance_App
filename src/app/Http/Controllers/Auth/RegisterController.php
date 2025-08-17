<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AttendanceController;
use App\Models\User;
use Illuminate\Http\StoreUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // バリデーション済みのデータ取得
        $validated = $request->validated();

        // ユーザー作成
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'employee',
        ]);

        // メール認証イベント発火
        event(new Registered($user));

        Auth::guard('employee')->login($user);

        return redirect()->intended('/attendance');
    }
}