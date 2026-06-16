<?php

namespace App\Helpers;

use Illuminate\Http\RedirectResponse;

class AuthHelper
{
    public static function logoutAllGuards(): void
    {
        $currentLocale = session('locale', 'ja');

        auth()->guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();

        session(['locale' => $currentLocale]);
    }

    public static function redirectIfAuthenticated(): ?RedirectResponse
    {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return null;
    }
}
