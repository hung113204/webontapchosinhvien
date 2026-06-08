<style>
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-40px); }
        to { opacity: 1; transform: translateX(0); }
    }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .profile-sidebar-card {
        animation: slideInLeft 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .profile-main-area {
        opacity: 0; /* Tránh chớp nháy trước khi diễn hoạt */
        animation: slideInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        animation-delay: 0.1s;
    }
</style>

<div class="profile-sidebar-card" id="shared-profile-sidebar">
    <div class="ps-nav-group">
        <div class="ps-nav-title">Tài khoản</div>
        
        <a href="{{ route('profile.index') }}" class="ps-nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}" 
           style="display: block; padding: 12px 20px; text-decoration: none; transition: 0.2s; 
           {{ request()->routeIs('profile.index') ? 'font-weight: 600; background: #eef2ff; color: #4f46e5; border-left: 3px solid #4f46e5;' : 'font-weight: 500; color: #475569; border-left: 3px solid transparent;' }}">
            Thông tin cá nhân
        </a>
        
        <a href="{{ route('profile.history') }}" class="ps-nav-link {{ request()->routeIs('profile.history') ? 'active' : '' }}" 
           style="display: block; padding: 12px 20px; text-decoration: none; transition: 0.2s; 
           {{ request()->routeIs('profile.history') ? 'font-weight: 600; background: #eef2ff; color: #4f46e5; border-left: 3px solid #4f46e5;' : 'font-weight: 500; color: #475569; border-left: 3px solid transparent;' }}">
            Hồ sơ học tập
        </a>

        <div class="ps-nav-title" style="padding-top: 15px;">Hỗ trợ</div>
        <!-- <a href="#" class="ps-nav-link" style="color: #475569; text-decoration: none; display: block; padding: 12px 20px; font-weight: 500; border-left: 3px solid transparent;">
            Đánh giá/góp ý của tôi
        </a> -->
        
        <!-- <form action="{{ route('logout') }}" method="POST" style="margin-top: 20px; padding: 0 20px;">
            @csrf
            <button type="submit" class="btn-logout" style="width: 100%; padding: 10px; background: #fee2e2; color: #ef4444; border: none; border-radius: 6px; cursor: pointer; font-weight: 500; display: flex; justify-content: center; align-items: center; gap: 8px; transition: 0.2s;">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </button>
        </form> -->
    </div>
</div>


