<nav class="header-nav" id="main-nav">
    <div class="navbar">
        <ul class="nav-menu">
            @if (isset($menus) && $menus->isNotEmpty())
                @foreach ($menus as $menu)
                    <li>
                        @php
                            $link = $menu->slug === '/' || $menu->slug === 'trang-chu' ? url('/') : url($menu->slug);
                        @endphp
                        <a href="{{ $link }}">
                            @if (!empty($menu->hinh_anh))
                                <img src="{{ Storage::url($menu->hinh_anh) }}" alt="{{ $menu->tieu_de }}"
                                    style="width: 20px; height: 20px; object-fit: cover; margin-right: 6px; border-radius: 4px;">
                            @elseif(!empty($menu->icon_class))
                                <i class="{{ $menu->icon_class }}"></i>
                            @endif
                            {{ $menu->tieu_de }}
                            @if ($menu->slug == 'hoi-ai')
                                <span class="badge-new">AI</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
</nav>
@auth
<div class="user-dropdown-menu" id="userMenu">
    <div class="dropdown-inner">
        <div class="dropdown-body">
            <a href="{{ route('profile.index') }}" class="dropdown-item"><i class="fas fa-id-card"></i> Tài khoản của tôi</a>
            <a href="{{ route('client.phongquiz.join') }}" class="dropdown-item"><i class="fas fa-bolt" style="color: #eab308;"></i> Đấu trường Quiz Live</a>
            <a href="{{ route('profile.history') }}" class="dropdown-item"><i class="fas fa-chart-pie"></i> Hồ sơ học tập</a>
            <a href="#" class="dropdown-item"><i class="fas fa-comment-dots"></i> Góp ý của tôi</a>
        </div>
        
        <div class="dropdown-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item dropdown-logout">
                    <i class="fas fa-sign-out-alt"></i> Thoát
                </button>
            </form>
        </div>
    </div>
</div>
@endauth

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.nav-menu li a');
        buttons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                let ripple = document.createElement('span');
                ripple.classList.add('ripple');
                this.appendChild(ripple);

                let x = e.clientX - e.target.getBoundingClientRect().left;
                let y = e.clientY - e.target.getBoundingClientRect().top;

                ripple.style.left = `${x}px`;
                ripple.style.top = `${y}px`;

                // Kích thước của ripple dựa trên kích thước lớn nhất của phần tử
                let size = Math.max(this.clientWidth, this.clientHeight);
                ripple.style.width = ripple.style.height = `${size}px`;

                // Đưa tâm của ripple vào vị trí click bằng margin để không xung đột transform animation
                ripple.style.marginLeft = `-${size / 2}px`;
                ripple.style.marginTop = `-${size / 2}px`;

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
    });
</script>
@endpush