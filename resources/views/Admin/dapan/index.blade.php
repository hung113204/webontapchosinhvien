@extends('Admin.layouts.admin')

@section('title', 'Quản lý đáp án')
@section('header_action')
    {{-- Nút Thêm mới đáp án gọi hàm JS đã có --}}
    <button type="button" onclick="openAddModal()" class="btn btn-primary"
        style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span>Thêm đáp án mới</span>
    </button>
@endsection
@section('content')
        @if (session('success'))
        <div style="padding: 16px; background: #ecfdf5; color: #065f46; border-radius: 12px; margin-bottom: 24px; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; color: #10b981;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong style="display: block; font-size: 15px;">Thành công!</strong>
                <span style="font-size: 14px;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div style="padding: 16px; background: #fef2f2; color: #991b1b; border-radius: 12px; margin-bottom: 24px; border: 1px solid #fecaca; display: flex; align-items: flex-start; gap: 12px; box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.1);">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 24px; height: 24px; color: #ef4444; flex-shrink: 0;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong style="display: block; font-size: 15px; margin-bottom: 4px;">Đã có lỗi xảy ra:</strong>
                <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div class="content-section">
        <div class="table-card">
            <div class="table-header-gradient">
                <div class="header-content">
                    @if ($cauHoi)
                        <div class="header-text">
                            <h2 class="header-title">Đáp án cho câu hỏi</h2>
                            <div class="question-display">{!! $cauHoi->noi_dung !!}</div>
                        </div>
                        <a href="{{ route('admin.dapan.index') }}" class="btn-add-white" style="text-decoration: none;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Quay lại quản lý đáp án 
                        </a>
                    @else
                        <div class="header-text">
                            <h2 class="header-title">Danh sách tất cả đáp án</h2>
                        </div>
                        <a href="{{ route('admin.cauhoi.index') }}" class="btn-add-white">Chọn câu hỏi</a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th style="width: 180px;">NỘI DUNG ĐÁP ÁN</th>
                            <th>CÂU HỎI</th>
                            <th style="width: 130px;">TRẠNG THÁI</th>
                            <th style="width: 130px;">NGÀY TẠO</th>
                            <th style="width: 130px;">NGÀY SỬA</th>
                            <th style="width: 130px;">HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dapAns as $item)
                            <tr>
                                <td><span class="id-text">{{ $item->id }}</span></td>
                                <td>
                                    <div class="answer-label-container">
                                        <span class="answer-label {{ $item->is_dung ? 'answer-correct' : 'answer-wrong' }}">
                                            {{ $item->ky_hieu }}.{!! $item->noi_dung !!}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="question-text">{!! $item->cauHoi->noi_dung ?? 'N/A' !!}</div>
                                </td>
                                <td class="text-center">
                                    <label class="switch">
                                        <input type="checkbox" class="toggle-status" data-id="{{ $item->id }}"
                                            {{ $item->trang_thai ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                    <div class="status-label">
                                        <small>{{ $item->trang_thai ? 'Hiển thị' : 'Đang ẩn' }}</small>
                                    </div>
                                </td>
                                <td><span class="date-text">{{ $item->created_at->format('d/m/Y H:i') }}</span></td>
                                <td><span class="date-text">{{ $item->updated_at->format('d/m/Y H:i') }}</span></td>
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <button type="button" class="btn-action btn-edit" title="Sửa"
                                            onclick="window.editDapAn({{ json_encode($item) }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="btn-action btn-delete" title="Xóa"
                                            onclick="deleteDataAjax({{ $item->id }}, '{{ strip_tags($item->noi_dung) }}', '{{ route('admin.dapan.destroy', $item->id) }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Chưa có đáp án nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="table-footer">
                    <div class="showing-info">
                        Hiển thị từ {{ $dapAns->firstItem() ?? 0 }} đến {{ $dapAns->lastItem() ?? 0 }}
                        trong tổng số {{ $dapAns->total() }} đáp án
                    </div>
                    <div class="pagination-wrapper">
                        {{ $dapAns->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL THÊM/SỬA ĐÁP ÁN - THIẾT KẾ MỚI --}}
    <div id="modalDapAn" class="modal-overlay" onclick="closeModal('modalDapAn')">
        <div class="modal-card" onclick="event.stopPropagation()">
            {{-- HEADER GRADIENT --}}
            <div class="modal-header">
                <h3 id="modalTitle" class="modal-title">Thêm đáp án mới</h3>
                <button type="button" class="close-btn" onclick="closeModal('modalDapAn')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="formDapAn" action="{{ route('admin.dapan.save') }}" method="POST" enctype="multipart/form-data"
                novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="input_id">
                <input type="hidden" name="cau_hoi_id" id="input_cau_hoi_id" value="{{ $cauHoi->id ?? '' }}">

                <div class="modal-body">
                    {{-- HIỂN THỊ CÂU HỎI --}}
                    <div id="question-display-area" class="question-display-box" style="display: none;">
                        <label class="form-label">Câu hỏi</label>
                        <div id="question-content" class="question-content-display"></div>
                    </div>

                    {{-- NỘI DUNG ĐÁP ÁN --}}
                    <div class="form-group">
                        <label class="form-label required">Nội dung đáp án</label>
                        <textarea name="noi_dung" id="editor-dapan" class="form-input" placeholder="Nhập nội dung hoặc công thức LaTeX..."
                            rows="4"></textarea>
                    </div>

                    {{-- KÝ HIỆU VÀ THỨ TỰ --}}
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ký hiệu (A, B...)</label>
                            <input type="text" name="ky_hieu" id="input_ky_hieu" class="form-input" placeholder="A"
                                maxlength="5">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="input_thu_tu" class="form-input" placeholder="0"
                                min="0">
                        </div>
                    </div>

                    {{-- HÌNH ẢNH --}}
                    <div class="form-group">
                        <label class="form-label">Hình ảnh minh họa</label>
                        <input type="file" name="hinh_anh" id="input_hinh_anh" class="form-input" accept="image/*">
                        <div id="preview_image" class="image-preview"></div>
                    </div>

                    {{-- ĐÁP ÁN ĐÚNG --}}
                    <div class="form-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="is_dung" id="input_is_dung" value="1">
                            <span class="checkbox-label">Đánh dấu là ĐÁP ÁN ĐÚNG</span>
                        </label>
                    </div>
                </div>

                {{-- FOOTER BUTTONS --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalDapAn')">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        {{-- Thêm 2 span này để điều khiển hiển thị --}}
                        <span class="btn-text"><i class="fas fa-save"></i> Lưu thông tin</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fas fa-circle-notch fa-spin"></i> Đang lưu...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('Admin.dapan.style')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        // Hàm mở modal thêm đáp án
        function openAddModal() {
            const modal = document.getElementById('modalDapAn');
            const form = document.getElementById('formDapAn');

            form.reset();
            document.getElementById('modalTitle').innerText = "Thêm đáp án mới";
            document.getElementById('formMethod').value = "POST";
            document.getElementById('input_id').value = "";
            document.getElementById('preview_image').innerHTML = "";

            // Ẩn phần hiển thị câu hỏi
            document.getElementById('question-display-area').style.display = 'none';

            // Reset CKEditor nếu có
            if (window.dapanEditor) {
                window.dapanEditor.setData('');
            }

            // Reset vị trí modal
            if (window.resetModalPosition) {
                window.resetModalPosition();
            }

            form.action = "{{ route('admin.dapan.save') }}";
            modal.style.display = "flex";
            const modalBody = document.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0; // Tự động cuộn lên đầu trang
            }
        }

        // Hàm sửa đáp án - FIX LỖI ĐỔ DỮ LIỆU
        function editDapAn(data) {
            const modalBody = document.querySelector('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0; // Tự động cuộn lên đầu trang
            }
            const modal = document.getElementById('modalDapAn');
            const form = document.getElementById('formDapAn');

            // Reset form trước
            form.reset();

            document.getElementById('modalTitle').innerText = "Chỉnh sửa đáp án";

            // Hiển thị câu hỏi
            const questionArea = document.getElementById('question-display-area');
            const questionContent = document.getElementById('question-content');

            if (data.cau_hoi && data.cau_hoi.noi_dung) {
                questionContent.innerHTML = data.cau_hoi.noi_dung;
                questionArea.style.display = 'block';

                // Render MathJax nếu có công thức
                if (typeof MathJax !== 'undefined') {
                    MathJax.typesetPromise([questionContent]).catch((err) => console.log(err));
                }
            } else {
                questionArea.style.display = 'none';
            }

            // Gán giá trị vào các ô input
            document.getElementById('input_id').value = data.id;
            document.getElementById('input_ky_hieu').value = data.ky_hieu || "";
            document.getElementById('input_thu_tu').value = data.thu_tu || 0;
            document.getElementById('input_is_dung').checked = (data.is_dung == 1);
            document.getElementById('input_cau_hoi_id').value = data.cau_hoi_id;

            // Đổ dữ liệu vào CKEditor
            if (window.dapanEditor) {
                window.dapanEditor.setData(data.noi_dung || '');
            }

            // Hiển thị ảnh cũ
            if (data.hinh_anh) {
                document.getElementById('preview_image').innerHTML =
                    `<img src="/storage/${data.hinh_anh}" style="max-width: 150px; margin-top: 10px; border-radius: 8px; border: 2px solid #e5e7eb;">`;
            } else {
                document.getElementById('preview_image').innerHTML = "";
            }

            // Đổi method thành PUT cho Update

            form.action = "{{ route('admin.dapan.save', ['id' => ':id']) }}".replace(':id', data.id);
            document.getElementById('formMethod').value = "PUT";

            // Reset vị trí modal
            if (window.resetModalPosition) {
                window.resetModalPosition();
            }

            modal.style.display = "flex";
        }

        // Gán hàm vào window để có thể gọi từ onclick
        window.editDapAn = editDapAn;

        // Hàm đóng modal
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        // ===== DRAGGABLE MODAL =====
        (function() {
            let isDragging = false;
            let currentX;
            let currentY;
            let initialX;
            let initialY;
            let xOffset = 0;
            let yOffset = 0;

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.querySelector('.modal-card');
                const header = document.querySelector('.modal-header');

                if (header && modal) {
                    header.addEventListener('mousedown', dragStart);
                    document.addEventListener('mousemove', drag);
                    document.addEventListener('mouseup', dragEnd);

                    // Touch events for mobile
                    header.addEventListener('touchstart', dragStart, {
                        passive: false
                    });
                    document.addEventListener('touchmove', drag, {
                        passive: false
                    });
                    document.addEventListener('touchend', dragEnd);
                }
            });

            function dragStart(e) {
                const modal = document.querySelector('.modal-card');

                if (e.type === "touchstart") {
                    initialX = e.touches[0].clientX - xOffset;
                    initialY = e.touches[0].clientY - yOffset;
                } else {
                    initialX = e.clientX - xOffset;
                    initialY = e.clientY - yOffset;
                }

                if (e.target.closest('.modal-header')) {
                    isDragging = true;
                }
            }

            function drag(e) {
                if (isDragging) {
                    e.preventDefault();

                    const modal = document.querySelector('.modal-card');

                    if (e.type === "touchmove") {
                        currentX = e.touches[0].clientX - initialX;
                        currentY = e.touches[0].clientY - initialY;
                    } else {
                        currentX = e.clientX - initialX;
                        currentY = e.clientY - initialY;
                    }

                    xOffset = currentX;
                    yOffset = currentY;

                    setTranslate(currentX, currentY, modal);
                }
            }

            function dragEnd(e) {
                initialX = currentX;
                initialY = currentY;
                isDragging = false;
            }

            function setTranslate(xPos, yPos, el) {
                el.style.transform = `translate(${xPos}px, ${yPos}px)`;
            }

            // Reset position khi đóng/mở modal
            window.resetModalPosition = function() {
                const modal = document.querySelector('.modal-card');
                if (modal) {
                    modal.style.transform = 'translate(0px, 0px)';
                    xOffset = 0;
                    yOffset = 0;
                    currentX = 0;
                    currentY = 0;
                }
            };
        })();
    </script>
    <script>
        // Xử lý hiệu ứng loading khi submit form
        document.getElementById('formDapAn').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            const text = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.btn-loading');

            // Vô hiệu hóa nút để tránh click nhiều lần
            btn.disabled = true;
            btn.style.opacity = '0.8';

            // Chuyển đổi hiển thị
            text.style.display = 'none';
            loading.style.display = 'inline-block';

            // Đảm bảo dữ liệu CKEditor được cập nhật vào textarea trước khi gửi
            if (window.dapanEditor) {
                document.getElementById('editor-dapan').value = window.dapanEditor.getData();
            }
        });
    </script>
@endpush
