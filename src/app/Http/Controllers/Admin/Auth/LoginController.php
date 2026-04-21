<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create() {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'メールアドレスを入力してください',
                'password.required' => 'パスワードを入力してください',
            ]
        );

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.attendance.list'); 
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->onlyInput('email');
    }


    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

}
