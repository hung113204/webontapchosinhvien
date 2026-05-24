@extends('Admin.layouts.admin')
@section('title', 'Thêm mới bài kiểm tra')
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
    <!-- Form Section -->
    <section class="form-section">
        <div class="form-card">
            <div class="card-header" style="padding: 0 0 24px 0; border-bottom: 2px solid var(--gray-200);">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--gray-900)">
                    🎯 Tạo bài kiểm tra mới
                </h3>
                <p style="font-size: 14px; color: var(--gray-600); margin-top: 6px">
                    Chọn chế độ tạo đề: Tự động (random) hoặc Thủ công (chọn từng câu)
                </p>
            </div>

            <form id="exam-form" method="POST" action="{{ route('admin.baikiemtra.save') }}" novalidate
                style="margin-top: 32px">
                @csrf

                <!-- Basic Information -->
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên bài kiểm tra <span class="required">*</span></label>
                        <input type="text" name="ten_bai" id="ten_bai" class="form-input"
                            placeholder="VD: Kiểm tra giữa kỳ - Cấu trúc dữ liệu" value="{{ old('ten_bai') }}" required />
                    </div>

                    <div class="form-group">
                        <label>Môn học <span class="required">*</span></label>
                        <select class="form-select" name="mon_hoc_id" id="mon-hoc-select" required>
                            <option value="">Chọn môn học</option>
                            @foreach ($monHocs as $monHoc)
                                <option value="{{ $monHoc->id }}"
                                    {{ old('mon_hoc_id') == $monHoc->id ? 'selected' : '' }}>
                                    {{ $monHoc->ten_mon_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Chương học <span class="required">*</span></label>
                        <div class="multi-select-container" id="chapter-tags-container">
                            <div class="tags-wrapper" id="selected-chapters"></div>
                            <select class="form-select-inline" id="chapter-dropdown">
                                <option value="">+ Thêm chương...</option>
                            </select>
                        </div>
                        <input type="hidden" name="chuong_hoc_ids" id="chuong_hoc_ids_input" />
                        <span class="form-help">Bạn có thể chọn một hoặc nhiều chương cùng lúc.</span>
                        <!-- THỐNG KÊ CÂU HỎI THEO CHƯƠNG ĐÃ CHỌN -->
                        <div id="question-bank-stats" style="display:none; margin-top: 12px;">
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">

                                <div
                                    style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px; text-align:center;">
                                    <div style="font-size:11px; color:#15803d; font-weight:600; margin-bottom:6px;">🟢 Câu
                                        dễ</div>
                                    <div id="stat-de" style="font-size:26px; font-weight:700; color:#15803d;">0</div>
                                    <div style="font-size:11px; color:#86efac; margin-top:2px;">câu có sẵn</div>
                                </div>

                                <div
                                    style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:12px; text-align:center;">
                                    <div style="font-size:11px; color:#a16207; font-weight:600; margin-bottom:6px;">🟡 Trung
                                        bình</div>
                                    <div id="stat-tb" style="font-size:26px; font-weight:700; color:#a16207;">0</div>
                                    <div style="font-size:11px; color:#fbbf24; margin-top:2px;">câu có sẵn</div>
                                </div>

                                <div
                                    style="background:#fff1f2; border:1px solid #fecdd3; border-radius:10px; padding:12px; text-align:center;">
                                    <div style="font-size:11px; color:#be123c; font-weight:600; margin-bottom:6px;">🔴 Câu
                                        khó</div>
                                    <div id="stat-kho" style="font-size:26px; font-weight:700; color:#be123c;">0</div>
                                    <div style="font-size:11px; color:#fca5a5; margin-top:2px;">câu có sẵn</div>
                                </div>

                                <div
                                    style="background:#eff6ff; border:2px solid #93c5fd; border-radius:10px; padding:12px; text-align:center;">
                                    <div style="font-size:11px; color:#1d4ed8; font-weight:600; margin-bottom:6px;">📚 Tổng
                                        cộng</div>
                                    <div id="stat-tong" style="font-size:26px; font-weight:700; color:#1d4ed8;">0</div>
                                    <div style="font-size:11px; color:#93c5fd; margin-top:2px;">câu có sẵn</div>
                                </div>

                            </div>
                            <p style="margin:10px 0 0; font-size:12px; color:#6b7280;">
                                ℹ️ Số câu bạn nhập bên dưới không được vượt quá số câu có sẵn ở mỗi mức độ.
                            </p>
                            <div
                                style="margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: #fff;">
                                <div
                                    style="padding: 10px 14px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; font-size: 13px; font-weight: 600; color: #334155;">
                                    Thống kê theo từng chương
                                </div>
                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: collapse; min-width: 520px;">
                                        <thead>
                                            <tr style="background: #f8fafc;">
                                                <th style="padding: 10px 12px; text-align: left; font-size: 12px; color: #475569; border-bottom: 1px solid #e5e7eb;">Chương</th>
                                                <th style="padding: 10px 12px; text-align: center; font-size: 12px; color: #15803d; border-bottom: 1px solid #e5e7eb;">Dễ</th>
                                                <th style="padding: 10px 12px; text-align: center; font-size: 12px; color: #a16207; border-bottom: 1px solid #e5e7eb;">Trung bình</th>
                                                <th style="padding: 10px 12px; text-align: center; font-size: 12px; color: #be123c; border-bottom: 1px solid #e5e7eb;">Khó</th>
                                                <th style="padding: 10px 12px; text-align: center; font-size: 12px; color: #1d4ed8; border-bottom: 1px solid #e5e7eb;">Tổng</th>
                                            </tr>
                                        </thead>
                                        <tbody id="chapter-stats-body"></tbody>
                                        <tfoot>
                                            <tr style="background: #eff6ff;">
                                                <td style="padding: 10px 12px; font-size: 12px; font-weight: 700; color: #1e3a8a; border-top: 1px solid #bfdbfe;">Tổng tất cả chương</td>
                                                <td id="chapter-total-de" style="padding: 10px 12px; text-align: center; font-size: 12px; font-weight: 700; color: #15803d; border-top: 1px solid #bfdbfe;">0</td>
                                                <td id="chapter-total-tb" style="padding: 10px 12px; text-align: center; font-size: 12px; font-weight: 700; color: #a16207; border-top: 1px solid #bfdbfe;">0</td>
                                                <td id="chapter-total-kho" style="padding: 10px 12px; text-align: center; font-size: 12px; font-weight: 700; color: #be123c; border-top: 1px solid #bfdbfe;">0</td>
                                                <td id="chapter-total-tong" style="padding: 10px 12px; text-align: center; font-size: 12px; font-weight: 700; color: #1d4ed8; border-top: 1px solid #bfdbfe;">0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Time Configuration -->
                <div style="margin-bottom: 32px; margin-top: 32px;">
                    <h4 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                        ⏰ Cấu hình thời gian
                    </h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Thời gian làm bài (phút) <span class="required">*</span></label>
                            <input type="number" name="thoi_gian_phut" class="form-input" placeholder="60"
                                value="{{ old('thoi_gian_phut', 60) }}" required />
                            <span class="form-help">Thời gian tối đa sinh viên được làm bài</span>
                        </div>

                        <div class="form-group">
                            <label>Thời gian bắt đầu <span class="required">*</span></label>
                            <input type="datetime-local" name="thoi_gian_bat_dau" class="form-input"
                                value="{{ old('thoi_gian_bat_dau') }}" required />
                        </div>

                        <div class="form-group">
                            <label>Thời gian kết thúc <span class="required">*</span></label>
                            <input type="datetime-local" name="thoi_gian_ket_thuc" class="form-input"
                                value="{{ old('thoi_gian_ket_thuc') }}" required />
                        </div>
                    </div>
                </div>

                <!-- Attempts & Scoring -->
                <div style="margin-bottom: 32px; margin-top: 32px;">
                    <h4 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                        🎯 Cấu hình lượt thi & Điểm số
                    </h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Số lần làm bài <span class="required">*</span></label>
                            <input type="number" name="so_lan_lam_bai" class="form-input" placeholder="VD: 1"
                                value="{{ old('so_lan_lam_bai', 1) }}" min="1" required />
                            <span class="form-help">Số lần tối đa sinh viên được thực hiện bài thi này</span>
                        </div>

                        <div class="form-group">
                            <label>Cách tính điểm <span class="required">*</span></label>
                            <select class="form-select" name="cach_tinh_diem" required>
                                <option value="0" {{ old('cach_tinh_diem') == 0 ? 'selected' : '' }}>Lấy điểm lần cao
                                    nhất</option>
                                <option value="1" {{ old('cach_tinh_diem') == 1 ? 'selected' : '' }}>Lấy điểm lần
                                    cuối
                                    cùng</option>
                                <option value="2" {{ old('cach_tinh_diem') == 2 ? 'selected' : '' }}>Trung bình cộng
                                    các lần thi</option>
                            </select>
                            <span class="form-help">Áp dụng nếu số lần làm bài lớn hơn 1</span>
                        </div>
                    </div>
                </div>

                <!-- Question Selection MODE -->
                <div style="margin-bottom: 32px">
                    <h4 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                        📝 Lựa chọn câu hỏi
                    </h4>

                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" name="question-mode" id="auto-select" value="auto" checked />
                            <label for="auto-select">
                                <strong>🎲 Tự động lấy đề</strong>
                                <span class="form-help" style="display: block">Hệ thống sẽ tự động chọn ngẫu nhiên câu hỏi
                                    theo mức độ</span>
                            </label>
                        </div>

                        <div class="radio-item">
                            <input type="radio" name="question-mode" id="manual-select" value="manual" />
                            <label for="manual-select">
                                <strong>✍️ Chọn câu hỏi thủ công</strong>
                                <span class="form-help" style="display: block">Tự chọn từng câu hỏi cụ thể cho đề
                                    thi</span>
                            </label>
                        </div>
                    </div>

                    <!-- AUTO MODE CONFIG -->
                    <div id="auto-config"
                        style="margin-top: 20px; padding: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                        <input type="hidden" name="tu_dong_lay_de" id="tu_dong_lay_de" value="1">

                        <h5 style="font-size: 14px; font-weight: 600; margin-bottom: 16px; color: white;">
                            ⚙️ Cấu hình tự động lấy đề
                        </h5>
                        <div class="form-grid" style="grid-template-columns: repeat(3, 1fr); gap: 16px;">
                            <div class="form-group" style="margin-bottom: 0">
                                <label style="color: white; font-weight: 600;">🟢 Số câu dễ</label>
                                <input type="number" name="so_cau_de" class="form-input" id="so-cau-de"
                                    value="{{ old('so_cau_de', 5) }}" min="0"
                                    style="font-size: 18px; font-weight: 600; text-align: center;" />
                                <div id="hint-so-cau-de"
                                    style="margin-top: 8px; font-size: 12px; color: rgba(255,255,255,0.85); text-align: center;">
                                    Có sẵn 0 câu dễ
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0">
                                <label style="color: white; font-weight: 600;">🟡 Số câu trung bình</label>
                                <input type="number" name="so_cau_tb" class="form-input" id="so-cau-tb"
                                    value="{{ old('so_cau_tb', 10) }}" min="0"
                                    style="font-size: 18px; font-weight: 600; text-align: center;" />
                                <div id="hint-so-cau-tb"
                                    style="margin-top: 8px; font-size: 12px; color: rgba(255,255,255,0.85); text-align: center;">
                                    Có sẵn 0 câu trung bình
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0">
                                <label style="color: white; font-weight: 600;">🔴 Số câu khó</label>
                                <input type="number" name="so_cau_kho" class="form-input" id="so-cau-kho"
                                    value="{{ old('so_cau_kho', 5) }}" min="0"
                                    style="font-size: 18px; font-weight: 600; text-align: center;" />
                                <div id="hint-so-cau-kho"
                                    style="margin-top: 8px; font-size: 12px; color: rgba(255,255,255,0.85); text-align: center;">
                                    Có sẵn 0 câu khó
                                </div>
                            </div>
                        </div>

                        <!-- Tổng câu -->
                        <div
                            style="margin-top: 16px; padding: 12px; background: rgba(255,255,255,0.2); border-radius: 8px; text-align: center;">
                            <span style="color: white; font-size: 14px;">Tổng số câu: </span>
                            <strong id="total-questions" style="color: #fbbf24; font-size: 20px;">20</strong>
                            <span style="color: white; font-size: 14px;"> câu hỏi</span>
                        </div>
                    </div>

                    <!-- MANUAL MODE BUTTON -->
                    <div id="manual-config" style="display: none; margin-top: 20px;">
                        <button type="button" id="continue-manual-btn"
                            style="width: 100%; padding: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; transition: all 0.3s;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                style="width: 24px; height: 24px;">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span>Tiếp tục chọn câu hỏi thủ công</span>
                        </button>
                        <p style="text-align: center; margin-top: 12px; color: var(--gray-600); font-size: 13px;">
                            Bạn sẽ được chuyển sang trang soạn đề để chọn từng câu hỏi cụ thể
                        </p>
                    </div>
                </div>
                <div style="margin-bottom: 32px">
                    <h4 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                        🌐 Trạng thái hiển thị
                    </h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Trạng thái bài kiểm tra <span class="required">*</span></label>
                            <select class="form-select" name="trang_thai" required>
                                <option value="0" {{ old('trang_thai') == '0' ? 'selected' : '' }}>📝 Bản nháp
                                    (Draft)</option>
                                <option value="1" {{ old('trang_thai', '1') == '1' ? 'selected' : '' }}>✅ Đang mở
                                    (Active)</option>
                                <option value="2" {{ old('trang_thai') == '2' ? 'selected' : '' }}>🚫 Đã đóng
                                    (Closed)</option>
                            </select>
                            <span class="form-help">Bản nháp sẽ không hiển thị với sinh viên ngay cả khi đã đến giờ
                                thi.</span>
                        </div>
                    </div>
                </div>
                <!-- Options -->
                <!-- Tùy chọn nâng cao -->
                <div style="margin-bottom: 32px">
                    <h4 style="font-size: 16px; font-weight: 600; color: var(--gray-900); margin-bottom: 16px;">
                        🎛️ Tùy chọn nâng cao
                    </h4>
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="xem_diem" value="1"
                                {{ old('xem_diem', 0) ? 'checked' : '' }} />
                            <span>Cho phép xem điểm</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="xem_bai_lam" value="1"
                                {{ old('xem_bai_lam', 0) ? 'checked' : '' }} />
                            <span>Cho phép xem bài làm</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="dao_cau_hoi" value="1"
                                {{ old('dao_cau_hoi', 0) ? 'checked' : '' }} />
                            <span>Đảo thứ tự câu hỏi</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="dao_dap_an" value="1"
                                {{ old('dao_dap_an', 0) ? 'checked' : '' }} />
                            <span>Đảo thứ tự đáp án</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="nop_khi_chuyen_tab" value="1"
                                {{ old('nop_khi_chuyen_tab', 0) ? 'checked' : '' }} />
                            <span>Tự động nộp khi chuyển tab</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div id="submit-buttons"
                    style="display: flex; gap: 12px; justify-content: flex-end; padding-top: 24px; border-top: 2px solid var(--gray-200);">
                    <a href="{{ route('admin.baikiemtra.index') }}" class="btn btn-outline">Hủy bỏ</a>
                    <button type="submit" name="action" value="draft" class="btn btn-secondary">💾 Lưu nháp</button>
                    <button type="submit" name="action" value="publish" class="btn btn-primary">✅ Tạo và xuất
                        bản</button>
                </div>
            </form>
        </div>
    </section>

    @php
        $oldSelectedChapterIds = collect(explode(',', old('chuong_hoc_ids', '')))
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter(function ($id) {
                return $id > 0;
            })
            ->values()
            ->all();
    @endphp

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.selectedChapters = {};

                const monHocSelect = document.getElementById('mon-hoc-select');
                const chapterDropdown = document.getElementById('chapter-dropdown');
                const selectedChaptersDiv = document.getElementById('selected-chapters');
                const chuongIdsInput = document.getElementById('chuong_hoc_ids_input');
                const radioAuto = document.getElementById('auto-select');
                const radioManual = document.getElementById('manual-select');
                const autoConfig = document.getElementById('auto-config');
                const manualConfig = document.getElementById('manual-config');
                const submitButtons = document.getElementById('submit-buttons');
                const continueBtn = document.getElementById('continue-manual-btn');
                const tuDongLayDeInput = document.getElementById('tu_dong_lay_de');
                const soCauDeInput = document.getElementById('so-cau-de');
                const soCauTbInput = document.getElementById('so-cau-tb');
                const soCauKhoInput = document.getElementById('so-cau-kho');
                const totalQuestionsSpan = document.getElementById('total-questions');
                const questionBankStats = document.getElementById('question-bank-stats');
                const chapterStatsBody = document.getElementById('chapter-stats-body');
                const oldSelectedChapterIds = @json($oldSelectedChapterIds);

                // Load chương học khi chọn môn
                monHocSelect.addEventListener('change', function() {
                    const monHocId = this.value;
                    chapterDropdown.innerHTML = '<option value="">+ Thêm chương...</option>';
                    window.selectedChapters = {};
                    renderSelectedChapters();

                    if (!monHocId) return;

                    fetch(`{{ url('/admin/baikiemtra/api/chuong-hoc') }}/${monHocId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && Array.isArray(data.data)) {
                                data.data.forEach(chapter => {
                                    const opt = document.createElement('option');
                                    opt.value = chapter.id;
                                    opt.textContent = chapter.ten_chuong;
                                    chapterDropdown.appendChild(opt);

                                    if (oldSelectedChapterIds.includes(Number(chapter.id))) {
                                        window.selectedChapters[chapter.id] = chapter.ten_chuong;
                                    }
                                });

                                if (Object.keys(window.selectedChapters).length > 0) {
                                    renderSelectedChapters();
                                }
                            }
                        })
                        .catch(err => console.error('Lỗi load chương:', err));
                });

                // Thêm chương được chọn
                chapterDropdown.addEventListener('change', function() {
                    if (!this.value) return;
                    const chapterId = this.value;
                    const chapterName = this.options[this.selectedIndex].text;

                    if (!window.selectedChapters[chapterId]) {
                        window.selectedChapters[chapterId] = chapterName;
                        renderSelectedChapters();
                    }
                    this.value = '';
                });

                // Render các chương đã chọn
                function renderSelectedChapters() {
                    selectedChaptersDiv.innerHTML = '';
                    const ids = Object.keys(window.selectedChapters)
                        .map(id => Number(id))
                        .filter(id => !isNaN(id) && id > 0);

                    if (ids.length === 0) {
                        chuongIdsInput.value = '';
                        questionBankStats.style.display = 'none';
                        chapterStatsBody.innerHTML = '';
                        updateQuestionHints({
                            de: 0,
                            tb: 0,
                            kho: 0
                        });
                        return;
                    }

                    ids.forEach(id => {
                        const name = window.selectedChapters[id];
                        if (!name) return;

                        const tag = document.createElement('div');
                        tag.className = 'tag-item';
                        tag.style.cssText =
                            `display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; font-size: 13px; color: white; margin: 4px 4px 4px 0; font-weight: 500;`;
                        tag.innerHTML = `
                            <span>${name}</span>
                            <button type="button" class="btn-remove-chapter" data-id="${id}">×</button>
                        `;
                        selectedChaptersDiv.appendChild(tag);
                    });

                    const uniqueIds = [...new Set(ids)];
                    chuongIdsInput.value = uniqueIds.join(',');

                    // Gán sự kiện xóa
                    document.querySelectorAll('.btn-remove-chapter').forEach(btn => {
                        btn.onclick = function() {
                            const idToRemove = this.getAttribute('data-id');
                            delete window.selectedChapters[idToRemove];
                            renderSelectedChapters();
                        };
                    });
                    fetchQuestionStats(ids);
                }

                function fetchQuestionStats(ids) {
                    fetch('{{ route('admin.baikiemtra.api.question-stats') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                chuong_hoc_ids: ids
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) return;

                            const de = data.de || 0;
                            const tb = data.tb || 0;
                            const kho = data.kho || 0;
                            const tong = data.tong || (de + tb + kho);

                            document.getElementById('stat-de').textContent = de;
                            document.getElementById('stat-tb').textContent = tb;
                            document.getElementById('stat-kho').textContent = kho;
                            document.getElementById('stat-tong').textContent = tong;
                            renderChapterStats(data.chapters || [], {
                                de,
                                tb,
                                kho,
                                tong
                            });
                            updateQuestionHints({
                                de,
                                tb,
                                kho
                            });

                            // Cập nhật màu cảnh báo nếu người dùng nhập quá
                            checkOverLimit();

                            questionBankStats.style.display = 'block';
                        })
                        .catch(err => console.error('Lỗi load stats:', err));
                }

                // Kiểm tra nhập quá số câu có sẵn → tô đỏ ô input
                function renderChapterStats(chapters, totals) {
                    if (!chapterStatsBody) return;

                    if (!chapters.length) {
                        chapterStatsBody.innerHTML = `
                            <tr>
                                <td colspan="5" style="padding: 14px 12px; text-align: center; font-size: 12px; color: #64748b;">
                                    Chưa có dữ liệu câu hỏi cho các chương đã chọn.
                                </td>
                            </tr>
                        `;
                    } else {
                        chapterStatsBody.innerHTML = chapters.map(chapter => `
                            <tr>
                                <td style="padding: 10px 12px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9;">${chapter.ten_chuong}</td>
                                <td style="padding: 10px 12px; text-align: center; font-size: 13px; color: #15803d; border-bottom: 1px solid #f1f5f9;">${chapter.de}</td>
                                <td style="padding: 10px 12px; text-align: center; font-size: 13px; color: #a16207; border-bottom: 1px solid #f1f5f9;">${chapter.tb}</td>
                                <td style="padding: 10px 12px; text-align: center; font-size: 13px; color: #be123c; border-bottom: 1px solid #f1f5f9;">${chapter.kho}</td>
                                <td style="padding: 10px 12px; text-align: center; font-size: 13px; color: #1d4ed8; font-weight: 600; border-bottom: 1px solid #f1f5f9;">${chapter.tong}</td>
                            </tr>
                        `).join('');
                    }

                    document.getElementById('chapter-total-de').textContent = totals.de || 0;
                    document.getElementById('chapter-total-tb').textContent = totals.tb || 0;
                    document.getElementById('chapter-total-kho').textContent = totals.kho || 0;
                    document.getElementById('chapter-total-tong').textContent = totals.tong || 0;
                }

                function updateQuestionHints(stats) {
                    document.getElementById('hint-so-cau-de').textContent = `Có sẵn ${stats.de || 0} câu dễ`;
                    document.getElementById('hint-so-cau-tb').textContent =
                        `Có sẵn ${stats.tb || 0} câu trung bình`;
                    document.getElementById('hint-so-cau-kho').textContent = `Có sẵn ${stats.kho || 0} câu khó`;
                }

                function checkOverLimit() {
                    const limits = {
                        'so-cau-de': parseInt(document.getElementById('stat-de').textContent) || 0,
                        'so-cau-tb': parseInt(document.getElementById('stat-tb').textContent) || 0,
                        'so-cau-kho': parseInt(document.getElementById('stat-kho').textContent) || 0,
                    };

                    ['so-cau-de', 'so-cau-tb', 'so-cau-kho'].forEach(inputId => {
                        const input = document.getElementById(inputId);
                        if (!input) return;
                        const val = parseInt(input.value) || 0;
                        input.style.borderColor = val > limits[inputId] ? '#dc2626' : '';
                        input.title = val > limits[inputId] ?
                            `⚠️ Vượt quá! Chỉ có ${limits[inputId]} câu có sẵn` :
                            '';
                    });
                }

                // Gọi checkOverLimit mỗi khi người dùng thay đổi số câu
                ['so-cau-de', 'so-cau-tb', 'so-cau-kho'].forEach(id => {
                    document.getElementById(id)?.addEventListener('input', checkOverLimit);
                });

                // Toggle giữa auto và manual mode
                function toggleMode() {
                    const isManual = radioManual.checked;
                    autoConfig.style.display = isManual ? 'none' : 'block';
                    manualConfig.style.display = isManual ? 'block' : 'none';
                    submitButtons.style.display = isManual ? 'none' : 'flex';
                    tuDongLayDeInput.value = isManual ? '0' : '1';
                }
                [radioAuto, radioManual].forEach(r => r?.addEventListener('change', toggleMode));
                toggleMode();

                // Tính tổng câu hỏi
                function updateTotalQuestions() {
                    const total = (parseInt(soCauDeInput.value) || 0) +
                        (parseInt(soCauTbInput.value) || 0) +
                        (parseInt(soCauKhoInput.value) || 0);
                    totalQuestionsSpan.textContent = total;
                }
                [soCauDeInput, soCauTbInput, soCauKhoInput].forEach(i => i?.addEventListener('input',
                    updateTotalQuestions));
                updateTotalQuestions();
                updateQuestionHints({
                    de: 0,
                    tb: 0,
                    kho: 0
                });

                if (monHocSelect.value) {
                    monHocSelect.dispatchEvent(new Event('change'));
                }

                // Xử lý nút "Tiếp tục chọn câu hỏi thủ công"
                continueBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const tenBai = document.getElementById('ten_bai').value.trim();
                    const monHocId = monHocSelect.value;
                    const chuongIds = document.getElementById('chuong_hoc_ids_input').value;

                    if (!tenBai || !monHocId || !chuongIds) {
                        alert('❌ Vui lòng điền đủ: Tên bài, Môn học và chọn ít nhất 1 Chương!');
                        return;
                    }

                    // Thu thập dữ liệu từ form
                    const formData = new FormData(document.getElementById('exam-form'));
                    formData.set('action', 'draft');
                    formData.set('tu_dong_lay_de', '0');

                    fetch("{{ route('admin.baikiemtra.save') }}", {
                            method: "POST",
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = data.redirect;
                            } else {
                                alert('❌ Lỗi: ' + (data.message || 'Có lỗi xảy ra'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('❌ Có lỗi xảy ra khi lưu dữ liệu');
                        });
                });

                function handleFormSubmit(actionType) {
                    const form = document.getElementById('exam-form');
                    const formData = new FormData(form);

                    // Đảm bảo action được gửi lên để Controller xử lý trạng thái
                    formData.append('action', actionType);

                    // Vô hiệu hóa nút để tránh bấm nhiều lần gây tạo 2 bài
                    const btn = event.target;
                    btn.disabled = true;
                    btn.innerHTML = 'Đang xử lý...';

                    fetch(form.action, {
                            method: "POST",
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (res.ok && data.success) {
                                window.location.href = data.redirect;
                            } else {
                                // Hiển thị lỗi Validation cụ thể (ví dụ: lỗi thời gian)
                                let errorMsg = data.message || 'Có lỗi xảy ra';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).flat().join('\n');
                                }
                                alert('❌ Thất bại:\n' + errorMsg);
                                btn.disabled = false;
                                btn.innerHTML = actionType === 'draft' ? '💾 Lưu nháp' : '✅ Tạo và xuất bản';
                            }
                        })
                        .catch(err => {
                            console.error('Fetch error:', err);
                            btn.disabled = false;
                        });
                }
            });
        </script>
    @endpush
@endsection
