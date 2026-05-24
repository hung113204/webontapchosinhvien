@extends('Admin.layouts.admin')
@section('title', 'Quản lý bài kiểm tra')
@section('header_action')
    <a href="{{ route('admin.baikiemtra.create') }}" class="btn btn-primary"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer; text-decoration: none;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm bài kiểm tra mới</span>
    </a>
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

    <section class="filters-section" style="padding-bottom: 0">
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ $baiKiemTras->total() }}</h4>
                    <p>Tổng số đề thi</p>
                </div>
            </div>
            <div class="stat-mini-card" style="border-left-color: var(--success)">
                <div class="stat-mini-icon" style="background: var(--success)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ $baiKiemTras->where('trang_thai', 1)->count() }}</h4>
                    <p>Đang hoạt động</p>
                </div>
            </div>
        </div>
    </section>

    <section class="filters-section">
        <form method="GET" action="{{ route('admin.baikiemtra.index') }}" id="filter-form">
            <div class="filter-group">
                <div class="filter-item">
                    <label>Môn học</label>
                    <select class="form-select" name="mon_hoc_id" onchange="this.form.submit()">
                        <option value="">Tất cả môn học</option>
                        @foreach ($monHocs as $monHoc)
                            <option value="{{ $monHoc->id }}"
                                {{ request('mon_hoc_id') == $monHoc->id ? 'selected' : '' }}>
                                {{ $monHoc->ten_mon_hoc }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Trạng thái</label>
                    <select class="form-select" name="trang_thai" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="1" {{ request('trang_thai') === '1' ? 'selected' : '' }}>Đang mở</option>
                        <option value="2" {{ request('trang_thai') === '2' ? 'selected' : '' }}>Đã đóng</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Đã xóa</label>
                    <select class="form-select" name="show_deleted" onchange="this.form.submit()">
                        <option value="" {{ request('show_deleted') == '' ? 'selected' : '' }}>Đang hoạt động
                        </option>
                        <option value="yes" {{ request('show_deleted') == 'yes' ? 'selected' : '' }}>Chỉ đã xóa</option>
                        <option value="all" {{ request('show_deleted') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    </select>
                </div>

                <div class="filter-item">
                    <label>Tìm kiếm đề thi</label>
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Nhập tên bài kiểm tra hoặc mã ID..." />
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thông tin bài thi</th>
                        <th>Cấu hình</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baiKiemTras as $item)
                        <tr class="{{ $item->trashed() ? 'bg-gray-50' : '' }}">
                            <td>#{{ $item->id }}</td>
                            <td>
                                <div style="font-weight: 600; {{ $item->trashed() ? 'color: #9ca3af;' : '' }}">
                                    {{ $item->ten_bai }}
                                    @if ($item->trashed())
                                        <span style="color: #ef4444; font-size: 11px; margin-left: 8px;">🗑️ Đã xóa</span>
                                    @endif
                                </div>
                                <div style="font-size: 12px; color: var(--gray-500)">
                                    {{ $item->monHoc->ten_mon ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 13px">⏱ {{ $item->thoi_gian_phut }} phút</div>
                                <div style="font-size: 13px">📝 {{ $item->cauHois->count() }} câu hỏi</div>
                            </td>
                            <td>
                                <div style="font-size: 12px">
                                    BĐ:
                                    {{ $item->thoi_gian_bat_dau ? \Carbon\Carbon::parse($item->thoi_gian_bat_dau)->format('d/m/H:i') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if ($item->trashed())
                                    <span class="status-badge"
                                        style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                        🗑️ Thùng rác
                                    </span>
                                @else
                                    <span
                                        class="status-badge 
            {{ $item->trang_thai == 0 ? 'status-draft' : ($item->trang_thai == 1 ? 'status-active' : 'status-closed') }}">
                                        {{ $item->trang_thai == 0 ? '📝 Bản nháp' : ($item->trang_thai == 1 ? '✅ Đang mở' : '🚫 Đã đóng') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons" style="display: flex; gap: 8px;">
                                    @if ($item->trashed())
                                        {{-- Nếu bài thi đã bị xóa mềm --}}
                                        <button onclick="restoreExam({{ $item->id }}, '{{ $item->ten_bai }}')"
                                            class="btn-action" title="Khôi phục đề thi"
                                            style="background-color: #d1fae5; color: #059669; border: 1px solid #10b981; padding: 8px; border-radius: 6px; cursor: pointer;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="width: 16px; height: 16px;">
                                                <polyline points="23 4 23 10 17 10"></polyline>
                                                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                            </svg>
                                        </button>

                                        <button onclick="forceDeleteExam({{ $item->id }}, '{{ $item->ten_bai }}')"
                                            class="btn-action" title="Xóa vĩnh viễn"
                                            style="background-color: #fee2e2; color: #dc2626; border: 1px solid #ef4444; padding: 8px; border-radius: 6px; cursor: pointer;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="width: 16px; height: 16px;">
                                                <path
                                                    d="M3 6h18m-2 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path>
                                                <line x1="10" y1="11" x2="10" y2="17">
                                                </line>
                                                <line x1="14" y1="11" x2="14" y2="17">
                                                </line>
                                            </svg>
                                        </button>
                                    @else
                                        {{-- Nếu bài thi đang hoạt động bình thường --}}
                                        <a href="{{ route('admin.baikiemtra.edit', $item->id) }}"
                                            class="btn-action btn-edit" style="padding: 8px; border-radius: 6px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="width: 16px; height: 16px;">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </a>

                                        <button onclick="deleteExam({{ $item->id }}, '{{ $item->ten_bai }}')"
                                            class="btn-action btn-delete" style="padding: 8px; border-radius: 6px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" style="width: 16px; height: 16px;">
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
                            <td colspan="6" style="text-align: center; padding: 40px; color: #9ca3af;">
                                Không có bài kiểm tra nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="table-footer">
                <div class="showing-info">
                    Hiển thị từ {{ $baiKiemTras->firstItem() ?? 0 }} đến {{ $baiKiemTras->lastItem() ?? 0 }}
                    trong tổng số {{ $baiKiemTras->total() }} bài kiểm tra
                </div>
                <div class="table-footer">
                    <div class="pagination">
                        {{-- Sử dụng template phân trang tùy chỉnh hoặc mặc định nhưng bọc trong cấu trúc div --}}
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center">
                            {{-- Nút về trang trước --}}
                            @if ($baiKiemTras->onFirstPage())
                                <span class="pagination-btn" aria-disabled="true"
                                    style="opacity: 0.5; cursor: not-allowed;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        style="width:16px;">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $baiKiemTras->previousPageUrl() }}" class="pagination-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        style="width:16px;">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </a>
                            @endif

                            {{-- Các số trang --}}
                            <div style="display: flex; gap: 5px; margin: 0 10px;">
                                @foreach ($baiKiemTras->getUrlRange(max(1, $baiKiemTras->currentPage() - 2), min($baiKiemTras->lastPage(), $baiKiemTras->currentPage() + 2)) as $page => $url)
                                    @if ($page == $baiKiemTras->currentPage())
                                        <span class="pagination-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Nút sang trang sau --}}
                            @if ($baiKiemTras->hasMorePages())
                                <a href="{{ $baiKiemTras->nextPageUrl() }}" class="pagination-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        style="width:16px;">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            @else
                                <span class="pagination-btn" aria-disabled="true"
                                    style="opacity: 0.5; cursor: not-allowed;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        style="width:16px;">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Xóa mềm (Soft Delete)
            function deleteExam(id, name) {
                Swal.fire({
                    title: "Xóa bài kiểm tra?",
                    text: `Bạn có chắc muốn xóa "${name}"? Có thể khôi phục lại sau.`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ef4444",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Đồng ý xóa",
                    cancelButtonText: "Hủy"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = "{{ route('admin.baikiemtra.destroy', ':id') }}".replace(':id', id);

                        fetch(url, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        "content"),
                                    "Accept": "application/json",
                                },
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Thành công!", data.message, "success").then(() => location.reload());
                                } else {
                                    Swal.fire("Lỗi!", data.message, "error");
                                }
                            });
                    }
                });
            }

            // Khôi phục (Restore)
            function restoreExam(id, name) {
                Swal.fire({
                    title: "Khôi phục đề thi?",
                    text: `Bạn có muốn khôi phục lại đề thi "${name}" không?`,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#10b981",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Đồng ý khôi phục",
                    cancelButtonText: "Hủy"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = "{{ route('admin.baikiemtra.restore', ':id') }}".replace(':id', id);

                        fetch(url, {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        "content"),
                                    "Accept": "application/json",
                                },
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire("Thành công!", data.message, "success").then(() => location.reload());
                                } else {
                                    Swal.fire("Lỗi!", data.message, "error");
                                }
                            });
                    }
                });
            }

            // Xóa vĩnh viễn (Force Delete)
            function forceDeleteExam(id, name) {
                Swal.fire({
                    title: "⚠️ Xóa vĩnh viễn?",
                    html: `
            <p style="color: #ef4444; font-weight: 600;">CẢNH BÁO: Hành động này không thể hoàn tác!</p>
            <p>Bạn có chắc chắn muốn xóa <strong>VĨNH VIỄN</strong> đề thi "${name}"?</p>
            <p style="font-size: 13px; color: #6b7280;">Tất cả dữ liệu liên quan sẽ bị xóa hoàn toàn.</p>
        `,
                    icon: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#dc2626",
                    cancelButtonColor: "#6b7280",
                    confirmButtonText: "Xác nhận xóa vĩnh viễn",
                    cancelButtonText: "Hủy bỏ",
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // SỬA TẠI ĐÂY: Đổi 'restore' thành 'force-delete'
                        const url = "{{ route('admin.baikiemtra.force-delete', ':id') }}".replace(':id', id);

                        Swal.fire({
                            title: 'Đang xóa vĩnh viễn...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(url, {
                                method: "DELETE", // Đảm bảo method là DELETE
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        "content"),
                                    "Accept": "application/json",
                                },
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Đã xóa vĩnh viễn!",
                                        text: data.message,
                                        timer: 2000
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire("Lỗi!", data.message, "error");
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire("Lỗi!", "Không thể kết nối đến máy chủ", "error");
                            });
                    }
                });
            }
             function toggleStatus(id, btn) {
            fetch(`/admin/baikiemtra/toggle-status/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return alert('Lỗi: ' + data.message);

                    const isNowOn = data.trang_thai;
                    const thumb = btn.querySelector('div');
                    const label = document.getElementById('lbl_' + id);

                    btn.dataset.status = isNowOn ? '1' : '0';
                    btn.style.background = isNowOn ? '#333' : '#e5e7eb';
                    thumb.style.transform = isNowOn ? 'translateX(24px)' : 'translateX(0)';
                    label.textContent = isNowOn ? 'Hoạt động' : 'Tạm ẩn';
                    label.style.color = isNowOn ? '#333' : '#9ca3af';
                })
                .catch(() => alert('Có lỗi kết nối!'));
        }
        </script>
    @endpush
@endsection
