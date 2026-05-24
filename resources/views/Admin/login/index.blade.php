<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng nhập Admin - Hệ Thống Ôn Tập CNTT</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
 <link rel="stylesheet" href="{{ asset('backend/asset/css/login.css') }}" />
</head>
<body>

<div class="login-page">
  <!-- ====== LEFT: SLIDESHOW ====== -->
  <div class="slide-panel">
    <!-- Slides -->
    <div class="slide active" id="slide-0">
      <img class="slide-img" src="{{ asset('backend/asset/images/giaodienlogin.png') }}" alt="Học sinh trong lớp học" />
      <div class="slide-overlay"></div>
    </div>
    <div class="slide" id="slide-1">
      <img class="slide-img" src="{{ asset('backend/asset/images/giaodienlogin1.png') }}" alt="Phòng thi" />
      <div class="slide-overlay"></div>
    </div>
    <div class="slide" id="slide-2">
      <img class="slide-img" src="{{ asset('backend/asset/images/giaodienlogin2.jpg') }}" alt="Giảng dạy hiện đại" />
      <div class="slide-overlay"></div>
    </div>

    <!-- Logo -->
    <div class="slide-logo">
      <div class="logo-icon">
        <i class="fas fa-graduation-cap"></i>
      </div>
      <div class="logo-text">
        <h1>EduAdmin</h1>
        <span>Hệ thống quản lý thi trực tuyến</span>
      </div>
    </div>

    <!-- Stats -->
    <div class="slide-stats">
      <div class="stat-chip">
        <div class="stat-chip-icon">
          <i class="fas fa-users"></i>
        </div>
        <div>
          <div class="stat-chip-val" id="c-students">0</div>
          <div class="stat-chip-lbl">Học sinh</div>
        </div>
      </div>
      <div class="stat-chip">
        <div class="stat-chip-icon">
          <i class="fas fa-file-alt"></i>
        </div>
        <div>
          <div class="stat-chip-val" id="c-exams">0</div>
          <div class="stat-chip-lbl">Đề thi</div>
        </div>
      </div>
      <div class="stat-chip">
        <div class="stat-chip-icon">
          <i class="fas fa-star"></i>
        </div>
        <div>
          <div class="stat-chip-val" id="c-score">0</div>
          <div class="stat-chip-lbl">Điểm TB</div>
        </div>
      </div>
    </div>

    <!-- Caption -->
    <div class="slide-caption">
      <div class="caption-tag">
        <div class="caption-dot"></div>
        <span>Hệ thống đang hoạt động</span>
      </div>
      <h2 id="slide-h">Nâng tầm giáo dục <span>số hoá</span></h2>
      <p id="slide-p">Tạo đề thi, quản lý học sinh và phân tích kết quả ngay trên một nền tảng thống nhất, hiệu quả.</p>
    </div>

    <!-- Dots -->
    <div class="slide-dots">
      <button class="slide-dot active" data-i="0"></button>
      <button class="slide-dot" data-i="1"></button>
      <button class="slide-dot" data-i="2"></button>
    </div>
  </div>

  <!-- ====== RIGHT: LOGIN FORM ====== -->
  <div class="form-panel">
    <div class="form-inner">
      <!-- Header -->
      <div class="form-header">
        <div class="form-header-row">
          <div class="welcome-badge">
            <i class="fas fa-shield-alt"></i>
            Admin Portal
          </div>
          <span class="badge badge-active">v2.4</span>
        </div>
        <h2>Xin chào, Admin 👋</h2>
        <p>Đăng nhập để quản lý hệ thống.</p>
      </div>

      <!-- Form -->
      <form id="loginForm" novalidate>
        <!-- Username -->
        <div class="form-group">
          <label class="form-label" for="username">Tên đăng nhập</label>
          <div class="input-wrapper">
            <input type="text" class="form-input" id="username" name="username"
              placeholder="Nhập email hoặc tên đăng nhập"
              autocomplete="username" required />
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Mật khẩu</label>
          <div class="input-wrapper">
            <input type="password" class="form-input" id="password" name="password"
              placeholder="Nhập mật khẩu"
              autocomplete="current-password" required />
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <button type="button" class="input-action" id="togglePwd">
              <svg id="eyeShow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              <svg id="eyeHide" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Remember & Forgot -->
        <div class="form-row">
          <label class="checkbox-wrapper">
            <input type="checkbox" id="remember" name="remember" />
            <div class="checkbox-custom"></div>
            <span class="checkbox-label">Ghi nhớ đăng nhập</span>
          </label>
          <a href="#" class="forgot-link" id="forgotPasswordLink" onclick="showForgotModal(event)">Quên mật khẩu?</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary btn-submit" id="loginBtn">
          <span class="btn-label" id="btnText" style="display:inline-flex;align-items:center;gap:8px;">
            <i class="fas fa-sign-in-alt"></i> Đăng nhập
          </span>
          <span id="spinnerWrap" style="display:none;align-items:center;justify-content:center;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);">
            <span style="display:inline-block;width:20px;height:20px;border:2.5px solid rgba(255,255,255,0.35);border-top-color:white;border-radius:50%;animation:spin 0.7s linear infinite;"></span>
          </span>
        </button>

        <!-- Alert Area -->
        <div class="alert-area" id="alertArea"></div>
      </form>

      <div class="form-divider">
        <div class="form-divider-line"></div>
        <span class="form-divider-text">Bảo mật & An toàn</span>
        <div class="form-divider-line"></div>
      </div>

      <!-- Footer -->
      <div class="form-footer">
        <div class="security-note">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          <span>Đăng nhập an toàn với hệ thống bảo mật nhiều lớp của chúng tôi.</span>
        </div>
        <div class="version-note">EduAdmin © 2025 · v2.4.1</div>
      </div>
    </div>
  </div>
</div>

<!-- ====== FORGOT PASSWORD MODAL ====== -->
<div class="modal" id="forgotPasswordModal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Khôi phục mật khẩu</h2>
      <button class="modal-close" type="button" onclick="closeForgotModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="forgotPasswordForm">
        <div class="form-group">
          <label class="form-label" for="recoveryEmail">Email đăng ký</label>
          <div class="input-wrapper">
            <input type="email" class="form-input" id="recoveryEmail"
              placeholder="Nhập email bạn đã đăng ký" required />
            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
        </div>
        <p class="recovery-info">
          <i class="fas fa-info-circle"></i>
          <span>Hệ thống sẽ gửi hướng dẫn khôi phục mật khẩu vào email của bạn.</span>
        </p>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" type="button" onclick="closeForgotModal()">Hủy</button>
      <button class="btn btn-primary" type="button" id="sendRecovery">Gửi yêu cầu</button>
    </div>
  </div>
</div>

<script>
  // ====== SLIDESHOW ======
  const slides = document.querySelectorAll('.slide');
  const dots = document.querySelectorAll('.slide-dot');
  const data = [
    { h: 'Nâng tầm giáo dục <span>số hoá</span>', p: 'Tạo đề thi, quản lý học sinh và phân tích kết quả ngay trên một nền tảng thống nhất.' },
    { h: 'Kỳ thi <span>an toàn</span> & minh bạch', p: 'Hệ thống chống gian lận thông minh, kết quả tự động ngay sau khi nộp bài.' },
    { h: 'Giảng dạy <span>thông minh</span> hơn', p: 'Phân tích điểm số giúp giáo viên hiểu rõ năng lực từng học sinh một cách trực quan.' }
  ];
  let cur = 0, iv;

  function goTo(i) {
    slides[cur].classList.remove('active');
    dots[cur].classList.remove('active');
    cur = i;
    slides[cur].classList.add('active');
    dots[cur].classList.add('active');
    document.getElementById('slide-h').innerHTML = data[cur].h;
    document.getElementById('slide-p').textContent = data[cur].p;
  }

  iv = setInterval(() => goTo((cur + 1) % slides.length), 5000);
  dots.forEach(d => d.addEventListener('click', () => {
    clearInterval(iv);
    goTo(+d.dataset.i);
    iv = setInterval(() => goTo((cur + 1) % slides.length), 5000);
  }));

  // ====== ANIMATED COUNTERS ======
  function animCount(id, target, dur, dec = 0) {
    const el = document.getElementById(id); if (!el) return;
    const t0 = performance.now();
    (function tick(now) {
      const p = Math.min((now - t0) / dur, 1);
      const v = (1 - Math.pow(1-p, 3)) * target;
      el.textContent = dec ? v.toFixed(dec) : Math.floor(v).toLocaleString('vi');
      if (p < 1) requestAnimationFrame(tick);
    })(performance.now());
  }
  setTimeout(() => {
    animCount('c-students', 1284, 1800);
    animCount('c-exams', 347, 1500);
    animCount('c-score', 7.8, 1200, 1);
  }, 500);

  // ====== TOGGLE PASSWORD ======
  document.getElementById('togglePwd').addEventListener('click', (e) => {
    e.preventDefault();
    const inp = document.getElementById('password');
    const show = document.getElementById('eyeShow');
    const hide = document.getElementById('eyeHide');
    if (inp.type === 'password') {
      inp.type = 'text'; show.style.display = 'none'; hide.style.display = 'block';
    } else {
      inp.type = 'password'; show.style.display = 'block'; hide.style.display = 'none';
    }
  });

  // ====== MODAL MANAGEMENT ======
  function showForgotModal(e) {
    e.preventDefault();
    document.getElementById('forgotPasswordModal').classList.add('active');
  }

  function closeForgotModal() {
    document.getElementById('forgotPasswordModal').classList.remove('active');
  }

  document.getElementById('forgotPasswordModal').addEventListener('click', (e) => {
    if (e.target.id === 'forgotPasswordModal') closeForgotModal();
  });

  // ====== LOGIN FORM SUBMIT ======
  document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;
    const btn = document.getElementById('loginBtn');
    const area = document.getElementById('alertArea');
    area.innerHTML = '';

    if (!username || !password) {
      area.innerHTML = `<div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Vui lòng nhập đầy đủ thông tin đăng nhập.
      </div>`;
      return;
    }

    // Hiện spinner, ẩn text
    const btnText = document.getElementById('btnText');
    const spinnerWrap = document.getElementById('spinnerWrap');
    btnText.style.display = 'none';
    spinnerWrap.style.display = 'flex';
    btn.disabled = true;

    const resetBtn = () => {
      btnText.style.display = 'inline-flex';
      spinnerWrap.style.display = 'none';
      btn.disabled = false;
    };

    try {
      const response = await fetch('{{ route("admin.login.submit") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ username, password, remember })
      });

      const data = await response.json();

      if (data.success) {
        area.innerHTML = `<div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          ${data.message}
        </div>`;
        setTimeout(() => window.location.href = data.redirect, 1500);
      } else {
        area.innerHTML = `<div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          ${data.message}
        </div>`;
        resetBtn();
      }
    } catch (error) {
      area.innerHTML = `<div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        Lỗi kết nối. Vui lòng thử lại.
      </div>`;
      resetBtn();
    }
  });

  // ====== SEND RECOVERY ======
  document.getElementById('sendRecovery').addEventListener('click', async function() {
    const email = document.getElementById('recoveryEmail').value.trim();
    if (!email) {
      alert('Vui lòng nhập email');
      return;
    }
    
    try {
      const response = await fetch('{{ route("admin.password.email") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email })
      });

      const data = await response.json();
      alert(data.message);
      if (data.success) closeForgotModal();
    } catch (error) {
      alert('Lỗi: ' + error.message);
    }
  });
</script>
</body>
</html>