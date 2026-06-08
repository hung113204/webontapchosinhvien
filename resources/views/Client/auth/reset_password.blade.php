@extends('Client.layouts.app')

@section('title', 'Đặt mật khẩu mới - IT Study Support')

@push('styles')
<style>
    /* Ẩn icon con mắt mặc định của trình duyệt Edge/Chrome để không bị trùng 2 con mắt */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
@endpush

@section('content')
    <div class="login-page">
        {{-- PHẦN TRÁI: ẢNH GIF --}}
        <div class="login-visual">
            <div class="visual-gif-container">
                <img src="{{ asset('./frontend/asset/images/login.gif') }}" alt="Reset Password Visual" class="visual-gif" />
            </div>
        </div>

        {{-- PHẦN PHẢI: FORM --}}
        <div class="login-form-panel">
            <div class="form-header">
                <h1>Đặt mật khẩu mới</h1>
                @if (!empty($email))
                    <p>Tài khoản: <strong>{{ $email }}</strong></p>
                @else
                    <p>Chọn mật khẩu mạnh để bảo vệ tài khoản học tập của bạn.</p>
                @endif
            </div>

            @if ($errors->any())
                <div style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.reset.post') }}" method="POST" id="reset-form">
                @csrf
                <input type="hidden" name="email" value="{{ $email ?? '' }}" />

                <div class="field">
                    <label>Mã OTP (6 chữ số)</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-hash input-icon-left" aria-hidden="true"></i>
                        <input type="text" name="token" id="otp-input" placeholder="● ● ● ● ● ●"
                            required maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                            style="letter-spacing:6px; font-weight:800; font-size:1.1rem; text-align:center; {{ $errors->has('token') ? 'border-color:#f87171;' : '' }}" />
                    </div>
                </div>

                <div class="field">
                    <label>Mật khẩu mới</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-lock input-icon-left" aria-hidden="true"></i>
                        <input type="password" name="password" id="new-password" placeholder="Tối thiểu 8 ký tự" required
                            style="{{ $errors->has('password') ? 'border-color:#f87171;' : '' }}" />
                        <button type="button" class="input-icon-right" aria-label="Hiện/ẩn mật khẩu"
                            onclick="togglePassword('new-password', this)"
                            style="background:none; border:none; cursor:pointer; padding:0;">
                            <i class="ti ti-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="field">
                    <label>Xác nhận mật khẩu</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-lock input-icon-left" aria-hidden="true"></i>
                        <input type="password" name="password_confirmation" id="confirm-pw" placeholder="Nhập lại mật khẩu" required />
                        <button type="button" class="input-icon-right" aria-label="Hiện/ẩn mật khẩu"
                            onclick="togglePassword('confirm-pw', this)"
                            style="background:none; border:none; cursor:pointer; padding:0;">
                            <i class="ti ti-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="reset-btn" style="margin-top: 10px;">
                    Xác nhận đặt lại mật khẩu
                </button>
            </form>

            <div class="divider"><span>Hoặc</span></div>

            <p style="text-align:center; margin-top:25px; font-size:14px; color:#334155;">
                <a href="{{ route('client.login') }}" style="color:#3b82f6; font-weight:700; text-decoration:none;">Quay lại đăng nhập</a>
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

        document.getElementById('otp-input').addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    </script>
@endsection
