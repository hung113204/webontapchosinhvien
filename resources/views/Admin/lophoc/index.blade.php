@extends('Admin.layouts.admin')
@section('title', 'Quản lý Lớp học')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm lớp học mới</span>
    </button>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
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
    <section class="filters-section">
        <form action="{{ route('admin.lophoc.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Năm học</label>
                <select name="nam_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả năm học</option>
                    @foreach ($dsNamHoc as $nh)
                        <option value="{{ $nh->id }}" {{ request('nam_hoc_id') == $nh->id ? 'selected' : '' }}>
                            {{ $nh->ten_nam_hoc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Khối</label>
                <select name="khoi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả khối</option>
                    @foreach ($dsKhoi as $khoi)
                        <option value="{{ $khoi->id }}" {{ request('khoi_id') == $khoi->id ? 'selected' : '' }}>
                            {{ $khoi->ten_khoi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm lớp</label>
                <div class="search-box">
                    <input type="text" name="keyword" placeholder="Nhập tên lớp..." value="{{ request('keyword') }}" />
                    <button type="submit" style="border:none; background:none;"><svg>...</svg></button>
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách lớp học</h3>
                    <span class="count-badge">Dữ liệu hệ thống</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="100">Mã ID</th>
                            <th>Tên lớp</th>
                            <th>Khối</th>
                            <th>Sĩ số</th>
                            <th>Năm học</th>
                            <th>Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dsLopHoc as $lop)
                            <tr>
                                <td><input type="checkbox" class="checkbox" /></td>
                                <td><span class="id-badge">LH{{ $lop->id }}</span></td>
                                <td><strong>{{ $lop->ten_lop }}</strong></td>
                                <td><span class="badge">{{ $lop->khoi->ten_khoi }}</span></td>
                                <td>
                                    <span class="badge" style="background-color: #f3f4f6; color: #374151;">
                                        {{ $lop->sinh_viens_count }} thành viên
                                    </span>
                                </td>
                                <td>{{ $lop->namHoc->ten_nam_hoc }}</td>
                                <td>
                                    <span class="badge {{ $lop->trang_thai ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $lop->trang_thai ? 'Hoạt động' : 'Đã khóa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- Nút Sửa: Truyền object $lop vào hàm JS --}}
                                        <button class="btn-action btn-edit" title="Sửa"
                                            onclick="editLop({{ $lop }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        {{-- Nút Xóa: Dùng thẻ <a> để khớp với Route::get của bạn --}}
                                        <button class="btn-action btn-delete" title="Xóa"
                                            onclick="deleteLopHoc({{ $lop->id }}, '{{ $lop->ten_lop }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                {{ $dsLopHoc->appends(request()->query())->links() }}
            </div>
        </div>
    </section>
    <div id="modalLopHoc" class="modal-overlay" onclick="closeModal('modalLopHoc')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm lớp học mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalLopHoc')">&times;</button>
            </div>

            <form id="formLopHoc" action="{{ route('admin.lophoc.save') }}" method="POST" novalidate>
                @csrf
                {{-- Input ẩn chứa ID để dùng chung hàm Save cho cả Thêm và Sửa --}}
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Tên lớp học *</label>
                        <input type="text" name="ten_lop" id="input_ten_lop" class="form-input" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                            placeholder="Ví dụ: 10A1, 11B2...">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Khối lớp *</label>
                        <select name="khoi_id" id="input_khoi_id" class="form-select"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn khối học --</option>
                            @foreach ($dsKhoi as $khoi)
                                <option value="{{ $khoi->id }}">{{ $khoi->ten_khoi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Năm học *</label>
                        <select name="nam_hoc_id" id="input_nam_hoc_id" class="form-select"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn năm học --</option>
                            @foreach ($dsNamHoc as $nh)
                                <option value="{{ $nh->id }}">{{ $nh->ten_nam_hoc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="input_trang_thai" class="form-select"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Đã khóa/Kết thúc</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalLopHoc')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu lớp học</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Hàm mở modal thêm mới
        function openModalAdd() {
            const modal = document.getElementById('modalLopHoc');
            const form = document.getElementById('formLopHoc');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Thêm lớp học mới";
                if (form) {
                    form.reset();
                    document.getElementById('input_id').value = "";
                    form.action = "{{ route('admin.lophoc.save') }}";
                }
                modal.classList.add('show');
            }
        }

        function editLop(data) {
            const modal = document.getElementById('modalLopHoc');
            if (modal) {
                document.getElementById('modalTitle').innerText = "Chỉnh sửa lớp học";

                // Đổ dữ liệu từ 'data' vào form
                document.getElementById('input_id').value = data.id;
                document.getElementById('input_ten_lop').value = data.ten_lop;
                document.getElementById('input_khoi_id').value = data.khoi_id;
                document.getElementById('input_nam_hoc_id').value = data.nam_hoc_id;
                document.getElementById('input_trang_thai').value = data.trang_thai ? 1 : 0;

                modal.classList.add('show');
            }
        }

        // Hàm đóng Modal dùng chung
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = "none";
            }
        }

        // Khởi tạo các hiệu ứng thông báo giống Khối học
        document.addEventListener("DOMContentLoaded", function() {
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

        function deleteLopHoc(id, tenLop) {
            // Đường dẫn URL xóa
            const url = `{{ route('admin.lophoc.destroy', '') }}/${id}`;

            // Gọi hàm dùng chung từ file ajax-delete.js
            deleteDataAjax(id, tenLop, url);
        }
    </script>
@endsection
