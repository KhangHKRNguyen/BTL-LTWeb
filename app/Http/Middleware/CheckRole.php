<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Kiểm tra xem user đã đăng nhập chưa
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Kiểm tra tài khoản bị khóa (inactive)
        if ($user->status === 'inactive') {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
            ]);
        }

        // Kiểm tra sai vai trò (Role)
        if ($user->role !== $role) {
            // Điều hướng thông minh về đúng trang của vai trò hiện tại
            return match($user->role) {
                'admin'   => redirect()->route('admin.accounts.index'),
                'teacher' => redirect()->route('teacher.attendance.index'),
                'student' => redirect()->route('student.results.index'),
                default   => redirect('/'),
            };
        }

        return $next($request);
    }
}