@extends('Admin.layouts.admin')

@section('title', 'Quản lý đáp án')

@section('header_action')
    @if ($cauHoi)
        <button type="button" class="btn btn-primary" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i>
            <span>Thêm đáp án</span>
        </button>
    @else
        <a href="{{ route('admin.cauhoi.index') }}" class="btn btn-primary">
            <i class="fa-solid fa-list-check"></i>
            <span>Chọn câu hỏi</span>
        </a>
    @endif
@endsection

@section('content')
    @php
        $totalAnswers = $stats['total'] ?? 0;
        $correctAnswers = $stats['correct'] ?? 0;
        $hiddenAnswers = $stats['hidden'] ?? 0;
        $imageAnswers = $stats['with_image'] ?? 0;
    @endphp

    <div class="answers-page">
        @if (session('success'))
            <div class="answer-alert answer-alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <strong>Thành công</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="answer-alert answer-alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Không thể xử lý</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="answer-alert answer-alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div>
                    <strong>Vui lòng kiểm tra lại thông tin</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <section class="answers-hero">
            <div class="answers-hero-main">
                <span class="answers-eyebrow">Ngân hàng đáp án</span>
                <h2>{{ $cauHoi ? 'Quản lý đáp án của câu hỏi' : 'Quản lý toàn bộ đáp án' }}</h2>
                @if ($cauHoi)
                    <div class="answers-question-card">
                        <div class="answers-question-meta">
                            <span>#{{ $cauHoi->id }}</span>
                            <span>{{ $cauHoi->chuongHoc->monHoc->ten_mon_hoc ?? 'Chưa có môn học' }}</span>
                            <span>{{ $cauHoi->chuongHoc->ten_chuong ?? 'Chưa có chương' }}</span>
                        </div>
                        <div class="answers-question-content">{!! $cauHoi->noi_dung !!}</div>
                    </div>
                @else
                    <p class="answers-hero-copy">
                        Theo dõi, tìm kiếm và chỉnh sửa đáp án trên toàn hệ thống. Để thêm mới, hãy chọn một câu hỏi cụ thể trước.
                    </p>
                @endif
            </div>

            <div class="answers-hero-actions">
                @if ($cauHoi)
                    <a href="{{ route('admin.dapan.index') }}" class="answers-light-btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        Tất cả đáp án
                    </a>
                    <a href="{{ route('admin.cauhoi.edit', $cauHoi->id) }}" class="answers-light-btn">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Sửa câu hỏi
                    </a>
                @else
                    <a href="{{ route('admin.cauhoi.index') }}" class="answers-light-btn">
                        <i class="fa-solid fa-list-check"></i>
                        Chọn câu hỏi
                    </a>
                @endif
            </div>
        </section>

        <section class="answers-stats">
            <div class="answers-stat">
                <span class="answers-stat-icon answers-stat-total"><i class="fa-solid fa-layer-group"></i></span>
                <div>
                    <strong>{{ number_format($totalAnswers) }}</strong>
                    <span>Tổng đáp án</span>
                </div>
            </div>
            <div class="answers-stat">
                <span class="answers-stat-icon answers-stat-correct"><i class="fa-solid fa-check"></i></span>
                <div>
                    <strong>{{ number_format($correctAnswers) }}</strong>
                    <span>Đáp án đúng</span>
                </div>
            </div>
            <div class="answers-stat">
                <span class="answers-stat-icon answers-stat-hidden"><i class="fa-solid fa-eye-slash"></i></span>
                <div>
                    <strong>{{ number_format($hiddenAnswers) }}</strong>
                    <span>Đang ẩn</span>
                </div>
            </div>
            <div class="answers-stat">
                <span class="answers-stat-icon answers-stat-image"><i class="fa-solid fa-image"></i></span>
                <div>
                    <strong>{{ number_format($imageAnswers) }}</strong>
                    <span>Có hình ảnh</span>
                </div>
            </div>
        </section>

        <section class="answers-filter-card">
            <form action="{{ route('admin.dapan.index') }}" method="GET" class="answers-filter-form">
                @if ($cauHoi)
                    <input type="hidden" name="cau_hoi_id" value="{{ $cauHoi->id }}">
                @endif

                <div class="answers-filter-item answers-filter-search">
                    <label>Tìm kiếm</label>
                    <div class="answers-search-box">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nội dung đáp án hoặc câu hỏi...">
                    </div>
                </div>

                <div class="answers-filter-item">
                    <label>Loại đáp án</label>
                    <select name="correct" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('correct') === '1' ? 'selected' : '' }}>Đáp án đúng</option>
                        <option value="0" {{ request('correct') === '0' ? 'selected' : '' }}>Đáp án nhiễu</option>
                    </select>
                </div>

                <div class="answers-filter-item">
                    <label>Trạng thái</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Đang ẩn</option>
                    </select>
                </div>

                <div class="answers-filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i>
                        Lọc
                    </button>
                    <a href="{{ $cauHoi ? route('admin.dapan.index', ['cau_hoi_id' => $cauHoi->id]) : route('admin.dapan.index') }}"
                        class="btn btn-secondary">
                        <i class="fa-solid fa-rotate-left"></i>
                        Đặt lại
                    </a>
                </div>
            </form>
        </section>

        <section class="answers-table-card">
            <div class="answers-table-header">
                <div>
                    <h3>Danh sách đáp án</h3>
                    <p>Đang hiển thị {{ $dapAns->firstItem() ?? 0 }} - {{ $dapAns->lastItem() ?? 0 }} trên {{ $dapAns->total() }} đáp án</p>
                </div>
                @if ($cauHoi)
                    <button type="button" class="answers-add-inline" onclick="openAddModal()">
                        <i class="fa-solid fa-plus"></i>
                        Thêm đáp án
                    </button>
                @endif
            </div>

            <div class="answers-table-wrap">
                <table class="answers-table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">ID</th>
                            <th>Đáp án</th>
                            <th>Câu hỏi</th>
                            <th style="width: 150px;">Phân loại</th>
                            <th style="width: 150px;">Trạng thái</th>
                            <th style="width: 145px;">Cập nhật</th>
                            <th style="width: 132px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dapAns as $item)
                            <tr>
                                <td>
                                    <span class="answers-id">#{{ $item->id }}</span>
                                </td>
                                <td>
                                    <div class="answers-answer-cell">
                                        <span class="answers-letter">{{ $item->ky_hieu ?: '?' }}</span>
                                        <div class="answers-answer-body">
                                            <div class="answers-answer-text">{!! $item->noi_dung !!}</div>
                                            @if ($item->hinh_anh)
                                                <a href="{{ asset('storage/' . $item->hinh_anh) }}" target="_blank" class="answers-image-link">
                                                    <i class="fa-solid fa-image"></i>
                                                    Xem hình minh họa
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="answers-question-cell">
                                        <strong>{{ $item->cauHoi->chuongHoc->monHoc->ten_mon_hoc ?? 'N/A' }}</strong>
                                        <span>{{ \Illuminate\Support\Str::limit(strip_tags($item->cauHoi->noi_dung ?? 'N/A'), 120) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if ($item->is_dung)
                                        <span class="answers-pill answers-pill-correct">
                                            <i class="fa-solid fa-check"></i>
                                            Đúng
                                        </span>
                                    @else
                                        <button type="button" class="answers-pill answers-pill-muted answers-mark-btn"
                                            data-id="{{ $item->id }}">
                                            <i class="fa-regular fa-circle"></i>
                                            Đặt đúng
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <label class="answers-switch" title="Bật/tắt hiển thị">
                                        <input type="checkbox" class="toggle-status" data-id="{{ $item->id }}"
                                            {{ $item->trang_thai ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                    <small class="answers-status-text">{{ $item->trang_thai ? 'Hiển thị' : 'Đang ẩn' }}</small>
                                </td>
                                <td>
                                    <span class="answers-date">{{ optional($item->updated_at)->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-action btn-edit" title="Chỉnh sửa"
                                            data-payload='@json($item)' onclick="openEditFromButton(this)">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button type="button" class="btn-action btn-delete" title="Xóa"
                                            onclick="deleteDataAjax({{ $item->id }}, @json(\Illuminate\Support\Str::limit(strip_tags($item->noi_dung), 45)), '{{ route('admin.dapan.destroy', $item->id) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="answers-empty">
                                        <i class="fa-regular fa-rectangle-list"></i>
                                        <strong>Chưa có đáp án phù hợp</strong>
                                        <span>Thử đổi bộ lọc hoặc chọn một câu hỏi để thêm đáp án mới.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="answers-table-footer" style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eef2f6; background: #fff; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                <div class="showing-info" style="font-size: 13px; color: #6b7280;">
                    Hiển thị {{ $dapAns->firstItem() ?? 0 }}-{{ $dapAns->lastItem() ?? 0 }} trong tổng số
                    {{ $dapAns->total() }} đáp án
                </div>
                @if($dapAns->hasPages())
                <div class="pagination" style="list-style: none; margin: 0;">
                    <style>
                        .answers-table-footer .pagination ul, .answers-table-footer .pagination li { list-style: none !important; margin: 0; padding: 0; }
                    </style>
                    {{ $dapAns->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </div>
        </section>
    </div>

    <div id="modalDapAn" class="answers-modal-overlay" onclick="closeModal('modalDapAn')">
        <div class="answers-modal" onclick="event.stopPropagation()">
            <div class="answers-modal-header">
                <div>
                    <span id="modalModeLabel">Đáp án</span>
                    <h3 id="modalTitle">Thêm đáp án</h3>
                </div>
                <button type="button" class="answers-modal-close" onclick="closeModal('modalDapAn')" aria-label="Đóng">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="formDapAn" action="{{ route('admin.dapan.save') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="input_id">
                <input type="hidden" name="cau_hoi_id" id="input_cau_hoi_id" value="{{ $cauHoi->id ?? '' }}">

                <div class="answers-modal-body">
                    <div id="question-display-area" class="answers-modal-question" style="{{ $cauHoi ? '' : 'display: none;' }}">
                        <label>Câu hỏi</label>
                        <div id="question-content">{!! $cauHoi->noi_dung ?? '' !!}</div>
                    </div>

                    <div class="answers-form-group">
                        <label for="editor-dapan">Nội dung đáp án <span>*</span></label>
                        <textarea name="noi_dung" id="editor-dapan" placeholder="Nhập nội dung đáp án..." rows="5"></textarea>
                    </div>

                    <div class="answers-form-grid">
                        <div class="answers-form-group">
                            <label for="input_ky_hieu">Ký hiệu</label>
                            <input type="text" name="ky_hieu" id="input_ky_hieu" maxlength="1" placeholder="A">
                        </div>
                        <div class="answers-form-group">
                            <label for="input_thu_tu">Thứ tự</label>
                            <input type="number" name="thu_tu" id="input_thu_tu" min="0" placeholder="0">
                        </div>
                    </div>

                    <div class="answers-form-group">
                        <label for="input_hinh_anh">Hình minh họa</label>
                        <input type="file" name="hinh_anh" id="input_hinh_anh" accept="image/*">
                        <div id="preview_image" class="answers-preview"></div>
                    </div>

                    <div class="answers-option-row">
                        <label class="answers-check-option">
                            <input type="checkbox" name="is_dung" id="input_is_dung" value="1">
                            <span>
                                <strong>Đánh dấu là đáp án đúng</strong>
                                <small>Khi bật, các đáp án đúng khác của câu hỏi này sẽ được bỏ chọn.</small>
                            </span>
                        </label>
                        <label class="answers-check-option">
                            <input type="hidden" name="trang_thai" value="0">
                            <input type="checkbox" name="trang_thai" id="input_trang_thai" value="1" checked>
                            <span>
                                <strong>Hiển thị đáp án</strong>
                                <small>Ẩn đáp án nếu chưa muốn dùng trong bài kiểm tra.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="answers-modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalDapAn')">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <span class="btn-text"><i class="fa-solid fa-floppy-disk"></i> Lưu đáp án</span>
                        <span class="btn-loading" style="display: none;"><i class="fa-solid fa-circle-notch fa-spin"></i> Đang lưu...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('Admin.dapan.style')
@endsection

@push('scripts')
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']]
            },
            svg: { fontCache: 'global' }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        let dapanEditor;

        document.addEventListener('DOMContentLoaded', function() {
            if (window.ClassicEditor && document.querySelector('#editor-dapan')) {
                ClassicEditor
                    .create(document.querySelector('#editor-dapan'), {
                        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
                        language: 'vi'
                    })
                    .then(editor => {
                        dapanEditor = editor;
                        window.dapanEditor = editor;
                    })
                    .catch(error => console.error('Không thể khởi tạo CKEditor:', error));
            }

            document.querySelectorAll('.toggle-status').forEach(input => {
                input.addEventListener('change', function() {
                    toggleAnswerStatus(this);
                });
            });

            document.querySelectorAll('.answers-mark-btn').forEach(button => {
                button.addEventListener('click', function() {
                    markCorrect(this.dataset.id);
                });
            });

            document.getElementById('input_hinh_anh')?.addEventListener('change', previewSelectedImage);

            document.getElementById('formDapAn')?.addEventListener('submit', function() {
                if (window.dapanEditor) {
                    document.getElementById('editor-dapan').value = window.dapanEditor.getData();
                }

                const btn = document.getElementById('btnSubmit');
                btn.disabled = true;
                btn.querySelector('.btn-text').style.display = 'none';
                btn.querySelector('.btn-loading').style.display = 'inline-flex';
            });
        });

        function openAddModal() {
            const questionId = document.getElementById('input_cau_hoi_id').value;
            if (!questionId) {
                Swal.fire('Chưa chọn câu hỏi', 'Vui lòng chọn một câu hỏi trước khi thêm đáp án.', 'warning');
                return;
            }

            const form = document.getElementById('formDapAn');
            form.reset();
            form.action = "{{ route('admin.dapan.save') }}";

            document.getElementById('modalTitle').innerText = 'Thêm đáp án';
            document.getElementById('modalModeLabel').innerText = 'Tạo mới';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('input_id').value = '';
            document.getElementById('input_cau_hoi_id').value = questionId;
            document.getElementById('input_trang_thai').checked = true;
            document.getElementById('preview_image').innerHTML = '';

            if (window.dapanEditor) {
                window.dapanEditor.setData('');
            }

            openModal('modalDapAn');
        }

        function openEditFromButton(button) {
            editDapAn(JSON.parse(button.dataset.payload));
        }

        function editDapAn(data) {
            const form = document.getElementById('formDapAn');
            form.reset();

            document.getElementById('modalTitle').innerText = 'Chỉnh sửa đáp án';
            document.getElementById('modalModeLabel').innerText = `#${data.id}`;
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('input_id').value = data.id;
            document.getElementById('input_cau_hoi_id').value = data.cau_hoi_id;
            document.getElementById('input_ky_hieu').value = data.ky_hieu || '';
            document.getElementById('input_thu_tu').value = data.thu_tu ?? 0;
            document.getElementById('input_is_dung').checked = data.is_dung == 1;
            document.getElementById('input_trang_thai').checked = data.trang_thai == 1;

            const questionArea = document.getElementById('question-display-area');
            const questionContent = document.getElementById('question-content');
            if (data.cau_hoi && data.cau_hoi.noi_dung) {
                questionContent.innerHTML = data.cau_hoi.noi_dung;
                questionArea.style.display = 'block';
                renderMath(questionContent);
            } else {
                questionArea.style.display = 'none';
            }

            if (window.dapanEditor) {
                window.dapanEditor.setData(data.noi_dung || '');
            } else {
                document.getElementById('editor-dapan').value = data.noi_dung || '';
            }

            document.getElementById('preview_image').innerHTML = data.hinh_anh
                ? `<img src="/storage/${data.hinh_anh}" alt="Hình đáp án">`
                : '';

            form.action = "{{ route('admin.dapan.save', ['id' => ':id']) }}".replace(':id', data.id);
            openModal('modalDapAn');
        }

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
            document.querySelector('.answers-modal-body')?.scrollTo({ top: 0 });
            renderMath(document.getElementById(id));
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function previewSelectedImage(event) {
            const file = event.target.files?.[0];
            const preview = document.getElementById('preview_image');
            preview.innerHTML = '';
            if (!file) return;

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            img.alt = 'Xem trước hình đáp án';
            preview.appendChild(img);
        }

        function toggleAnswerStatus(input) {
            fetch("{{ route('admin.dapan.toggle-status', ['id' => ':id']) }}".replace(':id', input.dataset.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Không thể cập nhật trạng thái.');
                    const label = input.closest('td').querySelector('.answers-status-text');
                    label.textContent = data.trang_thai ? 'Hiển thị' : 'Đang ẩn';
                })
                .catch(error => {
                    input.checked = !input.checked;
                    Swal.fire('Lỗi', error.message, 'error');
                });
        }

        function markCorrect(id) {
            Swal.fire({
                title: 'Đặt làm đáp án đúng?',
                text: 'Các đáp án đúng khác của cùng câu hỏi sẽ được bỏ chọn.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch("{{ route('admin.dapan.mark-correct', ['id' => ':id']) }}".replace(':id', id), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Không thể cập nhật đáp án đúng.');
                        Swal.fire('Thành công', data.message, 'success').then(() => window.location.reload());
                    })
                    .catch(error => Swal.fire('Lỗi', error.message, 'error'));
            });
        }

        function renderMath(element) {
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise([element]).catch(error => console.warn(error));
            }
        }
    </script>
@endpush
