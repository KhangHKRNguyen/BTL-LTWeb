<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Rules\Recaptcha; // Đã giữ nguyên import Rule kiểm tra captcha của bạn

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
        {
            $rules = [
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ];

            // Nếu người dùng nhập sai từ 2 lần trở lên, bắt kiểm tra Captcha hình ảnh
            if (session('login_attempts', 0) >= 2) {
                $rules['captcha'] = [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        // Lấy mã lưu trong Laravel Session ở bước 1 ra
                        $expected = session('captcha_code');

                        // So sánh không phân biệt chữ hoa chữ thường giống như hàm strcasecmp
                        if (!$expected || strcasecmp(trim($value), $expected) !== 0) {
                            $fail('Mã bảo vệ hình ảnh không chính xác. Vui lòng thử lại.');
                        }
                    }
                ];
            }

            return $rules;
        }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            session(['login_attempts' => session('login_attempts', 0) + 1]);
            
            // Xóa mã captcha cũ để buộc sinh hình ảnh mới hoàn toàn ở lượt sau
            session()->forget('captcha_code');

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        session()->forget('login_attempts');
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}