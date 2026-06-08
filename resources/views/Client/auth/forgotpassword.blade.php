@extends('Client.layouts.app')

@section('title', 'Quên mật khẩu - IT Study Support')

@section('content')
    <div class="login-page">
        {{-- PHẦN TRÁI: ẢNH GIF --}}
        <div class="login-visual">
            <div class="visual-gif-container">
                <img src="{{ asset('./frontend/asset/images/login.gif') }}" alt="Forgot Password Visual" class="visual-gif" />
            </div>
        </div>

        {{-- PHẦN PHẢI: FORM --}}
        <div class="login-form-panel">
            <div class="form-header">
                <h1>Quên mật khẩu?</h1>
                <p>Nhập email đăng ký, chúng tôi sẽ gửi mã OTP 6 số để đặt lại mật khẩu.</p>
            </div>

            @if (session('status'))
                <div style="background:#dcfce7; border:1px solid #86efac; color:#16a34a; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    <i class="fas fa-check-circle" style="margin-right:6px;"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                    <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(!session('status'))
            <form action="{{ route('password.email') }}" method="POST" id="forgot-form">
                @csrf

                <div class="field">
                    <label>Email</label>
                    <div class="input-wrap" style="position: relative;">
                        <i class="ti ti-mail input-icon-left" aria-hidden="true"></i>
                        <input type="email" name="email" id="email-input" value="{{ old('email') }}" placeholder="student@it.edu.vn"
                            required style="{{ $errors->has('email') ? 'border-color:#f87171;' : '' }}" autofocus />
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submit-btn" style="margin-top: 10px;">
                    Gửi mã OTP
                </button>
            </form>
            @else
            <div style="text-align:center; margin-top: 10px;">
                <a href="{{ route('password.reset.form', ['email' => old('email', request()->query('email', ''))]) }}"
                   class="btn-submit"
                   style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; justify-content:center; color: #fff;">
                    Nhập mã OTP ngay <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            @endif

            <div class="divider"><span>Hoặc</span></div>

            <p style="text-align:center; margin-top:25px; font-size:14px; color:#334155;">
                Nhớ mật khẩu rồi?
                <a href="{{ route('client.login') }}" style="color:#3b82f6; font-weight:700; text-decoration:none;">Quay lại đăng nhập</a>
            </p>
        </div>
    </div>
@endsection