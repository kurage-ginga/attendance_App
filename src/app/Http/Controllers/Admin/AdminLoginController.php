<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.attendance.index');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => '管理者アカウントのみログイン可能です。']);
            }
        }

        return back()->withErrors(['email' => '認証に失敗しました。']);
    }
}