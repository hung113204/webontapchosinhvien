@extends('Admin.layouts.admin')
@section('title', 'Quản lý Danh mục Trang chủ')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm danh mục mới</span>
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
        <form action="{{ route('admin.danhmuctrangchu.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Loại danh mục</label>
                <select name="loai_danh_muc" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả loại</option>
                    <option value="banner" {{ request('loai_danh_muc') == 'banner' ? 'selected' : '' }}>Banner</option>
                    <option value="course_list" {{ request('loai_danh_muc') == 'course_list' ? 'selected' : '' }}>Danh sách khóa học</option>
                    <option value="exam_list" {{ request('loai_danh_muc') == 'exam_list' ? 'selected' : '' }}>Danh sách đề thi</option>
                    <option value="feature_list" {{ request('loai_danh_muc') == 'feature_list' ? 'selected' : '' }}>Tính năng (Feature)</option>
                    <option value="stats" {{ request('loai_danh_muc') == 'stats' ? 'selected' : '' }}>Thống kê (Stats)</option>
                    <option value="testimonial" {{ request('loai_danh_muc') == 'testimonial' ? 'selected' : '' }}>Đánh giá
                        (Testimonial)</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="0" {{ request('trang_thai') == '0' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
            </div>

            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm tên danh mục trang chủ..." />
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách danh mục trang chủ</h3>
                    <span class="count-badge">Tổng: {{ $stats['total'] ?? 0 }} bản ghi</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="80">Hình ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Loại</th>
                            <th width="100">Thứ tự</th>
                            <th>Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($danhMuc as $item)
                            <tr>
                                <td><input type="checkbox" class="checkbox" /></td>
                                <td>
                                    @if ($item->hinh_anh)
                                        <img src="{{ asset('storage/' . $item->hinh_anh) }}" alt="{{ $item->tieu_de }}"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd;">
                                    @else
                                        <div
                                            style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 10px;">
                                            No IMG</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $item->tieu_de }}</strong><br>
                                    <span style="font-size: 12px; color: #6b7280;">{{ $item->slug }}</span>
                                </td>
                                <td>
                                    <span class="badge"
                                        style="background-color: #e0e7ff; color: #3730a3;">{{ strtoupper($item->loai_danh_muc) }}</span>
                                </td>
                                <td><strong>{{ $item->thu_tu }}</strong></td>
                                <td>
                                    <span class="badge {{ $item->trang_thai ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $item->trang_thai ? 'Hiển thị' : 'Đang ẩn' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Sửa"
                                            onclick="editDanhMuc({{ $item }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <button class="btn-action btn-delete" title="Xóa"
                                            onclick="deleteDanhMuc({{ $item->id }}, '{{ $item->tieu_de }}')">
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
                {{ $danhMuc->appends(request()->query())->links() }}
            </div>
        </div>
    </section>

    <div id="modalDanhMuc" class="modal-overlay" onclick="dongModalDanhMuc('modalDanhMuc')">
        <div class="modal-card" style="max-width: 600px; max-height: 90vh; overflow-y: auto;"
            onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm danh mục mới</h3>
                <button type="button" class="close-btn" onclick="dongModalDanhMuc('modalDanhMuc')">&times;</button>
            </div>

            {{-- Chú ý: Cần có enctype="multipart/form-data" để upload ảnh --}}
            <form id="formDanhMuc" action="{{ route('admin.danhmuctrangchu.save') }}" method="POST"
                enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Tiêu đề *</label>
                        <input type="text" name="tieu_de" id="input_tieu_de" class="form-input" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                            placeholder="Nhập tiêu đề...">
                    </div>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Đường dẫn (Slug) *</label>
                        <input type="text" name="slug" id="input_slug" class="form-input" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                            placeholder="vi-du-duong-dan...">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label>Loại danh mục *</label>
                            <select name="loai_danh_muc" id="input_loai_danh_muc" class="form-select" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="">-- Chọn loại --</option>
                                <option value="banner">Banner</option>
                                <option value="course_list">Danh sách khóa học</option>
                                <option value="exam_list">Danh sách đề thi</option>
                                <option value="feature_list">Tính năng (Feature)</option>
                                <option value="stats">Thống kê (Stats)</option>
                                <option value="testimonial">Đánh giá</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="trang_thai" id="input_trang_thai" class="form-select"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="1">Đang hiển thị</option>
                                <option value="0">Đang ẩn</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Mô tả (Tùy chọn)</label>
                        <textarea name="mo_ta" id="input_mo_ta" class="form-input" rows="3"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;" placeholder="Nhập mô tả ngắn..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Hình ảnh</label>

                        <div id="current_image_wrapper"
                            style="display: none; align-items: center; gap: 15px; margin-bottom: 10px; padding: 10px; background: #f8fafc; border: 1px idashed #cbd5e1; border-radius: 8px;">
                            <img id="preview_old_img" src=""
                                style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <label
                                style="color: #ef4444; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                <input type="checkbox" name="delete_image" value="1"> Xóa ảnh hiện tại
                            </label>
                        </div>

                        <input type="file" name="hinh_anh" id="input_hinh_anh" class="form-input" accept="image/*"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <small style="color: #6b7280;">Chọn file mới nếu muốn thay đổi hình ảnh.</small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label>Icon Class</label>
                            <input type="text" name="icon_class" id="input_icon_class" class="form-input"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                                placeholder="fa-solid fa-star">
                        </div>
                        <div class="form-group">
                            <label>Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="input_thu_tu" class="form-input" value="0"
                                min="0"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                        <div class="form-group">
                            <label>Số lượng hiển thị</label>
                            <input type="number" name="so_luong_hien_thi" id="input_so_luong_hien_thi"
                                class="form-input" value="4" min="1"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer"
                    style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-secondary"
                        onclick="dongModalDanhMuc('modalDanhMuc')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Hàm mở modal thêm mới
        function openModalAdd() {
            const modal = document.getElementById('modalDanhMuc');
            const form = document.getElementById('formDanhMuc');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Thêm danh mục mới";
                if (form) {
                    form.reset();
                    document.getElementById('input_id').value = "";
                    form.action = "{{ route('admin.danhmuctrangchu.save') }}";
                }
                modal.classList.add('show');
                modal.style.display = "flex";
            }
        }

        // Đổ dữ liệu vào modal để sửa
        function editDanhMuc(data) {
            const modal = document.getElementById('modalDanhMuc');
            const form = document.getElementById('formDanhMuc');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Chỉnh sửa danh mục";
                form.reset();

                // Đổ dữ liệu cũ
                document.getElementById('input_id').value = data.id;
                document.getElementById('input_tieu_de').value = data.tieu_de;
                document.getElementById('input_slug').value = data.slug || '';
                document.getElementById('input_loai_danh_muc').value = data.loai_danh_muc;
                document.getElementById('input_mo_ta').value = data.mo_ta || '';
                document.getElementById('input_icon_class').value = data.icon_class || '';
                document.getElementById('input_thu_tu').value = data.thu_tu;
                document.getElementById('input_so_luong_hien_thi').value = data.so_luong_hien_thi;
                document.getElementById('input_trang_thai').value = data.trang_thai ? 1 : 0;

                // Xử lý hiển thị khu vực ảnh cũ
                const wrapper = document.getElementById('current_image_wrapper');
                const imgTag = document.getElementById('preview_old_img');
                if (data.hinh_anh) {
                    wrapper.style.display = 'flex';
                    imgTag.src = `{{ asset('storage') }}/${data.hinh_anh}`;
                } else {
                    wrapper.style.display = 'none';
                }

                modal.classList.add('show');
                modal.style.display = "flex";
            }
        }

        // Hàm đóng Modal
        function dongModalDanhMuc(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = "none";

                // Mẹo nhỏ: Xóa sạch dữ liệu form khi đóng để lần sau mở lên không bị dính chữ cũ
                const form = document.getElementById('formDanhMuc');
                if (form) form.reset();
            }
        }

        // Ẩn alert tự động
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

        // Xóa dữ liệu (Sử dụng chung file ajax-delete.js của bạn)
        function deleteDanhMuc(id, title) {
            const url = `{{ route('admin.danhmuctrangchu.destroy', '') }}/${id}`;
            deleteDataAjax(id, title, url);
        }
        document.getElementById('input_tieu_de').addEventListener('keyup', function() {
            // Chỉ tự động điền khi đang ở chế độ thêm mới (input_id trống)
            // Nếu đang sửa thì không nên đổi slug tự động để tránh hỏng SEO / link cũ
            (document.getElementById('input_id').value === "")
            let title = this.value;
            let slug = title.toLowerCase();

            // Đổi ký tự có dấu thành không dấu
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');

            // Xóa các ký tự đặc biệt
            slug = slug.replace(/[^a-z0-9\s-]/g, '');
            // Đổi khoảng trắng thành ký tự gạch ngang
            slug = slug.replace(/\s+/g, '-');
            // Xóa các ký tự gạch ngang dư thừa
            slug = slug.replace(/-+/g, '-');
            // Bỏ gạch ngang ở đầu và cuối
            slug = slug.replace(/^-+|-+$/g, '');

            document.getElementById('input_slug').value = slug;

        });
    </script>
@endsection
