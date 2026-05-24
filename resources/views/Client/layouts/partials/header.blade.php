<div class="header-brand-bar">
    <div class="container">
        <div class="brand-left">
            <a href="{{ url('/') }}">
                <img src="{{ asset('frontend/asset/images/t7.png') }}" alt="IT Study Logo" class="brand-logo-icon" />
            </a>
        </div>

        <div class="brand-center">
            <div class="brand-text">
                <h1>
                    Hỗ trợ ôn tập
                    <span class="accent">Khoa CNTT</span> &nbsp;–&nbsp;
                    <span class="accent2">Học thông minh</span>
                </h1>
                <p class="tagline">
                    "Nắm vững kiến thức hôm nay, tự tin bước vào tương lai số"
                </p>
            </div>
            <div class="brand-mascot-wrap">
                <img src="{{ asset('frontend/asset/images/t2.png') }}" alt="Mascot" class="brand-mascot" />
            </div>
        </div>

        <div class="header-auth">
            @auth
                @php
                    $authUser = Auth::user();
                    $avatarSrc = null;

                    if ($authUser->avatar_url) {
                        $avatarSrc = str_starts_with($authUser->avatar_url, 'http')
                            ? $authUser->avatar_url
                            : \Illuminate\Support\Facades\Storage::url($authUser->avatar_url);
                    }
                @endphp
                <div class="user-dropdown-wrap">
                    <button class="user-dropdown-toggle" id="userDropdownBtn" onclick="toggleUserMenu(event)">
                        @if ($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="Avatar" class="user-avatar-img"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        @endif
                        <div class="user-avatar-initials" @if ($avatarSrc) style="display: none;" @endif>
                            {{ strtoupper(substr($authUser->ho_ten, 0, 1)) }}
                        </div>
                        <span class="user-name">{{ $authUser->ho_ten }}</span>
                        <svg class="chevron-icon" id="chevronIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </div>
            @else
                <a href="{{ route('client.login') }}" class="btn-auth btn-login">Đăng nhập</a>
                <a href="{{ route('register') }}" class="btn-auth btn-register">Đăng ký</a>
            @endauth
        </div>

        <button class="mobile-menu-btn" id="mobile-menu-btn" aria-label="Mở menu" aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</div>

<div class="mobile-overlay" id="mobile-overlay"></div>
{{-- STYLE DÀNH CHO DROPDOWN BÊN TRONG HEADER --}}


{{-- SCRIPT TẮT/MỞ MENU --}}
<script>
    function toggleUserMenu(e) {
        e.preventDefault();
        e.stopPropagation();

        const menu = document.getElementById('userMenu');
        const chevron = document.getElementById('chevronIcon');
        const btn = document.getElementById('userDropdownBtn');

        const isOpen = menu.classList.contains('open');

        if (!isOpen) {
            const btnRect = btn.getBoundingClientRect();

            menu.style.position = 'fixed';
            menu.style.top = btnRect.bottom + 'px'; // ← Ngay dưới NÚT, không phải bar
            menu.style.right = (window.innerWidth - btnRect.right) + 'px';
            menu.style.left = 'auto';
            menu.style.zIndex = '9999';
        }

        menu.classList.toggle('open', !isOpen);
        chevron?.classList.toggle('open', !isOpen);
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userMenu');
        const btn = document.getElementById('userDropdownBtn');
        if (menu?.classList.contains('open') && !btn.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.remove('open');
            document.getElementById('chevronIcon')?.classList.remove('open');
        }
    });

    window.addEventListener('scroll', function() {
        const menu = document.getElementById('userMenu');
        if (menu?.classList.contains('open')) {
            const btn = document.getElementById('userDropdownBtn');
            if (btn) {
                menu.style.top = btn.getBoundingClientRect().bottom + 'px';
                menu.style.right = (window.innerWidth - btn.getBoundingClientRect().right) + 'px';
            }
        }
    });

    window.addEventListener('resize', function() {
        const menu = document.getElementById('userMenu');
        if (menu?.classList.contains('open')) {
            const btn = document.getElementById('userDropdownBtn');
            if (btn) {
                menu.style.top = btn.getBoundingClientRect().bottom + 'px';
                menu.style.right = (window.innerWidth - btn.getBoundingClientRect().right) + 'px';
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mainNav = document.getElementById('main-nav');

        if (!mobileMenuBtn || !mainNav) {
            return;
        }

        const mobileMenuIcon = mobileMenuBtn.querySelector('i');

        function setMobileMenuState(isOpen) {
            mainNav.classList.toggle('mobile-open', isOpen);
            mobileMenuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (mobileMenuIcon) {
                mobileMenuIcon.classList.toggle('fa-bars', !isOpen);
                mobileMenuIcon.classList.toggle('fa-times', isOpen);
            }
            // nhớ
            const mobileOverlay = document.getElementById('mobile-overlay');
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('active', isOpen);
            }
        }

        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            setMobileMenuState(!mainNav.classList.contains('mobile-open'));
        });

        document.addEventListener('click', function(e) {
            if (
                window.innerWidth <= 768 &&
                mainNav.classList.contains('mobile-open') &&
                !mainNav.contains(e.target) &&
                !mobileMenuBtn.contains(e.target)
            ) {
                setMobileMenuState(false);
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                setMobileMenuState(false);
            }
        });
    });
</script>
