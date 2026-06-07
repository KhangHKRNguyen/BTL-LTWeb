<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Rules\Recaptcha;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
                'email' => ['required', 'email'],
                'captcha' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        // Lấy mã captcha lưu trong Laravel Session ra so sánh
                        $expected = session('captcha_code');

                        if (!$expected || strcasecmp(trim($value), $expected) !== 0) {
                            $fail('Mã bảo vệ hình ảnh không chính xác. Vui lòng thử lại.');
                        }
                    }
                ],
            ]);

            // Nếu vượt qua validate thành công, xóa luôn mã session cũ để tránh dùng lại (Replay Attack)
            session()->forget('captcha_code');

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
