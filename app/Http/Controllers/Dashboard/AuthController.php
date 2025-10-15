<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show_login()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-login-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function login(AdminLoginRequest $request)
    {
        $data = $request->afterValidation();
        if (Auth::guard('web')->attempt(Arr::except($data, 'remember'), $data['remember'])) {
            return redirect()->route('profile.show');
        }
        return redirect()->back()->with(['error' => 'invalid credentials']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('show_login');
    }
}
