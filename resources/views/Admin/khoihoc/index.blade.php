@extends('Admin.layouts.Admin')
@section('title', 'Quản lý Khối học')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm khối học mới</span>
    </button>
@endsection

@section('content')
    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Thống kê thực tế --}}
    <section class="filters-section" style="padding-bottom: 0">
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $dsKhoiHoc->total()) }}</h4>
                    <p>Tổng số khối</p>
                </div>
            </div>
            <div class="stat-mini-card" style="border-left-color: var(--success)">
                <div class="stat-mini-icon" style="background: var(--success)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $dsKhoiHoc->where('trang_thai', 1)->count()) }}</h4>
                    <p>Đang hoạt động</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Tìm kiếm --}}
    <section class="filters-section">
        <form action="{{ route('admin.khoihoc.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Đã kết thúc</option>
                </select>
            </div>
            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên khối..." />
                </div>
            </div>
        </form>
    </section>

    {{-- Bảng danh sách --}}
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách khối học</h3>
                    <span class="count-badge">Dữ liệu hệ thống</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="100">Mã ID</th>
                            <th>Tên khối</th>
                            <th>Ngày tạo</th>
                            <th>Ngày sửa</th>
                            <th>Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dsKhoiHoc as $item)
                        <tr>
                            <td><input type="checkbox" class="checkbox" /></td>
                            <td><span class="id-badge">KH{{ $item->id }}</span></td>
                            {{-- Lưu ý: CSDL dùng ten_khoi  --}}
                            <td><strong>{{ $item->ten_khoi }}</strong></td>
                            <td>{{ $item->created_at ? $item->created_at->format('d/m/Y') : '---' }}</td>
                            <td>{{ $item->updated_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $item->trang_thai == 1 ? 'badge-active' : 'badge-draft' }}">
                                    {{ $item->trang_thai == 1 ? 'Đang hoạt động' : 'Đã kết thúc' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit" title="Sửa" 
                                        onclick="editKhoiHoc({{ $item->id }}, '{{ $item->ten_khoi }}', {{ $item->trang_thai }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></svg>
                                    </button>
                                    {{-- FIX: Chuyển form POST sang link GET để khớp Route [cite: 3] --}}
                                    <a href="{{ route('admin.khoihoc.delete', $item->id) }}" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Xóa khối này?')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                {{ $dsKhoiHoc->appends(request()->all())->links() }}
            </div>
        </div>
    </section>

    {{-- Modal Thêm/Sửa --}}
    <div id="modalAddKhoiHoc" class="modal-overlay" onclick="closeModal('modalAddKhoiHoc')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm khối học mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalAddKhoiHoc')">&times;</button>
            </div>
            <form id="formKhoiHoc" action="{{ route('admin.khoihoc.save') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Tên khối học *</label>
                        {{-- QUAN TRỌNG: Thống nhất tên trường với Controller --}}
                        @php
                            // Kiểm tra xem database dùng trường nào
                            $fieldName = 'ten_khoi'; // Hoặc 'ten_khoi_hoc' tùy database
                        @endphp
                        <input type="text" name="ten_khoi" id="input_ten_khoi" class="form-input" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                               placeholder="Ví dụ: Khối 10, Khối 11, Khối 12">
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" id="input_trang_thai" class="form-select" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Đã kết thúc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddKhoiHoc')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu khối học</button>
                </div>
            </form>
        </div>
    </div>

<script>
    // CSS dinâmico para moda

    // Hàm mở modal thêm mới
    function openModalAdd() {
        const modal = document.getElementById('modalAddKhoiHoc');
        const form = document.getElementById('formKhoiHoc');
        
        if (modal) {
            document.getElementById('modalTitle').innerText = "Thêm khối học mới";
            if (form) {
                form.reset();
                form.action = "{{ route('admin.khoihoc.save') }}";
            }
            modal.classList.add('show');
        }
    }

    // Hàm mở modal sửa
    function editKhoiHoc(id, ten, trangThai) {
        const modal = document.getElementById('modalAddKhoiHoc');
        const form = document.getElementById('formKhoiHoc');
        
        if (modal && form) {
            document.getElementById('modalTitle').innerText = "Chỉnh sửa khối học";
            form.action = "{{ url('admin/khoi-hoc/save') }}/" + id;
            document.getElementById('input_ten_khoi').value = ten;
            document.getElementById('input_trang_thai').value = trangThai;
            modal.classList.add('show');
        }
    }

    // Hàm đóng Modal (Nút X và nút Hủy)
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
        }
    }

    // Khởi tạo khi trang load
    document.addEventListener("DOMContentLoaded", function () {
        // Tự động ẩn alerts sau 5 giây
        setTimeout(function () {
            const alerts = document.querySelectorAll(".alert");
            alerts.forEach((alert) => {
                alert.style.transition = "all 0.5s ease";
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-10px)";
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
@endsection