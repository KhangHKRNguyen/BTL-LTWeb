<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Chưa đăng nhập
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Tài khoản bị khóa
        if ($user->status === 'inactive') {
            auth()->logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
            ]);
        }

        // Sai vai trò
        if ($user->role !== $role) {
            // Điều hướng về đúng trang của vai trò hiện tại
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
