@extends('Admin.layouts.admin')
@section('title', 'Phân quyền hệ thống')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAddRole()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm vai trò mới</span>
    </button>
@endsection

@section('content')
    <style>
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            max-width: 90%;
            width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
        }

        .close-btn {
            border: none;
            background: #f3f4f6;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: #e5e7eb;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .permission-module {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 15px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e5e7eb;
            margin-bottom: 10px;
        }

        .module-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            color: #1f2937;
            cursor: pointer;
        }

        .module-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-actions {
            display: flex;
            gap: 12px;
        }

        .action-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #f3f4f6;
            color: #6b7280;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-toggle:hover {
            background: #e5e7eb;
        }

        .action-toggle.active {
            background: #3b82f6;
            color: white;
            border-color: #2563eb;
        }

        .hidden-permissions {
            display: none;
        }

        /* Style cho checkbox */
        .module-title input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .module-title input[type="checkbox"]:indeterminate {
            background-color: #f59e0b;
            border-color: #f59e0b;
        }
    </style>
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



    <!-- Filter Section -->
    <section class="filters-section">
        <form action="{{ route('admin.phanquyen.index') }}" method="GET" class="filter-group"
            style="grid-template-columns: 2fr 1fr;">
            <div class="filter-item">
                <label>Tìm kiếm vai trò</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nhập tên vai trò..." />
                </div>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
                @if (request()->has('search'))
                    <a href="{{ route('admin.phanquyen.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </section>

    <!-- Table Section -->
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách vai trò hệ thống</h3>
                    <span class="count-badge">Tổng số: {{ $dsRole->count() }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>Tên vai trò</th>
                            <th>Mô tả</th>
                            <th width="150">Số người dùng</th>
                            <th width="150">Số quyền</th>
                            <th width="120">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dsRole as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>
                                    <strong style="color: #111827;">{{ $role->ten_nhom_quyen }}</strong>
                                </td>
                                <td>
                                    @if ($role->mo_ta)
                                        <span title="{{ $role->mo_ta }}">{{ \Str::limit($role->mo_ta, 50) }}</span>
                                    @else
                                        <span style="color: #9CA3AF;">Chưa có mô tả</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $role->users_count > 0 ? 'student' : 'secondary' }}"
                                        style="background: {{ $role->users_count > 0 ? 'rgba(59, 130, 246, 0.1)' : 'rgba(107, 114, 128, 0.1)' }}; 
                                           color: {{ $role->users_count > 0 ? '#2563EB' : '#6B7280' }};">
                                        <i class="fas fa-users"></i> {{ $role->users_count ?? 0 }} người dùng
                                    </span>
                                </td>
                                <td>
                                    @php
                                        // Tính tổng tất cả các hành động được tích true trong các quyền của Role
                                        $totalActions = $role->permissions->reduce(function ($carry, $p) {
                                            return $carry +
                                                ($p->pivot->can_view +
                                                    $p->pivot->can_create +
                                                    $p->pivot->can_update +
                                                    $p->pivot->can_delete);
                                        }, 0);
                                    @endphp
                                    <div class="permission-stack">
                                        <span class="badge"
                                            style="background: {{ $totalActions > 0 ? '#4f46e5' : '#94a3b8' }}; color: white; padding: 5px 10px; border-radius: 20px;">
                                            <i class="fas fa-shield-alt"></i>
                                            <strong>{{ $totalActions }}</strong> hành động
                                        </span>
                                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">
                                            ({{ $role->permissions_count }} chức năng)
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="editRole({{ $role->id }})"
                                            title="Sửa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        @if ($role->users_count == 0)
                                            <button class="btn-action btn-delete" onclick="deleteRole({{ $role->id }})"
                                                title="Xóa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #6B7280;">
                                    <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;"></i>
                                    <p>Chưa có vai trò nào</p>
                                    <button class="btn btn-primary" onclick="openModalAddRole()" style="margin-top: 12px;">
                                        <i class="fas fa-plus"></i> Thêm vai trò đầu tiên
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal Add/Edit Role -->
    <div id="modalRole" class="modal-overlay" onclick="closeModal('modalRole')" style="display: none;">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm vai trò mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalRole')">&times;</button>
            </div>

            <form id="formRole" action="{{ route('admin.phanquyen.save') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên vai trò <span style="color: red">*</span></label>
                        <input type="text" name="ten_nhom_quyen" id="input_ten_nhom_quyen" class="form-input"
                            placeholder="VD: Giảng viên, Sinh viên, Quản trị viên" required>
                        <small class="text-danger" id="error_ten_nhom_quyen"></small>
                    </div>

                    <div class="form-group">
                        <label>Mô tả</label>
                        <textarea name="mo_ta" id="input_mo_ta" class="form-input" rows="3" placeholder="Mô tả vai trò này..."></textarea>
                        <small class="text-muted">Giải thích ngắn gọn về vai trò này</small>
                    </div>
                    <div class="form-group">
                        <label>Phân quyền <span style="color: red">*</span></label>
                        <div class="permissions-section">
                            @foreach ($dsPermission as $module => $permissions)
                                <div class="permission-module">
                                    <div class="module-header">
                                        <label class="module-title">
                                            <input type="checkbox" class="module-main-checkbox"
                                                data-module="{{ $module }}"
                                                onchange="toggleModule(this, '{{ $module }}')">
                                            <strong class="module-name">
                                                @php
                                                    $moduleNames = [
                                                        'users' => ['icon' => 'fas fa-users', 'name' => 'NGƯỜI DÙNG'],
                                                        'roles' => [
                                                            'icon' => 'fas fa-user-shield',
                                                            'name' => 'VAI TRÒ',
                                                        ],
                                                        'permissions' => [
                                                            'icon' => 'fas fa-key',
                                                            'name' => 'QUYỀN HỆ THỐNG',
                                                        ],

                                                        'subjects' => ['icon' => 'fas fa-book', 'name' => 'MÔN HỌC'],
                                                        'chapters' => [
                                                            'icon' => 'fas fa-folder-open',
                                                            'name' => 'CHƯƠNG HỌC',
                                                        ],
                                                        'lessons' => [
                                                            'icon' => 'fas fa-book-open',
                                                            'name' => 'BÀI HỌC',
                                                        ],

                                                        'classrooms' => [
                                                            'icon' => 'fas fa-chalkboard-teacher',
                                                            'name' => 'LỚP HỌC',
                                                        ],
                                                        'grades' => ['icon' => 'fas fa-layer-group', 'name' => 'KHỐI'],
                                                        'school-years' => [
                                                            'icon' => 'fas fa-calendar-alt',
                                                            'name' => 'NĂM HỌC',
                                                        ],

                                                        'questions' => [
                                                            'icon' => 'fas fa-question-circle',
                                                            'name' => 'CÂU HỎI',
                                                        ],
                                                        'exams' => [
                                                            'icon' => 'fas fa-file-alt',
                                                            'name' => 'BÀI KIỂM TRA',
                                                        ],

                                                        'results' => [
                                                            'icon' => 'fas fa-chart-bar',
                                                            'name' => 'KẾT QUẢ',
                                                        ],
                                                        'badges' => ['icon' => 'fas fa-award', 'name' => 'HUY HIỆU'],

                                                        // Module khác (nếu có slug không khớp)
                                                        'other' => ['icon' => 'fas fa-cog', 'name' => 'KHÁC'],
                                                    ];

                                                    $moduleInfo = $moduleNames[$module] ?? $moduleNames['other'];
                                                @endphp
                                                <i class="{{ $moduleInfo['icon'] }}"></i> {{ $moduleInfo['name'] }}
                                            </strong>
                                        </label>

                                        <!-- 4 nút quyền con -->
                                        <div class="module-actions">
                                            <span class="action-toggle" data-action="view"
                                                onclick="toggleAction(this, '{{ $module }}', 'view')">
                                                <i class="fas fa-eye"></i> Xem
                                            </span>
                                            <span class="action-toggle" data-action="create"
                                                onclick="toggleAction(this, '{{ $module }}', 'create')">
                                                <i class="fas fa-plus"></i> Thêm
                                            </span>
                                            <span class="action-toggle" data-action="update"
                                                onclick="toggleAction(this, '{{ $module }}', 'update')">
                                                <i class="fas fa-edit"></i> Sửa
                                            </span>
                                            <span class="action-toggle" data-action="delete"
                                                onclick="toggleAction(this, '{{ $module }}', 'delete')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Hidden inputs cho từng permission trong module -->
                                    <div class="hidden-permissions">
                                        @foreach ($permissions as $permission)
                                            <div class="permission-row" data-permission-id="{{ $permission->id }}">
                                                <input type="hidden" name="permissions[{{ $permission->id }}][view]"
                                                    class="perm-{{ $module }}-view" value="0">
                                                <input type="hidden" name="permissions[{{ $permission->id }}][create]"
                                                    class="perm-{{ $module }}-create" value="0">
                                                <input type="hidden" name="permissions[{{ $permission->id }}][update]"
                                                    class="perm-{{ $module }}-update" value="0">
                                                <input type="hidden" name="permissions[{{ $permission->id }}][delete]"
                                                    class="perm-{{ $module }}-delete" value="0">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalRole')">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu vai trò
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- index.blade.php - Phần modal permissions -->


    <style>
        .permission-module {
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 15px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .module-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px dashed #e5e7eb;
            margin-bottom: 10px;
        }

        .module-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            color: #1f2937;
            cursor: pointer;
        }

        .module-name {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .module-actions {
            display: flex;
            gap: 12px;
        }

        .action-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            background: #f3f4f6;
            color: #6b7280;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-toggle:hover {
            background: #e5e7eb;
        }

        .action-toggle.active {
            background: #3b82f6;
            color: white;
            border-color: #2563eb;
        }

        .hidden-permissions {
            display: none;
        }

        /* Style cho checkbox */
        .module-title input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .modal-overlay.show {
            display: flex !important;
            /* Thêm !important để ưu tiên */
        }

        .module-title input[type="checkbox"]:indeterminate {
            background-color: #f59e0b;
            border-color: #f59e0b;
        }
    </style>

    <script>
        // Biến lưu trữ thông tin permissions
        let permissionData = {};

        // Khi mở modal edit, load permission data
        function editRole(id) {
            fetch(`/admin/phanquyen/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    const modal = document.getElementById('modalRole');
                    if (modal) {
                        document.getElementById('modalTitle').innerText = "Chỉnh sửa vai trò";
                        document.getElementById('input_id').value = data.id;
                        document.getElementById('input_ten_nhom_quyen').value = data.ten_nhom_quyen;
                        document.getElementById('input_mo_ta').value = data.mo_ta || '';

                        // Reset tất cả permissions
                        resetAllPermissions();

                        // Lưu permission data để xử lý
                        permissionData = data.permissions || {};

                        // Áp dụng permissions từ database
                        applyPermissionsFromData(permissionData);

                        modal.classList.add('show');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Không thể tải thông tin vai trò!');
                });
        }

        // Reset tất cả permissions về trạng thái ban đầu
        function resetAllPermissions() {
            // Reset module checkboxes
            document.querySelectorAll('.module-main-checkbox').forEach(cb => {
                cb.checked = false;
                cb.indeterminate = false;
            });

            // Reset action toggles
            document.querySelectorAll('.action-toggle').forEach(toggle => {
                toggle.classList.remove('active');
            });

            // Reset hidden inputs
            document.querySelectorAll('.hidden-permissions input').forEach(input => {
                input.value = '0';
            });
        }
        // Áp dụng permissions từ dữ liệu database vào giao diện Modal
        function applyPermissionsFromData(permissions) {
            // Duyệt qua từng module lớn trong giao diện (USER, ROLE, SUBJECT...)
            document.querySelectorAll('.permission-module').forEach(moduleDiv => {
                const moduleCheckbox = moduleDiv.querySelector('.module-main-checkbox');
                const moduleName = moduleCheckbox.dataset.module;

                // Duyệt qua từng hàng chứa ID quyền ẩn trong module này
                moduleDiv.querySelectorAll('.permission-row').forEach(row => {
                    const pId = row.dataset.permissionId;
                    const pData = permissions[pId]; // Lấy object {can_view: true, ...} từ Controller

                    if (pData) {
                        const actions = ['view', 'create', 'update', 'delete'];

                        actions.forEach(act => {
                            // 1. Lấy giá trị từ dữ liệu Controller (can_view, can_create...)
                            const isAllowed = pData[`can_${act}`] === true || pData[
                                `can_${act}`] === 1;
                            const val = isAllowed ? '1' : '0';

                            // 2. Cập nhật giá trị vào các input hidden để gửi form
                            const hiddenInput = row.querySelector(`.perm-${moduleName}-${act}`);
                            if (hiddenInput) {
                                hiddenInput.value = val;
                            }

                            // 3. Cập nhật giao diện nút bấm (Action Toggle)
                            // Lưu ý: Chỉ cần 1 quyền con trong module có 'view=1' thì nút 'Xem' sẽ sáng
                            if (isAllowed) {
                                const btn = moduleDiv.querySelector(
                                    `.action-toggle[data-action="${act}"]`);
                                if (btn) {
                                    btn.classList.add('active');
                                }
                            }
                        });
                    }
                });

                // 4. Sau khi duyệt xong các quyền con, cập nhật trạng thái ô tích tổng của Module
                updateModuleCheckboxState(moduleName);
            });
        }

        // Toggle toàn bộ module
        function toggleModule(checkbox, module) {
            const isChecked = checkbox.checked;
            const actionToggles = document.querySelectorAll(`.action-toggle[onclick*="${module}"]`);

            // Update action toggles
            actionToggles.forEach(toggle => {
                if (isChecked) {
                    toggle.classList.add('active');
                } else {
                    toggle.classList.remove('active');
                }
            });

            // Update hidden inputs cho tất cả permissions trong module
            const hiddenInputs = document.querySelectorAll(
                `.perm-${module}-view, .perm-${module}-create, .perm-${module}-update, .perm-${module}-delete`);
            hiddenInputs.forEach(input => {
                input.value = isChecked ? '1' : '0';
            });

            // Reset indeterminate
            checkbox.indeterminate = false;
        }

        // Toggle action cho module
        function toggleAction(element, module, action) {
            element.classList.toggle('active');
            const isActive = element.classList.contains('active');

            // Update hidden inputs cho action này trong module
            const hiddenInputs = document.querySelectorAll(`.perm-${module}-${action}`);
            hiddenInputs.forEach(input => {
                input.value = isActive ? '1' : '0';
            });

            // Update module checkbox state
            updateModuleCheckboxState(module);
        }

        // Cập nhật trạng thái checkbox module
        function updateModuleCheckboxState(module) {
            const moduleCheckbox = document.querySelector(`.module-main-checkbox[data-module="${module}"]`);
            const actionToggles = document.querySelectorAll(`.action-toggle[onclick*="${module}"]`);

            // Kiểm tra xem tất cả actions có được active không
            let allActive = true;
            let anyActive = false;

            actionToggles.forEach(toggle => {
                if (toggle.classList.contains('active')) {
                    anyActive = true;
                } else {
                    allActive = false;
                }
            });

            if (allActive) {
                moduleCheckbox.checked = true;
                moduleCheckbox.indeterminate = false;
            } else if (anyActive) {
                moduleCheckbox.checked = false;
                moduleCheckbox.indeterminate = true;
            } else {
                moduleCheckbox.checked = false;
                moduleCheckbox.indeterminate = false;
            }
        }

        // Khi thêm mới role
        function openModalAddRole() {
            const modal = document.getElementById('modalRole');
            const form = document.getElementById('formRole');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Thêm vai trò mới";
                if (form) {
                    form.reset();
                    document.getElementById('input_id').value = "";

                    // Reset tất cả permissions
                    resetAllPermissions();
                }
                modal.classList.add('show');
            }
        }

        // Đóng modal
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
            }
        }

        // Xóa role
        function deleteRole(id) {
            if (confirm('Bạn có chắc chắn muốn xóa vai trò này?')) {
                fetch(`/admin/phanquyen/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Không thể xóa vai trò!');
                    });
            }
        }
    </script>
@endsection
