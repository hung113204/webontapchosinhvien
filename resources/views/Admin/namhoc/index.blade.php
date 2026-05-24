@extends('Admin.layouts.admin')
@section('title', 'Quản lý Năm học')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm năm học mới</span>
    </button>
@endsection

@section('content')
    {{-- Hiển thị thông báo --}}
    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <strong>✗</strong> {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <ul style="margin: 8px 0 0 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="filters-section" style="padding-bottom: 0">
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $dsNamHoc->total()) }}</h4>
                    <p>Tổng năm học</p>
                </div>
            </div>
            <div class="stat-mini-card" style="border-left-color: var(--success)">
                <div class="stat-mini-icon" style="background: var(--success)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $dsNamHoc->where('trang_thai', 1)->count()) }}</h4>
                    <p>Đang hoạt động</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Tìm kiếm --}}
    <section class="filters-section">
        <form action="{{ route('admin.namhoc.index') }}" method="GET" class="filter-group">
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên năm học (VD: 2024-2025)..." />
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách năm học</h3>
                    <span class="count-badge">Dữ liệu hệ thống</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="100">Mã ID</th>
                            <th>Tên năm học</th>
                            <th>Ngày tạo</th>
                            <th>Ngày sửa</th>
                            <th>Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dsNamHoc as $item)
                        <tr>
                            <td><input type="checkbox" class="checkbox" /></td>
                            <td><span class="id-badge">NH{{ $item->id }}</span></td>
                            <td><strong>{{ $item->ten_nam_hoc }}</strong></td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td>{{ $item->updated_at->format('d/m/Y') }}</td>
                            <td>
                                @if($item->trang_thai == 1)
                                    <span class="badge badge-active">Đang hoạt động</span>
                                @else
                                    <span class="badge badge-draft">Đã kết thúc</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    {{-- Nút Sửa --}}
                                    <button class="btn-action btn-edit" title="Sửa" 
                                        onclick="editNamHoc({{ $item->id }}, '{{ $item->ten_nam_hoc }}', {{ $item->trang_thai }})">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Nút Xóa - DÙNG FORM POST --}}
                                    <form action="{{ route('admin.namhoc.delete', $item->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa năm học {{ $item->ten_nam_hoc }}?')">
                                        @csrf
                                        <button type="submit" class="btn-action btn-delete" title="Xóa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <svg style="width: 64px; height: 64px; margin: 0 auto; color: #ddd;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <p style="color: #999; margin-top: 16px;">Chưa có năm học nào trong hệ thống</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-info">
                    @if($dsNamHoc->total() > 0)
                        Hiển thị {{ $dsNamHoc->firstItem() }}-{{ $dsNamHoc->lastItem() }} trong tổng số {{ $dsNamHoc->total() }} năm học
                    @else
                        Không có dữ liệu
                    @endif
                </div>
                <div class="pagination">
                    {{ $dsNamHoc->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Thêm/Sửa Năm Học --}}
    <div id="modalAddNamHoc" class="modal-overlay" onclick="closeModal('modalAddNamHoc')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm năm học mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalAddNamHoc')">&times;</button>
            </div>
            <form id="formNamHoc" action="{{ route('admin.namhoc.save') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">
                            Tên năm học <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="ten_nam_hoc" id="input_ten_nam_hoc" 
                               class="form-input" placeholder="Ví dụ: 2025 - 2026" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <small style="color: #666;">Nhập theo định dạng năm bắt đầu - năm kết thúc.</small>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 500;">Trạng thái</label>
                        <select name="trang_thai" id="input_trang_thai" class="form-select" 
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="1">Đang hoạt động</option>
                            <option value="0">Đã kết thúc</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddNamHoc')" 
                            style="padding: 8px 16px; border-radius: 6px; border: 1px solid #ddd; background: #f9f9f9;">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit"
                            style="padding: 8px 16px; border-radius: 6px; background: #4f46e5; color: white; border: none;">
                        Lưu năm học
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- Sem arquivo navigation.js --}}

<script>
    // Cấu hình route cho JavaScript
    window.routeSave = "{{ route('admin.namhoc.save') }}";
    
    // Mở modal thêm mới
    function openModalAdd() {
        document.getElementById('modalTitle').innerText = "Thêm năm học mới";
        document.getElementById('formNamHoc').action = "{{ route('admin.namhoc.save') }}";
        document.getElementById('input_ten_nam_hoc').value = "";
        document.getElementById('input_trang_thai').value = "1";
        document.getElementById('modalAddNamHoc').classList.add('show');
    }

    // Mở modal chỉnh sửa
    function editNamHoc(id, name, status) {
        document.getElementById('modalTitle').innerText = "Chỉnh sửa năm học";
        document.getElementById('formNamHoc').action = "{{ route('admin.namhoc.save') }}/" + id;
        document.getElementById('input_ten_nam_hoc').value = name;
        document.getElementById('input_trang_thai').value = String(status);
        document.getElementById('modalAddNamHoc').classList.add('show');
    }

    // Đóng modal
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }

    // Tự động ẩn alert sau 5 giây
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endsection