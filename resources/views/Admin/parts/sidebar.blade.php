{{-- 
<div class="sidebar-header">
    <div class="logo" style="justify-content: center;">
        <img src="{{ asset('backend/asset/images/t2.png') }}" alt="Logo Ôn Tập CNTT" class="custom-logo">
    </div>
</div>
--}}

<div class="sidebar-brand" style="display: flex; align-items: center; gap: 10px; justify-content: space-between; padding: 20px 20px;">
    <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit; flex: 1; overflow: hidden; min-width: 0;">
        <img src="{{ asset('backend/asset/images/t2.png') }}" alt="IT Study Support" class="sidebar-brand-logo" style="flex-shrink: 0;">
        <span class="sidebar-brand-name" style="white-space: nowrap;">IT Study Support</span>
    </a>
    <button type="button" id="sidebar-toggle" style="background: none; border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 6px; border-radius: 6px; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='none'" title="Thu gọn/Mở rộng">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>
</div>
<nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7" />
            <rect x="14" y="3" width="7" height="7" />
            <rect x="14" y="14" width="7" height="7" />
            <rect x="3" y="14" width="7" height="7" />
        </svg>
        <span>Dashboard</span>
    </a>

    {{-- Chỉ SuperAdmin mới thấy Danh mục trang chủ --}}
    @if(auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.danhmuctrangchu.index') }}"
        class="nav-item {{ request()->routeIs('admin.danhmuctrangchu.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
            <line x1="3" y1="9" x2="21" y2="9" />
            <line x1="9" y1="21" x2="9" y2="9" />
        </svg>
        <span>Danh mục trang chủ</span>
    </a>
    
    <a href="{{ route('admin.faq.index') }}"
        class="nav-item {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span>Câu hỏi thường gặp</span>
    </a>
    @endif

    <div class="nav-group">
        <div class="nav-group-title">Quản lý học tập</div>

        {{-- Chỉ SuperAdmin mới thấy Môn học, Chương học, Bài học --}}
       @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.monhoc.index') }}"
            class="nav-item {{ request()->routeIs('admin.monhoc.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
            </svg>
            <span>Môn học</span>
        </a>
        <a href="{{ route('admin.chuonghoc.index') }}"
            class="nav-item {{ request()->routeIs('admin.chuonghoc.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="16 18 22 12 16 6" />
                <polyline points="8 6 2 12 8 18" />
            </svg>
            <span>Chương học</span>
        </a>
        <a href="{{ route('admin.baihoc.index') }}"
            class="nav-item {{ request()->routeIs('admin.baihoc.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
            </svg>
            <span>Bài học</span>
        </a>
        @endif

        <a href="{{ route('admin.tiendo.students') }}" class="nav-item {{ request()->routeIs('admin.tiendo.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
            <span>Tiến độ học tập</span>
        </a>
    </div>

    <div class="nav-group">
        <div class="nav-group-title">Ngân hàng câu hỏi</div>
        <a href="{{ route('admin.cauhoi.index') }}"
            class="nav-item {{ request()->routeIs('admin.cauhoi.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
            <span>Câu hỏi</span>
        </a>

        <a href="{{ route('admin.dapan.index', ['cau_hoi_id' => request('cau_hoi_id', session('current_cau_hoi_id', 0))]) }}"
            class="nav-item {{ request()->routeIs('admin.dapan.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12" />
            </svg>
            <span>Đáp án</span>
        </a>
    </div>

    <div class="nav-group">
        <div class="nav-group-title">Bài kiểm tra</div>
        <a href="{{ route('admin.baikiemtra.index') }}"
            class="nav-item {{ request()->routeIs('admin.baikiemtra.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <span>Quản lý đề thi</span>
        </a>
        <a href="{{ route('admin.phienluyentap.index') }}"
            class="nav-item {{ request()->routeIs('admin.phienluyentap.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
            </svg>
            <span>Phiếu Luyện Tập</span>
        </a>
        <a href="{{ route('admin.phongquiz.index') }}"
            class="nav-item {{ request()->routeIs('admin.phongquiz.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
            </svg>
            <span>Phòng Quiz Realtime</span>
        </a>
        <a href="{{ route('admin.ketquathi.index') }}"
            class="nav-item {{ request()->routeIs('admin.ketquathi.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10" />
                <line x1="12" y1="20" x2="12" y2="4" />
                <line x1="6" y1="20" x2="6" y2="14" />
            </svg>
            <span>Kết quả thi</span>
        </a>
    </div>

    {{-- Nhóm Hệ thống: chỉ SuperAdmin mới thấy toàn bộ --}}
    @if(auth()->user()->isSuperAdmin())
    <div class="nav-group">
        <div class="nav-group-title">Hệ thống</div>
        <a href="{{ route('admin.users.index') }}"
            class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            <span>Người dùng</span>
        </a>
        <a href="{{ route('admin.phanquyen.index') }}"
            class="nav-item {{ request()->routeIs('admin.phanquyen.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span>Phân quyền</span>
        </a>
        <a href="{{ route('admin.permission.index') }}"
            class="nav-item {{ request()->routeIs('admin.permission.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
            <span>Quản lý quyền</span>
        </a>
    </div>
    @endif

</nav>

<div class="sidebar-footer">
    <div class="user-profile">
        <div class="user-avatar">
            @if (Auth::user()->avatar_url)
                <img src="{{ asset(Auth::user()->avatar_url) }}" alt="Avatar"
                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            @else
                {{ strtoupper(substr(Auth::user()->ho_ten, 0, 2)) }}
            @endif
        </div>

        <div class="user-info">
            <div class="user-name">{{ Auth::user()->ho_ten }}</div>
            <div class="user-role">
                {{ Auth::user()->role->ten_nhom_quyen ?? 'Chưa phân quyền' }}
            </div>
        </div>
        <a href="{{ route('admin.logout') }}" class="logout-btn" title="Đăng xuất"
            style="margin-left: auto; color: var(--text-muted); transition: color 0.2s;"
            onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" y1="12" x2="9" y2="12" />
            </svg>
        </a>
    </div>
</div>
