{{--
|--------------------------------------------------------------------------
| _ai_generate.blade.php
| Partial blade — Tab "Tạo câu hỏi bằng AI"
|
| Cách nhúng vào create.blade.php:
|   1. Thêm tab button vào danh sách tab (xem ghi chú bên dưới)
|   2. Thêm dòng sau sau tab excel-form:
|        @include('admin.cauhoi._ai_generate')
|--------------------------------------------------------------------------
--}}

{{-- ============================================================ --}}
{{-- TAB AI: TẠO CÂU HỎI BẰNG AI                                 --}}
{{-- ============================================================ --}}
<div id="ai-form" class="tab-content" style="display: none;">

    {{-- ---- Bước 1: Cấu hình sinh câu hỏi ---- --}}
    <div id="ai-config-area">

        {{-- Header mô tả --}}
        <div style="display: flex; align-items: center; gap: 14px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                    border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; color: white;">
            <div style="flex-shrink: 0;">
                <img src="{{ asset('frontend/asset/images/t2.png') }}" style="width: 44px; height: 44px; border-radius: 8px; object-fit: cover;" alt="AI">
            </div>
            <div>
                <div style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">✨ Tạo câu hỏi bằng AI</div>
                <div style="font-size: 13px; opacity: 0.9;">AI sẽ tự động sinh câu hỏi trắc nghiệm theo chương và mức độ bạn chọn. Xem trước và chọn những câu muốn lưu.</div>
            </div>
        </div>

        {{-- Form cấu hình --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">

            <div class="form-group">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                    Môn học <span style="color:red">*</span>
                </label>
                <select id="ai_mon_hoc_id"
                    style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="">-- Chọn môn học --</option>
                    @foreach ($dsMonHoc as $mon)
                        <option value="{{ $mon->id }}">{{ $mon->ten_mon_hoc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                    Chương học <span style="color:red">*</span>
                </label>
                <select id="ai_chuong_hoc_id"
                    style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="">-- Chọn chương --</option>
                    @foreach ($dsChuong as $chuong)
                        <option value="{{ $chuong->id }}" data-mon="{{ $chuong->mon_hoc_id }}">
                            {{ $chuong->ten_chuong }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Mức độ</label>
                <select id="ai_muc_do"
                    style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                    <option value="1">🟢 Dễ</option>
                    <option value="2" selected>🟡 Trung bình</option>
                    <option value="3">🔴 Khó</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                    Số lượng câu hỏi
                </label>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <input type="range" id="ai_so_luong_slider" min="1" max="20" value="5"
                        style="flex: 1; accent-color: #4f46e5;"
                        oninput="document.getElementById('ai_so_luong_display').textContent = this.value;
                                 document.getElementById('ai_so_luong').value = this.value;">
                    <span id="ai_so_luong_display"
                        style="background: #4f46e5; color: white; border-radius: 8px; padding: 4px 14px;
                               font-weight: 700; font-size: 16px; min-width: 48px; text-align: center;">5</span>
                    <input type="hidden" id="ai_so_luong" value="5">
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; margin-top: 4px;">
                    <span>1</span><span>20</span>
                </div>
            </div>

        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                Chủ đề / Từ khóa gợi ý
                <span style="font-weight: 400; color: #94a3b8; font-size: 13px;">(tùy chọn)</span>
            </label>
            <input type="text" id="ai_chu_de" placeholder="VD: định lý Pythagore, cấu trúc tế bào, lịch sử thế chiến..."
                style="width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0; border-radius: 8px;
                       font-size: 14px; box-sizing: border-box;">
            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">
                Để trống nếu muốn AI tự chọn nội dung phù hợp theo chương
            </small>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                📖 Tải lên tài liệu bài học <span style="font-weight: 400; color: #94a3b8; font-size: 13px;">(tùy chọn - Hỗ trợ Word .docx, .doc, PowerPoint .ppt, .pptx hoặc Text .txt)</span>
            </label>
            <div style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s;"
                 id="ai_file_dropzone" onclick="document.getElementById('ai_tai_lieu').click()">
                <input type="file" id="ai_tai_lieu" accept=".docx,.doc,.ppt,.pptx,.txt" style="display: none;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="width: 32px; height: 32px; margin: 0 auto 8px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="12" y1="18" x2="12" y2="12" />
                    <polyline points="9 15 12 12 15 15" />
                </svg>
                <div id="ai_file_label" style="color: #64748b; font-size: 13px; font-weight: 500;">
                    Chọn tài liệu bài học hoặc kéo thả vào đây
                </div>
                <div id="ai_file_info" style="color: #4f46e5; font-size: 12px; font-weight: 600; margin-top: 4px; display: none;"></div>
            </div>
            <small style="color: #94a3b8; font-size: 12px; margin-top: 4px; display: block;">
                AI sẽ đọc nội dung chữ trong tài liệu này để biên soạn câu hỏi bám sát thực tế nhất. Với PowerPoint, hệ thống đọc chữ trong slide/notes, không đọc chữ nằm trong ảnh.
            </small>
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="button" id="btnGenerateAI"
                style="display: inline-flex; align-items: center; gap: 10px; padding: 12px 32px;
                       background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white;
                       border: none; border-radius: 10px; font-weight: 700; font-size: 15px;
                       cursor: pointer; box-shadow: 0 4px 14px rgba(79,70,229,0.35);
                       transition: opacity 0.2s;"
                onmouseover="this.style.opacity='0.88'"
                onmouseout="this.style.opacity='1'">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="width:20px;height:20px;">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                Tạo câu hỏi ngay
            </button>
        </div>
    </div>

    {{-- ---- Bước 2: Kết quả preview ---- --}}
    <div id="ai-preview-area" style="display: none; margin-top: 30px;">
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin-bottom: 28px;">

        {{-- Tiêu đề kết quả --}}
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
            <h4 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;">
                AI đã tạo được
                <span id="ai_result_count" style="color: #4f46e5;">0</span> câu hỏi
                <span id="ai_result_info" style="font-size: 13px; color: #94a3b8; font-weight: 400;"></span>
            </h4>
            <button type="button" id="btnRegenerateAI"
                style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px;
                       background: white; border: 1px solid #4f46e5; color: #4f46e5;
                       border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px;">
                    <polyline points="1 4 1 10 7 10"/>
                    <path d="M3.51 15a9 9 0 1 0 .49-3.5"/>
                </svg>
                Tạo lại
            </button>
        </div>

        {{-- Thông báo hướng dẫn --}}
        <div style="background: #ede9fe; border: 1px solid #c4b5fd; border-radius: 8px;
                    padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" style="width:20px;height:20px;flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span style="color: #5b21b6; font-size: 14px;">
                Xem lại các câu hỏi bên dưới. <strong>Bỏ chọn</strong> những câu không cần thiết, sau đó bấm <strong>Lưu vào hệ thống</strong>.
            </span>
        </div>

        {{-- Chọn tất cả --}}
        <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="ai_select_all" checked
                style="width: 17px; height: 17px; accent-color: #4f46e5; cursor: pointer;">
            <label for="ai_select_all" style="font-weight: 600; cursor: pointer; color: #374151;">Chọn tất cả</label>
            <span id="ai_selected_count"
                style="margin-left: auto; background: #ede9fe; color: #5b21b6;
                       padding: 3px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                0 câu được chọn
            </span>
        </div>

        {{-- Danh sách câu hỏi --}}
        <div id="ai_questions_list"></div>

        {{-- Nút hành động --}}
        <div style="margin-top: 28px; display: flex; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px;">
            <button type="button" id="btnSaveAI"
                style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 36px;
                       background: #22c55e; color: white; border: none; border-radius: 10px;
                       font-weight: 700; font-size: 15px; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="width:18px;height:18px;">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Lưu vào hệ thống
            </button>
            <button type="button" id="btnCancelAI"
                style="padding: 12px 28px; background: #f1f5f9; color: #64748b;
                       border: 1px solid #e2e8f0; border-radius: 10px; font-weight: 600;
                       font-size: 15px; cursor: pointer;">
                Hủy / Làm lại
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- JAVASCRIPT — Tab AI                                           --}}
{{-- ============================================================ --}}
<script>
(function () {
    'use strict';

    /* ---------- Lọc chương theo môn học ---------- */
    document.getElementById('ai_mon_hoc_id').addEventListener('change', function () {
        const monId = this.value;
        document.querySelectorAll('#ai_chuong_hoc_id option').forEach(opt => {
            if (!opt.value) return; // Giữ option mặc định
            opt.style.display = (!monId || opt.dataset.mon === monId) ? '' : 'none';
        });
        document.getElementById('ai_chuong_hoc_id').value = '';
    });

    /* ---------- Dữ liệu câu hỏi AI ---------- */
    let aiQuestions = [];

    /* ---------- Drag & Drop / Chọn tài liệu AI ---------- */
    const dropzone = document.getElementById('ai_file_dropzone');
    const fileInput = document.getElementById('ai_tai_lieu');
    const fileLabel = document.getElementById('ai_file_label');
    const fileInfo = document.getElementById('ai_file_info');

    // Drag events
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#4f46e5';
        dropzone.style.background = '#f5f3ff';
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '#cbd5e1';
        dropzone.style.background = '#f8fafc';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#cbd5e1';
        dropzone.style.background = '#f8fafc';
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    fileInput.addEventListener('change', handleFileSelect);

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            fileLabel.style.display = 'none';
            fileInfo.textContent = `📄 Đã chọn: ${file.name} (${(file.size / 1024).toFixed(1)} KB)`;
            fileInfo.style.display = 'block';
            dropzone.style.borderColor = '#22c55e';
            dropzone.style.background = '#f0fdf4';
        } else {
            fileLabel.style.display = 'block';
            fileInfo.style.display = 'none';
            dropzone.style.borderColor = '#cbd5e1';
            dropzone.style.background = '#f8fafc';
        }
    }

    /* ---------- Nút: Tạo câu hỏi ---------- */
    document.getElementById('btnGenerateAI').addEventListener('click', generateAI);
    document.getElementById('btnRegenerateAI').addEventListener('click', generateAI);

    async function generateAI() {
        const chuongId = document.getElementById('ai_chuong_hoc_id').value;
        const mucDo    = document.getElementById('ai_muc_do').value;
        const soLuong  = document.getElementById('ai_so_luong').value;
        const chuDe    = document.getElementById('ai_chu_de').value.trim();
        const taiLieu  = fileInput.files[0];

        if (!chuongId) {
            Swal.fire('Thiếu thông tin', 'Vui lòng chọn Chương học trước khi tạo câu hỏi.', 'warning');
            return;
        }

        Swal.fire({
            title: '✨ AI đang tạo câu hỏi...',
            html: `<div style="color:#6b7280;font-size:14px;">Đang sinh <strong>${soLuong}</strong> câu hỏi, vui lòng đợi trong giây lát...</div>`,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        const formData = new FormData();
        formData.append('chuong_hoc_id', chuongId);
        formData.append('muc_do', mucDo);
        formData.append('so_luong', soLuong);
        formData.append('chu_de', chuDe);
        if (taiLieu) {
            formData.append('tai_lieu', taiLieu);
        }

        try {
            const res = await fetch('{{ route("admin.cauhoi.generateAI") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await res.json();
            Swal.close();

            if (!data.success) {
                Swal.fire('Lỗi', data.message || 'AI không phản hồi. Vui lòng thử lại.', 'error');
                return;
            }

            aiQuestions = data.questions || [];
            renderAIPreview(aiQuestions, data.mon_hoc, data.chuong_hoc);

        } catch (err) {
            Swal.close();
            Swal.fire('Lỗi kết nối', 'Không thể kết nối tới AI: ' + err.message, 'error');
        }
    }

    /* ---------- Render danh sách câu hỏi preview ---------- */
    function renderAIPreview(questions, monHoc, chuongHoc) {
        document.getElementById('ai_result_count').textContent = questions.length;
        document.getElementById('ai_result_info').textContent  =
            monHoc && chuongHoc ? `— ${monHoc} / ${chuongHoc}` : '';

        const mucDoLabel = { '1': 'Dễ', '2': 'Trung bình', '3': 'Khó' };
        const mucDoColor = { '1': '#22c55e', '2': '#f59e0b', '3': '#ef4444' };
        const currentMucDo = document.getElementById('ai_muc_do').value;

        let html = '';
        questions.forEach((q, idx) => {
            html += `
            <div class="ai-question-card" data-ai-index="${idx}"
                style="border: 1px solid #c4b5fd; border-radius: 10px; padding: 20px;
                       margin-bottom: 16px; background: #faf5ff; transition: all 0.2s;">

                <div style="display: flex; align-items: flex-start; margin-bottom: 14px;">
                    <input type="checkbox" class="ai-q-checkbox" data-index="${idx}" checked
                        style="width:17px;height:17px;margin-right:12px;margin-top:4px;accent-color:#4f46e5;cursor:pointer;">

                    <span style="background:#4f46e5;color:white;padding:5px 12px;border-radius:6px;
                                 font-weight:700;margin-right:12px;min-width:42px;text-align:center;flex-shrink:0;">
                        ${idx + 1}
                    </span>

                    <div style="flex:1;">
                        <div style="font-size:15px;font-weight:600;color:#1e293b;line-height:1.6;margin-bottom:8px;">
                            ${escapeHtml(q.noi_dung)}
                        </div>
                        <span style="background:${mucDoColor[currentMucDo]}22;color:${mucDoColor[currentMucDo]};
                                     border:1px solid ${mucDoColor[currentMucDo]}44;
                                     padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                            ${mucDoLabel[currentMucDo] ?? 'Trung bình'}
                        </span>
                    </div>
                </div>

                <div style="margin-left: 71px; margin-bottom: 12px;">
                    ${(q.answers || []).map((ans, aIdx) => {
                        const letter = String.fromCharCode(65 + aIdx);
                        const correct = ans.is_dung;
                        return `
                        <div style="display:flex;align-items:center;padding:9px 12px;margin:6px 0;border-radius:8px;
                                    background:${correct ? '#dcfce7' : 'white'};
                                    border:1px solid ${correct ? '#22c55e' : '#e2e8f0'};">
                            <span style="display:inline-flex;align-items:center;justify-content:center;
                                         width:30px;height:30px;border-radius:50%;margin-right:10px;
                                         font-weight:700;font-size:13px;flex-shrink:0;
                                         background:${correct ? '#22c55e' : '#94a3b8'};color:white;">
                                ${letter}
                            </span>
                            <span style="font-size:14px;color:#374151;flex:1;">${escapeHtml(ans.noi_dung)}</span>
                            ${correct ? '<span style="margin-left:auto;color:#22c55e;font-size:12px;font-weight:700;">✓ Đúng</span>' : ''}
                        </div>`;
                    }).join('')}
                </div>

                ${q.giai_thich ? `
                <div style="margin-left:71px;padding:10px 14px;background:#fef9c3;
                             border-left:3px solid #f59e0b;border-radius:0 6px 6px 0;margin-bottom:8px;">
                    <small style="color:#92400e;font-size:13px;">
                        <strong>Giải thích:</strong> ${escapeHtml(q.giai_thich)}
                    </small>
                </div>` : ''}

                ${q.goi_y ? `
                <div style="margin-left:71px;padding:8px 14px;background:#f0fdf4;
                             border-left:3px solid #22c55e;border-radius:0 6px 6px 0;">
                    <small style="color:#166534;font-size:13px;">
                        <strong>Gợi ý:</strong> ${escapeHtml(q.goi_y)}
                    </small>
                </div>` : ''}
            </div>`;
        });

        document.getElementById('ai_questions_list').innerHTML = html;
        updateSelectedCount();

        document.getElementById('ai-preview-area').style.display = 'block';
        document.getElementById('ai-preview-area').scrollIntoView({ behavior: 'smooth', block: 'start' });

        setTimeout(() => {
            if (window.MathJax && window.MathJax.typesetPromise) {
                MathJax.typesetPromise().catch((err) => console.log('MathJax error:', err));
            }
        }, 100);
    }

    /* ---------- Checkbox: chọn tất cả ---------- */
    document.getElementById('ai_select_all').addEventListener('change', function () {
        document.querySelectorAll('.ai-q-checkbox').forEach(cb => {
            cb.checked = this.checked;
            styleCard(cb);
        });
        updateSelectedCount();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('ai-q-checkbox')) {
            styleCard(e.target);
            updateSelectedCount();
            syncSelectAll();
        }
    });

    function styleCard(cb) {
        const card = cb.closest('[data-ai-index]');
        if (!card) return;
        card.style.background     = cb.checked ? '#faf5ff' : '#f9fafb';
        card.style.borderColor    = cb.checked ? '#c4b5fd' : '#e2e8f0';
        card.style.opacity        = cb.checked ? '1' : '0.55';
    }

    // syncSelectAll function
    function syncSelectAll() {
        const total   = document.querySelectorAll('.ai-q-checkbox').length;
        const checked = document.querySelectorAll('.ai-q-checkbox:checked').length;
        document.getElementById('ai_select_all').checked = total > 0 && total === checked;
    }

    function updateSelectedCount() {
        const n = document.querySelectorAll('.ai-q-checkbox:checked').length;
        document.getElementById('ai_selected_count').textContent = `${n} câu được chọn`;
    }

    /* ---------- Nút: Lưu câu hỏi ---------- */
    document.getElementById('btnSaveAI').addEventListener('click', async function () {
        const selectedIndexes = [];
        document.querySelectorAll('.ai-q-checkbox:checked').forEach(cb => {
            selectedIndexes.push(parseInt(cb.dataset.index));
        });

        if (selectedIndexes.length === 0) {
            Swal.fire('Chưa chọn câu', 'Vui lòng tick ít nhất 1 câu hỏi để lưu.', 'warning');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Xác nhận lưu?',
            html: `Bạn sẽ lưu <strong>${selectedIndexes.length}</strong> câu hỏi vào hệ thống.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Có, lưu ngay',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#22c55e',
        });

        if (!confirm.isConfirmed) return;

        Swal.fire({
            title: 'Đang lưu...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        const selectedQuestions = selectedIndexes.map(i => aiQuestions[i]);

        try {
            const res = await fetch('{{ route("admin.cauhoi.saveAI") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    chuong_hoc_id : document.getElementById('ai_chuong_hoc_id').value,
                    muc_do        : document.getElementById('ai_muc_do').value,
                    questions     : selectedQuestions,
                }),
            });

            const data = await res.json();
            Swal.close();

            if (data.success) {
                await Swal.fire({
                    title: 'Thành công! 🎉',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#4f46e5',
                });
                if (data.redirect) window.location.href = data.redirect;
                else window.location.href = '{{ route("admin.cauhoi.index") }}';
            } else {
                Swal.fire('Lỗi', data.message || 'Có lỗi xảy ra khi lưu.', 'error');
            }
        } catch (err) {
            Swal.close();
            Swal.fire('Lỗi', 'Không thể lưu: ' + err.message, 'error');
        }
    });

    /* ---------- Nút: Hủy / Làm lại ---------- */
    document.getElementById('btnCancelAI').addEventListener('click', function () {
        document.getElementById('ai-preview-area').style.display = 'none';
        document.getElementById('ai_questions_list').innerHTML = '';
        aiQuestions = [];
        document.getElementById('ai_select_all').checked = true;
        
        // Reset file input & dropzone
        fileInput.value = '';
        handleFileSelect();
        
        document.getElementById('ai-config-area').scrollIntoView({ behavior: 'smooth' });
    });

    /* ---------- Utility ---------- */
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
})();
</script>
