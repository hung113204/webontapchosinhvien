<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    /**
     * Chuyển hướng người dùng đến trang xác thực Google.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Xử lý callback sau khi Google xác thực xong.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Tìm user theo email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Tài khoản bị khóa → không cho đăng nhập
                if ((int) $user->trang_thai === 0) {
                    return redirect()->route('client.login')
                        ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
                }

                // Cập nhật google_id và avatar nếu chưa có
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id'  => $googleUser->getId(),
                        'avatar_url' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // 2. Tạo user mới — sinh mã SV duy nhất an toàn
                $prefix    = explode('@', $googleUser->getEmail())[0];
                $ma_sv_tam = $prefix;

                // Thêm hậu tố ngẫu nhiên cho đến khi không còn trùng
                // Dùng Str::random() thay rand() để ít va chạm hơn
                while (User::where('ma_sv', $ma_sv_tam)->exists()) {
                    $ma_sv_tam = $prefix . '_' . Str::random(5);
                }

                $user = User::create([
                    'google_id'      => $googleUser->getId(),
                    'ho_ten'         => $googleUser->getName(),
                    'email'          => $googleUser->getEmail(),
                    'ma_sv'          => $ma_sv_tam,
                    'mat_khau'       => null,   // nullable trong migration
                    'vai_tro_id'     => 3,      // Sinh viên
                    'avatar_url'     => $googleUser->getAvatar(),
                    'trang_thai'     => 1,
                    'is_first_login' => 1,      // Yêu cầu cập nhật thông tin lần đầu
                ]);
            }

            // 3. Đăng nhập
            Auth::login($user, true); // true = remember me

            // 4. Redirect: quay lại trang định vào trước đó, hoặc về trang chủ
            return redirect()->intended(route('client.home'))
                ->with('success', 'Chào mừng ' . $user->ho_ten . ' quay trở lại!');

        } catch (Exception $e) {
            // Ghi log để debug, KHÔNG hiển thị lỗi thô ra ngoài
            Log::error('[GoogleLogin] ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('client.login')
                ->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.');
        }
    }
}