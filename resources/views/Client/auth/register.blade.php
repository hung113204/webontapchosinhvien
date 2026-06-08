@extends('Client.layouts.app')

@section('title', 'Đăng ký tài khoản - IT Study Support')

@push('styles')
<style>
    /* Chỉ giữ lại style cho strength bar (vì style1.css không có sẵn) */
    .strength-bar-wrap {
        display: flex;
        gap: 6px;
        margin-top: 8px;
    }
    .strength-bar {
        flex: 1;
        height: 4px;
        border-radius: 4px;
        background: #e2e8f0;
        transition: background 0.2s;
    }
    .strength-label {
        font-size: 11px;
        text-align: right;
        margin-top: 5px;
        color: #94a3b8;
    }
    /* Đảm bảo icon right không bị đè */
    .register-form-panel .input-wrap i.input-icon-right {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        font-size: 0.9rem;
        transition: color 0.2s;
        z-index: 2;
    }
    .register-form-panel .input-wrap i.input-icon-right:hover {
        color: var(--blue);
    }
    
    /* Ẩn icon con mắt mặc định của trình duyệt Edge/Chrome để không bị trùng 2 con mắt */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="register-page">
    {{-- BÊN TRÁI: VISUAL (GIF + THÔNG TIN) --}}
    <div class="register-visual">
        <div class="register-visual-content">
            {{-- Ảnh GIF --}}
            <img src="{{ asset('./frontend/asset/images/signup.gif') }}" alt="Register Visual" class="register-visual-gif" />

            {{-- Bảng thông tin các bước --}}
            <div class="register-visual-info">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-info">
                        <strong>Điền thông tin & tạo tài khoản</strong>
                        <span>Chỉ mất 30 giây để hoàn thành</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-info">
                        <strong>Xác thực email</strong>
                        <span>Nhận link kích hoạt trong hộp thư</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-info">
                        <strong>Bắt đầu ôn tập ngay</strong>
                        <span>Truy cập toàn bộ kho tài liệu & đề thi</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">4</div>
                    <div class="step-info">
                        <strong>Nhận đề xuất từ AI</strong>
                        <span>Lộ trình học cá nhân hóa theo kết quả của bạn</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BÊN PHẢI: FORM ĐĂNG KÝ --}}
    <div class="register-form-panel">
        <div class="form-header">
            <h1>Tạo tài khoản mới ✨</h1>
            <p>Đã có tài khoản? <a href="{{ route('client.login') }}">Đăng nhập tại đây</a></p>
        </div>

        @if ($errors->any())
            <div style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
                <ul style="margin:0; padding:0; list-style-type:none;">
                    @foreach ($errors->all() as $error)
                        <li style="{{ !$loop->last ? 'margin-bottom:6px;' : '' }}">
                            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>{{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" novalidate>
            @csrf

            {{-- Họ và tên --}}
            <div class="field">
                <label>Họ và tên</label>
                <div class="input-wrap">
                    <i class="fas fa-user input-icon-left"></i>
                    <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Nguyễn Văn A" required />
                </div>
            </div>

            {{-- Email --}}
            <div class="field">
                <label>Email</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon-left"></i>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="student@it.edu.vn" required />
                </div>
            </div>

            {{-- Mật khẩu --}}
            <div class="field">
                <label>Mật khẩu</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" name="password" id="reg-password" placeholder="Tối thiểu 8 ký tự" required oninput="checkStrength(this.value)" />
                    <i class="fas fa-eye input-icon-right" id="toggle-reg-pw" onclick="togglePassword('reg-password', this)"></i>
                </div>
                {{-- 
                <div class="strength-bar-wrap">
                    <div class="strength-bar" id="bar1"></div>
                    <div class="strength-bar" id="bar2"></div>
                    <div class="strength-bar" id="bar3"></div>
                    <div class="strength-bar" id="bar4"></div>
                </div>
                <div class="strength-label" id="strength-label"></div>
                --}}
            </div>

            {{-- Xác nhận mật khẩu --}}
            <div class="field">
                <label>Xác nhận mật khẩu</label>
                <div class="input-wrap">
                    <i class="fas fa-lock input-icon-left"></i>
                    <input type="password" name="password_confirmation" id="confirm-password" placeholder="Nhập lại mật khẩu" required />
                    <i class="fas fa-eye input-icon-right" id="toggle-confirm-pw" onclick="togglePassword('confirm-password', this)"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i> Tạo tài khoản
            </button>
        </form>

        <div class="form-note">
            <i class="fas fa-check-circle"></i>
            <span>Đăng ký thành công là có thể đăng nhập và vào thi ngay!</span>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconElement) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            iconElement.classList.remove('fa-eye');
            iconElement.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            iconElement.classList.remove('fa-eye-slash');
            iconElement.classList.add('fa-eye');
        }
    }

    function checkStrength(val) {
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const colors = ['#ef4444', '#f97316', '#eab308', '#10b981'];
        const labels = ['Yếu', 'Trung bình', 'Khá', 'Mạnh'];
        for (let i = 1; i <= 4; i++) {
            const bar = document.getElementById('bar' + i);
            bar.style.backgroundColor = i <= score ? colors[score - 1] : '#e2e8f0';
        }
        const lbl = document.getElementById('strength-label');
        lbl.textContent = val.length ? (labels[score - 1] || '') : '';
        lbl.style.color = score > 0 ? colors[score - 1] : '#94a3b8';
    }
</script>
@endsection
