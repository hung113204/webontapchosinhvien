@extends('Admin.layouts.admin')
@section('title', 'Quản lý Phòng thi đấu Realtime')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 10px 18px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2); transition: all 0.2s;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
        </svg>
        <span>Tạo phòng thi đấu mới</span>
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
        <form action="{{ route('admin.phongquiz.index') }}" method="GET" class="filter-group">
            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nhập mã PIN hoặc tên phòng chơi..." />
                </div>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; height: 42px; background: #4f46e5; border: none; color: white; border-radius: 6px;">
                    Tìm kiếm
                </button>
                @if (request()->has('search'))
                    <a href="{{ route('admin.phongquiz.index') }}" class="btn btn-secondary"
                        style="padding: 10px 20px; height: 42px; margin-left: 8px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; border-radius: 6px; text-decoration: none; color: #333;">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách phòng thi đấu Realtime</h3>
                    <span class="count-badge">Trực tuyến</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="120">Mã PIN Phòng</th>
                            <th width="260">Tên phòng</th>
                            <th width="200">Môn học</th>
                            <th width="120">Mức độ</th>
                            <th width="110">Tổng số câu</th>
                            <th width="100">Học sinh</th>
                            <th width="150">Người tạo</th>
                            <th width="140">Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $item)
                            <tr>
                                {{-- Mã PIN --}}
                                <td>
                                    <span class="id-badge"
                                        style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%); color: #3730a3; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 14px; letter-spacing: 1px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        {{ $item->ma_phong }}
                                    </span>
                                </td>

                                {{-- Tên phòng --}}
                                <td>
                                    <strong style="display: block; color: #111827; font-size: 14px;">
                                        {{ $item->ten_phong }}
                                    </strong>
                                    <small style="color: #6b7280; font-size: 11px;">
                                        Tạo ngày: {{ $item->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>

                                {{-- Môn học --}}
                                <td>
                                    <span style="font-weight: 500; color: #4b5563;">
                                        {{ $item->monHoc->ten_mon_hoc ?? 'Môn học đã xóa' }}
                                    </span>
                                </td>

                                {{-- Mức độ --}}
                                <td>
                                    @if($item->muc_do_cau_hoi === 1)
                                        <span class="badge" style="background: #ecfdf5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Nhận biết</span>
                                    @elseif($item->muc_do_cau_hoi === 2)
                                        <span class="badge" style="background: #fffbeb; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Thông hiểu</span>
                                    @elseif($item->muc_do_cau_hoi === 3)
                                        <span class="badge" style="background: #fef2f2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Vận dụng</span>
                                    @else
                                        <span class="badge" style="background: #f3f4f6; color: #374151; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">Hỗn hợp</span>
                                    @endif
                                </td>

                                {{-- Tổng số câu --}}
                                <td>
                                    <div style="font-weight: 600; color: #1e40af; text-align: center;">
                                        {{ $item->tong_so_cau }} <small style="font-weight: normal; color: #9ca3af;">câu</small>
                                    </div>
                                    <div style="font-size: 11px; color: #9ca3af; text-align: center;">{{ $item->thoi_gian_tra_loi_cau_hoi }}s / câu</div>
                                </td>

                                {{-- Số học sinh --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                        <span style="font-weight: 700; color: #2563eb; font-size: 14px;">
                                            {{ $item->thanh_vien_count ?? 0 }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Người tạo --}}
                                <td>
                                    <span style="color: #4b5563; font-size: 13px; font-weight: 500;">
                                        {{ $item->chuPhong->ho_ten ?? 'Hệ thống' }}
                                    </span>
                                </td>

                                {{-- Trạng thái --}}
                                <td>
                                    @if($item->trang_thai === 1)
                                        <span class="badge" style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; padding: 6px 12px; border-radius: 999px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 6px; height: 6px; background: #ea580c; border-radius: 50%;"></span>
                                            Đang đợi...
                                        </span>
                                    @elseif($item->trang_thai === 2)
                                        <span class="badge animate-pulse" style="background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; padding: 6px 12px; border-radius: 999px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 0 8px rgba(22, 163, 74, 0.2);">
                                            <span style="width: 6px; height: 6px; background: #16a34a; border-radius: 50%; display: inline-block;"></span>
                                            ĐANG THI ĐẤU
                                        </span>
                                    @else
                                        <span class="badge" style="background: #f9fafb; color: #6b7280; border: 1px solid #e5e7eb; padding: 6px 12px; border-radius: 999px; font-weight: 500; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 6px; height: 6px; background: #9ca3af; border-radius: 50%;"></span>
                                            Đã kết thúc
                                        </span>
                                    @endif
                                </td>

                                {{-- Thao tác --}}
                                <td>
                                    <div class="action-buttons" style="display: flex; gap: 8px;">
                                        @if($item->trang_thai !== 3)
                                            <a href="{{ route('admin.phongquiz.room', $item->ma_phong) }}" class="btn-action btn-edit" title="Vào quản lý phòng chơi"
                                                style="background: #e0e7ff; color: #4338ca; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: none; text-decoration: none;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                                                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.phongquiz.room', $item->ma_phong) }}" class="btn-action btn-edit" title="Xem kết quả chung cuộc"
                                                style="background: #f3f4f6; color: #4b5563; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: none; text-decoration: none;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <line x1="12" y1="16" x2="12" y2="12" />
                                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                                </svg>
                                            </a>
                                        @endif

                                        <form action="{{ route('admin.phongquiz.destroy', $item->id) }}" method="POST"
                                            style="display:inline; margin:0; padding:0;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng thi đấu này vĩnh viễn?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete" title="Xóa phòng thi đấu"
                                                style="background: #fee2e2; color: #ef4444; padding: 6px; border-radius: 6px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
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
                                <td colspan="9" style="text-align: center; padding: 60px 20px;">
                                    <div class="empty-state" style="max-width: 400px; margin: 0 auto;">
                                        <div class="empty-state-icon"
                                            style="width: 80px; height: 80px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);">
                                            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="white" stroke-width="2">
                                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                                            </svg>
                                        </div>
                                        <h3 style="font-size: 20px; color: #111827; margin-bottom: 8px; font-weight: 600;">
                                            Chưa có phòng thi đấu nào
                                        </h3>
                                        <p style="color: #6b7280; margin-bottom: 20px; font-size: 14px;">
                                            Hãy tạo phòng chơi trắc nghiệm Realtime đầu tiên để mời sinh viên thi đấu, ôn tập bài học trực tuyến ngay bây giờ!
                                        </p>
                                        <button class="btn btn-primary" onclick="openModalAdd()"
                                            style="padding: 12px 24px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                                            Tạo phòng đầu tiên
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-info">
                    Hiển thị từ {{ $rooms->firstItem() ?? 0 }} đến {{ $rooms->lastItem() ?? 0 }}
                    trong tổng số {{ $rooms->total() }} phòng
                </div>
                <div class="pagination-wrapper">
                    {{ $rooms->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>

    {{-- MODAL TẠO PHÒNG QUIZ REALTIME --}}
    <div id="modalPhongQuiz" class="modal-overlay" onclick="closeModal('modalPhongQuiz')"
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); transition: all 0.3s ease;">
        <div class="modal-card" onclick="event.stopPropagation()"
            style="background: white; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden; transform: scale(0.95); transition: all 0.3s ease; border: 1px solid #e5e7eb;">
            <div class="modal-header" style="padding: 18px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; background: #f9fafb;">
                <h3 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#4f46e5" stroke-width="2.5">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                    </svg>
                    Tạo phòng thi đấu Realtime
                </h3>
                <button type="button" class="close-btn" onclick="closeModal('modalPhongQuiz')"
                    style="background: transparent; border: none; font-size: 24px; font-weight: 500; cursor: pointer; color: #9ca3af; transition: color 0.15s; outline: none;">&times;</button>
            </div>
            <form id="formPhongQuiz" action="{{ route('admin.phongquiz.store') }}" method="POST" novalidate data-no-loading>
                @csrf

                <div class="modal-body" style="padding: 24px; display: grid; grid-template-columns: 1fr; gap: 18px;">
                    {{-- Tên phòng --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px; font-size: 14px;">Tên phòng thi đấu</label>
                        <input type="text" name="ten_phong" id="input_ten_phong" class="form-input" placeholder="Ví dụ: Đấu trường tri thức CNTT, Ôn tập chương 1..."
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none; transition: border 0.15s;" required>
                        <small class="text-danger" id="error_ten_phong" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></small>
                    </div>

                    {{-- Chọn môn học --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px; font-size: 14px;">Chọn Môn Học <span style="color: red">*</span></label>
                        <select name="mon_hoc_id" id="input_mon_hoc_id" class="form-select"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none; background-color: white;" required>
                            <option value="">-- Chọn môn học áp dụng --</option>
                            @foreach($monHocs as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->ten_mon_hoc }} ({{ $sub->ma_mon_hoc }})</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="error_mon_hoc_id" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        {{-- Mức độ --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px; font-size: 14px;">Mức độ câu hỏi</label>
                            <select name="muc_do_cau_hoi" id="input_muc_do_cau_hoi" class="form-select"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none; background-color: white;">
                                <option value="">Hỗn hợp tất cả</option>
                                <option value="1">Nhận biết</option>
                                <option value="2">Thông hiểu</option>
                                <option value="3">Vận dụng</option>
                            </select>
                            <small class="text-danger" id="error_muc_do_cau_hoi" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></small>
                        </div>

                        {{-- Số lượng câu --}}
                        <div class="form-group">
                            <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px; font-size: 14px;">Số lượng câu hỏi</label>
                            <input type="number" name="tong_so_cau" id="input_tong_so_cau" class="form-input" value="10" min="5" max="50"
                                style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none;" required>
                            <small style="color: #6b7280; font-size: 11px;">Giới hạn: 5 - 50 câu</small>
                            <small class="text-danger" id="error_tong_so_cau" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></small>
                        </div>
                    </div>

                    {{-- Thời gian trả lời --}}
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 6px; font-size: 14px;">Thời gian suy nghĩ / câu (giây)</label>
                        <input type="number" name="thoi_gian_tra_loi_cau_hoi" id="input_thoi_gian_tra_loi_cau_hoi" class="form-input" value="30" min="10" max="120"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; outline: none;" required>
                        <small style="color: #6b7280; font-size: 11px;">Thời gian tối thiểu: 10 giây, tối đa: 120 giây</small>
                        <small class="text-danger" id="error_thoi_gian_tra_loi_cau_hoi" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: none;"></small>
                    </div>
                </div>

                <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid #e5e7eb; text-align: right; background: #f9fafb; display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalPhongQuiz')"
                        style="padding: 10px 18px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; font-weight: 600; color: #374151; background: white; cursor: pointer;">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit"
                        style="padding: 10px 20px; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; color: white; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.15);">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        Khởi tạo phòng
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mở Modal thêm
        function openModalAdd() {
            const modal = document.getElementById('modalPhongQuiz');
            const form = document.getElementById('formPhongQuiz');

            if (modal) {
                if (form) {
                    form.reset();
                    clearFormErrors();
                }
                modal.style.display = "flex";
                setTimeout(() => {
                    modal.querySelector('.modal-card').style.transform = "scale(1)";
                }, 50);
            }
        }

        // Đóng Modal
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.querySelector('.modal-card').style.transform = "scale(0.95)";
                setTimeout(() => {
                    modal.style.display = "none";
                }, 200);
            }
        }

        // Reset lỗi form
        function clearFormErrors() {
            document.querySelectorAll('.text-danger').forEach(el => {
                el.innerText = '';
                el.style.display = 'none';
            });
            document.querySelectorAll('.form-input, .form-select').forEach(el => {
                el.style.borderColor = '';
            });
        }

        // AJAX tạo phòng
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formPhongQuiz');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                clearFormErrors();

                const btnSubmit = document.getElementById('btnSubmit');
                const originalHtml = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span style="display:inline-block; animation: spin 1s linear infinite; margin-right: 5px;">↻</span> Đang khởi tạo...';

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(async response => {
                    let data;
                    try {
                        data = await response.json();
                    } catch (err) {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalHtml;
                        alert('Lỗi xử lý phản hồi từ server!');
                        return;
                    }

                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalHtml;

                    if (response.ok && data.success) {
                        // Thành công -> chuyển hướng trực tiếp đến phòng chờ của Admin
                        window.location.href = data.redirect;
                    } else if (response.status === 422 && data.errors) {
                        // Hiển thị lỗi validate từng field
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const errorBadge = document.getElementById('error_' + key);
                            if (errorBadge) {
                                errorBadge.innerText = messages[0];
                                errorBadge.style.display = 'block';
                            }
                            const inputField = document.getElementsByName(key)[0];
                            if (inputField) inputField.style.borderColor = '#ef4444';
                        }
                    } else {
                        alert(data.message || 'Lỗi không xác định!');
                    }
                })
                .catch(error => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = originalHtml;
                    console.error('Fetch error:', error);
                    alert('Có lỗi kết nối, vui lòng thử lại!');
                });
            });
        });
    </script>

    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .7; transform: scale(1.05); }
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .modal-overlay {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrapper nav { display: flex; justify-content: center; }
        .pagination-wrapper .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-wrapper .pagination li { list-style: none; }
        .pagination-wrapper .pagination li a,
        .pagination-wrapper .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1;
        }
        .pagination-wrapper .pagination li a:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
            color: #4338ca;
        }
        .pagination-wrapper .pagination li.active span,
        .pagination-wrapper .pagination li.active a {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-color: #4f46e5;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
        }
        .pagination-wrapper .pagination li.disabled span {
            background: #f9fafb;
            border-color: #f3f4f6;
            color: #d1d5db;
            cursor: not-allowed;
        }
    </style>
@endsection
