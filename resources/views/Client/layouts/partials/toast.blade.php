{{--
    ══════════════════════════════════════════════════════
    TOAST NOTIFICATION SYSTEM (MODERN CENTER-TOP DROPDOWN)
    Đặt file này vào: resources/views/Client/layouts/partials/toast.blade.php
    Sau đó include vào layout chính (app.blade.php):
        @include('Client.layouts.partials.toast')
    ══════════════════════════════════════════════════════
--}}

{{-- Render toast từ session Laravel hoặc lỗi Validation --}}
@if (session('success'))
    <div id="__blade_toast" data-type="success" data-message="{{ session('success') }}"></div>
@elseif (session('status'))
    <div id="__blade_toast" data-type="info" data-message="{{ session('status') }}"></div>
@elseif (session('error'))
    <div id="__blade_toast" data-type="error" data-message="{{ session('error') }}"></div>
@elseif (session('warning'))
    <div id="__blade_toast" data-type="warning" data-message="{{ session('warning') }}"></div>
@elseif ($errors->any())
    <div id="__blade_toast" data-type="error" data-message="{{ $errors->first() }}"></div>
@endif

{{-- Container chứa tất cả toast (Căn giữa phía trên màn hình) --}}
<div id="toast-container" aria-live="polite"></div>

<style>
/* ── Container (Top-Center Dropdown) ── */
#toast-container {
    position: fixed;
    top: 24px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000000;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    pointer-events: none;
    width: 100%;
    max-width: 500px;
    padding: 0 20px;
}

/* ── Toast card (Glassmorphism & High Premium Design) ── */
.toast-item {
    pointer-events: all;
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    background: rgba(255, 255, 255, 0.90);
    backdrop-filter: blur(12px) saturate(180%);
    -webkit-backdrop-filter: blur(12px) saturate(180%);
    border-radius: 16px;
    padding: 14px 18px;
    box-shadow:
        0 15px 35px rgba(0, 0, 0, 0.08),
        0 5px 15px rgba(0, 0, 0, 0.04),
        0 0 0 1px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
    position: relative;
    overflow: hidden;
    
    /* Smooth Drop-down Animation with Elastic bounce */
    animation: toast-in 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}

.toast-item.toast-hiding {
    animation: toast-out 0.3s cubic-bezier(0.25, 1, 0.5, 1) forwards;
}

/* ── Progress bar ở cạnh dưới (Neon Gradient Countdown) ── */
.toast-item::after {
    content: '';
    position: absolute;
    bottom: 0; 
    left: 0;
    height: 3.5px;
    width: 100%;
    border-radius: 0 0 16px 16px;
    transform-origin: left;
    animation: toast-progress var(--toast-duration, 5s) linear forwards;
}

/* ── Icon Badge ── */
.toast-icon {
    width: 38px; 
    height: 38px;
    border-radius: 12px;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
}

/* ── Text body ── */
.toast-body { 
    flex: 1; 
    min-width: 0; 
}
.toast-title {
    font-size: 0.88rem;
    font-weight: 800;
    margin-bottom: 2px;
    line-height: 1.3;
}
.toast-message {
    font-size: 0.82rem;
    line-height: 1.45;
    color: #475569;
    font-weight: 500;
}

/* ── Close button ── */
.toast-close {
    background: none; 
    border: none; 
    cursor: pointer;
    color: #94a3b8; 
    font-size: 0.95rem;
    padding: 6px; 
    border-radius: 50%;
    line-height: 1; 
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.toast-close:hover { 
    background: rgba(0, 0, 0, 0.05); 
    color: #334155; 
    transform: rotate(90deg);
}

/* ── Success Type ── */
.toast-success .toast-icon { 
    background: #e6fcf5; 
    color: #0ca678; 
    border: 1px solid rgba(12, 166, 120, 0.15);
}
.toast-success .toast-title { color: #097957; }
.toast-success::after { background: linear-gradient(90deg, #0ca678, #37b24d); }

/* ── Error Type ── */
.toast-error .toast-icon { 
    background: #fff5f5; 
    color: #f03e3e; 
    border: 1px solid rgba(240, 62, 62, 0.15);
}
.toast-error .toast-title { color: #c92a2a; }
.toast-error::after { background: linear-gradient(90deg, #f03e3e, #ff8787); }

/* ── Warning Type ── */
.toast-warning .toast-icon { 
    background: #fff9db; 
    color: #f59f00; 
    border: 1px solid rgba(245, 159, 0, 0.15);
}
.toast-warning .toast-title { color: #e67700; }
.toast-warning::after { background: linear-gradient(90deg, #f59f00, #ffd43b); }

/* ── Info Type ── */
.toast-info .toast-icon { 
    background: #e7f5ff; 
    color: #1c7ed6; 
    border: 1px solid rgba(28, 126, 214, 0.15);
}
.toast-info .toast-title { color: #1864ab; }
.toast-info::after { background: linear-gradient(90deg, #1c7ed6, #74c0fc); }

/* ── Entrance & Exit Keyframes ── */
@keyframes toast-in {
    from { 
        opacity: 0; 
        transform: translateY(-80px) scale(0.92); 
    }
    to   { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}
@keyframes toast-out {
    from { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
        max-height: 120px; 
        margin-bottom: 0; 
    }
    to   { 
        opacity: 0; 
        transform: translateY(-40px) scale(0.9); 
        max-height: 0; 
        margin-bottom: -12px; 
        padding-top: 0; 
        padding-bottom: 0; 
        border-width: 0;
    }
}
@keyframes toast-progress {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}

/* Responsive adjustment for Mobile */
@media (max-width: 600px) {
    #toast-container {
        top: 16px;
        max-width: calc(100% - 32px);
        padding: 0;
    }
}
</style>

<script>
(function () {
    // ── Cấu hình icon & tiêu đề theo type ──
    const CONFIG = {
        success: { icon: 'fas fa-check-circle', title: 'Thành công!'       },
        error:   { icon: 'fas fa-exclamation-triangle',  title: 'Có lỗi xảy ra!'   },
        warning: { icon: 'fas fa-exclamation-circle', title: 'Cảnh báo!' },
        info:    { icon: 'fas fa-info-circle',   title: 'Thông báo'        },
    };

    /**
     * Hiển thị toast.
     * @param {string} message  - Nội dung thông báo
     * @param {string} type     - 'success' | 'error' | 'warning' | 'info'
     * @param {number} duration - Thời gian tự đóng (ms), mặc định 5000
     */
    window.showToast = function (message, type = 'success', duration = 5000) {
        const cfg       = CONFIG[type] || CONFIG.info;
        const container = document.getElementById('toast-container');
        if (!container) return;

        const el = document.createElement('div');
        el.className = `toast-item toast-${type}`;
        el.style.setProperty('--toast-duration', duration + 'ms');
        el.innerHTML = `
            <div class="toast-icon"><i class="${cfg.icon}"></i></div>
            <div class="toast-body">
                <div class="toast-title">${cfg.title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Đóng"><i class="fas fa-times"></i></button>
        `;

        // Nút đóng
        el.querySelector('.toast-close').addEventListener('click', () => dismissToast(el));

        // Rơi từ trên xuống, thêm vào container
        container.appendChild(el);

        // Tự đóng sau duration ms
        const timer = setTimeout(() => dismissToast(el), duration);
        el._toastTimer = timer;
    };

    function dismissToast(el) {
        clearTimeout(el._toastTimer);
        el.classList.add('toast-hiding');
        el.addEventListener('animationend', () => el.remove(), { once: true });
    }

    // ── Tự động render toast từ Blade session hoặc validation error ──
    document.addEventListener('DOMContentLoaded', function () {
        const blade = document.getElementById('__blade_toast');
        if (blade) {
            const type    = blade.dataset.type    || 'info';
            const message = blade.dataset.message || '';
            if (message) {
                // Thêm độ trễ nhỏ để trang render xong mượt mà rồi mới rơi xuống
                setTimeout(() => {
                    showToast(message, type, 5000);
                }, 300);
            }
            blade.remove();
        }
    });
})();
</script>