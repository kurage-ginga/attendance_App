<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('employee')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/attendance');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }
}