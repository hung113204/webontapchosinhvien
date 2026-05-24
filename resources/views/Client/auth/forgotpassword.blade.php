@extends('Client.layouts.app')

@section('title', 'Quên mật khẩu - IT Study Support')

@section('content')
<section class="login-section">
    <div class="container">
        <div class="login-layout">

            {{-- ══ LEFT PANEL ══ --}}
            <div class="login-info-panel">
                <div class="login-brand">
                    <div class="login-brand-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="login-brand-text">
                        <h2>IT Study Support</h2>
                        <p>Khoa Công nghệ Thông tin – ĐH Văn Hiến</p>
                    </div>
                </div>

                <h3>Khôi phục <span class="hl">mật khẩu</span><br /> chỉ trong vài bước đơn giản</h3>
                <p>Nhập địa chỉ email đã đăng ký, hệ thống sẽ gửi mã OTP 6 số vào hộp thư của bạn.</p>

                <div class="login-benefits">
                    <div class="login-benefit-item">
                        <div class="benefit-icon"><i class="fas fa-envelope-open-text"></i></div>
                        <span>Email xác nhận gửi trong vài giây</span>
                    </div>
                    <div class="login-benefit-item">
                        <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                        <span>Mã OTP chỉ có hiệu lực trong 15 phút</span>
                    </div>
                    <div class="login-benefit-item">
                        <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                        <span>Mật khẩu mới được mã hóa an toàn tuyệt đối</span>
                    </div>
                </div>

                {{-- Tip box --}}
                <div class="fp-tip-box">
                    <div class="fp-tip-icon"><i class="fas fa-lightbulb"></i></div>
                    <div class="fp-tip-content">
                        <strong>Mẹo nhỏ:</strong> Kiểm tra cả thư mục <em>Spam / Junk</em> nếu bạn không thấy email trong hộp thư chính.
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT CARD ══ --}}
            <div class="auth-card">

                {{-- ✅ Success / email đã gửi --}}
                @if (session('status'))
                    <div class="fp-success-banner">
                        <div class="fp-success-icon"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <strong>Email đã được gửi!</strong>
                            <p>{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                {{-- ❌ Error messages --}}
                @if ($errors->any())
                    <div class="fp-error-banner">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Tabs --}}
                <div class="auth-tabs" style="display: flex;">
                    <a href="{{ route('client.login') }}" class="auth-tab"
                        style="flex: 1; text-decoration: none; color: #94a3b8; text-align: center;">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="auth-tab"
                        style="flex: 1; text-decoration: none; color: #94a3b8; text-align: center;">
                        <i class="fas fa-user-plus"></i> Đăng ký
                    </a>
                </div>

                <div class="auth-card-body">

                    {{-- Header icon --}}
                    <div class="fp-card-header">
                        <div class="fp-card-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h4 class="fp-card-title">Quên mật khẩu?</h4>
                        <p class="auth-card-subtitle" style="margin-bottom: 0;">
                            Nhập email đăng ký, chúng tôi sẽ gửi mã OTP 6 số để đặt lại mật khẩu.
                        </p>
                    </div>

                    {{-- Step indicator --}}
                    <div class="fp-steps">
                        <div class="fp-step {{ session('status') ? 'done' : 'active' }}" id="step-1">
                            <div class="fp-step-dot">
                                @if(session('status'))
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-envelope"></i>
                                @endif
                            </div>
                            <span>Nhập email</span>
                        </div>
                        <div class="fp-step-line {{ session('status') ? 'done' : '' }}" id="line-1"></div>
                        <div class="fp-step {{ session('status') ? 'active' : '' }}" id="step-2">
                            <div class="fp-step-dot">
                                @if(session('status'))
                                    <i class="fas fa-inbox"></i>
                                @else
                                    2
                                @endif
                            </div>
                            <span>Nhận mã OTP</span>
                        </div>
                        <div class="fp-step-line" id="line-2"></div>
                        <div class="fp-step" id="step-3">
                            <div class="fp-step-dot">3</div>
                            <span>Đặt mật khẩu mới</span>
                        </div>
                    </div>

                    {{-- Form (ẩn khi đã gửi thành công) --}}
                    @if(!session('status'))
                    <form action="{{ route('password.email') }}" method="POST" id="forgot-form">
                        @csrf

                        <div class="auth-form-group">
                            <label class="auth-form-label">
                                <i class="fas fa-envelope"></i> Địa chỉ Email
                            </label>
                            <div class="auth-input-wrap">
                                <input
                                    class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    type="email"
                                    name="email"
                                    id="email-input"
                                    value="{{ old('email') }}"
                                    placeholder="student@it.edu.vn"
                                    required
                                    autofocus
                                />
                                <i class="fas fa-envelope auth-input-icon"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-auth-submit" id="submit-btn" style="margin-top: 8px;">
                            <i class="fas fa-paper-plane" id="btn-icon"></i>
                            <span id="btn-text">Gửi mã OTP</span>
                        </button>
                    </form>
                    @else
                    {{-- Nút đi tới trang nhập OTP khi đã gửi thành công --}}
                    <div style="text-align:center; margin-top: 8px;">
                        <a href="{{ route('password.reset.form', ['email' => old('email', request()->query('email', ''))]) }}"
                           class="btn-auth-submit"
                           style="display:inline-flex; align-items:center; gap:8px; text-decoration:none; justify-content:center;">
                            <i class="fas fa-arrow-right"></i> Nhập mã OTP ngay
                        </a>
                    </div>
                    @endif

                    {{-- Back to login --}}
                    <div class="fp-back-link">
                        <a href="{{ route('client.login') }}">
                            <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                        </a>
                    </div>

                    <div class="auth-card-notes">
                        <div class="auth-card-note">
                            <i class="fas fa-info-circle"></i>
                            Chỉ email đã đăng ký hệ thống mới nhận được mã OTP
                        </div>
                        <div class="auth-card-note">
                            <i class="fas fa-clock"></i>
                            Mã OTP có hiệu lực trong vòng 15 phút
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══ STYLES ══ --}}
<style>
    .fp-tip-box {
        display: flex; align-items: flex-start; gap: 12px;
        background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3);
        border-radius: 12px; padding: 14px 16px; margin-top: 28px;
    }
    .fp-tip-icon {
        width: 34px; height: 34px; background: rgba(245,158,11,0.2);
        border-radius: 8px; display: flex; align-items: center;
        justify-content: center; color: #f59e0b; font-size: 0.95rem; flex-shrink: 0;
    }
    .fp-tip-content { font-size: 0.82rem; color: rgba(255,255,255,0.75); line-height: 1.55; }
    .fp-tip-content strong { color: #fcd34d; display: block; margin-bottom: 3px; font-size: 0.83rem; }
    .fp-tip-content em { color: #fcd34d; font-style: normal; font-weight: 600; }

    .fp-success-banner {
        display: flex; align-items: flex-start; gap: 14px;
        background: #d1fae5; border: 1.5px solid #6ee7b7;
        border-radius: 10px 10px 0 0; padding: 16px 20px; color: #065f46;
    }
    .fp-success-icon { font-size: 1.4rem; color: #059669; flex-shrink: 0; margin-top: 2px; }
    .fp-success-banner strong { display: block; font-size: 0.92rem; font-weight: 700; margin-bottom: 3px; }
    .fp-success-banner p { font-size: 0.82rem; margin: 0; line-height: 1.5; color: #047857; }

    /* Error banner */
    .fp-error-banner {
        background: #fee2e2; border: 1.5px solid #fca5a5;
        border-radius: 10px 10px 0 0; padding: 14px 20px; color: #dc2626;
    }
    .fp-error-banner ul { margin: 0; padding: 0; list-style: none; }
    .fp-error-banner li { font-size: 0.84rem; display: flex; align-items: center; gap: 6px; }

    /* Invalid input highlight */
    .auth-input.is-invalid { border-color: #ef4444 !important; }

    .fp-card-header { text-align: center; margin-bottom: 24px; }
    .fp-card-icon {
        width: 58px; height: 58px;
        background: linear-gradient(135deg,#dbeafe,#bfdbfe);
        border-radius: 16px; display: flex; align-items: center;
        justify-content: center; font-size: 1.4rem; color: var(--blue);
        margin: 0 auto 14px; box-shadow: 0 4px 16px rgba(37,99,235,0.15);
    }
    .fp-card-title { font-size: 1.15rem; font-weight: 800; color: #1e293b; margin-bottom: 6px; }

    /* ── Steps ── */
    .fp-steps { display: flex; align-items: center; margin-bottom: 24px; padding: 0 4px; }
    .fp-step { display: flex; flex-direction: column; align-items: center; gap: 5px; flex-shrink: 0; }
    .fp-step-dot {
        width: 30px; height: 30px; border-radius: 50%;
        background: #e2eaf6; color: #94a3b8;
        font-size: 0.75rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        transition: background 0.4s ease, box-shadow 0.4s ease, color 0.3s ease;
    }
    .fp-step.active .fp-step-dot {
        background: linear-gradient(135deg, var(--blue), #1d4ed8);
        color: #fff; box-shadow: 0 3px 10px rgba(37,99,235,0.35);
    }
    .fp-step.done .fp-step-dot {
        background: linear-gradient(135deg, #059669, #047857);
        color: #fff; box-shadow: 0 3px 10px rgba(5,150,105,0.35);
    }
    .fp-step span {
        font-size: 0.68rem; font-weight: 600; color: #94a3b8;
        white-space: nowrap; transition: color 0.4s;
    }
    .fp-step.active span { color: var(--blue); }
    .fp-step.done span { color: #059669; }
    .fp-step-line {
        flex: 1; height: 2px; background: #e2eaf6;
        margin: 0 6px; margin-bottom: 18px; border-radius: 2px;
        transition: background 0.5s ease;
    }
    .fp-step-line.done { background: #059669; }

    /* ── Button loading state ── */
    .btn-auth-submit.loading { opacity: 0.8; pointer-events: none; cursor: not-allowed; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .fa-spinner { animation: spin 0.8s linear infinite; display: inline-block; }

    .fp-back-link { text-align: center; margin-top: 16px; }
    .fp-back-link a {
        font-size: 0.82rem; font-weight: 600; color: #64748b;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;
    }
    .fp-back-link a:hover { color: var(--blue); }
    .fp-back-link a i { font-size: 0.75rem; }
</style>

{{-- ══ JAVASCRIPT ══ --}}
<script>
const forgotForm = document.getElementById('forgot-form');
if (forgotForm) {
    forgotForm.addEventListener('submit', function (e) {
        const emailInput = document.getElementById('email-input');
        if (!emailInput.value.trim()) return; // để HTML5 validation tự xử lý

        e.preventDefault();

        const step1   = document.getElementById('step-1');
        const step2   = document.getElementById('step-2');
        const line1   = document.getElementById('line-1');
        const btn     = document.getElementById('submit-btn');
        const btnIcon = document.getElementById('btn-icon');
        const btnText = document.getElementById('btn-text');
        const form    = this;

        btn.classList.add('loading');
        btnIcon.className = 'fas fa-spinner';
        btnText.textContent = 'Đang gửi...';

        setTimeout(() => {
            step1.classList.remove('active');
            step1.classList.add('done');
            step1.querySelector('.fp-step-dot').innerHTML = '<i class="fas fa-check"></i>';
            line1.classList.add('done');
        }, 400);

        setTimeout(() => {
            step2.classList.add('active');
            step2.querySelector('.fp-step-dot').innerHTML = '<i class="fas fa-inbox"></i>';
        }, 750);

        setTimeout(() => {
            form.submit();
        }, 1000);
    });
}
</script>
@endsection