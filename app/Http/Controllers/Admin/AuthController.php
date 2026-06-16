<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if ($redirect = AuthHelper::redirectIfAuthenticated()) {
            return $redirect;
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        AuthHelper::logoutAllGuards();

        $request->validate([
            'email'    => 'required|email|string',
            'password' => 'required|string',
        ], [
            'email.required'    => __('validation.custom.email.required'),
            'email.email'       => __('validation.custom.email.invalid'),
            'password.required' => __('validation.custom.password.required'),
        ]);

        $credentials = array_merge($request->only('email', 'password'), ['is_active' => true]);

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['error' => __('auth.login_failed')]);
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();

        return redirect()->route('admin.login');
    }
}
