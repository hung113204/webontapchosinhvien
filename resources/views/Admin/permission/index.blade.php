@extends('Admin.layouts.admin')
@section('title', 'Quản lý danh mục quyền')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAddPermission()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <i class="fas fa-plus"></i>
        <span style="font-weight: 500;">Thêm quyền mới</span>
    </button>
@endsection

@section('content')
    <style>
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
            width: 500px;
            max-width: 90%;
            border-radius: 12px;
            padding: 20px;
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

        .form-group small {
            color: #6b7280;
            font-size: 12px;
            display: block;
            margin-top: 4px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
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

    <section class="filters-section">
        <form action="{{ route('admin.permission.index') }}" method="GET" class="filter-group"
            style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px;">
            <div class="filter-item">
                <label>Tìm kiếm</label>
                <div class="search-box" style="position: relative;">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tên quyền hoặc slug..."
                        style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                </div>
            </div>
            <div class="filter-item">
                <label>Module</label>
                <select name="module" class="form-input"
                    style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                    <option value="">Tất cả Module</option>
                    @foreach ($modules as $m)
                        <option value="{{ $m }}" {{ request('module') == $m ? 'selected' : '' }}>
                            {{ strtoupper($m) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn btn-primary"
                    style="flex: 1; height: 42px; background: #4f46e5; color: white; border: none; border-radius: 8px;">Lọc</button>
                <a href="{{ route('admin.permission.index') }}" class="btn btn-secondary"
                    style="padding: 10px; background: #f3f4f6; border-radius: 8px;">Xóa</a>
            </div>
        </form>
    </section>

    <section class="table-section"
        style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-top: 20px;">
        <div class="table-responsive">
            <table class="data-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 15px; text-align: left;">ID</th>
                        <th style="padding: 15px; text-align: left;">Tên quyền</th>
                        <th style="padding: 15px; text-align: left;">Slug</th>
                        <th style="padding: 15px; text-align: left;">Module</th>
                        <th style="padding: 15px; text-align: center;">Số vai trò dùng</th>
                        <th style="padding: 15px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dsPermission as $per)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px;">#{{ $per->id }}</td>
                            <td style="padding: 15px;">
                                <strong style="color: #1e293b;">{{ $per->name }}</strong>
                            </td>
                            <td style="padding: 15px;"><code
                                    style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $per->slug }}</code>
                            </td>
                            <td style="padding: 15px;">
                                <span class="badge"
                                    style="background: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;">
                                    {{ strtoupper($per->module) }}
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <span class="badge"
                                    style="background: #f3f4f6; color: #374151; padding: 4px 10px; border-radius: 20px;">
                                    {{ $per->roles_count ?? 0 }} vai trò
                                </span>
                            </td>
                            <td style="padding: 15px; text-align: right; white-space: nowrap;">
                                {{-- Nút Sửa - LUÔN HIỂN THỊ --}}
                                <button onclick="editPermission({{ $per->id }})"
                                    style="color: #4f46e5; background: #eff6ff; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin-right: 5px;"
                                    title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>

                                {{-- Nút Xóa - CHỈ HIỂN THỊ KHI: không có vai trò dùng VÀ không phải quyền hệ thống --}}
                                @php
                                    $canDelete =
                                        $per->roles_count == 0 &&
                                        ($per->is_system == 0 || $per->is_system === false || $per->is_system === null);
                                @endphp

                                @if ($canDelete)
                                    <button onclick="deletePermission({{ $per->id }})"
                                        style="color: #ef4444; background: #fef2f2; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;"
                                        title="Xóa">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                @else
                                    <span style="color: #cbd5e1; padding: 8px 12px; font-size: 12px;"
                                        title="@if ($per->roles_count > 0) Đang được {{ $per->roles_count }} vai trò sử dụng @else Quyền hệ thống @endif">
                                        <i class="fas fa-lock"></i> Khóa
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 40px; text-align: center; color: #64748b;">Chưa có quyền nào
                                được định nghĩa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Modal - QUAN TRỌNG: Thêm onclick để đóng modal --}}
    <div id="modalPermission" class="modal-overlay" onclick="closeModal('modalPermission')">
        {{-- QUAN TRỌNG: event.stopPropagation() để click vào card không đóng modal --}}
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle" style="margin: 0;">Thêm quyền mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalPermission')">&times;</button>
            </div>
            <form id="formPermission" action="{{ route('admin.permission.save') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group">
                        <label>Tên quyền <span style="color: red">*</span></label>
                        <input type="text" name="name" id="input_name" class="form-input" required
                            placeholder="Ví dụ: Xem danh sách người dùng">
                    </div>
                    <div class="form-group">
                        <label>Slug <span style="color: red">*</span></label>
                        <input type="text" name="slug" id="input_slug" class="form-input" required
                            placeholder="Ví dụ: users.view">
                        <small>Format: module.action (VD: users.view)</small>
                    </div>
                    <div class="form-group">
                        <label>Module <span style="color: red">*</span></label>
                        <input type="text" name="module" id="input_module" class="form-input" required
                            placeholder="Ví dụ: users, exams...">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalPermission')">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalAddPermission() {
            const modal = document.getElementById('modalPermission');
            const form = document.getElementById('formPermission');

            document.getElementById('modalTitle').innerText = "Thêm quyền mới";
            form.reset();
            document.getElementById('input_id').value = "";

            modal.classList.add('show');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                const form = document.getElementById('formPermission');
                if (form) {
                    form.reset();
                }
            }
        }

        function editPermission(id) {
            fetch(`/admin/permission/${id}/edit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể tải dữ liệu');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('modalTitle').innerText = "Chỉnh sửa quyền";
                    document.getElementById('input_id').value = data.id;
                    document.getElementById('input_name').value = data.name;
                    document.getElementById('input_slug').value = data.slug;
                    document.getElementById('input_module').value = data.module;
                    document.getElementById('modalPermission').classList.add('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Không thể tải thông tin quyền! Vui lòng thử lại.');
                });
        }

        function deletePermission(id) {
            if (confirm('Bạn có chắc chắn muốn xóa quyền này?')) {
                fetch(`/admin/permission/${id}`, {
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
                        alert('Không thể xóa quyền!');
                    });
            }
        }

        // Đóng modal khi nhấn ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('modalPermission');
                if (modal && modal.classList.contains('show')) {
                    closeModal('modalPermission');
                }
            }
        });

        // Auto hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll(".alert");
                alerts.forEach((alert) => {
                    alert.style.transition = "all 0.5s ease";
                    alert.style.opacity = "0";
                    alert.style.transform = "translateY(-10px)";
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });
        // Tự động tạo slug khi nhập tên quyền
        document.getElementById('input_name').addEventListener('keyup', function() {
            let name = this.value;
            // Chỉ tự động tạo slug nếu đang ở chế độ THÊM MỚI (không có ID)
            let id = document.getElementById('input_id').value;

            if (!id) {
                document.getElementById('input_slug').value = strToSlug(name);
            }
        });

        function strToSlug(title) {
            let slug = title.toLowerCase();

            // Chuyển ký tự có dấu thành không dấu
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');

            // Xóa các ký tự đặc biệt
            slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');

            // Chuyển khoảng trắng thành gạch ngang
            slug = slug.replace(/ /gi, "-");

            // Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
            slug = slug.replace(/\-\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-\-/gi, '-');
            slug = slug.replace(/\-\-\-/gi, '-');
            slug = slug.replace(/\-\-/gi, '-');

            // Xóa các ký tự gạch ngang ở đầu và cuối
            slug = '@' + slug + '@';
            slug = slug.replace(/\@\-|\-\@|\@/gi, '');

            return slug;
        }
    </script>
@endsection
