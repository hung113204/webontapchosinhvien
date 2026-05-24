<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotClientAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // ✅ Redirect về trang login Client, không phải Admin
            return redirect()->route('client.login')
                ->with('error', 'Bạn cần đăng nhập để tiếp tục.');
        }

        return $next($request);
    }
}