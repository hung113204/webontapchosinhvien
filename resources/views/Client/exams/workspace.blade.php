{{-- resources/views/Client/exams/workspace.blade.php --}}
@extends('Client.layouts.app')

@section('title', 'Thi thử: ' . $baiKiemTra->ten_bai)

@section('content')

    {{-- KHÔNG dùng class exam-interface-wrap vì CSS gốc set display:none --}}
    <div id="exam-workspace">

        <div class="exam-topbar">
            <div class="container">
                <div class="exam-topbar-inner">
                    <div class="exam-topbar-info">
                        <h2><i class="fas fa-brain"></i> {{ $baiKiemTra->ten_bai }}</h2>
                        <p>{{ $baiKiemTra->monHoc->ten_mon_hoc }} &nbsp;&bull;&nbsp; <i class="bi bi-clock"></i> {{ $thoiGianPhut }} phút &nbsp;&bull;&nbsp; {{ $tongSoCau }} câu hỏi</p>
                    </div>

                    <div class="exam-timer-wrap">
                        <div class="timer-circle-wrap">
                            <svg class="timer-svg" width="72" height="72" viewBox="0 0 72 72">
                                <circle class="timer-bg" cx="36" cy="36" r="30"></circle>
                                <circle class="timer-prog" cx="36" cy="36" r="30" id="timer-ring"></circle>
                            </svg>
                            <div class="timer-digits">
                                <span id="timer-display">{{ str_pad($thoiGianPhut, 2, '0', STR_PAD_LEFT) }}:00</span>
                            </div>
                        </div>
                        <div class="timer-label-sm">Thời gian<br>còn lại</div>
                    </div>

                    <button class="btn-submit-top" id="submit-exam-btn" type="button">
                        <i class="fas fa-paper-plane"></i> Nộp bài
                    </button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('client.exams.submit') }}" id="exam-form">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session_id }}">

            <input type="hidden" name="so_lan_vi_pham_tab" id="so_lan_vi_pham_tab" value="0">

            <div id="submit-loading-overlay"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.95); z-index: 9999; flex-direction: column; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
                <div class="spinner-border" style="width: 3.5rem; height: 3.5rem; color: #0d6efd;" role="status"></div>
                <h3 style="margin-top: 20px; font-weight: 800; color: #1e293b;">Đang nộp bài...</h3>
                <p style="color: #64748b; font-size: 1.1rem; text-align: center;">Hệ thống đang chấm điểm <br>và gọi AI phân
                    tích bài làm của bạn 🤖</p>
            </div>

            <div class="container">
                <div class="exam-layout">

                    <aside class="exam-sidebar">
                        <div class="sidebar-block">
                            <div class="sidebar-block-title">
                                <i class="fas fa-list-ol"></i> Danh sách câu hỏi
                            </div>
                            <div class="q-nav-grid">
                                @foreach ($cauHois as $idx => $cauHoi)
                                    <button type="button" class="q-nav-btn {{ $idx === 0 ? 'current' : '' }}"
                                        data-q="{{ $idx + 1 }}" onclick="goToQuestion({{ $idx + 1 }})">
                                        {{ $idx + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="sidebar-block">
                            <div class="sidebar-block-title"><i class="fas fa-info-circle"></i> Thông tin</div>
                            <div class="info-rows">
                                <div class="info-row">
                                    <span class="info-row-label">Câu hiện tại</span>
                                    <span class="info-row-value" id="info-current">1</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Đã trả lời</span>
                                    <span class="info-row-value" id="info-answered">0</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Đánh dấu</span>
                                    <span class="info-row-value" id="info-marked">0</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-row-label">Còn lại</span>
                                    <span class="info-row-value" id="info-remaining">{{ $tongSoCau }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sidebar-block">
                            <div class="sidebar-block-title"><i class="fas fa-flag"></i> Chú thích</div>
                            <div class="legend-items">
                                <div class="legend-item"><span class="legend-dot answered"></span> Đã trả lời</div>
                                <div class="legend-item"><span class="legend-dot current"></span> Hiện tại</div>
                                <div class="legend-item"><span class="legend-dot marked"></span> Đánh dấu</div>
                                <div class="legend-item"><span class="legend-dot unanswered"></span> Chưa trả lời</div>
                            </div>
                        </div>
                    </aside>

                    <main class="exam-main-area">
                        @foreach ($cauHois as $idx => $cauHoi)
                            <div class="question-card" id="question-card-{{ $idx + 1 }}">
                                <input type="hidden" name="marked[{{ $cauHoi->id }}]"
                                    id="input-mark-{{ $idx + 1 }}" value="0">
                                <input type="hidden" name="times[{{ $cauHoi->id }}]"
                                    id="input-time-{{ $idx + 1 }}" value="0">
                                <div class="q-card-header">
                                    <div class="q-meta">
                                        <span class="q-number">Câu {{ $idx + 1 }}</span>
                                        @php
                                            $mucDoLabel = match ($cauHoi->muc_do) {
                                                1 => 'Dễ',
                                                3 => 'Khó',
                                                default => 'Trung bình',
                                            };
                                            $mucDoClass = match ($cauHoi->muc_do) {
                                                1 => 'easy',
                                                3 => 'hard',
                                                default => 'mid',
                                            };
                                        @endphp
                                        <span class="q-level level-{{ $mucDoClass }}">{{ $mucDoLabel }}</span>
                                    </div>
                                    <button type="button" class="btn-mark" id="mark-btn-{{ $idx + 1 }}"
                                        onclick="toggleMark({{ $idx + 1 }})">
                                        <i class="far fa-bookmark"></i> Đánh dấu
                                    </button>
                                </div>

                                <div class="question-text">{!! $cauHoi->noi_dung !!}</div>

                                <div class="options-list" id="options-{{ $idx + 1 }}">
                                    @if($cauHoi->loai_cau_hoi == 4)
                                        {{-- Câu tự luận: hiển thị ô nhập văn bản --}}
                                        <div style="padding: 8px 0;">
                                            <p style="font-size: 13px; color: #64748b; margin-bottom: 10px; font-style: italic;">
                                                <i class="fas fa-pen-nib"></i> Câu hỏi tự luận – hãy nhập câu trả lời của bạn vào bên dưới:
                                            </p>
                                            <textarea name="answers[{{ $cauHoi->id }}]"
                                                rows="6"
                                                placeholder="Nhập câu trả lời của bạn vào đây..."
                                                style="width:100%; padding:14px; border:1.5px solid #cbd5e1; border-radius:10px; font-family:inherit; font-size:15px; resize:vertical; line-height:1.6; transition: border-color 0.2s;"
                                                onfocus="this.style.borderColor='#6366f1'"
                                                onblur="this.style.borderColor='#cbd5e1'"
                                                oninput="onTextAnswerInput({{ $idx + 1 }}, this)"
                                            ></textarea>
                                        </div>
                                    @elseif($cauHoi->loai_cau_hoi == 3)
                                        {{-- Câu điền khuyết: tự động tìm [...], ..., hoặc ___ và thay bằng input --}}
                                        <div class="fill-blank-area" style="padding: 15px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                                            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px; font-style: italic;">
                                                <i class="fas fa-edit"></i> Điền vào chỗ trống:
                                            </p>
                                            <div class="fill-blank-content" style="line-height: 2.5; font-size: 16px;">
                                                @php
                                                    $content = $cauHoi->noi_dung;
                                                    $count = 0;
                                                    // Tìm tất cả các dấu hiệu ô trống phổ biến: [...], ..., .., ___, gạch dưới, hoặc dấu chấm lửng
                                                    $renderedContent = preg_replace_callback('/\[\.\.\.\]|\.{2,}|…|_{2,}/', function($matches) use ($cauHoi, &$count) {
                                                        $count++;
                                                        return '<input type="text" name="answers['.$cauHoi->id.'][]" 
                                                            class="blank-input" 
                                                            placeholder="('.$count.')"
                                                            style="display: inline-block; width: 150px; border: none; border-bottom: 2px solid #cbd5e1; background: transparent; padding: 2px 8px; margin: 0 5px; outline: none; transition: all 0.2s; font-weight: 600; color: #4f46e5; text-align: center;"
                                                            onfocus="this.style.borderBottomColor=\'#4f46e5\'; this.style.background=\'#eff6ff\'"
                                                            onblur="if(!this.value) { this.style.borderBottomColor=\'#cbd5e1\'; this.style.background=\'transparent\' }"
                                                            oninput="onFillBlankInput('.($count).')">';
                                                    }, $content);
                                                @endphp
                                                {!! $renderedContent !!}
                                                
                                                {{-- Nếu không có dấu hiệu ô trống nào, mặc định hiện 1 ô cuối --}}
                                                @if($count == 0)
                                                    <div style="margin-top: 15px;">
                                                        <input type="text" name="answers[{{ $cauHoi->id }}][]" 
                                                            class="form-control" 
                                                            placeholder="Nhập câu trả lời..."
                                                            style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid #cbd5e1;"
                                                            oninput="onFillBlankInput(1)">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <script>
                                            function onFillBlankInput(blankIdx) {
                                                // Thông báo cho hệ thống là đã trả lời
                                                if(typeof onTextAnswerInput === 'function') {
                                                    onTextAnswerInput({{ $idx + 1 }}, {value: 'answered'});
                                                }
                                            }
                                        </script>
                                    @else
                                        @foreach ($cauHoi->dapAns as $dapAnIdx => $dapAn)
                                            @php $letter = ['A','B','C','D'][$dapAnIdx] ?? chr(65 + $dapAnIdx); @endphp
                                            <label class="option-item">
                                                <input type="radio" name="answers[{{ $cauHoi->id }}]"
                                                    value="{{ $dapAn->id }}"
                                                    onchange="onAnswerSelect({{ $idx + 1 }}, this)" />
                                                <span class="option-letter">{{ $letter }}</span>
                                                <span class="option-text">{!! $dapAn->noi_dung !!}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </main>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            const TOTAL_Q = {{ $tongSoCau }};
            const TOTAL_TIME = {{ $thoiGianPhut * 60 }};
            const NOP_KHI_CHUYEN_TAB = {{ $nopKhiChuyenTab ? 'true' : 'false' }};

            let currentQ = 1,
                answers = {},
                marked = new Set(),
                timeLeft = TOTAL_TIME,
                timerInterval = null;
            let isClickScrolling = false; // flag tránh observer ghi đè khi click sidebar

            function syncExamSidebarTop() {
                const workspace = document.getElementById('exam-workspace');
                const topbar = document.querySelector('.exam-topbar');
                if (!workspace || !topbar) return;

                const topbarBottom = Math.max(0, topbar.getBoundingClientRect().bottom);
                workspace.style.setProperty('--exam-sidebar-top', `${Math.ceil(topbarBottom + 16)}px`);
            }

            document.addEventListener('DOMContentLoaded', () => {
                startTimer();
                syncExamSidebarTop();
                window.addEventListener('scroll', syncExamSidebarTop, { passive: true });
                window.addEventListener('resize', syncExamSidebarTop);

                // [MỚI THÊM / SỬA LẠI] --- ĐẾM SỐ LẦN CHUYỂN TAB HOẶC RỜI KHỎI TRANG ---
                let viPhamCount = 0;
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        viPhamCount++;
                        document.getElementById('so_lan_vi_pham_tab').value =
                            viPhamCount; // Cập nhật số lần vi phạm vào thẻ ẩn

                        if (NOP_KHI_CHUYEN_TAB) {
                            alert('⚠️ Bạn đã chuyển tab! Bài thi sẽ được nộp tự động.');
                            submitExam();
                        }
                    }
                });

                // ── IntersectionObserver: highlight câu đang scroll tới ──
                const observer = new IntersectionObserver((entries) => {
                    // Chỉ cập nhật nếu KHÔNG đang scroll từ click sidebar
                    if (isClickScrolling) return;
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const q = parseInt(entry.target.id.replace('question-card-', ''));
                            currentQ = q;
                            document.getElementById('info-current').textContent = q;
                            updateNavBtns();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '-40% 0px -50% 0px',
                    threshold: 0
                });

                for (let i = 1; i <= TOTAL_Q; i++) {
                    const card = document.getElementById('question-card-' + i);
                    if (card) observer.observe(card);
                }
            });

            // ── Click sidebar: set currentQ ngay, rồi scroll mượt ──
            function goToQuestion(q) {
                const card = document.getElementById('question-card-' + q);
                if (!card) return;

                // Cập nhật ngay lập tức, không chờ scroll
                currentQ = q;
                document.getElementById('info-current').textContent = q;
                updateNavBtns();

                // Tạm tắt observer trong lúc scroll
                isClickScrolling = true;
                syncExamSidebarTop();
                const topbarBottom = Math.max(0, document.querySelector('.exam-topbar')?.getBoundingClientRect().bottom ?? 80);
                const y = card.getBoundingClientRect().top + window.scrollY - topbarBottom - 16;
                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });

                // Bật lại observer sau khi scroll xong (~700ms)
                clearTimeout(window._scrollTimer);
                window._scrollTimer = setTimeout(() => {
                    isClickScrolling = false;
                }, 700);
            }

            const CIRC = 2 * Math.PI * 30;
            let questionTimes = {};

            function trackQuestionTime() {
                // Mỗi giây, cộng 1 vào câu hỏi đang hiển thị (currentQ)
                if (!questionTimes[currentQ]) questionTimes[currentQ] = 0;
                questionTimes[currentQ]++;

                // Cập nhật giá trị vào thẻ input hidden tương ứng để khi nộp bài sẽ gửi đi
                const inputTime = document.getElementById('input-time-' + currentQ);
                if (inputTime) {
                    inputTime.value = questionTimes[currentQ];
                }
            }

            function startTimer() {
                const ring = document.getElementById('timer-ring');
                ring.style.strokeDasharray = ring.style.strokeDashoffset = CIRC;
                timerInterval = setInterval(() => {
                    if (--timeLeft <= 0) {
                        clearInterval(timerInterval);
                        autoSubmit();
                        return;
                    }
                    trackQuestionTime();
                    updateTimerDisplay();
                }, 1000);
            }

            function updateTimerDisplay() {
                const m = Math.floor(timeLeft / 60),
                    s = timeLeft % 60;
                document.getElementById('timer-display').textContent =
                    String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                const progress = timeLeft / TOTAL_TIME;
                const ring = document.getElementById('timer-ring');
                ring.style.strokeDashoffset = CIRC * (1 - progress);
                ring.style.stroke = progress > 0.4 ? 'var(--green)' : progress > 0.15 ? 'var(--gold)' : '#ef4444';
            }

            function onAnswerSelect(qIdx, radioEl) {
                answers[qIdx] = radioEl.value;
                document.querySelectorAll('#options-' + qIdx + ' .option-item').forEach(el => el.classList.remove('selected'));
                radioEl.closest('.option-item').classList.add('selected');
                updateNavBtns();
                updateCounts();
            }

            function onTextAnswerInput(qIdx, textareaEl) {
                if (textareaEl.value.trim().length > 0) {
                    answers[qIdx] = textareaEl.value;
                } else {
                    delete answers[qIdx];
                }
                updateNavBtns();
                updateCounts();
            }

            function toggleMark(qIdx) {
                const btn = document.getElementById('mark-btn-' + qIdx);
                const inputMark = document.getElementById('input-mark-' + qIdx); // Thẻ input ẩn vừa thêm ở Bước 1

                if (marked.has(qIdx)) {
                    marked.delete(qIdx);
                    btn.innerHTML = '<i class="far fa-bookmark"></i> Đánh dấu';
                    btn.classList.remove('active');
                    if (inputMark) inputMark.value = "0"; // Gửi về 0 (không đánh dấu)
                } else {
                    marked.add(qIdx);
                    btn.innerHTML = '<i class="fas fa-bookmark"></i> Bỏ đánh dấu';
                    btn.classList.add('active');
                    if (inputMark) inputMark.value = "1"; // Gửi về 1 (có đánh dấu)
                }
                updateNavBtns();
                updateCounts();
            }

            function updateNavBtns() {
                document.querySelectorAll('.q-nav-btn').forEach(btn => {
                    const n = parseInt(btn.dataset.q);
                    btn.classList.remove('current', 'answered', 'marked');

                    if (n === currentQ) btn.classList.add('current');
                    else if (answers[n] !== undefined) btn.classList.add('answered');

                    if (marked.has(n)) {
                        btn.classList.add('marked');
                        if (!btn.querySelector('.q-star')) {
                            const star = document.createElement('span');
                            star.className = 'q-star';
                            star.textContent = '★';
                            star.style.cssText =
                                'position:absolute;top:1px;right:2px;font-size:8px;color:#f59e0b;pointer-events:none;line-height:1;';
                            btn.style.position = 'relative';
                            btn.appendChild(star);
                        }
                    } else {
                        const star = btn.querySelector('.q-star');
                        if (star) star.remove();
                    }
                });
            }

            function updateCounts() {
                const c = Object.keys(answers).length;
                document.getElementById('info-answered').textContent = c;
                document.getElementById('info-marked').textContent = marked.size;
                document.getElementById('info-remaining').textContent = TOTAL_Q - c;
            }

            document.getElementById('submit-exam-btn').addEventListener('click', () => {
                const u = TOTAL_Q - Object.keys(answers).length;
                if (confirm(u === 0 ?
                        'Bạn đã trả lời tất cả câu hỏi.\n\nXác nhận nộp bài?' :
                        `Bạn còn ${u} câu chưa trả lời.\n\nBạn có chắc muốn nộp bài không?`
                    )) submitExam();
            });

            function autoSubmit() {
                alert('⏰ Hết thời gian! Bài thi sẽ được nộp tự động.');
                submitExam();
            }

            // [MỚI THÊM / SỬA LẠI] --- HÀM SUBMIT FORM ---
            function submitExam() {
                clearInterval(timerInterval);

                // 1. Khóa nút nộp bài tránh người dùng spam click nhiều lần
                document.getElementById('submit-exam-btn').disabled = true;

                // 2. Hiển thị màn hình Loading đẹp mắt
                document.getElementById('submit-loading-overlay').style.display = 'flex';

                // 3. Tiến hành submit
                document.getElementById('exam-form').submit();
            }
        </script>
    @endpush

@endsection
