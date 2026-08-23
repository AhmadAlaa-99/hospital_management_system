<?php

namespace App\Exceptions;

use App\Helpers\DashboardAuth;
use App\Helpers\FriendlyError;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'انتهت صلاحية الجلسة.'], 419);
            }

            if (DashboardAuth::isLogoutRequest($request)) {
                return DashboardAuth::afterLogoutRedirect();
            }

            return redirect()->route('login')
                ->with('error', 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مرة أخرى.');
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || config('app.debug')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return null;
            }

            if ($e instanceof QueryException || $e instanceof \PDOException) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => FriendlyError::message($e->getMessage())]);
            }

            return null;
        });
    }
}
