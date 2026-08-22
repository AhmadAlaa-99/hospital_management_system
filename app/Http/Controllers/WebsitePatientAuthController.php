<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PatientRegistrationService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class WebsitePatientAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'password.required' => 'يرجى إدخال كلمة المرور.',
        ]);

        if (!Auth::guard('patient')->attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة. تحقق من البريد وكلمة المرور.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'patient' => [
                'name' => optional(Auth::guard('patient')->user())->name,
                'email' => Auth::guard('patient')->user()->email,
            ],
        ]);
    }

    public function register(Request $request, PatientRegistrationService $registration)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|min:2|max:100',
                'email' => 'required|email|unique:patients,email',
                'phone' => 'required|string|min:8|max:20|unique:patients,Phone',
                'password' => 'required|string|min:6|confirmed',
            ], [
                'name.required' => 'يرجى إدخال الاسم الكامل.',
                'name.min' => 'الاسم يجب أن يكون حرفين على الأقل.',
                'email.required' => 'يرجى إدخال البريد الإلكتروني.',
                'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
                'email.unique' => 'هذا البريد مسجّل مسبقاً. جرّب تسجيل الدخول أو استخدم بريداً آخر.',
                'phone.required' => 'يرجى إدخال رقم الهاتف.',
                'phone.min' => 'رقم الهاتف قصير جداً.',
                'phone.unique' => 'رقم الهاتف مسجّل مسبقاً. جرّب تسجيل الدخول أو استخدم رقماً آخر.',
                'password.required' => 'يرجى إدخال كلمة المرور.',
                'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
                'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            ]);

            $patient = $registration->register($data);

            Auth::guard('patient')->login($patient);
            $request->session()->regenerate();

            return response()->json([
                'ok' => true,
                'success' => true,
                'message' => 'تم إنشاء الحساب وتسجيل الدخول',
                'patient' => [
                    'name' => $patient->name,
                    'email' => $patient->email,
                ],
            ]);
        } catch (ValidationException $e) {
            $messages = collect($e->errors())->flatten()->values()->all();

            return response()->json([
                'ok' => false,
                'message' => $messages[0] ?? 'تعذر إنشاء الحساب بسبب بيانات غير صحيحة.',
                'errors' => $e->errors(),
                'messages' => $messages,
            ], 422);
        } catch (QueryException $e) {
            report($e);

            $message = 'تعذر إنشاء الحساب. تأكد من أن البريد ورقم الهاتف غير مسجّلين مسبقاً.';
            $sqlMessage = $e->getMessage();
            if (str_contains($sqlMessage, 'patients_phone') || str_contains($sqlMessage, 'Phone')) {
                $message = 'رقم الهاتف مسجّل مسبقاً. جرّب تسجيل الدخول أو استخدم رقماً آخر.';
            } elseif (str_contains($sqlMessage, 'patients_email') || str_contains($sqlMessage, 'email')) {
                $message = 'البريد الإلكتروني مسجّل مسبقاً. جرّب تسجيل الدخول أو استخدم بريداً آخر.';
            }

            return response()->json([
                'ok' => false,
                'message' => $message,
            ], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'تعذر إنشاء الحساب. يرجى المحاولة لاحقاً.',
            ], 500);
        }
    }
}
