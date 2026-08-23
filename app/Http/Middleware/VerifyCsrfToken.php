<?php

namespace App\Http\Middleware;

use App\Helpers\DashboardAuth;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'logout',
        'logout/*',
        '*/logout',
        '*/logout/*',
    ];

    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            if (DashboardAuth::isLogoutRequest($request)) {
                return DashboardAuth::afterLogoutRedirect();
            }

            throw $e;
        }
    }
}
