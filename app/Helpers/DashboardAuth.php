<?php

namespace App\Helpers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class DashboardAuth
{
    public static function guards(): array
    {
        return ['admin', 'doctor', 'web', 'patient', 'ray_employee', 'laboratorie_employee'];
    }

    public static function user(): ?Authenticatable
    {
        foreach (self::guards() as $guard) {
            if ($user = auth($guard)->user()) {
                return $user;
            }
        }

        return null;
    }

    public static function activeGuard(): ?string
    {
        foreach (self::guards() as $guard) {
            if (auth($guard)->check()) {
                return $guard;
            }
        }

        return null;
    }

    public static function logoutRouteName(): string
    {
        switch (self::activeGuard()) {
            case 'admin':
                return 'logout.admin';
            case 'doctor':
                return 'logout.doctor';
            case 'ray_employee':
                return 'logout.ray_employee';
            case 'laboratorie_employee':
                return 'logout.laboratorie_employee';
            case 'patient':
                return 'logout.patient';
            default:
                return 'logout.user';
        }
    }

    public static function isLogoutRequest(Request $request): bool
    {
        if ($request->routeIs('logout.*')) {
            return true;
        }

        return (bool) preg_match('#(^|/)logout(/|$)#', $request->path());
    }

    public static function afterLogoutRedirect()
    {
        return redirect()->route('login')
            ->with('error', 'تم تسجيل الخروج. يرجى تسجيل الدخول مجدداً.');
    }
}
