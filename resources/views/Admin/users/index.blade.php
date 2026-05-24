@extends('Admin.layouts.admin')
@section('title', 'Quản lý người dùng')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm người dùng mới</span>
    </button>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger"
            style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <strong>✗</strong> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger"
            style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .filters-section {
            background: white; border-radius: 12px;
            padding: 24px; margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .filter-group { display: grid; gap: 16px; align-items: end; }
        .filter-item label { display: block; font-weight: 500; margin-bottom: 8px; color: #374151; font-size: 14px; }
        .search-box { position: relative; display: flex; align-items: center; }
        .search-box svg { position: absolute; left: 12px; color: #9CA3AF; pointer-events: none; }
        .search-box input {
            width: 100%; padding: 10px 12px 10px 40px;
            border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; transition: all 0.2s;
        }
        .search-box input:focus { outline: none; border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .form-select {
            width: 100%; padding: 10px 12px; border: 1px solid #E5E7EB;
            border-radius: 8px; font-size: 14px; background: white; cursor: pointer; transition: all 0.2s;
        }
        .form-select:focus { outline: none; border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .btn {
            padding: 10px 20px; border-radius: 8px; font-weight: 500; font-size: 14px;
            border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary { background: #4F46E5; color: white; }
        .btn-primary:hover { background: #4338CA; }
        .btn-secondary { background: #F3F4F6; color: #374151; }
        .btn-secondary:hover { background: #E5E7EB; }
        .table-section { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .table-title { display: flex; align-items: center; gap: 12px; }
        .table-title h3 { margin: 0; font-size: 20px; font-weight: 600; color: #111827; }
        .count-badge { background: #EEF2FF; color: #4F46E5; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 500; }
        .table-responsive { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th {
            background: #F9FAFB; padding: 12px; text-align: left; font-weight: 600;
            font-size: 13px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #E5E7EB;
        }
        .data-table tbody td { padding: 16px 12px; border-bottom: 1px solid #F3F4F6; font-size: 14px; color: #374151; }
        .data-table tbody tr:hover { background: #F9FAFB; }
        .row-trashed { background-color: #fef2f2 !important; opacity: 0.85; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; }
        .badge-student  { background: rgba(59,130,246,0.1); color: #2563EB; }
        .badge-teacher  { background: rgba(16,185,129,0.1); color: #059669; }
        .badge-admin    { background: rgba(239,68,68,0.1);  color: #DC2626; }
        .badge-male     { background: rgba(59,130,246,0.1); color: #2563EB; }
        .badge-female   { background: rgba(236,72,153,0.1); color: #DB2777; }
        .badge-active   { background: rgba(16,185,129,0.1); color: #059669; }
        .badge-inactive { background: rgba(239,68,68,0.1);  color: #DC2626; }
        .badge-deleted  { background: #fee2e2; color: #991b1b; }
        /* action buttons → kế thừa từ pages.css (giống trang mon học) */
        .action-buttons { display: flex; gap: 8px; }
        .btn-restore { background: rgba(16,185,129,0.1); color: #10B981; width: 32px; height: 32px; border: none; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .btn-restore svg { width: 16px; height: 16px; }
        .btn-restore:hover { background: #10B981; color: white; }
        .avatar-wrapper { display: flex; align-items: center; gap: 10px; }
        .avatar-wrapper img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #E5E7EB; }
        .avatar-wrapper strong { color: #111827; font-weight: 500; }
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px); animation: fadeIn 0.2s;
        }
        .modal-overlay.show { display: flex !important; align-items: center; justify-content: center; }
        .modal-card {
            background: white; border-radius: 12px; width: 90%; max-width: 900px;
            max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); animation: slideUp 0.3s;
        }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; font-size: 20px; font-weight: 600; color: #111827; }
        .close-btn {
            width: 32px; height: 32px; border-radius: 6px; border: none; background: #F3F4F6;
            color: #6B7280; font-size: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;
        }
        .close-btn:hover { background: #E5E7EB; color: #374151; }
        .modal-body { padding: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 8px; color: #374151; font-size: 14px; }
        .form-input {
            width: 100%; padding: 10px 12px; border: 1px solid #E5E7EB;
            border-radius: 8px; font-size: 14px; transition: all 0.2s; box-sizing: border-box;
        }
        .form-input:focus { outline: none; border-color: #4F46E5; box-shadow: 0 0 0 3px rgba(79,70,229,0.1); }
        .text-danger { color: #DC2626; font-size: 12px; margin-top: 4px; display: block; }
        .text-muted  { color: #6B7280;  font-size: 12px; margin-top: 4px; display: block; }
        .modal-footer { padding: 20px 24px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; gap: 12px; }
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 24px; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; color: #374151; text-decoration: none; transition: all 0.2s; }
        .pagination a:hover { background: #F3F4F6; }
        .pagination .active { background: #4F46E5; color: white; border-color: #4F46E5; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>

    {{-- ── FILTER ── --}}
    <section class="filters-section">
        <div class="filter-group" style="grid-template-columns: repeat(4, 1fr)">
            <div class="filter-item">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16">
                        <circle cx="11" cy="11" r="8" /><path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" placeholder="Tên, mã SV..." id="search-user" value="{{ request('search') }}">
                </div>
            </div>
            <div class="filter-item">
                <label>Vai trò</label>
                <select class="form-select" id="filter-role">
                    <option value="">Tất cả vai trò</option>
                    @foreach ($dsRole as $role)
                        <option value="{{ $role->id }}" {{ request('vai_tro_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->ten_nhom_quyen ?? $role->ten_vai_tro }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Trạng thái</label>
                <select class="form-select" id="filter-status">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Đã khóa</option>
                </select>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end; gap: 8px">
                <button class="btn btn-secondary" style="flex: 1" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Làm lại
                </button>
                <button class="btn btn-primary" style="flex: 1" onclick="applyFilters()">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </div>
    </section>

    {{-- ── TABLE ── --}}
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách tài khoản hệ thống</h3>
                    <span class="count-badge">Tổng số: {{ $dsUser->total() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Họ tên</th>
                            <th>Mã SV/GV</th>
                            <th>Email</th>
                            <th>SĐT / Giới tính</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dsUser as $user)
                            <tr class="{{ $user->trashed() ? 'row-trashed' : '' }}">
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="avatar-wrapper">
                                        {{--
                                            ✅ FIX LỖI 1 & 2: Avatar 404
                                            Bug cũ: avatar_url đã chứa URL đầy đủ (https://...) từ Google
                                            nhưng vẫn bị prepend thêm /storage/ → thành /storage/https://... → 404
                                            Fix: kiểm tra nếu avatar_url bắt đầu bằng http thì dùng thẳng,
                                            còn không thì mới prepend storage/
                                        --}}
                                        @php
                                            $avatarSrc = 'https://ui-avatars.com/api/?name=' . urlencode($user->ho_ten) . '&background=4F46E5&color=fff';
                                            if ($user->avatar_url) {
                                                $avatarSrc = str_starts_with($user->avatar_url, 'http')
                                                    ? $user->avatar_url
                                                    : \Illuminate\Support\Facades\Storage::url($user->avatar_url);
                                            }
                                        @endphp
                                        <img src="{{ $avatarSrc }}" alt="Avatar"
                                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->ho_ten) }}&background=4F46E5&color=fff'">
                                        <strong>{{ $user->ho_ten }}</strong>
                                    </div>
                                </td>
                                <td>{{ $user->ma_sv ?? 'N/A' }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <div>{{ $user->so_dien_thoai ?? 'N/A' }}</div>
                                    <div style="margin-top: 4px;">
                                        @if ($user->gioi_tinh)
                                            <span class="badge badge-{{ $user->gioi_tinh == 'MALE' ? 'male' : 'female' }}">
                                                {{ $user->gioi_tinh == 'MALE' ? 'Nam' : 'Nữ' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($user->role->ma_vai_tro ?? 'student') }}">
                                        {{ $user->role->ten_nhom_quyen ?? ($user->role->ten_vai_tro ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($user->trashed())
                                        <span class="badge badge-deleted">
                                            <i class="fas fa-trash-alt"></i> Đã xóa mềm
                                        </span>
                                    @else
                                        <span class="badge badge-{{ $user->trang_thai ? 'active' : 'inactive' }}">
                                            {{ $user->trang_thai ? 'Hoạt động' : 'Đã khóa' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if ($user->trashed())
                                            <button class="btn-action btn-restore"
                                                onclick="restoreUser({{ $user->id }}, '{{ addslashes($user->ho_ten) }}')"
                                                title="Khôi phục">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="23 4 23 10 17 10"></polyline>
                                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                                </svg>
                                            </button>
                                            <button class="btn-action btn-delete"
                                                onclick="forceDeleteUser({{ $user->id }}, '{{ addslashes($user->ho_ten) }}')"
                                                title="Xóa vĩnh viễn">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M21 4H8l-7 8 7 8h13a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"></path>
                                                    <line x1="18" y1="9" x2="12" y2="15"></line>
                                                    <line x1="12" y1="9" x2="18" y2="15"></line>
                                                </svg>
                                            </button>
                                        @else
                                            <button class="btn-action btn-edit"
                                                onclick="editUser({{ $user->id }})" title="Sửa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </button>
                                            <button type="button" class="btn-action btn-delete"
                                                onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->ho_ten) }}')"
                                                title="Xóa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px; color: #6B7280;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                                    <p>Không có dữ liệu</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

           <div class="table-footer">
    <div class="showing-info">
        Hiển thị {{ $dsUser->firstItem() ?? 0 }}-{{ $dsUser->lastItem() ?? 0 }} trong tổng số
        {{ $dsUser->total() }} người dùng
    </div>

    @if ($dsUser->hasPages())
        <div class="pagination" style="list-style: none;">
            <style>
                .pagination ul, .pagination li { list-style: none !important; margin: 0; padding: 0; }
            </style>
            {{ $dsUser->appends(request()->all())->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
        </div>
    </section>

    {{-- ── MODAL THÊM / SỬA ── --}}
    <div id="modalUser" class="modal-overlay" onclick="closeModal('modalUser')" style="display: none;">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm người dùng mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalUser')">&times;</button>
            </div>

            <form id="formUser" action="{{ route('admin.users.storeOrUpdate') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                {{-- ✅ value="" rõ ràng để filled() trong controller nhận đúng khi thêm mới --}}
                <input type="hidden" name="id" id="input_id" value="">

                <div class="modal-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
                    <div class="form-group">
                        <label>Họ và tên <span style="color: red">*</span></label>
                        <input type="text" name="ho_ten" id="input_ho_ten" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Mã SV/GV</label>
                        <input type="text" name="ma_sv" id="input_ma_sv" class="form-input" placeholder="VD: SV001, GV001">
                    </div>

                    <div class="form-group">
                        <label>Email <span style="color: red">*</span></label>
                        <input type="email" name="email" id="input_email" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="so_dien_thoai" id="input_so_dien_thoai" class="form-input" placeholder="0123456789">
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu <span style="color: red" id="required_password">*</span></label>
                        <input type="password" name="mat_khau" id="input_mat_khau" class="form-input">
                        <small class="text-muted">Để trống nếu không muốn đổi (khi sửa)</small>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu <span style="color: red" id="required_password_confirm">*</span></label>
                        <input type="password" name="mat_khau_confirmation" id="input_mat_khau_confirmation" class="form-input">
                    </div>

                    <div class="form-group">
                        <label>Vai trò <span style="color: red">*</span></label>
                        <select name="vai_tro_id" id="input_vai_tro_id" class="form-select" required>
                            <option value="">-- Chọn vai trò --</option>
                            @foreach ($dsRole as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->ten_nhom_quyen ?? $role->ten_vai_tro }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Giới tính</label>
                        <select name="gioi_tinh" id="input_gioi_tinh" class="form-select">
                            <option value="">-- Chọn giới tính --</option>
                            <option value="MALE">Nam</option>
                            <option value="FEMALE">Nữ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="ngay_sinh" id="input_ngay_sinh" class="form-input">
                    </div>

                    <div class="form-group" style="grid-column: span 2">
                        <label>Địa chỉ</label>
                        <textarea name="dia_chi" id="input_dia_chi" class="form-input" rows="2"></textarea>
                    </div>

                    <div class="form-group" style="grid-column: span 2">
                        <label>Ảnh đại diện</label>
                        <input type="file" name="avatar_url" id="avatar_url_input" class="form-input"
                            accept="image/*" onchange="previewAvatar(event)">
                        <small class="text-muted">Định dạng: jpeg, png, jpg. Kích thước tối đa: 2MB</small>
                        <div id="avatar_preview" style="display: none; margin-top: 10px;">
                            <img id="preview_avatar" src=""
                                style="max-width: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <div style="display: flex; gap: 15px; margin-top: 8px;">
                            <label style="cursor: pointer;">
                                <input type="radio" name="trang_thai" id="status_active" value="1" checked> Hoạt động
                            </label>
                            <label style="cursor: pointer;">
                                <input type="radio" name="trang_thai" id="status_inactive" value="0"> Khóa
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalUser')">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save"></i> Lưu thông tin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ─── MỞ MODAL THÊM MỚI ───
        function openModalAdd() {
            const modal = document.getElementById('modalUser');
            const form  = document.getElementById('formUser');

            document.getElementById('modalTitle').innerText = "Thêm người dùng mới";
            form.reset();

            // ✅ Đảm bảo input_id rỗng khi thêm mới
            document.getElementById('input_id').value = "";
            document.getElementById('avatar_preview').style.display = 'none';
            document.getElementById('status_active').checked = true;

            // Bắt buộc nhập mật khẩu khi thêm mới
            document.getElementById('required_password').style.display = 'inline';
            document.getElementById('required_password_confirm').style.display = 'inline';
            document.getElementById('input_mat_khau').setAttribute('required', 'required');
            document.getElementById('input_mat_khau_confirmation').setAttribute('required', 'required');

            modal.classList.add('show');
            modal.style.display = "";
        }

        // ─── MỞ MODAL SỬA ───
        function editUser(id) {
            fetch(`/admin/users/${id}/get-info`)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;
                        document.getElementById('modalTitle').innerText = "Chỉnh sửa thông tin";

                        document.getElementById('input_id').value            = data.id;
                        document.getElementById('input_ho_ten').value        = data.ho_ten || '';
                        document.getElementById('input_ma_sv').value         = data.ma_sv || '';
                        document.getElementById('input_email').value         = data.email || '';
                        document.getElementById('input_so_dien_thoai').value = data.so_dien_thoai || '';
                        document.getElementById('input_vai_tro_id').value    = data.vai_tro_id || '';
                        document.getElementById('input_gioi_tinh').value     = data.gioi_tinh || '';
                        document.getElementById('input_ngay_sinh').value     = data.ngay_sinh || '';
                        document.getElementById('input_dia_chi').value       = data.dia_chi || '';

                        // Trạng thái
                        document.getElementById(data.trang_thai ? 'status_active' : 'status_inactive').checked = true;

                        // Bỏ bắt buộc nhập mật khẩu khi sửa
                        document.getElementById('input_mat_khau').value = '';
                        document.getElementById('input_mat_khau_confirmation').value = '';
                        document.getElementById('required_password').style.display = 'none';
                        document.getElementById('required_password_confirm').style.display = 'none';
                        document.getElementById('input_mat_khau').removeAttribute('required');
                        document.getElementById('input_mat_khau_confirmation').removeAttribute('required');

                        // ✅ FIX LỖI AVATAR: kiểm tra http trước khi prepend /storage/
                        if (data.avatar_url) {
                            const src = data.avatar_url.startsWith('http')
                                ? data.avatar_url
                                : '{{ rtrim(\Illuminate\Support\Facades\Storage::url(''), '/') }}/' + data.avatar_url;
                            document.getElementById('preview_avatar').src = src;
                            document.getElementById('avatar_preview').style.display = 'block';
                        } else {
                            document.getElementById('avatar_preview').style.display = 'none';
                        }

                        const modal = document.getElementById('modalUser');
                        modal.classList.add('show');
                        modal.style.display = "";
                    } else {
                        alert(result.message || 'Không thể tải thông tin!');
                    }
                })
                .catch(() => alert('Lỗi kết nối máy chủ!'));
        }

        // ─── ĐÓNG MODAL ───
        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('show');
            modal.style.display = "none";
        }

        // ─── PREVIEW AVATAR ───
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_avatar').src = e.target.result;
                    document.getElementById('avatar_preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // ─── XÓA MỀM ───
        function deleteUser(id, name) {
            if (confirm(`Bạn có chắc chắn muốn đưa "${name}" vào thùng rác?`)) {
                fetch(`/admin/users/destroy/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); else alert(data.message); })
                .catch(() => alert('Lỗi kết nối máy chủ!'));
            }
        }

        // ─── KHÔI PHỤC ───
        function restoreUser(id, name) {
            if (confirm(`Bạn có chắc chắn muốn khôi phục tài khoản của "${name}"?`)) {
                fetch(`/admin/users/restore/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); else alert(data.message); })
                .catch(() => alert('Lỗi kết nối máy chủ!'));
            }
        }

        // ─── XÓA VĨNH VIỄN ───
        function forceDeleteUser(id, name) {
            if (confirm(`CẢNH BÁO: Bạn chuẩn bị xóa vĩnh viễn "${name}" khỏi Database. Hành động này không thể hoàn tác!\n\nBạn có chắc chắn không?`)) {
                fetch(`/admin/users/force-delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); else alert(data.message); })
                .catch(() => alert('Lỗi kết nối máy chủ!'));
            }
        }

        // ─── FILTER ───
        function applyFilters() {
            const params = new URLSearchParams();
            const search = document.getElementById('search-user').value;
            const role   = document.getElementById('filter-role').value;
            const status = document.getElementById('filter-status').value;
            if (search) params.append('search', search);
            if (role)   params.append('vai_tro_id', role);
            if (status !== '') params.append('trang_thai', status);
            window.location.href = '{{ route('admin.users.index') }}?' + params.toString();
        }

        function resetFilters() {
            window.location.href = '{{ route('admin.users.index') }}';
        }

        document.getElementById('search-user').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });

        // ─── AUTO HIDE ALERT ───
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
@endsection
