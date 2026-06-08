@extends('Admin.layouts.admin')
@section('title', 'Quản lý Câu hỏi')

@section('header_action')
    <a href="{{ route('admin.cauhoi.create', ['chuong_hoc_id' => request('chuong_hoc_id')]) }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm câu hỏi mới</span>
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

    <section class="filters-section">
        <form action="{{ route('admin.cauhoi.index') }}" method="GET" class="filter-group" id="filter-form">
            <div class="filter-item">
                <label>Môn học</label>
                <select name="mon_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả môn học</option>
                    @foreach (\App\Models\MonHoc::where('trang_thai', true)->orderBy('ten_mon_hoc')->get() as $mh)
                        <option value="{{ $mh->id }}" {{ request('mon_hoc_id') == $mh->id ? 'selected' : '' }}>
                            {{ $mh->ten_mon_hoc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label>Chương</label>
                <select name="chuong_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả chương</option>
                    @if (request('mon_hoc_id'))
                        @foreach (\App\Models\ChuongHoc::where('mon_hoc_id', request('mon_hoc_id'))->where('trang_thai', true)->get() as $ch)
                            <option value="{{ $ch->id }}" {{ request('chuong_hoc_id') == $ch->id ? 'selected' : '' }}>
                                {{ $ch->ten_chuong }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="filter-item">
                <label>Mức độ</label>
                <select name="muc_do" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả mức độ</option>
                    <option value="1" {{ request('muc_do') == '1' ? 'selected' : '' }}>Nhận biết</option>
                    <option value="2" {{ request('muc_do') == '2' ? 'selected' : '' }}>Thông hiểu</option>
                    <option value="3" {{ request('muc_do') == '3' ? 'selected' : '' }}>Vận dụng</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="trashed" class="form-select" onchange="this.form.submit()">
                    <option value="0" {{ request('trashed') == '0' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="1" {{ request('trashed') == '1' ? 'selected' : '' }}>Trong thùng rác</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" placeholder="Nhấn Enter để tìm..."
                        value="{{ request('search') }}" />
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>
                        @if(request('trashed') == '1')
                            Thùng rác: Danh sách câu hỏi đã xóa
                        @else
                            Danh sách câu hỏi
                        @endif
                    </h3>
                    <span class="count-badge">{{ $cauHois->total() ?? 0 }} câu hỏi</span>
                </div>
                <div class="table-actions" style="display: flex; gap: 10px; align-items: center;">
                    {{-- Nút Xóa Nhiều ẩn/hiện tự động --}}
                    <button type="button" class="btn btn-danger" id="btn-bulk-delete" style="display: none; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 6px; font-weight: 500; height: 38px; border: none; background: #ef4444; color: white; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onclick="bulkDelete()" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                        </svg>
                        Xóa đã chọn (<span id="selected-count">0</span>)
                    </button>

                    <button class="btn-icon" title="Import"
                        onclick="window.location.href='{{ route('admin.cauhoi.create') }}'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="7 10 12 15 17 10" />
                            <line x1="12" y1="15" x2="12" y2="3" />
                        </svg>
                    </button>
                    <button class="btn-icon" title="Export" onclick="exportQuestions()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                            <polyline points="17 8 12 3 7 8" />
                            <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="checkbox" id="select-all" />
                            </th>
                            <th width="80">ID</th>
                            <th>Nội dung câu hỏi</th>
                            <th width="180">Môn học</th>
                            <th width="200">Chương</th>
                            <th width="120">Mức độ</th>
                            <th width="100">Đáp án</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cauHois as $item)
                            <tr style="{{ request('trashed') == '1' ? 'opacity: 0.7; background: #fdf2f2;' : '' }}">
                                <td><input type="checkbox" class="checkbox" name="ids[]" value="{{ $item->id }}" />
                                </td>
                                <td><span class="id-badge">#{{ $item->id }}</span></td>
                                <td>
                                    <div class="question-content">
                                        {{ Str::limit(strip_tags($item->noi_dung), 100) }}
                                        @if ($item->hinh_anh)
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2"
                                                style="width: 16px; height: 16px; color: var(--primary); display: inline; margin-left: 5px;">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                                <circle cx="8.5" cy="8.5" r="1.5" />
                                                <polyline points="21 15 16 10 5 21" />
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td><strong>{{ $item->chuongHoc->monHoc->ten_mon_hoc ?? 'N/A' }}</strong></td>
                                <td>{{ $item->chuongHoc->ten_chuong ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $badges = [1 => 'badge-easy', 2 => 'badge-medium', 3 => 'badge-hard'];
                                        $labels = [1 => 'Nhận biết', 2 => 'Thông hiểu', 3 => 'Vận dụng', 4 => 'Vận dụng cao'];
                                    @endphp
                                    <span class="badge {{ $badges[$item->muc_do] ?? 'badge-draft' }}">
                                        {{ $labels[$item->muc_do] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #f3f4f6; color: #374151;">
                                        {{ $item->dap_ans_count ?? ($item->dapAns ? $item->dapAns->count() : 0) }} đáp án
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view" title="Xem chi tiết"
                                            onclick="viewQuestion({{ $item }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                        </button>

                                        @if(request('trashed') == '1')
                                            {{-- Nút Khôi Phục --}}
                                            <button class="btn-action" style="color: #10b981;" title="Khôi phục"
                                                onclick="restoreQuestion({{ $item->id }})">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="1 4 1 10 7 10"></polyline>
                                                    <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                                                </svg>
                                            </button>

                                            {{-- Nút Xóa Vĩnh Viễn --}}
                                            <button class="btn-action btn-delete" style="color: #ef4444;" title="Xóa vĩnh viễn"
                                                onclick="deleteDataAjax(
                                                {{ $item->id }},
                                                 'XÓA VĨNH VIỄN - {{ addslashes(Str::limit(strip_tags($item->noi_dung), 30)) }}',
                                                 '{{ route('admin.cauhoi.forceDelete', $item->id) }}')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </button>
                                        @else
                                            {{-- Các nút bình thường --}}
                                            <a href="{{ route('admin.cauhoi.edit', $item->id) }}" class="btn-action btn-edit"
                                                title="Chỉnh sửa">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                                </svg>
                                            </a>

                                            <button class="btn-action btn-delete" title="Xóa mềm (vào thùng rác)"
                                                onclick="deleteDataAjax(
                                                {{ $item->id }},
                                                 '{{ addslashes(Str::limit(strip_tags($item->noi_dung), 30)) }}',
                                                 '{{ route('admin.cauhoi.destroy', $item->id) }}')">
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
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <circle cx="12" cy="12" r="10" />
                                                <line x1="12" y1="8" x2="12" y2="12" />
                                                <line x1="12" y1="16" x2="12.01" y2="16" />
                                            </svg>
                                        </div>
                                        <h3>Không tìm thấy câu hỏi</h3>
                                        <p>Không có câu hỏi nào phù hợp với bộ lọc của bạn.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-info">
                    Hiển thị {{ $cauHois->firstItem() ?? 0 }}-{{ $cauHois->lastItem() ?? 0 }} trong tổng số
                    {{ $cauHois->total() ?? 0 }} câu hỏi
                </div>
                <div class="table-footer">
                    <div class="pagination">
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center">
                            @if ($cauHois->onFirstPage())
                                <span class="pagination-btn" aria-disabled="true"
                                    style="opacity: 0.5; cursor: not-allowed;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $cauHois->previousPageUrl() }}" class="pagination-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;">
                                        <polyline points="15 18 9 12 15 6"></polyline>
                                    </svg>
                                </a>
                            @endif

                            <div style="display: flex; gap: 5px; margin: 0 10px;">
                                @foreach ($cauHois->getUrlRange(max(1, $cauHois->currentPage() - 2), min($cauHois->lastPage(), $cauHois->currentPage() + 2)) as $page => $url)
                                    @if ($page == $cauHois->currentPage())
                                        <span class="pagination-btn active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            @if ($cauHois->hasMorePages())
                                <a href="{{ $cauHois->nextPageUrl() }}" class="pagination-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>
                            @else
                                <span class="pagination-btn" aria-disabled="true" style="opacity: 0.5; cursor: not-allowed;">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;">
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

    <div id="modalViewQuestion" class="modal-overlay" onclick="closeModal('modalViewQuestion')">
        <div class="modal-card" onclick="event.stopPropagation()" style="max-width: 800px;">
            <div class="modal-header">
                <h3>Chi tiết câu hỏi</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalViewQuestion')">&times;</button>
            </div>

            <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: var(--gray-700); display: block; margin-bottom: 8px;">Thông tin chung</label>
                    <div style="background: var(--gray-50); padding: 15px; border-radius: 8px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div><strong>Môn học:</strong> <span id="view_mon_hoc"></span></div>
                            <div><strong>Chương:</strong> <span id="view_chuong"></span></div>
                            <div><strong>Bài học:</strong> <span id="view_bai_hoc" style="color: #4f46e5; font-weight: 500;"></span></div>
                            <div><strong>Mức độ:</strong> <span id="view_muc_do"></span></div>
                            <div><strong>Số đáp án:</strong> <span id="view_so_dap_an"></span></div>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: var(--gray-700); display: block; margin-bottom: 8px;">Nội dung câu hỏi</label>
                    <div id="view_noi_dung" style="background: white; padding: 15px; border: 1px solid var(--gray-200); border-radius: 8px; line-height: 1.6;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; color: var(--gray-700); display: block; margin-bottom: 8px;">Đáp án</label>
                    <div id="view_dap_an_list"></div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;" id="view_giai_thich_group" style="display: none;">
                    <label style="font-weight: 600; color: var(--gray-700); display: block; margin-bottom: 8px;">Giải thích đáp án đúng</label>
                    <div id="view_giai_thich" style="background: #d1fae5; padding: 15px; border-left: 4px solid #10b981; border-radius: 4px;">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalViewQuestion')">Đóng</button>
            </div>
        </div>
    </div>

    <script>
        // HÀM MỚI BỔ SUNG: Xử lý chức năng Khôi phục (Restore)
        function restoreQuestion(id) {
            if (confirm('Bạn có chắc chắn muốn khôi phục câu hỏi này?')) {
                fetch(`/admin/cauhoi/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload(); // Tải lại trang sau khi khôi phục thành công
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi trong quá trình xử lý!');
                });
            }
        }

        // Xử lý ẩn/hiện nút Xóa Nhanh
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('input[name="ids[]"]');
            const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
            const selectedCount = document.getElementById('selected-count');

            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('input[name="ids[]"]:checked');
                const count = checkedBoxes.length;
                
                if (count > 0) {
                    bulkDeleteBtn.style.display = 'flex';
                    selectedCount.innerText = count;
                } else {
                    bulkDeleteBtn.style.display = 'none';
                }
                
                if (selectAll) {
                    selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkDeleteButton();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkDeleteButton);
            });
        });

        // Hàm xử lý khi bấm Xóa nhiều
        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('input[name="ids[]"]:checked');
            if (checkedBoxes.length === 0) return;

            if (confirm(`Bạn có chắc chắn muốn xóa ${checkedBoxes.length} mục đã chọn?`)) {
                const ids = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
                const isTrashed = {{ request('trashed') == '1' ? 'true' : 'false' }};
                
                fetch('{{ route('admin.cauhoi.bulkDelete') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: ids,
                        is_trashed: isTrashed
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi trong quá trình xử lý!');
                });
            }
        }

        // JS cũ giữ nguyên
        function viewQuestion(item) {
            const modal = document.getElementById('modalViewQuestion');

            document.getElementById('view_mon_hoc').innerText = item.chuong_hoc?.mon_hoc?.ten_mon_hoc || 'N/A';
            document.getElementById('view_chuong').innerText = item.chuong_hoc?.ten_chuong || 'N/A';
            const elBaiHoc = document.getElementById('view_bai_hoc');
            if (elBaiHoc) {
                elBaiHoc.innerText = item.bai_hoc?.ten_bai_hoc || 'Dùng chung cho Chương';
            }
            const mucDoLabels = { 1: 'Nhận biết', 2: 'Thông hiểu', 3: 'Vận dụng', 4: 'Vận dụng cao' };
            document.getElementById('view_muc_do').innerText = mucDoLabels[item.muc_do] || 'N/A';
            document.getElementById('view_noi_dung').innerHTML = item.noi_dung;

            const listDapAn = document.getElementById('view_dap_an_list');
            listDapAn.innerHTML = ''; 

            if (item.dap_ans && item.dap_ans.length > 0) {
                document.getElementById('view_so_dap_an').innerText = item.dap_ans.length;

                item.dap_ans.forEach(ans => {
                    const isCorrect = ans.is_dung == 1;
                    const div = document.createElement('div');
                    div.style.padding = '10px';
                    div.style.margin = '5px 0';
                    div.style.borderRadius = '5px';
                    div.style.border = isCorrect ? '2px solid #10b981' : '1px solid #e5e7eb';
                    div.style.backgroundColor = isCorrect ? '#ecfdf5' : 'white';

                    let imgHtml = '';
                    if (ans.hinh_anh) {
                        imgHtml = `<div style="margin-top: 8px;"><img src="/storage/${ans.hinh_anh}" style="max-height: 120px; border-radius: 4px; border: 1px solid #e5e7eb;"></div>`;
                    }

                    div.innerHTML = `<strong>${ans.ky_hieu || ''}.</strong> ${ans.noi_dung} ${imgHtml} ${isCorrect ? ' <span style="color:#10b981; font-weight:bold;">(Đúng)</span>' : ''}`;
                    listDapAn.appendChild(div);
                });
            } else {
                document.getElementById('view_so_dap_an').innerText = '0';
            }

            const giaiThichGroup = document.getElementById('view_giai_thich_group');
            if (item.giai_thich) {
                document.getElementById('view_giai_thich').innerHTML = item.giai_thich;
                giaiThichGroup.style.display = 'block';
            } else {
                giaiThichGroup.style.display = 'none';
            }

            modal.classList.add('show');

            if (window.MathJax && window.MathJax.typesetPromise) {
                MathJax.typesetPromise().catch((err) => console.log('MathJax error:', err));
            }
        }

        window.closeModal = function(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('modalViewQuestion');
            }
        });

        // Cấu hình MathJax
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']]
            },
            startup: {
                typeset: true // Tự động render khi tải trang
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection