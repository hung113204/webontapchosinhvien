@extends('Admin.layouts.admin')
@section('title', 'Quản lý Chương học')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm chương học mới</span>
    </button>
@endsection

@section('content')
    {{-- Thông báo --}}
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
        <form action="{{ route('admin.chuonghoc.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Lọc theo Môn học</label>
                <select name="mon_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả môn học</option>
                    @foreach ($dsMonHoc as $mh)
                        <option value="{{ $mh->id }}"
                            {{ request('mon_hoc_id') == $mh->id || (isset($monHoc) && $monHoc->id == $mh->id) ? 'selected' : '' }}>
                            {{ $mh->ten_mon_hoc }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item" style="grid-column: span 3">
                <label>Tìm kiếm chương</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" placeholder="Nhập tên chương học..."
                        value="{{ request('search') }}" />
                </div>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="height: 42px; padding: 10px 20px;">
                    T&igrave;m ki&#7871;m
                </button>
                @if (request()->filled('search') || request()->filled('mon_hoc_id'))
                    <a href="{{ route('admin.chuonghoc.index') }}" class="btn btn-secondary"
                        style="height: 42px; padding: 10px 20px;">
                        X&oacute;a l&#7885;c
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách chương học</h3>
                    <span class="count-badge">{{ isset($monHoc) ? $monHoc->ten_mon_hoc : 'Tất cả môn học' }}</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="80">Thứ tự</th>
                            <th>Tên chương học</th>
                            <th>Thuộc môn học</th>
                            <th>Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dsChuong as $chuong)
                            <tr>
                                <td><input type="checkbox" class="checkbox" /></td>
                                <td><span class="id-badge">{{ $chuong->thu_tu }}</span></td>
                                <td><strong>{{ $chuong->ten_chuong }}</strong></td>
                                <td><span class="badge">{{ $chuong->monHoc->ten_mon_hoc ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="badge {{ $chuong->trang_thai ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $chuong->trang_thai ? 'Hoạt động' : 'Tạm ẩn' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Sửa"
                                            onclick="editChuong({{ $chuong }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-action btn-delete" title="Xóa chương học"
                                            onclick="deleteDataAjax({{ $chuong->id }}, '{{ addslashes($chuong->ten_chuong) }}', '{{ route('admin.chuonghoc.destroy', $chuong->id) }}')">
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
        </div>
    </section>

    {{-- Modal Chương Học --}}
    <div id="modalChuongHoc" class="modal-overlay" onclick="closeModal('modalChuongHoc')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm chương học mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalChuongHoc')">&times;</button>
            </div>

            <form id="formChuongHoc" action="{{ route('admin.chuonghoc.save') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Môn học *</label>
                        <select name="mon_hoc_id" id="input_mon_hoc_id" class="form-select" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">-- Chọn môn học --</option>
                            @foreach ($dsMonHoc as $mh)
                                <option value="{{ $mh->id }}">{{ $mh->ten_mon_hoc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Tên chương học *</label>
                        <input type="text" name="ten_chuong" id="input_ten_chuong" class="form-input" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                            placeholder="Ví dụ: Chương 1: Giới thiệu...">
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Thứ tự hiển thị</label>
                        <input type="number" name="thu_tu" id="input_thu_tu" class="form-input" value="0"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="input_trang_thai" class="form-select"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Tạm ẩn</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalChuongHoc')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu chương học</button>
                </div>
            </form>
        </div>
    </div>

    @include('Admin.chuonghoc.scripts')
@endsection
