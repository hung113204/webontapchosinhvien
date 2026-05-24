@extends('Admin.layouts.admin')
@section('title', 'Phân công giảng dạy')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm phân công mới</span>
    </button>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    <section class="filters-section">
        <form action="{{ route('admin.phanconggiangday.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Giáo viên</label>
                <select name="user_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả giáo viên</option>
                    @foreach ($dsGiaoVien as $gv)
                        <option value="{{ $gv->id }}">{{ $gv->ho_ten }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Lớp học</label>
                <select name="lop_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả lớp</option>
                    @foreach ($dsLopHoc as $lop)
                        <option value="{{ $lop->id }}" {{ request('lop_hoc_id') == $lop->id ? 'selected' : '' }}>
                            {{ $lop->ten_lop }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item">
                <label>Môn học</label>
                <select name="mon_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả môn</option>
                    @foreach ($dsMonHoc as $mon)
                        <option value="{{ $mon->id }}" {{ request('mon_hoc_id') == $mon->id ? 'selected' : '' }}>
                            {{ $mon->ten_mon_hoc }}</option>
                    @endforeach
                </select>
            </div>
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
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách phân công giảng dạy</h3>
                    <span class="count-badge">{{ $dsPhanCong->total() }} bản ghi</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th>Giáo viên</th>
                            <th>Môn học</th>
                            <th>Lớp học</th>
                            <th>Năm học</th>
                            <th>Ngày cập nhật</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dsPhanCong as $pc)
                            <tr>
                                <td><input type="checkbox" class="checkbox" /></td>
                                <td><strong>{{ $pc->giaoVien->ho_ten ?? 'N/A' }}</strong></td>
                                <td><span class="badge"
                                        style="background: #eef2ff; color: #4338ca;">{{ $pc->monHoc->ten_mon_hoc }}</span>
                                </td>
                                <td><span class="badge"
                                        style="background: #fdf2f8; color: #be185d;">{{ $pc->lopHoc->ten_lop }}</span></td>
                                <td>{{ $pc->namHoc->ten_nam_hoc }}</td>
                                <td>{{ $pc->updated_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Sửa"
                                            onclick="editPhanCong({{ $pc }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <button class="btn-action btn-delete" title="Xóa"
                                            onclick="deletePhanCong({{ $pc->id }}, '{{ $pc->giaoVien->ho_ten }}', '{{ $pc->monHoc->ten_mon_hoc }}', '{{ $pc->lopHoc->ten_lop }}')">
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
                {{ $dsPhanCong->appends(request()->query())->links() }}
            </div>
        </div>
    </section>

    <div id="modalPhanCong" class="modal-overlay" onclick="closeModal('modalPhanCong')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm phân công mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalPhanCong')">&times;</button>
            </div>

            <form id="formPhanCong" action="{{ route('admin.phanconggiangday.save') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Giáo viên *</label>
                        <select name="user_id" id="input_user_id" class="form-select" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn giáo viên --</option>
                            @foreach ($dsGiaoVien as $gv)
                                <option value="{{ $gv->id }}">{{ $gv->ho_ten }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Môn học *</label>
                        <select name="mon_hoc_id" id="input_mon_hoc_id" class="form-select" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn môn học --</option>
                            @foreach ($dsMonHoc as $mon)
                                <option value="{{ $mon->id }}">{{ $mon->ten_mon_hoc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Lớp học *</label>
                        <select name="lop_hoc_id" id="input_lop_hoc_id" class="form-select" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn lớp học --</option>
                            @foreach ($dsLopHoc as $lop)
                                <option value="{{ $lop->id }}">{{ $lop->ten_lop }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Năm học *</label>
                        <select name="nam_hoc_id" id="input_nam_hoc_id" class="form-select" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn năm học --</option>
                            @foreach ($dsNamHoc as $nh)
                                <option value="{{ $nh->id }}">{{ $nh->ten_nam_hoc }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalPhanCong')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu phân công</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalAdd() {
            const modal = document.getElementById('modalPhanCong');
            document.getElementById('modalTitle').innerText = "Thêm phân công mới";
            document.getElementById('formPhanCong').reset();
            document.getElementById('input_id').value = "";
            modal.classList.add('show');
        }

        function editPhanCong(data) {
            const modal = document.getElementById('modalPhanCong');
            document.getElementById('modalTitle').innerText = "Chỉnh sửa phân công";
            document.getElementById('input_id').value = data.id;
            document.getElementById('input_user_id').value = data.user_id;
            document.getElementById('input_mon_hoc_id').value = data.mon_hoc_id;
            document.getElementById('input_lop_hoc_id').value = data.lop_hoc_id;
            document.getElementById('input_nam_hoc_id').value = data.nam_hoc_id;
            modal.classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        function deletePhanCong(id, teacherName, subjectName, className) {
            // Tạo nội dung thông báo chi tiết
            const displayName = `Phân công giáo viên ${teacherName} dạy môn ${subjectName} tại lớp ${className}`;

            // Đường dẫn route xóa
            const url = `{{ route('admin.phanconggiangday.destroy', '') }}/${id}`;

            // Gọi hàm dùng chung từ file ajax-delete.js của bạn
            deleteDataAjax(id, displayName, url);
        }
    </script>
@endsection
