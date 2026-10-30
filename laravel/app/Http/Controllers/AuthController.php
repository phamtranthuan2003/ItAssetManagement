<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(): View|RedirectResponse
    {
        return Auth::check()
            ? redirect()->route(Auth::user()->landingRoute())
            : view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials + ['status' => 1], $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không chính xác, hoặc tài khoản đã bị khóa.'])->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($request->user()->landingRoute()))->with('success', 'Đăng nhập thành công.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất an toàn.');
    }

    public function profile(): View
    {
        return view('profile');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $request->user()->forceFill(['password' => Hash::make($data['new_password'])])->save();

        return back()->with('success', 'Đổi mật khẩu thành công.');
    }
}
