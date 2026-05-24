@extends('Admin.layouts.admin')
@section('title', 'Quản lý Môn học')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm môn học mới</span>
    </button>
@endsection

@section('modal-content')
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
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ $monHoc->total() ?? 0 }}</h4>
                    <p>Tổng số môn học</p>
                </div>
            </div>
        </div>
    </section>

    <section class="filters-section">
        <form action="{{ route('admin.monhoc.index') }}" method="GET" class="filter-group">
            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nhập tên hoặc mã môn học..." />
                </div>
            </div>
            <div class="filter-item" style="display: flex; align-items: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; height: 42px;">
                    Tìm kiếm
                </button>
                @if (request()->has('search'))
                    <a href="{{ route('admin.monhoc.index') }}" class="btn btn-secondary"
                        style="padding: 10px 20px; height: 42px; margin-left: 8px;">
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
                    <h3>Danh sách môn học</h3>
                    <span class="count-badge">Học kỳ {{ date('Y') }}</span>
                </div>
                <div class="table-actions">
                    <span class="selected-info" id="selectedCount" style="display: none;">
                        Đã chọn: <span id="selectedNumber">0</span>
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" class="checkbox" id="selectAll" />
                            </th>
                            <th width="100">Mã môn</th>
                            <th width="280">Tên môn học</th>
                            <th width="80">Tín chỉ</th>
                            <th width="100">Bài học</th> {{-- Thêm mới --}}
                            <th width="100">Câu hỏi</th> {{-- Thêm mới --}}
                            <th width="120">Số chương</th>
                            <th width="120">Học Viên</th>
                            <th width="150">Ngày tạo</th>
                            <th width="110">Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monHoc as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" class="checkbox row-checkbox" data-id="{{ $item->id }}" />
                                </td>

                                {{-- Mã môn học --}}
                                <td>
                                    <span class="id-badge"
                                        style="background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                                        {{ $item->ma_mon_hoc }}
                                    </span>
                                </td>

                                {{-- Tên môn học + Hình ảnh --}}
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px">
                                        @if ($item->hinh_anh)
                                            <img src="{{ asset('storage/' . $item->hinh_anh) }}" width="40"
                                                height="40"
                                                style="border-radius:8px; object-fit:cover; border: 2px solid #e5e7eb;"
                                                alt="{{ $item->ten_mon_hoc }}"
                                                title="Hình ảnh môn {{ $item->ten_mon_hoc }}">
                                        @else
                                            <img src="{{ asset('frontend/asset/images/default_subject.png') }}" width="40"
                                                height="40"
                                                style="border-radius:8px; object-fit:cover; border: 2px solid #e5e7eb;"
                                                alt="{{ $item->ten_mon_hoc }}"
                                                title="Hình ảnh môn {{ $item->ten_mon_hoc }}">
                                        @endif
                                        <div>
                                            <strong style="display: block; color: #111827; font-size: 14px;">
                                                {{ $item->ten_mon_hoc }}
                                            </strong>
                                            @if (isset($item->mo_ta_ngan) && $item->mo_ta_ngan)
                                                <small
                                                    style="color: #6b7280; font-size: 12px; display: block; margin-top: 2px;">
                                                    {{ Str::limit($item->mo_ta_ngan, 40) }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Số tín chỉ --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                            stroke="#6366f1" stroke-width="2">
                                            <path d="M12 2L2 7l10 5 10-5-10-5z" />
                                            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" />
                                        </svg>
                                        <span
                                            style="font-weight: 600; color: #4338ca;">{{ $item->so_tin_chi ?? 0 }}</span>
                                        <span style="color: #6b7280; font-size: 12px;">TC</span>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1e40af;">
                                        {{ $item->bai_hocs_count ?? 0 }}
                                    </div>
                                    <div style="font-size: 11px; color: #9ca3af;">bài học</div>
                                </td>

                                {{-- Số câu hỏi --}}
                                <td>
                                    <div style="font-weight: 600; color: #1e40af;">
                                        {{ $item->cau_hois_count ?? 0 }}
                                    </div>
                                    <div style="font-size: 11px; color: #9ca3af;">câu hỏi</div>
                                </td>

                                {{-- Số chương học --}}
                                <td>
                                    @php
                                        $soChuong = isset($item->chuongHocs) ? $item->chuongHocs->count() : 0;
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div
                                            style="width: 36px; height: 36px; background: {{ $soChuong > 0 ? '#dbeafe' : '#f3f4f6' }}; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                                stroke="{{ $soChuong > 0 ? '#2563eb' : '#9ca3af' }}" stroke-width="2">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div
                                                style="font-weight: 600; color: {{ $soChuong > 0 ? '#1e40af' : '#6b7280' }}; font-size: 15px;">
                                                {{ $soChuong }}
                                            </div>
                                            <div style="font-size: 11px; color: #9ca3af;">chương</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div
                                            style="width: 36px; height: 36px; background: #f0fdf4; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: 1px solid #bbf7d0;">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                                stroke="#16a34a" stroke-width="2">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                                <circle cx="9" cy="7" r="4" />
                                                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #15803d; font-size: 15px;">
                                                {{ number_format($item->so_luong_nguoi_hoc) }}
                                            </div>
                                            <div style="font-size: 11px; color: #86efac; font-weight: 500;">Học viên</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Ngày tạo --}}
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                                            stroke="#9ca3af" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"
                                                ry="2" />
                                            <line x1="16" y1="2" x2="16" y2="6" />
                                            <line x1="8" y1="2" x2="8" y2="6" />
                                            <line x1="3" y1="10" x2="21" y2="10" />
                                        </svg>
                                        <div>
                                            <div style="font-size: 13px; color: #374151; font-weight: 500;">
                                                {{ $item->created_at->format('d/m/Y') }}
                                            </div>
                                            <div style="font-size: 11px; color: #9ca3af;">
                                                {{ $item->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Trạng thái --}}
                                {{-- Trạng thái (có thể click để toggle) --}}
                                {{-- Trạng thái toggle --}}
                                <td>
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <span id="lbl_{{ $item->id }}"
                                            style="font-size:13px; font-weight:500; min-width:70px;
                   color:{{ $item->trang_thai ? '#333' : '#9ca3af' }};">
                                            {{ $item->trang_thai ? 'Hoạt động' : 'Tạm ẩn' }}
                                        </span>
                                        <button onclick="toggleStatus({{ $item->id }}, this)"
                                            data-status="{{ $item->trang_thai ? '1' : '0' }}"
                                            style="
                width:52px; height:28px; border-radius:999px; border:none;
                background:{{ $item->trang_thai ? '#333' : '#e5e7eb' }};
                position:relative; cursor:pointer; padding:0;
                transition: background 0.25s;
            ">
                                            <div
                                                style="
                width:22px; height:22px; border-radius:50%; background:#fff;
                position:absolute; top:3px; left:3px;
                box-shadow:0 1px 3px rgba(0,0,0,0.18);
                transition: transform 0.25s cubic-bezier(.4,0,.2,1);
                transform: translateX({{ $item->trang_thai ? '24px' : '0px' }});
            ">
                                            </div>
                                        </button>
                                    </div>
                                </td>

                                {{-- Thao tác --}}
                                <td>
                                    <div class="action-buttons" style="display: flex; gap: 8px;">
                                        <button class="btn-action btn-edit" title="Sửa môn học"
                                            onclick="editMonHoc({{ $item }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('admin.monhoc.destroy', $item->id) }}" method="POST"
                                            style="display:inline; margin:0; padding:0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action btn-delete" title="Xóa môn học"
                                                onclick="deleteDataAjax({{ $item->id }}, '{{ addslashes($item->ten_mon_hoc) }}', '{{ route('admin.monhoc.destroy', $item->id) }}')">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6" />
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
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
                                            style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);">
                                            <svg viewBox="0 0 24 24" width="40" height="40" fill="none"
                                                stroke="white" stroke-width="2">
                                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                                            </svg>
                                        </div>
                                        <h3 style="font-size: 20px; color: #111827; margin-bottom: 8px; font-weight: 600;">
                                            Chưa có môn học nào
                                        </h3>
                                        <p style="color: #6b7280; margin-bottom: 20px; font-size: 14px;">
                                            Hệ thống chưa có môn học nào. Hãy thêm môn học đầu tiên để bắt đầu quản
                                            lý nội dung giảng dạy.
                                        </p>
                                        <button class="btn btn-primary" onclick="openModalAdd()"
                                            style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;">
                                            Thêm môn học đầu tiên
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($monHoc->hasPages())
                <div class="pagination-wrapper" style="padding: 20px 20px 16px">
                    {{ $monHoc->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </section>

    {{-- MODAL THÊM/SỬA MÔN HỌC --}}
    @include('Admin.monhoc.modal')

    <script>
        // ===== MỞ MODAL THÊM MỚI =====
        function openModalAdd() {
            const modal = document.getElementById('modalMonHoc');
            const form = document.getElementById('formMonHoc');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Thêm môn học mới";
                if (form) {
                    form.reset();
                    document.getElementById('input_id').value = "";
                    document.getElementById('input_so_tin_chi').value = "3";
                    form.action = "{{ route('admin.monhoc.storeOrUpdate') }}";

                    // Ẩn preview image
                    const imagePreview = document.getElementById('image_preview');
                    if (imagePreview) imagePreview.style.display = 'none';

                    // Reset lỗi cũ
                    clearFormErrors();

                    // Set trạng thái mặc định
                    document.querySelectorAll('input[name="trang_thai"]').forEach(radio => {
                        radio.checked = radio.value === '1';
                    });
                }
                modal.classList.add('show');
                modal.style.display = "";
            }
        }

        // ===== MỞ MODAL SỬA =====
        function editMonHoc(data) {
            const modal = document.getElementById('modalMonHoc');
            if (modal) {
                document.getElementById('modalTitle').innerText = "Chỉnh sửa môn học";

                // Reset lỗi cũ trước khi đổ dữ liệu
                clearFormErrors();

                // Đổ dữ liệu
                document.getElementById('input_id').value = data.id;
                document.getElementById('input_ten_mon_hoc').value = data.ten_mon_hoc;
                document.getElementById('input_ma_mon_hoc').value = data.ma_mon_hoc || '';
                document.getElementById('input_so_tin_chi').value = data.so_tin_chi || 3;
                document.getElementById('input_muc_do_mon_hoc').value = data.muc_do_mon_hoc ?
                    data.muc_do_mon_hoc.toString() : '';
                document.getElementById('input_mau_sac').value = data.mau_sac || '#4f46e5';
                document.getElementById('input_mo_ta_ngan').value = data.mo_ta_ngan || '';
                document.getElementById('input_mo_ta_chi_tiet').value = data.mo_ta_chi_tiet || '';
                document.getElementById('input_thu_tu').value = data.thu_tu || 0;
                document.getElementById('input_icon_class').value = data.icon_class || '';
                document.getElementById('input_is_featured').checked = !!data.is_featured;
                document.getElementById('input_is_popular').checked = !!data.is_popular;

                // Set radio trạng thái
                document.querySelectorAll('input[name="trang_thai"]').forEach(radio => {
                    radio.checked = radio.value == (data.trang_thai ? '1' : '0');
                });

                // Preview ảnh
                const imagePreview = document.getElementById('image_preview');
                const previewImage = document.getElementById('preview_image');
                if (data.hinh_anh && previewImage) {
                    previewImage.src = '/storage/' + data.hinh_anh;
                    if (imagePreview) imagePreview.style.display = 'block';
                } else if (imagePreview) {
                    imagePreview.style.display = 'none';
                }

                modal.classList.add('show');
                modal.style.display = "";
            }
        }

        // ===== ĐÓNG MODAL =====
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = "none";
                const imagePreview = document.getElementById('image_preview');
                if (imagePreview) imagePreview.style.display = 'none';
            }
        }

        // ===== RESET LỖI FORM =====
        function clearFormErrors() {
            // Xóa text lỗi
            document.querySelectorAll('.text-danger[id^="error_"]').forEach(el => {
                el.innerText = '';
                el.style.display = 'none';
            });
            // Xóa viền đỏ
            document.querySelectorAll('#formMonHoc .form-input, #formMonHoc .form-select').forEach(el => {
                el.style.borderColor = '';
            });
        }

        // ===== PREVIEW ẢNH =====
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('preview_image');
                const previewContainer = document.getElementById('image_preview');
                output.src = reader.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        // ===== AJAX SUBMIT FORM =====
        document.addEventListener('DOMContentLoaded', function() {
            const formMonHoc = document.getElementById('formMonHoc');
            if (!formMonHoc) return;

            formMonHoc.addEventListener('submit', function(e) {
                // Ngăn navigation.js bắt được sự kiện này
                e.preventDefault();
                e.stopImmediatePropagation(); // ← KEY FIX: chặn navigation.js chạy showLoading()

                // Reset lỗi cũ
                clearFormErrors();

                // Loading trên nút
                const btnSubmit = document.getElementById('btnSubmit');
                const originalHtml = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

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
                            alert('Lỗi parse phản hồi từ server!');
                            return;
                        }

                        // Luôn restore nút sau khi parse xong
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalHtml;

                        if (response.ok && data.success) {
                            //alert(data.message);
                            // Thành công → reload
                            window.location.reload();
                        } else if (response.status === 422 && data.errors) {
                            // Hiển thị lỗi validate từng field
                            for (const [key, messages] of Object.entries(data.errors)) {
                                const errorBadge = document.getElementById('error_' + key);
                                if (errorBadge) {
                                    errorBadge.innerText = messages[0];
                                    errorBadge.style.color = 'red';
                                    errorBadge.style.display = 'block';
                                }
                                const inputField = document.getElementsByName(key)[0];
                                if (inputField) inputField.style.borderColor = 'red';
                            }
                        } else {
                            alert('Lỗi: ' + (data.message || 'Không xác định'));
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

        function toggleStatus(id, btn) {
            fetch(`/admin/mon-hoc/toggle-status/${id}`, {
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
                    window.location.reload();
                })
                .catch(() => alert('Có lỗi kết nối!'));
        }
    </script>
@endsection
