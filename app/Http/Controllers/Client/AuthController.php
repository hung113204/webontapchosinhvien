<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\Client\RegisterRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return view('Client.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string',
                'password' => 'required|string',
            ],
            [
                'email.required' => 'Vui lòng nhập Email hoặc Mã sinh viên.',
                'password.required' => 'Vui lòng nhập mật khẩu.',
            ],
        );

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'ma_sv';
        $user = User::where($loginType, $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->mat_khau)) {
            return back()
                ->withErrors(['email' => 'Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại.'])
                ->onlyInput('email');
        }

        if ($user->isAdmin()) {
            return back()->withErrors([
                'email' => 'Đây là cổng đăng nhập của Sinh viên. Quản trị viên/Giáo viên vui lòng truy cập /admin/login.',
            ]);
        }

        if (! $user->isActive()) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.',
            ]);
        }

        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();
        $request->session()->forget('quiz_nickname');
        $request->session()->forget('quiz_guest_user_id');

        return redirect('/')->with('success', 'Đăng nhập thành công!')
            ->withoutCookie('quiz_nickname')
            ->withoutCookie('quiz_guest_user_id');
    }

    public function register(RegisterRequest $request)
    {
        $roleId = 3;

        $latestUser = User::where('ma_sv', 'like', 'SV%')->orderBy('id', 'desc')->first();

        if ($latestUser) {
            $lastNumber = (int) substr($latestUser->ma_sv, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $newMaSv = 'SV' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        User::create([
            'ho_ten' => $request->fullname,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->password),
            'vai_tro_id' => $roleId,
            'trang_thai' => 1,
            'is_first_login' => 1,
            'ma_sv' => $newMaSv,
        ]);

        return redirect()->route('client.login')->with('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập với tài khoản vừa tạo.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Bạn đã đăng xuất thành công.')
            ->withoutCookie('quiz_nickname')
            ->withoutCookie('quiz_guest_user_id');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/');
        }

        return view('Client.auth.register');
    }

    public function showForgotForm()
    {
        return view('Client.auth.forgotpassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(
            ['email' => 'required|email|exists:users,email'],
            ['email.exists' => 'Email này chưa được đăng ký trong hệ thống.'],
        );

        $otp = (string) random_int(100000, 999999);
        $expires = Carbon::now()->addMinutes(15);

        User::where('email', $request->email)->update([
            'reset_password_token' => $otp,
            'reset_password_expires_at' => $expires,
        ]);

        $email = $request->email;

        try {
            Mail::to($email)->send(new ResetPasswordMail($otp));
        } catch (\Exception $e) {
            Log::error('Password reset mail failed: ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'Không gửi được mã OTP tới email của bạn. Vui lòng kiểm tra lại cấu hình SMTP rồi thử lại.',
            ]);
        }

        return redirect()
            ->route('password.reset.form', ['email' => $email])
            ->with('status', 'Mã OTP đã được gửi thành công tới Gmail của bạn.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        if (! $email) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'Liên kết không hợp lệ, vui lòng thử lại.']);
        }

        return view('Client.auth.reset_password', compact('email'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'token' => 'required|digits:6',
                'password' => 'required|min:8|confirmed',
            ],
            [
                'token.digits' => 'Mã OTP phải là 6 chữ số.',
                'password.min' => 'Mật khẩu tối thiểu 8 ký tự.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ],
        );

        $user = User::where('email', $request->email)
            ->where('reset_password_token', $request->token)
            ->where('reset_password_expires_at', '>=', Carbon::now())
            ->first();

        if (! $user) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['token' => 'Mã OTP không hợp lệ hoặc đã hết hạn.']);
        }

        $user->update([
            'mat_khau' => Hash::make($request->password),
            'reset_password_token' => null,
            'reset_password_expires_at' => null,
        ]);

        return redirect()->route('client.login')->with('status', 'Mật khẩu đã được đặt lại thành công, vui lòng đăng nhập.');
    }
}
