@extends('Client.layouts.app')

@section('title', 'Đặt mật khẩu mới - IT Study Support')

@section('content')
    <section class="login-section">
        <div class="container">
            <div class="login-layout">
                <div class="login-info-panel">
                    <div class="login-brand">
                        <div class="login-brand-icon"><i class="fas fa-graduation-cap"></i></div>
                        <div class="login-brand-text">
                            <h2>IT Study Support</h2>
                            <p>Khoa Công nghệ Thông tin - ĐH Văn Hiến</p>
                        </div>
                    </div>

                    <h3>Tạo <span class="hl">mật khẩu mới</span><br /> an toàn cho tài khoản</h3>
                    <p>Chọn mật khẩu mạnh để bảo vệ tài khoản học tập của bạn.</p>

                    <div class="login-benefits">
                        <div class="login-benefit-item">
                            <div class="benefit-icon"><i class="fas fa-shield-alt"></i></div>
                            <span>Tối thiểu 8 ký tự</span>
                        </div>
                        <div class="login-benefit-item">
                            <div class="benefit-icon"><i class="fas fa-lock"></i></div>
                            <span>Không dùng lại mật khẩu cũ</span>
                        </div>
                        <div class="login-benefit-item">
                            <div class="benefit-icon"><i class="fas fa-user-secret"></i></div>
                            <span>Không chia sẻ mật khẩu với bất kỳ ai</span>
                        </div>
                    </div>
                </div>

                <div class="auth-card">
                    @if ($errors->any())
                        <div style="padding:14px 20px; background:#fee2e2; color:#dc2626; border-radius:10px 10px 0 0; border-bottom:1px solid #fca5a5;">
                            <ul style="margin:0; padding-left:18px; font-size:0.84rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="auth-tabs" style="display:flex;">
                        <a href="{{ route('client.login') }}" class="auth-tab" style="flex:1; text-decoration:none; color:#94a3b8; text-align:center;">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="auth-tab" style="flex:1; text-decoration:none; color:#94a3b8; text-align:center;">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </a>
                    </div>

                    <div class="auth-card-body">
                        <div style="text-align:center; margin-bottom:22px;">
                            <div style="width:58px; height:58px; background:linear-gradient(135deg,#d1fae5,#a7f3d0); border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; margin:0 auto 14px; box-shadow:0 4px 16px rgba(5,150,105,0.15);">
                                <i class="fas fa-key" style="color:#059669;"></i>
                            </div>
                            <h4 style="font-size:1.15rem; font-weight:800; color:#1e293b; margin-bottom:6px;">
                                Đặt mật khẩu mới
                            </h4>
                            @if (!empty($email))
                                <p class="auth-card-subtitle" style="margin-bottom:0;">
                                    Tài khoản: <strong>{{ $email }}</strong>
                                </p>
                            @endif
                        </div>

                        <form action="{{ route('password.reset.post') }}" method="POST" id="reset-form">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? '' }}" />

                            <div class="auth-form-group">
                                <label class="auth-form-label">
                                    <i class="fas fa-hashtag"></i> Mã OTP (6 chữ số)
                                </label>
                                <div class="auth-input-wrap">
                                    <input
                                        class="auth-input {{ $errors->has('token') ? 'is-invalid' : '' }}"
                                        type="text"
                                        name="token"
                                        id="otp-input"
                                        placeholder="● ● ● ● ● ●"
                                        required
                                        maxlength="6"
                                        inputmode="numeric"
                                        autocomplete="one-time-code"
                                        style="letter-spacing:6px; font-weight:800; font-size:1.1rem; text-align:center;"
                                    />
                                    <i class="fas fa-key auth-input-icon"></i>
                                </div>
                            </div>

                            <div class="auth-form-group">
                                <label class="auth-form-label">
                                    <i class="fas fa-lock"></i> Mật khẩu mới
                                </label>
                                <div class="auth-input-wrap">
                                    <input
                                        class="auth-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                        type="password"
                                        name="password"
                                        id="new-password"
                                        placeholder="Tối thiểu 8 ký tự"
                                        required
                                    />
                                    <i class="fas fa-eye auth-input-icon" id="toggle-new-pw" onclick="togglePassword('new-password','toggle-new-pw')" style="cursor:pointer;"></i>
                                </div>
                                <div class="pw-strength-wrap" id="pw-strength-wrap" style="display:none;">
                                    <div class="pw-strength-bar">
                                        <div class="pw-strength-fill" id="pw-strength-fill"></div>
                                    </div>
                                    <span class="pw-strength-label" id="pw-strength-label"></span>
                                </div>
                            </div>

                            <div class="auth-form-group">
                                <label class="auth-form-label">
                                    <i class="fas fa-lock"></i> Xác nhận mật khẩu
                                </label>
                                <div class="auth-input-wrap">
                                    <input
                                        class="auth-input"
                                        type="password"
                                        name="password_confirmation"
                                        id="confirm-pw"
                                        placeholder="Nhập lại mật khẩu"
                                        required
                                    />
                                    <i class="fas fa-eye auth-input-icon" id="toggle-confirm-pw" onclick="togglePassword('confirm-pw','toggle-confirm-pw')" style="cursor:pointer;"></i>
                                </div>
                                <div id="pw-match-msg" style="font-size:0.78rem; margin-top:5px; display:none;"></div>
                            </div>

                            <button type="submit" class="btn-auth-submit green" id="reset-btn" style="margin-top:8px;">
                                <i class="fas fa-check-circle" id="reset-btn-icon"></i>
                                <span id="reset-btn-text">Xác nhận đặt lại mật khẩu</span>
                            </button>
                        </form>

                        <div style="text-align:center; margin-top:16px;">
                            <a href="{{ route('client.login') }}" style="font-size:0.82rem; font-weight:600; color:#64748b; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                                <i class="fas fa-arrow-left" style="font-size:0.75rem;"></i>
                                Quay lại đăng nhập
                            </a>
                        </div>

                        <div class="auth-card-notes">
                            <div class="auth-card-note">
                                <i class="fas fa-clock"></i>
                                Mã OTP chỉ có hiệu lực 15 phút kể từ lúc yêu cầu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .auth-input.is-invalid { border-color: #ef4444 !important; }
        .pw-strength-wrap { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
        .pw-strength-bar {
            flex: 1;
            height: 5px;
            background: #e2e8f0;
            border-radius: 99px;
            overflow: hidden;
        }
        .pw-strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 99px;
            transition: width 0.35s ease, background 0.35s ease;
        }
        .pw-strength-label { font-size: 0.75rem; font-weight: 700; white-space: nowrap; }
        #otp-input { font-family: monospace; }
    </style>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = `fas fa-eye${show ? '-slash' : ''} auth-input-icon`;
        }

        document.getElementById('otp-input').addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });

        const pwInput = document.getElementById('new-password');
        const fillEl = document.getElementById('pw-strength-fill');
        const labelEl = document.getElementById('pw-strength-label');
        const wrapEl = document.getElementById('pw-strength-wrap');

        function getStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (pw.length >= 12) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;
            return score;
        }

        const levels = [
            { label: 'Rất yếu', color: '#ef4444', pct: 20 },
            { label: 'Yếu', color: '#f97316', pct: 40 },
            { label: 'Trung bình', color: '#eab308', pct: 60 },
            { label: 'Mạnh', color: '#22c55e', pct: 80 },
            { label: 'Rất mạnh', color: '#059669', pct: 100 },
        ];

        pwInput.addEventListener('input', function () {
            const val = this.value;
            if (!val) {
                wrapEl.style.display = 'none';
                return;
            }

            wrapEl.style.display = 'flex';

            const score = Math.min(getStrength(val), 5) - 1;
            const lvl = levels[Math.max(score, 0)];
            fillEl.style.width = lvl.pct + '%';
            fillEl.style.background = lvl.color;
            labelEl.textContent = lvl.label;
            labelEl.style.color = lvl.color;

            checkMatch();
        });

        const confirmPw = document.getElementById('confirm-pw');
        const matchMsg = document.getElementById('pw-match-msg');

        function checkMatch() {
            const pw = pwInput.value;
            const cf = confirmPw.value;

            if (!cf) {
                matchMsg.style.display = 'none';
                return;
            }

            matchMsg.style.display = 'block';
            if (pw === cf) {
                matchMsg.textContent = 'Mat khau khop';
                matchMsg.style.color = '#059669';
            } else {
                matchMsg.textContent = 'Mat khau chua khop';
                matchMsg.style.color = '#ef4444';
            }
        }

        confirmPw.addEventListener('input', checkMatch);

        document.getElementById('reset-form').addEventListener('submit', function () {
            const btn = document.getElementById('reset-btn');
            const btnIcon = document.getElementById('reset-btn-icon');
            const btnText = document.getElementById('reset-btn-text');

            btn.classList.add('loading');
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';
            btnIcon.className = 'fas fa-spinner fa-spin';
            btnText.textContent = 'Đang xử lý...';
        });
    </script>
@endsection
