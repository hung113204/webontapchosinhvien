@extends('Client.layouts.app')
@section('title', 'Đang làm bài: ' . $monHoc->ten_mon_hoc)

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css" />
<style>
    body { background: #f0f7ff; }
    .pq-opt-text p { margin: 0 !important; display: inline-block; }
    .question-wrapper { display: none; animation: fadeIn 0.3s ease; }
    .question-wrapper.active { display: block; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .practice-sidebar {
        position: sticky !important;
        top: 80px !important;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    .practice-sidebar::-webkit-scrollbar { width: 4px; }
    .practice-sidebar::-webkit-scrollbar-track { background: transparent; }
    .practice-sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')

    <section class="practice-section" style="padding: 10px 0 60px;">
        <div class="container">

            <form id="practice-form" action="{{ route('client.practice.submit') }}" method="POST" autocomplete="off">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session_id }}">
                <input type="hidden" name="elapsed_seconds" id="elapsed_seconds" value="0">

                {{-- ══ THANH ĐIỀU HƯỚNG TRÊN CÙNG ══ --}}
                <div class="practice-topbar mb-4" style="margin-bottom: 24px;">
                    <div class="practice-topbar-info">
                        <h2>{{ $monHoc->ten_mon_hoc }}</h2>
                        <p>
                            @if($cheDo == 'learn')
                                <i class="fas fa-book-reader" style="color: var(--blue);"></i> Chế độ: Vừa học vừa làm
                            @else
                                <i class="fas fa-stopwatch" style="color: #ef4444;"></i> Chế độ: Làm như thi thật
                            @endif
                        </p>
                    </div>
                    <div class="practice-topbar-stats">
                        <div class="topbar-stat">
                            <span class="topbar-stat-label">Thời gian làm bài</span>
                            <span class="topbar-stat-value" id="timer-display" style="color: var(--blue); font-family: monospace; font-size: 1.2rem;">00:00</span>
                        </div>
                        <button type="button" class="btn-submit-top" onclick="confirmSubmit()">
                            <i class="fas fa-paper-plane"></i> Nộp bài ngay
                        </button>
                    </div>
                </div>

                {{-- ══ KHU VỰC CHÍNH ══ --}}
                <div class="practice-layout">

                    {{-- ── SIDEBAR ── --}}
                    <aside class="practice-sidebar" style="top: 20px;">
                        <div class="pq-mini-grid-card">
                            <div class="pq-mini-header">
                                <div class="pq-mini-title">
                                    <i class="fas fa-th-large"></i> Danh sách câu hỏi
                                </div>
                                <div class="pq-mini-count"><span id="answered-count">0</span>/{{ count($cauHois) }}</div>
                            </div>
                            <div class="pq-mini-grid" id="question-nav-grid">
                                @foreach($cauHois as $index => $cau)
                                    <div class="pq-mini-btn {{ $index == 0 ? 'current' : '' }}"
                                         id="nav-btn-{{ $index }}"
                                         onclick="goToQuestion({{ $index }})">
                                        {{ $index + 1 }}
                                    </div>
                                @endforeach
                            </div>
                            <div class="pq-mini-legend">
                                <div class="pq-legend-item"><div class="pq-legend-dot current"></div> Hiện tại</div>
                                <div class="pq-legend-item"><div class="pq-legend-dot answered"></div> Đã chọn</div>
                                <div class="pq-legend-item"><div class="pq-legend-dot marked"></div> Đánh dấu</div>
                                <div class="pq-legend-item"><div class="pq-legend-dot empty"></div> Chưa làm</div>
                            </div>
                        </div>
                    </aside>

                    {{-- ── KHUNG CÂU HỎI ── --}}
                    <main class="practice-main">

                        @foreach($cauHois as $index => $cau)
                        <div class="question-wrapper {{ $index == 0 ? 'active' : '' }}" id="question-wrapper-{{ $index }}">
                            <div class="practice-question-card">

                                <div class="pq-card-header">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span class="pq-number">Câu {{ $index + 1 }}</span>
                                        @if($cau->muc_do == 1) <span class="pq-type">Dễ</span>
                                        @elseif($cau->muc_do == 2) <span class="pq-type" style="background:#fefce8; color:#ca8a04;">Trung bình</span>
                                        @else <span class="pq-type" style="background:#fef2f2; color:#dc2626;">Khó</span>
                                        @endif
                                    </div>
                                    {{-- <div class="pq-points">ID: #{{ $cau->id }}</div> --}}
                                </div>

                                <div class="pq-text ck-content">{!! $cau->noi_dung !!}</div>

                                <div class="pq-options">
                                    @if($cau->loai_cau_hoi == 4)
                                        <textarea name="answers[{{ $cau->id }}]" 
                                                  class="form-control" 
                                                  rows="6" 
                                                  autocomplete="off"
                                                  placeholder="Nhập câu trả lời tự luận của bạn vào đây..."
                                                  style="width: 100%; padding: 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: inherit; font-size: 15px; resize: vertical;"
                                                  oninput="handleTextAnswer({{ $index }}, this)"
                                        ></textarea>
                                    @elseif($cau->loai_cau_hoi == 3)
                                        {{-- Câu điền khuyết: Tự động nhận diện ..., [...], ___ --}}
                                        <div class="fill-blank-area" style="padding: 15px; background: #f8fafc; border-radius: 12px; border: 1.5px solid #e2e8f0;">
                                            <div class="fill-blank-content" style="line-height: 2.5; font-size: 16px; color: #1e293b;">
                                                @php
                                                    $content = $cau->noi_dung;
                                                    $count = 0;
                                                    // Tìm tất cả các dấu hiệu ô trống phổ biến: [...], ..., .., ___, gạch dưới, hoặc dấu chấm lửng
                                                    $renderedContent = preg_replace_callback('/\[\.\.\.\]|\.{2,}|…|_{2,}/', function($matches) use ($cau, &$count, $index) {
                                                        $count++;
                                                        return '<input type="text" name="answers['.$cau->id.'][]" 
                                                            class="blank-input" 
                                                            autocomplete="off"
                                                            placeholder="('.$count.')"
                                                            style="display: inline-block; width: 140px; border: none; border-bottom: 2px solid #cbd5e1; background: transparent; padding: 2px 8px; margin: 0 5px; outline: none; transition: all 0.2s; font-weight: 600; color: #4f46e5; text-align: center;"
                                                            onfocus="this.style.borderBottomColor=\'#4f46e5\'; this.style.background=\'#eff6ff\'"
                                                            onblur="if(!this.value) { this.style.borderBottomColor=\'#cbd5e1\'; this.style.background=\'transparent\' }"
                                                            oninput="handleTextAnswer('.$index.', this)">';
                                                    }, $content);
                                                @endphp
                                                {!! $renderedContent !!}
                                                
                                                @if($count == 0)
                                                    <div style="margin-top: 15px;">
                                                        <input type="text" name="answers[{{ $cau->id }}][]" 
                                                            class="form-control" 
                                                            autocomplete="off"
                                                            placeholder="Nhập đáp án của bạn..."
                                                            style="width: 100%; padding: 12px; border-radius: 8px; border: 1.5px solid #cbd5e1;"
                                                            oninput="handleTextAnswer({{ $index }}, this)">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        @foreach($cau->dapAns as $ans)
                                            <label class="pq-option" id="label-ans-{{ $ans->id }}">
                                                <input type="radio"
                                                       name="answers[{{ $cau->id }}]"
                                                       value="{{ $ans->id }}"
                                                       data-index="{{ $index }}"
                                                       @if($cheDo == 'learn') data-correct="{{ $ans->is_dung ? 'true' : 'false' }}" @endif
                                                       onchange="handleSelectOption({{ $index }}, {{ $ans->id }})">
                                                <span class="pq-opt-letter">{{ ['A','B','C','D','E','F'][$loop->index] ?? '' }}</span>
                                                <span class="pq-opt-text ck-content">
                                                    {!! $ans->noi_dung !!}
                                                    @if($ans->hinh_anh)
                                                        <div style="margin-top: 10px;">
                                                            <img src="{{ asset('storage/' . $ans->hinh_anh) }}" style="max-height: 150px; border-radius: 6px; border: 1px solid #e2e8f0; display: block; max-width: 100%;">
                                                        </div>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- Chế độ Học: nút kiểm tra + giải thích --}}
                                @if($cheDo == 'learn')
                                    <div style="margin-top: 20px; border-top: 1px dashed #e2eaf6; padding-top: 20px;">
                                        <button type="button" class="btn-quiz" id="btn-check-{{ $index }}"
                                                onclick="checkAnswerLearnMode({{ $index }}, {{ $cau->id }})">
                                            <i class="fas fa-check-double"></i> Kiểm tra đáp án ngay
                                        </button>
                                                                                <div id="explanation-box-{{ $index }}" style="display: none; margin-top: 15px;">
                                            <div class="alert" id="alert-msg-{{ $index }}" style="margin-bottom: 10px;"></div>
                                            
                                            @if($cau->loai_cau_hoi == 3 || $cau->loai_cau_hoi == 4)
                                                <div class="alert alert-success ck-content" style="display: flex; flex-direction: column; gap: 5px; margin-bottom: 10px; background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                                                    <strong style="color: #15803d;"><i class="fas fa-check"></i> Đáp án tham khảo:</strong>
                                                    <ul style="margin: 0; padding-left: 20px;">
                                                    @foreach($cau->dapAns as $ans)
                                                        @if($ans->is_dung)
                                                            <li>{!! $ans->noi_dung !!}</li>
                                                        @endif
                                                    @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            @if($cau->giai_thich)
                                                <div class="alert alert-info ck-content" style="display: flex; flex-direction: column; gap: 5px;">
                                                    <strong style="color: var(--blue);"><i class="fas fa-lightbulb"></i> Giải thích chi tiết:</strong>
                                                    <div>{!! $cau->giai_thich !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                        @endforeach

                        {{-- ── THANH ĐIỀU HƯỚNG PREV / NEXT ── --}}
                        <div class="practice-nav-controls" style="margin-top: 10px;">
                            <button type="button" class="btn-pnav" id="btn-prev" onclick="goPrev()" disabled>
                                <i class="fas fa-arrow-left"></i> Câu trước
                            </button>
                            <div class="pnav-middle">
                                <button type="button" class="btn-pmark" id="btn-mark" onclick="toggleMark()">
                                    <i class="far fa-flag"></i> Đánh dấu
                                </button>
                                <button type="button" class="btn-pclear" onclick="clearAnswer()">
                                    <i class="fas fa-eraser"></i> Bỏ chọn
                                </button>
                            </div>
                            <button type="button" class="btn-pnext" id="btn-next" onclick="goNext()">
                                Câu tiếp theo <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                    </main>
                </div>
            </form>
        </div>
    </section>

@endsection

{{-- ✅ @push('scripts') phải nằm NGOÀI @section --}}
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const totalQuestions = {{ count($cauHois) }};
    let currentIndex     = 0;
    let answeredQuestions = new Set();
    let markedQuestions   = new Set();
    let secondsElapsed    = 0;
    let timerInterval;

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof hljs !== 'undefined') hljs.highlightAll();
        if (window.MathJax) MathJax.typesetPromise();
        startTimer();
        updateUI();
    });

    // ── ĐỒNG HỒ ──
    function startTimer() {
        const display = document.getElementById('timer-display');
        timerInterval = setInterval(() => {
            secondsElapsed++;
            const m = Math.floor(secondsElapsed / 60).toString().padStart(2, '0');
            const s = (secondsElapsed % 60).toString().padStart(2, '0');
            display.innerText = `${m}:${s}`;
        }, 1000);
    }

    // ── ĐIỀU HƯỚNG CÂU HỎI ──
    function goToQuestion(index) {
        document.getElementById(`question-wrapper-${currentIndex}`).classList.remove('active');
        currentIndex = index;
        document.getElementById(`question-wrapper-${currentIndex}`).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        updateUI();
    }

    function goNext() {
        if (currentIndex < totalQuestions - 1) {
            goToQuestion(currentIndex + 1);
        } else {
            confirmSubmit();
        }
    }

    function goPrev() {
        if (currentIndex > 0) goToQuestion(currentIndex - 1);
    }

    // ── CHỌN ĐÁP ÁN ──
    function handleSelectOption(qIndex, ansId) {
        const options = document.querySelectorAll(`#question-wrapper-${qIndex} .pq-option`);
        options.forEach(opt => opt.classList.remove('selected'));
        document.getElementById(`label-ans-${ansId}`).classList.add('selected');
        answeredQuestions.add(qIndex);
        updateGridColors();
    }

    function handleTextAnswer(qIndex, element) {
        if (element.value.trim().length > 0) {
            answeredQuestions.add(qIndex);
        } else {
            answeredQuestions.delete(qIndex);
        }
        updateGridColors();
    }

    // ── BỎ CHỌN ──
        function clearAnswer() {
        const radios = document.querySelectorAll('#question-wrapper-' + currentIndex + ' input[type="radio"]');
        radios.forEach(r => r.checked = false);
        const textInputs = document.querySelectorAll('#question-wrapper-' + currentIndex + ' input[type="text"], #question-wrapper-' + currentIndex + ' textarea');
        textInputs.forEach(t => t.value = '');
        const options = document.querySelectorAll('#question-wrapper-' + currentIndex + ' .pq-option');
        options.forEach(opt => opt.classList.remove('selected', 'correct', 'wrong'));

        const btnCheck = document.getElementById('btn-check-' + currentIndex);
        if (btnCheck) {
            btnCheck.disabled = false;
            btnCheck.style.opacity = '1';
            btnCheck.innerHTML = '<i class="fas fa-check-double"></i> Kiểm tra đáp án ngay';
            radios.forEach(r => r.disabled = false);
            textInputs.forEach(t => t.readOnly = false);
            document.getElementById('explanation-box-' + currentIndex).style.display = 'none';
        }

        answeredQuestions.delete(currentIndex);
        updateGridColors();
    }

    // ── ĐÁNH DẤU ──
    function toggleMark() {
        if (markedQuestions.has(currentIndex)) {
            markedQuestions.delete(currentIndex);
        } else {
            markedQuestions.add(currentIndex);
        }
        updateUI();
    }

    // ── CẬP NHẬT UI ──
    function updateUI() {
        document.getElementById('btn-prev').disabled = (currentIndex === 0);

        const btnNext = document.getElementById('btn-next');
        if (currentIndex === totalQuestions - 1) {
            btnNext.innerHTML = '<i class="fas fa-paper-plane"></i> Nộp bài';
            btnNext.style.background = '#ef4444';
        } else {
            btnNext.innerHTML = 'Câu tiếp theo <i class="fas fa-arrow-right"></i>';
            btnNext.style.background = '';
        }

        const btnMark = document.getElementById('btn-mark');
        if (markedQuestions.has(currentIndex)) {
            btnMark.classList.add('marked');
            btnMark.innerHTML = '<i class="fas fa-flag"></i> Đã đánh dấu';
        } else {
            btnMark.classList.remove('marked');
            btnMark.innerHTML = '<i class="far fa-flag"></i> Đánh dấu';
        }

        updateGridColors();
    }

    function updateGridColors() {
        document.querySelectorAll('.pq-mini-btn').forEach(btn => btn.className = 'pq-mini-btn');
        for (let i = 0; i < totalQuestions; i++) {
            const btn = document.getElementById(`nav-btn-${i}`);
            if (i === currentIndex)        btn.classList.add('current');
            else if (markedQuestions.has(i))  btn.classList.add('marked');
            else if (answeredQuestions.has(i)) btn.classList.add('answered');
        }
        document.getElementById('answered-count').innerText = answeredQuestions.size;
    }

    // ── CHẾ ĐỘ HỌC: KIỂM TRA ĐÁP ÁN ──
        function checkAnswerLearnMode(qIndex, qId) {
        const radios = document.querySelectorAll('input[type="radio"][name="answers[' + qId + ']"]');
        const textInputs = document.querySelectorAll('input[type="text"][name^="answers[' + qId + ']"], textarea[name="answers[' + qId + ']"]');
        
        let hasAnswered = false;

        if (radios.length > 0) {
            let selectedRadio = null;
            let correctRadio  = null;

            radios.forEach(r => {
                if (r.checked) {
                    selectedRadio = r;
                    hasAnswered = true;
                }
                if (r.getAttribute('data-correct') === 'true') correctRadio = r;
            });

            if (!hasAnswered) {
                Swal.fire({ icon: 'warning', title: 'Khoan đã', text: 'Vui lòng chọn 1 đáp án trước khi kiểm tra!', confirmButtonColor: '#3b82f6' });
                return;
            }

            const options = document.querySelectorAll('#question-wrapper-' + qIndex + ' .pq-option');
            options.forEach(opt => opt.classList.remove('correct', 'wrong', 'selected'));

            const alertBox = document.getElementById('alert-msg-' + qIndex);

            if (selectedRadio.value === correctRadio.value) {
                document.getElementById('label-ans-' + selectedRadio.value).classList.add('correct');
                alertBox.className = 'alert alert-success';
                alertBox.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Chính xác!</strong> Bạn đã chọn đúng.';
            } else {
                document.getElementById('label-ans-' + selectedRadio.value).classList.add('wrong');
                document.getElementById('label-ans-' + correctRadio.value).classList.add('correct');
                alertBox.className = 'alert alert-error';
                alertBox.innerHTML = '<i class="fas fa-times-circle"></i> <strong>Sai rồi!</strong> Đáp án đúng đã được bôi xanh bên trên.';
            }
            radios.forEach(r => r.disabled = true);
        } else if (textInputs.length > 0) {
            textInputs.forEach(input => {
                if (input.value.trim().length > 0) hasAnswered = true;
            });

            if (!hasAnswered) {
                Swal.fire({ icon: 'warning', title: 'Khoan đã', text: 'Vui lòng nhập câu trả lời trước khi xem đáp án!', confirmButtonColor: '#3b82f6' });
                return;
            }

            const alertBox = document.getElementById('alert-msg-' + qIndex);
            alertBox.className = 'alert alert-info';
            alertBox.style.backgroundColor = '#eff6ff';
            alertBox.style.borderColor = '#bfdbfe';
            alertBox.style.color = '#1d4ed8';
            alertBox.innerHTML = '<i class="fas fa-info-circle"></i> <strong>Đã ghi nhận!</strong> Hãy đối chiếu câu trả lời của bạn với đáp án tham khảo bên dưới.';
            textInputs.forEach(input => input.readOnly = true);
        }

        document.getElementById('explanation-box-' + qIndex).style.display = 'block';
        const btnCheck = document.getElementById('btn-check-' + qIndex);
        btnCheck.disabled = true;
        btnCheck.style.opacity = '0.5';
        btnCheck.innerHTML = '<i class="fas fa-check-double"></i> Đã kiểm tra';
    }

    // ── NỘP BÀI ──
    function confirmSubmit() {
        let msg = 'Bạn đã hoàn thành xong bài luyện tập này?';
        if (answeredQuestions.size < totalQuestions) {
            msg = `Bạn mới làm được ${answeredQuestions.size}/${totalQuestions} câu. Bạn có chắc chắn muốn nộp bài sớm không?`;
        }

        Swal.fire({
            title: 'Xác nhận nộp bài',
            text: msg,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Nộp bài ngay',
            cancelButtonText: 'Tiếp tục làm'
        }).then((result) => {
            if (result.isConfirmed) {
                clearInterval(timerInterval);
                // Ghi lại thời gian thực tế người dùng làm bài
                document.getElementById('elapsed_seconds').value = secondsElapsed;
                // Mở khóa radio bị disabled (chế độ learn) để backend nhận đủ data
                document.querySelectorAll('input[type="radio"]').forEach(r => r.disabled = false);
                document.getElementById('practice-form').submit();
            }
        });
    }
</script>
@endpush
