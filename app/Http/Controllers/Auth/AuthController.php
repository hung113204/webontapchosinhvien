<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form (Admin/Teacher only)
     */
    public function showLogin()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $redirectUrl = route('home');
            // ✅ FIX: Dùng isAdmin() đã được fix trong User.php
            if ($user->isSuperAdmin()) {
                $redirectUrl = route('admin.dashboard');
            } elseif ($user->isTeacher()) {
                // Bạn cần tạo route này trong web.php
                $redirectUrl = route('teacher.dashboard');
            }

            // Sinh viên đang đăng nhập mà mò vào /admin/login → đá ra trang chủ
            Auth::logout();
            return redirect('/')->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        return view('Admin.login.index');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validated = $request->validate(
            [
                'username' => 'required|string',
                'password' => 'required|string|min:6',
            ],
            [
                'username.required' => 'Vui lòng nhập tên đăng nhập hoặc email',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min' => 'Mật khẩu phải ít nhất 6 ký tự',
            ],
        );

        $user = User::where('email', $validated['username'])->orWhere('ma_sv', $validated['username'])->first();

        // Sai tài khoản hoặc mật khẩu
        if (!$user || !Hash::check($validated['password'], $user->mat_khau)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
                ],
                401,
            );
        }

        // Tài khoản bị khóa
        if (!$user->isActive()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ],
                403,
            );
        }

        // ✅ FIX: Chặn sinh viên đăng nhập vào backend
        // isAdmin() = true nếu vai_tro_id là 1 (Admin) hoặc 2 (Teacher)
        if (!$user->isAdmin()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập trang quản trị. Sinh viên vui lòng đăng nhập tại trang chủ.',
                ],
                403,
            );
        }

        Auth::login($user, $request->boolean('remember'));

        // Cập nhật last_login_at
        try {
            $user->last_login_at = now();
            $user->save();
        } catch (\Exception $e) {
            Log::warning('Could not update last_login_at: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('admin.login'))->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('Auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không hợp lệ',
                'email.exists' => 'Email này không tồn tại trong hệ thống',
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Liên kết khôi phục mật khẩu đã được gửi vào email của bạn.',
        ]);
    }
}