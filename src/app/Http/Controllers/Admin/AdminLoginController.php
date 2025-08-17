<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::guard('admin')->attempt($credentials)) {
            if (Auth::guard('admin')->user()->isAdmin()) {
                return redirect()->route('admin.attendance.index');
            } else {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => '管理者アカウントのみログイン可能です。']);
            }
        }

        return back()->withErrors(['email' => 'ログイン情報が登録されていません'])->onlyInput('email');
    }
}