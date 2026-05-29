<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudent
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra nếu user là student
        if (auth()->check() && auth()->user()->role === 'student') {
            return $next($request);
        }

        // Nếu không phải student, redirect về dashboard
        return redirect('/dashboard')->with('error', 'Không có quyền truy cập');
    }
}
