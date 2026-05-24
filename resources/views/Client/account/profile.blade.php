@extends('Client.layouts.app')

@section('title', 'Thông tin tài khoản - IT Study Support')

@section('content')
    <section class="profile-section-clean">
        <div class="container">
            <div class="profile-grid-layout">

                <div class="profile-sidebar-card">
                    <div class="ps-nav-group">
                        <div class="ps-nav-title">Tài khoản</div>
                        <a href="#" class="ps-nav-link active">Thông tin cá nhân</a>
                        <a href="{{ route('profile.history') }}"
                            style="display: block; padding: 12px 20px; font-size: 0.9rem; font-weight: 500; color: #475569; border-left: 3px solid transparent; text-decoration: none; transition: 0.2s;">
                            Hồ sơ học tập
                        </a>

                        <div class="ps-nav-title" style="padding-top: 15px;">Hỗ trợ</div>
                        <a href="#" class="ps-nav-link">Đánh giá/góp ý của tôi</a>
                    </div>
                </div>

                <div class="profile-main-area">

                    <div class="profile-header-top">
                        <div>
                            <h2>Thông tin tài khoản</h2>
                            <p>Quản lý hồ sơ cá nhân và thông tin học tập.</p>
                        </div>
                        <a href="{{ url('/') }}" class="btn-back-home">Trang chủ</a>
                    </div>

                    @if (session('success'))
                        <div class="profile-alert success">
                            <i class="fas fa-check-circle" style="margin-right: 6px;"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="profile-alert error">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="profile-cards-grid">
                        <div class="profile-card-col">
                            <div class="p-card">
                                <h3 class="p-card-title">Ảnh đại diện</h3>
                                <p class="p-card-desc">Hiển thị trên hệ thống học tập.</p>

                                <div class="avatar-action-wrap">
                                    @if (Auth::user()->avatar_url)
                                        <img src="{{ Storage::url(Auth::user()->avatar_url) }}" alt="Avatar"
                                            class="avatar-img-lg" id="avatar-preview-img">
                                    @else
                                        <div class="avatar-placeholder-lg" id="avatar-placeholder-lg">
                                            {{ mb_strtoupper(mb_substr(Auth::user()->ho_ten, 0, 2)) }}
                                        </div>
                                    @endif

                                    <div class="avatar-btns">
                                        <input type="file" name="avatar" id="avatar-input" form="main-profile-form"
                                            accept="image/jpeg, image/png, image/jpg" style="display: none;"
                                            onchange="previewAvatar(event)">

                                        <button type="button" class="btn-avatar-primary"
                                            onclick="document.getElementById('avatar-input').click();">
                                            Chọn ảnh mới
                                        </button>

                                        @if (Auth::user()->avatar_url)
                                            <form action="{{ route('profile.remove.avatar') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn-avatar-outline"
                                                    onclick="return confirm('Bạn có chắc muốn gỡ ảnh đại diện?');">
                                                    Gỡ ảnh
                                                </button>
                                            </form>
                                        @endif

                                        <span class="avatar-hint">JPG/PNG, hệ thống tự động crop vuông và nén nhẹ.</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-card">
                                <h3 class="p-card-title">Đổi mật khẩu</h3>
                                <div style="height: 1px; background: #f1f5f9; margin: 12px 0 20px;"></div>

                                <form action="{{ route('profile.update.password') }}" method="POST">
                                    @csrf
                                    <div class="p-form-group">
                                        <label class="p-form-label">Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="p-form-input" required>
                                    </div>

                                    <div class="p-form-group">
                                        <label class="p-form-label">Mật khẩu mới</label>
                                        <input type="password" name="new_password" class="p-form-input" required>
                                    </div>

                                    <div class="p-form-group mb-0">
                                        <label class="p-form-label">Xác nhận mật khẩu mới</label>
                                        <input type="password" name="new_password_confirmation" class="p-form-input"
                                            required>
                                    </div>

                                    <button type="submit" class="btn-update-pwd">Cập nhật mật khẩu</button>

                                    <p
                                        style="font-size: 0.8rem; color: #64748b; margin-top: 12px; line-height: 1.5; text-align: center;">
                                        Nếu đăng nhập bằng Google, hệ thống không hỗ trợ đổi mật khẩu tại đây.
                                    </p>
                                </form>
                            </div>
                        </div>

                        <div class="p-card full-height">
                            <h3 class="p-card-title large">Thông tin cá nhân</h3>
                            <p class="p-card-desc border-bottom">Phục vụ hiển thị và cá nhân hóa nội dung học tập của bạn.
                            </p>

                            <form id="main-profile-form" action="{{ route('profile.update.info') }}" method="POST"
                                enctype="multipart/form-data" style="display: flex; flex-direction: column; height: 100%;">
                                @csrf

                                <div class="p-form-row">
                                    <div class="p-form-group mb-0">
                                        <label class="p-form-label">Họ và tên</label>
                                        <input type="text" name="ho_ten" value="{{ Auth::user()->ho_ten }}"
                                            class="p-form-input" required>
                                    </div>
                                    <div class="p-form-group mb-0">
                                        <label class="p-form-label">Tên đăng nhập (Mã SV)</label>
                                        <input type="text" value="{{ Auth::user()->ma_sv }}" class="p-form-input"
                                            disabled>
                                    </div>
                                </div>

                                <div class="p-form-row">
                                    <div class="p-form-group mb-0">
                                        <label class="p-form-label">Email</label>
                                        <input type="email" value="{{ Auth::user()->email }}" class="p-form-input"
                                            disabled>
                                    </div>
                                    <div class="p-form-group mb-0">
                                        <label class="p-form-label">Số điện thoại</label>
                                        <input type="text" name="so_dien_thoai"
                                            value="{{ Auth::user()->so_dien_thoai }}" class="p-form-input">
                                    </div>
                                </div>

                                <div class="p-form-group mb-24">
                                    <label class="p-form-label">Ghi chú / Giới thiệu</label>
                                    <textarea name="ghi_chu" rows="4" placeholder="Thông tin thêm (nếu có)" class="p-form-input"
                                        style="resize: vertical;"></textarea>
                                </div>

                                <div class="p-card-footer">
                                    <p>Thông tin được dùng để hệ thống nhận diện và cấp chứng chỉ sau khóa học.</p>
                                    <button type="submit" class="btn-save-settings">Lưu thay đổi</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
    </section>
    <script>
        // Hàm hiển thị ảnh xem trước khi người dùng vừa chọn file
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgElement = document.getElementById('avatar-preview-img');
                    const placeholder = document.getElementById('avatar-placeholder-lg');

                    // Nếu đang có ảnh sẵn thì đổi source
                    if (imgElement) {
                        imgElement.src = e.target.result;
                    }
                    // Nếu chưa có ảnh (đang hiện chữ cái) thì tạo thẻ img đè lên
                    else if (placeholder) {
                        const img = document.createElement('img');
                        img.id = 'avatar-preview-img';
                        img.src = e.target.result;
                        img.className = 'avatar-img-lg';
                        placeholder.parentNode.replaceChild(img, placeholder);
                    }
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const sidebar = document.querySelector('.profile-sidebar-card');
        
        // Sau khi trang load xong 100ms thì thêm class 'show-up'
        setTimeout(() => {
            sidebar.classList.add('show-up');
        }, 100);
    });
</script>
@endsection
