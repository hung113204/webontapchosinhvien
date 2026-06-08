@extends('Client.layouts.app')

@section('title', 'Đăng nhập - IT Study Support')

@push('styles')
<style>
    /* Ẩn icon con mắt mặc định của trình duyệt Edge/Chrome để không bị trùng 2 con mắt */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    /* Đồng nhất thiết kế và responsive cho nút đăng nhập Google */
    .login-form-panel .btn-social {
        height: 54px !important;
        border-radius: 14px !important;
        border: 1.5px solid #cbd5e1 !important;
        background: #ffffff !important;
        color: #0f172a !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 12px !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .login-form-panel .btn-social:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        transform: translateY(-1px) !important;
    }

    .login-form-panel .btn-social img {
        width: 22px !important;
        height: 22px !important;
    }
</style>
@endpush

@section('content')
    <div class="login-page">
        {{-- PHẦN TRÁI: ẢNH GIF --}}
        <div class="login-visual">
            <div class="visual-gif-container">
                <img src="{{ asset('./frontend/asset/images/login.gif') }}" alt="Login Visual" class="visual-gif" />
            </div>
        </div>

        {{-- PHẦN PHẢI: FORM --}}
        <div class="login-form-panel">
            <div class="form-header">
                <h1>Đăng nhập</h1>
                <p>Sẵn sàng để bắt đầu buổi học hôm nay chưa?</p>
            </div>

            {{-- ✅ FIX 1: Hiển thị thông báo lỗi --}}
            @if ($errors->any())
                <div
                    style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- ✅ FIX 2: Hiển thị thông báo thành công (vd: sau đăng ký) --}}
            @if (session('success'))
                <div
                    style="background:#dcfce7; border:1px solid #86efac; color:#16a34a; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    <i class="fas fa-check-circle" style="margin-right:6px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="field">
                    <label>Email</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-mail input-icon-left" aria-hidden="true"></i>
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="Email của bạn"
                            required style="{{ $errors->has('email') ? 'border-color:#f87171;' : '' }}" />
                    </div>
                </div>

                <div class="field">
                    <label>Mật khẩu</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-lock input-icon-left" aria-hidden="true"></i>
                        <input type="password" name="password" id="password" placeholder="••••••••" required
                            style="{{ $errors->has('password') ? 'border-color:#f87171;' : '' }}" />
                        <button type="button" class="input-icon-right" aria-label="Hiện/ẩn mật khẩu"
                            onclick="togglePassword('password', this)"
                            style="background:none; border:none; cursor:pointer; padding:0;">
                            <i class="ti ti-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="options-row">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:#3b82f6; width:16px; height:16px;"> Ghi
                        nhớ tôi
                    </label>
                    <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-submit">Đăng nhập ngay</button>
            </form>

            <div class="divider"><span>Hoặc tiếp tục với</span></div>

            <div class="social-btns">
                <a href="{{ route('google.login') }}" class="btn-social">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="20" alt="Google"> Google
                </a>
                {{--  <a href="#" class="btn-social">
                <img src="https://www.svgrepo.com/show/448238/microsoft.svg" width="20" alt="Microsoft"> Microsoft
            </a> --}}
            </div>

            <p style="text-align:center; margin-top:35px; font-size:14px; color:#334155;">
                Bạn là thành viên mới?
                <a href="{{ route('register') }}" style="color:#3b82f6; font-weight:700; text-decoration:none;">Tham gia
                    ngay</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('ti-eye', 'ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.replace('ti-eye-off', 'ti-eye');
            }
        }
    </script>
@endsection
