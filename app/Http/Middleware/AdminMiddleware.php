<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ✅ Middleware bảo vệ toàn bộ routes /admin/*
 * Thêm vào $routeMiddleware trong app/Http/Kernel.php:
 *   'admin' => \App\Http\Middleware\AdminMiddleware::class,
 *
 * Sau đó dùng trong routes/web.php:
 *   Route::middleware(['auth', 'admin'])->prefix('admin')->group(...)
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Chưa đăng nhập → về trang login admin
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Đăng nhập rồi nhưng là sinh viên → đá ra, không cho vào
        if (!$user->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Bạn không có quyền truy cập trang quản trị.']);
        }

        // Tài khoản bị khóa
        if (!$user->isActive()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
        }

        return $next($request);
    }
}