@extends('Admin.layouts.admin')
@section('title', 'Soạn đề thủ công')

@section('header_action')
    <div class="header-right">
        <a href="{{ route('admin.baikiemtra.create') }}" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Quay lại
        </a>
        <button type="button" class="btn btn-primary" onclick="saveExam()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                <polyline points="17 21 17 13 7 13 7 21" />
                <polyline points="7 3 8 8 15 8" />
            </svg>
            Lưu đề thi
        </button>
    </div>
@endsection

@section('scripts_before')
    <script>
        // Vô hiệu hóa các script conflict để tránh lỗi JS khi render
        window.disableExamJS = true;
        window.initCheckboxes = function() {};
        window.initDragDrop = function() {};
        window.initActionButtons = function() {};
        window.updateStats = function() {};
        window.saveExam = function() {};
    </script>
@endsection

@section('content')
    <style>
        .paper-q-text pre, .q-content pre, .paper-answers pre, .q-select-item pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 12px 16px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 13.5px;
            line-height: 1.5;
            margin: 12px 0;
            white-space: pre-wrap;
        }
        .paper-q-text code, .q-content code, .paper-answers code, .q-select-item code {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 13.5px;
        }
        .paper-q-text pre code, .q-content pre code, .paper-answers pre code, .q-select-item pre code {
            background: transparent;
            color: inherit;
            padding: 0;
        }
    </style>
    <script>
      window.MathJax = {
        tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
        svg: { fontCache: 'global' }
      };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <form action="{{ route('admin.baikiemtra.save') }}" method="POST" id="exam-form">
        @csrf
        {{-- Các hidden input giữ nguyên để đảm bảo logic lưu trữ --}}
        <input type="hidden" name="id" value="{{ $id }}"> <input type="hidden" name="tu_dong_lay_de"
            value="0">
        <input type="hidden" name="ten_bai" id="hidden_ten_bai" value="">
        <input type="hidden" name="mon_hoc_id" id="hidden_mon_hoc_id" value="">
        <input type="hidden" name="chuong_hoc_ids" id="hidden_chuong_hoc_ids" value="">
        <input type="hidden" name="mo_ta" id="hidden_mo_ta" value="">
        <input type="hidden" name="nguoi_tao_id" id="hidden_nguoi_tao_id" value="{{ Auth::id() }}">
        <input type="hidden" name="thoi_gian_phut" id="hidden_thoi_gian_phut" value="">
        <input type="hidden" name="thoi_gian_bat_dau" id="hidden_thoi_gian_bat_dau" value="">
        <input type="hidden" name="thoi_gian_ket_thuc" id="hidden_thoi_gian_ket_thuc" value="">
        <input type="hidden" name="so_lan_lam_bai" id="hidden_so_lan_lam_bai" value="1">
        <input type="hidden" name="cach_tinh_diem" id="hidden_cach_tinh_diem" value="1">
        <input type="hidden" name="trang_thai" id="hidden_trang_thai" value="0">
        <input type="hidden" name="xem_diem" id="hidden_xem_diem" value="0">
        <input type="hidden" name="xem_bai_lam" id="hidden_xem_bai_lam" value="0">
        <input type="hidden" name="dao_cau_hoi" id="hidden_dao_cau_hoi" value="0">
        <input type="hidden" name="dao_dap_an" id="hidden_dao_dap_an" value="0">
        <input type="hidden" name="nop_khi_chuyen_tab" id="hidden_nop_khi_chuyen_tab" value="0">
        <input type="hidden" name="tu_dong_lay_de" value="0">
        <input type="hidden" name="cau_hoi_ids" id="cau_hoi_ids" value="">

        {{-- BỐ CỤC CHÍNH SỬ DỤNG CLASS TRONG MANUAL-EXAM.CSS --}}
        <div class="split-container">

            <section class="selection-panel">
                <div class="panel-header">
                    <h3>Chọn câu hỏi</h3>
                    <div class="panel-filters">
                        <select class="form-select" id="filter-chuong">
                            <option value="">Tất cả chương</option>
                        </select>
                        <select class="form-select" id="filter-muc-do">
                            <option value="">Tất cả mức độ</option>
                            <option value="1">Nhận biết</option>
                            <option value="2">Thông hiểu</option>
                            <option value="3">Vận dụng</option>
                        </select>
                    </div>
                </div>

                <div class="panel-search">
                    <div class="search-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <input type="text" id="search-question" placeholder="Tìm kiếm câu hỏi...">
                    </div>
                </div>

                <div class="question-selection-list">
                    {{-- Ví dụ cấu trúc mà JS sẽ render ra (Dùng class .q-select-item và .q-select-label của bạn) --}}
                    {{-- <div class="q-select-item">
                        <input type="checkbox" class="q-checkbox" id="q1">
                        <label class="q-select-label" for="q1">
                            <div class="q-content">
                                <p>Nội dung câu hỏi...</p>
                                <span class="badge badge-easy">Nhận biết</span>
                            </div>
                        </label>
                    </div> --}}
                </div>
            </section>

            <section class="preview-panel">
                <div class="preview-header">
                    <div class="preview-title">
                        <h3>Xem trước đề thi</h3>
                        <input type="text" class="exam-title-input" id="exam-title-display" readonly>
                    </div>

                    <div class="exam-stats">
                        <div class="stat-item">
                            <span class="stat-label">TỔNG CÂU:</span>
                            <span class="stat-value" id="total-questions">0</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <span class="stat-label">NHẬN BIẾT:</span>
                            <span class="stat-value stat-easy" id="easy-count">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">THÔNG HIỂU:</span>
                            <span class="stat-value stat-medium" id="medium-count">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">VẬN DỤNG:</span>
                            <span class="stat-value stat-hard" id="hard-count">0</span>
                        </div>
                    </div>
                </div>

                <div class="exam-paper">
                    <div class="paper-header">
                        <h1 class="paper-exam-title" id="paper-title">Tên đề thi</h1>
                        <div class="paper-meta">
                            <span>Môn: <strong id="paper-subject">Môn học</strong></span>
                            <span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                <span id="paper-time">Thời gian: -- phút</span>
                            </span>
                        </div>
                    </div>

                    {{-- NƠI RENDER CÁC CÂU HỎI ĐÃ CHỌN --}}
                    <div class="paper-questions" id="paper-questions-container">
                        {{-- Cấu trúc sẽ dùng class .paper-question-item và .paper-q-number của bạn --}}
                    </div>

                    {{-- TRẠNG THÁI TRỐNG SỬ DỤNG CLASS TRONG MANUAL-EXAM.CSS --}}
                    <div class="paper-empty-state" id="paper-empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M9 11H3v2h6m-6 4h6m13 0v-2c0-1.11-.89-2-2-2h-2m-4.83-5.83l2.83-2.83-1.41-1.41-2.83 2.83" />
                        </svg>
                        <p>Chưa có câu hỏi nào được chọn</p>
                        <span>Vui lòng chọn câu hỏi từ danh sách bên trái</span>
                    </div>
                </div>
            </section>
        </div>
    </form>




    @push('scripts')
        <script>
            $(document).ready(function() {
                window.formData = {};
                window.selectedQuestions = [];
                window.allQuestions = [];

                function normalizeSelectedQuestions() {
                    const uniqueQuestions = [];
                    const seenIds = new Set();

                    (window.selectedQuestions || []).forEach(function(question) {
                        if (!question || !question.id || seenIds.has(question.id)) {
                            return;
                        }

                        seenIds.add(question.id);
                        uniqueQuestions.push(question);
                    });

                    window.selectedQuestions = uniqueQuestions;
                }

                function showQuestionListLoading() {
                    $('.question-selection-list').html(
                        '<div style="text-align:center; padding:40px"><div class="spinner-border"></div><p>Đang tải...</p></div>'
                    );
                }

                function initExamData() {
                    const examId = {{ $id ?? 'null' }};
                    if (!examId || examId === null) {
                        $('.question-selection-list').html(
                            '<div style="text-align:center; padding:40px; color:#ef4444"><p>❌ Không tìm thấy ID bài kiểm tra</p></div>'
                        );
                        return;
                    }

                    showQuestionListLoading();

                    $.ajax({
                        url: "{{ route('admin.baikiemtra.edit', ':id') }}".replace(':id', examId),
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                window.formData = response.data;

                                // 1. Nạp thông tin cơ bản
                                $('#exam-title-display').val(window.formData.ten_bai);
                                $('#paper-title').text(window.formData.ten_bai);
                                $('#paper-subject').text(window.formData.mon_hoc?.ten_mon_hoc || 'N/A');
                                $('#paper-time').text('Thời gian: ' + window.formData.thoi_gian_phut +
                                    ' phút');

                                // 2. Nạp dữ liệu vào các hidden inputs 
                                Object.keys(window.formData).forEach(key => {
                                    const input = $(`#hidden_${key}`);
                                    if (!input.length) return;

                                    const value = typeof window.formData[key] === 'boolean' ?
                                        (window.formData[key] ? 1 : 0) :
                                        window.formData[key];

                                    input.val(value);
                                });

                                // 3. QUAN TRỌNG: Nạp câu hỏi cũ vào danh sách đã chọn để HIỂN THỊ NGAY
                                if (window.formData.cau_hois && window.formData.cau_hois.length > 0) {
                                    window.selectedQuestions = window.formData.cau_hois;
                                    normalizeSelectedQuestions();
                                    updatePreview(); // Vẽ lại tờ giấy thi bên phải
                                    updateStats(); // Cập nhật số lượng câu Nhận biết/Thông hiểu/Vận dụng
                                    updateHiddenInput(); // Cập nhật ID câu hỏi vào input để submit
                                }

                                // 4. Kiểm tra chương học và load ngân hàng bên trái
                                let chuongIds = window.formData.chuong_hoc_ids;
                                // Nếu chuongIds là mảng (do Laravel Cast), biến nó thành chuỗi để gửi API
                                if (Array.isArray(chuongIds)) {
                                    chuongIds = chuongIds.join(',');
                                }

                                if (!chuongIds || chuongIds === '' || chuongIds === 'null') {
                                    $('.question-selection-list').html(
                                        '<div style="text-align:center; padding:40px; color:#ef4444"><p>❌ Lỗi: Bài thi chưa có chương học</p></div>'
                                    );
                                    return;
                                }

                                loadQuestionsByChapters(chuongIds);
                            }
                        },
                        error: function(xhr) {
                            $('.question-selection-list').html(
                                '<div style="text-align:center; padding:40px; color:#ef4444"><p>❌ Lỗi kết nối dữ liệu</p></div>'
                            );
                        }
                    });
                }

                function loadQuestionsByChapters(chuongIds) {
                    console.log('📥 loadQuestionsByChapters được gọi với:', chuongIds);

                    if (!chuongIds) {
                        console.error('❌ chuongIds rỗng!');
                        $('.question-selection-list').html(
                            '<div style="text-align:center; padding:40px; color:#f59e0b"><p>⚠️ Chưa có chương học nào được chọn</p></div>'
                        );
                        return;
                    }

                    showQuestionListLoading();
                    console.log('🌐 Gọi API lấy câu hỏi...');

                    $.ajax({
                        url: "{{ route('admin.baikiemtra.api.cau-hoi-by-chuong-hocs') }}",
                        method: 'GET',
                        data: {
                            chuong_hoc_ids: chuongIds
                        },
                        success: function(response) {
                            console.log('✅ API trả về:', response);

                            if (response.success) {
                                window.allQuestions = response.data || [];
                                console.log('📚 Số câu hỏi nhận được:', window.allQuestions.length);

                                if (window.allQuestions.length === 0) {
                                    $('.question-selection-list').html(
                                        '<div style="text-align:center; padding:40px; color:#f59e0b"><p>⚠️ Không tìm thấy câu hỏi nào trong các chương đã chọn</p></div>'
                                    );
                                    return;
                                }

                                renderQuestions(window.allQuestions);
                                loadChaptersToFilter(window.allQuestions);

                                // Nếu đã có câu hỏi (trường hợp quay lại sửa)
                                if (window.formData.cau_hois && window.formData.cau_hois.length > 0) {
                                    window.selectedQuestions = window.formData.cau_hois;
                                    normalizeSelectedQuestions();
                                    updatePreview();
                                    updateHiddenInput();
                                    renderQuestions(window.allQuestions);
                                }
                            } else {
                                console.error('❌ API trả về success=false');
                                $('.question-selection-list').html(
                                    '<div style="text-align:center; padding:40px; color:#ef4444"><p>❌ ' +
                                    (response.message || 'Lỗi tải câu hỏi') + '</p></div>'
                                );
                            }
                        },
                        error: function(xhr) {
                            console.error('❌ Lỗi API:', xhr);
                            $('.question-selection-list').html(
                                '<div style="text-align:center; padding:40px; color:#ef4444"><p>❌ Lỗi kết nối API</p></div>'
                            );
                        }
                    });
                }

                window.renderQuestions = function(questions) {
                    const container = $('.question-selection-list');
                    container.empty();
                    questions.forEach((q, idx) => {
                        const isChecked = window.selectedQuestions.some(sq => sq.id === q.id);
                        const html = `
                    <div class="q-select-item ${isChecked ? 'active' : ''}" data-id="${q.id}" data-chuong="${q.chuong_hoc_id}" data-muc-do="${q.muc_do}" style="border: 1px solid #eee; padding:15px; margin-bottom:10px; border-radius:8px; cursor:pointer; background:${isChecked ? '#f0f7ff' : '#fff'}">
                        <div style="display:flex; gap:10px">
                            <input type="checkbox" ${isChecked ? 'checked' : ''} style="transform: scale(1.2)">
                            <div>
                                <div style="font-weight:600">Câu ${idx+1}: ${q.noi_dung.replace(/<[^>]*>/g, '').substring(0, 100)}...</div>
                                <small style="color:#666">Chương: ${q.chuong_hoc?.ten_chuong || 'N/A'} | Mức độ: ${getMucDoText(q.muc_do)}</small>
                            </div>
                        </div>
                    </div>`;
                        container.append(html);
                    });
                    attachEvents();
                    
                    // Render MathJax for equations
                    if (window.MathJax && window.MathJax.typesetPromise) {
                        window.MathJax.typesetPromise();
                    }
                };

                function attachEvents() {
                    $('.q-select-item').off('click').on('click', function() {
                        const id = $(this).data('id');
                        const checkbox = $(this).find('input');
                        const question = window.allQuestions.find(q => q.id === id);
                        const isSelected = window.selectedQuestions.some(q => q.id === id);

                        if (isSelected) {
                            window.selectedQuestions = window.selectedQuestions.filter(q => q.id !== id);
                            checkbox.prop('checked', false);
                            $(this).removeClass('active').css('background', '#fff');
                        } else if (question) {
                            window.selectedQuestions.push(question);
                            normalizeSelectedQuestions();
                            checkbox.prop('checked', true);
                            $(this).addClass('active').css('background', '#f0f7ff');
                        }
                        updatePreview();
                        updateHiddenInput();
                    });
                }

                // ✅ HÀM CẬP NHẬT PREVIEW - ĐÃ SỬA ĐỂ HIỂN THỊ ĐẦY ĐỦ CÂU HỎI VÀ ĐÁP ÁN
                window.updatePreview = function() {
                    normalizeSelectedQuestions();
                    const container = $('#paper-questions-container');
                    const empty = $('#paper-empty-state');

                    if (window.selectedQuestions.length === 0) {
                        container.hide();
                        empty.show();
                    } else {
                        empty.hide();
                        container.show().empty();

                        window.selectedQuestions.forEach((q, i) => {
                            // Xác định class mức độ dựa trên dữ liệu [cite: 3, 5]
                            let badgeClass = 'badge-easy';
                            let badgeText = 'Nhận biết';
                            if (q.muc_do == 2) {
                                badgeClass = 'badge-medium';
                                badgeText = 'Thông hiểu';
                            } else if (q.muc_do == 3) {
                                badgeClass = 'badge-hard';
                                badgeText = 'Vận dụng';
                            }

                            // Render danh sách đáp án sử dụng class .paper-answers 
                            let answersHtml = '';
                            if (q.dap_ans && Array.isArray(q.dap_ans) && q.dap_ans.length > 0) {
                                answersHtml = '<ul class="paper-answers">';
                                q.dap_ans.forEach((ans, idx) => {
                                    const answerLabel = String.fromCharCode(65 + idx); // A, B, C, D
                                    const isCorrect = ans.is_dung == 1 || ans.is_dung === true;
                                    // Nếu đáp án đúng, thêm style inline hoặc class để nhận diện
                                    answersHtml += `
                        <li style="${isCorrect ? 'border-color: var(--success); background: rgba(16, 185, 129, 0.05);' : ''}">
                            <strong>${answerLabel}.</strong> ${ans.noi_dung}
                            ${isCorrect ? '<span class="correct-indicator" style="color: var(--success); margin-left: 10px;">✓ Đúng</span>' : ''}
                        </li>
                    `;
                                });
                                answersHtml += '</ul>';
                            }

                            // Cấu trúc HTML mới sử dụng 3 cột: Số thứ tự | Nội dung | Hành động 
                            const questionHtml = `
                <div class="paper-question-item">
                    <div class="paper-q-number">${i + 1}</div>
                    
                    <div class="paper-q-content">
                        <div class="paper-q-text">
                            ${q.noi_dung}
                            <span class="badge ${badgeClass}" style="margin-left: 8px;">${badgeText}</span>
                        </div>
                        ${answersHtml}
                    </div>

                    <div class="paper-q-actions">
                        <button type="button" class="q-action-btn q-remove" onclick="removeSelectedQuestion(${q.id})" title="Xóa câu hỏi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
                            container.append(questionHtml);
                        });
                    }
                    updateStats();
                    
                    // Render MathJax for equations
                    if (window.MathJax && window.MathJax.typesetPromise) {
                        window.MathJax.typesetPromise();
                    }
                };

                // Hàm bổ trợ để xóa câu hỏi khi nhấn nút X
                window.removeSelectedQuestion = function(id) {
                    window.selectedQuestions = window.selectedQuestions.filter(q => q.id !== id);
                    normalizeSelectedQuestions();

                    // Cập nhật lại giao diện danh sách bên trái (bỏ tick checkbox)
                    $(`.q-select-item[data-id="${id}"]`).removeClass('active').css('background', '#fff');
                    $(`.q-select-item[data-id="${id}"] input`).prop('checked', false);

                    updatePreview();
                    updateHiddenInput();
                };

                window.updateStats = function() {
                    normalizeSelectedQuestions();
                    $('#total-questions').text(window.selectedQuestions.length);
                    $('#easy-count').text(window.selectedQuestions.filter(q => q.muc_do == 1).length);
                    $('#medium-count').text(window.selectedQuestions.filter(q => q.muc_do == 2).length);
                    $('#hard-count').text(window.selectedQuestions.filter(q => q.muc_do == 3).length);
                };

                window.updateHiddenInput = function() {
                    normalizeSelectedQuestions();
                    $('#cau_hoi_ids').val(window.selectedQuestions.map(q => q.id).join(','));
                };

                function getMucDoText(md) {
                    return md == 1 ? 'Nhận biết' : (md == 2 ? 'Thông hiểu' : 'Vận dụng');
                }

                function loadChaptersToFilter(questions) {
                    const filter = $('#filter-chuong');
                    const uniqueChuongs = [...new Set(questions.map(q => JSON.stringify(q.chuong_hoc)))];
                    filter.empty().append('<option value="">Tất cả chương</option>');
                    uniqueChuongs.forEach(str => {
                        const c = JSON.parse(str);
                        if (c) filter.append(`<option value="${c.id}">${c.ten_chuong}</option>`);
                    });
                }

                window.filterQuestions = function() {
                    const chuong = $('#filter-chuong').val();
                    const mucdo = $('#filter-muc-do').val();
                    const search = $('#search-question').val().toLowerCase();

                    $('.q-select-item').each(function() {
                        const q = $(this);
                        const matchC = !chuong || q.data('chuong') == chuong;
                        const matchM = !mucdo || q.data('muc-do') == mucdo;
                        const matchS = !search || q.text().toLowerCase().includes(search);
                        q.toggle(matchC && matchM && matchS);
                    });
                };

                // ✅ 4. HÀM LƯU ĐỀ THI
                window.saveExam = function() {
                    normalizeSelectedQuestions();
                    if (window.selectedQuestions.length === 0) {
                        alert('⚠️ Vui lòng chọn ít nhất một câu hỏi!');
                        return;
                    }

                    // Đảm bảo chuong_hoc_ids được cập nhật vào hidden input từ formData cũ
                    let chuongIds = window.formData.chuong_hoc_ids;
                    if (Array.isArray(chuongIds)) chuongIds = chuongIds.join(',');
                    $('#hidden_chuong_hoc_ids').val(chuongIds);

                    updateHiddenInput(); // Cập nhật cau_hoi_ids

                    Swal.fire({
                        title: 'Xác nhận lưu',
                        text: "Bạn có chắc chắn muốn lưu đề thi này?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#8b5cf6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy bỏ'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(document.getElementById('exam-form'));
                            $.ajax({
                                url: $('#exam-form').attr('action'),
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(res) {
                                    if (res.success) {
                                        Swal.fire({
                                            title: 'Thành công!',
                                            text: 'Đề thi đã được lưu thành công.',
                                            icon: 'success',
                                            confirmButtonColor: '#10b981'
                                        }).then(() => {
                                            window.location.href = res.redirect;
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMsg = 'Có lỗi xảy ra trong quá trình lưu.';
                                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                                    } else if(xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: errorMsg,
                                        icon: 'error',
                                        confirmButtonColor: '#ef4444'
                                    });
                                }
                            });
                        }
                    });
                };

                // GẮN SỰ KIỆN FILTER
                $('#filter-chuong, #filter-muc-do').on('change', window.filterQuestions);
                $('#search-question').on('keyup', window.filterQuestions);

                // CHẠY KHỞI TẠO
                initExamData();
            });
        </script>
    @endpush
@endsection
