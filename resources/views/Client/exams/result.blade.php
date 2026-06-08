{{-- resources/views/Client/exams/result.blade.php --}}
@extends('Client.layouts.app')

@section('title', 'Kết quả – ' . $baiKiemTra->ten_bai)

@push('styles')
<style>
    .scroll-top-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--blue, #3b82f6), #1d4ed8);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
        transition: all 0.3s ease;
        z-index: 100;
    }
    .scroll-top-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.5);
    }
    .scroll-top-btn.show {
        display: flex;
    }
</style>
@endpush

@section('content')

<div class="exam-result-page">
    <div class="container">

        {{-- ══════ Thẻ điểm tổng quan ══════ --}}
        <div class="result-hero {{ $result['xep_loai']['class'] }}">
            <div class="result-hero-inner">

                {{-- Vòng tròn điểm --}}
                <div class="result-score-ring">
                    <svg viewBox="0 0 120 120" width="140" height="140">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="8"/>
                        @php
                            $pct     = min($result['diem'] / $thangDiem, 1);
                            $circ    = 2 * M_PI * 52;
                            $offset  = $circ * (1 - $pct);
                        @endphp
                        <circle cx="60" cy="60" r="52" fill="none" stroke="white" stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circ }}"
                                stroke-dashoffset="{{ $offset }}"
                                transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="result-score-center">
                        <span class="result-score-num">{{ number_format($result['diem'], 1) }}</span>
                        <span class="result-score-den">/{{ $thangDiem }}</span>
                    </div>
                </div>

                {{-- Thông tin kết quả --}}
                <div class="result-hero-info">
                    <div class="result-badge-xep-loai">
                        @if(in_array($result['xep_loai']['class'], ['excellent','good']))
                            <i class="fas fa-trophy"></i>
                        @elseif($result['xep_loai']['class'] === 'fair')
                            <i class="fas fa-medal"></i>
                        @else
                            <i class="fas fa-redo-alt"></i>
                        @endif
                        {{ $result['xep_loai']['label'] }}
                    </div>
                    <h2 class="result-exam-name">{{ $baiKiemTra->ten_bai }}</h2>
                    <p class="result-subject-name">
                        <i class="fas fa-book"></i> {{ $baiKiemTra->monHoc->ten_mon_hoc }}
                    </p>

                    <div class="result-stats-chips">
                        <div class="result-chip correct">
                            <i class="fas fa-check-circle"></i>
                            <span class="result-chip-val">{{ $result['so_cau_dung'] }}</span>
                            <span class="result-chip-lbl">Câu đúng</span>
                        </div>
                        <div class="result-chip wrong">
                            <i class="fas fa-times-circle"></i>
                            <span class="result-chip-val">{{ $result['tong_so_cau'] - $result['so_cau_dung'] }}</span>
                            <span class="result-chip-lbl">Câu sai</span>
                        </div>
                        <div class="result-chip total">
                            <i class="fas fa-list-ol"></i>
                            <span class="result-chip-val">{{ $result['tong_so_cau'] }}</span>
                            <span class="result-chip-lbl">Tổng câu</span>
                        </div>
                        <div class="result-chip time">
                            <i class="fas fa-stopwatch"></i>
                            <span class="result-chip-val">{{ floor($result['thoi_gian_lam']/60) }}p{{ $result['thoi_gian_lam']%60 }}s</span>
                            <span class="result-chip-lbl">Thời gian</span>
                        </div>
                    </div>

                    <div class="result-action-btns">
                        <a href="{{ route('client.exams.show', $baiKiemTra->id) }}" class="btn-result-retake">
                            <i class="fas fa-redo"></i> Thi lại
                        </a>
                        <a href="{{ route('client.exams.index') }}" class="btn-result-back">
                            <i class="fas fa-list"></i> Danh sách đề thi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════ KHU VỰC NHẬN XÉT CỦA AI ══════ --}}
        <div class="result-detail-wrap result-ai-advisor">
            <div class="result-detail-heading result-detail-heading-ai">
                <img src="{{ asset('./frontend/asset/images/t2.png') }}"
                            style="width:60px;height:60px;object-fit:contain;border-radius:6px;">
                <span style="font-size: 1.2rem; font-weight: 800;">AI Cố vấn học tập</span>
            </div>
            
            <div id="ai-feedback-container" class="ai-feedback-container">
                @if(isset($ketQuaThi) && $ketQuaThi->trang_thai == 1)
                    <div class="ai-text ai-feedback-box">
                        {!! nl2br(e(is_array($ketQuaThi->ai_feedback) ? json_encode($ketQuaThi->ai_feedback, JSON_UNESCAPED_UNICODE) : $ketQuaThi->ai_feedback)) !!}
                    </div>
                @elseif(isset($ketQuaThi) && $ketQuaThi->trang_thai == 2)
                    <div id="ai-loading" style="text-align: center; padding: 20px 0;">
                        <i class="fas fa-circle-notch fa-spin" style="font-size: 2.5rem; color: #6366f1;"></i>
                        <p style="margin-top: 16px; color: #64748b; font-weight: 500;">Hệ thống AI đang phân tích bài làm của bạn để đưa ra lộ trình học...</p>
                    </div>
                    <div id="ai-result-text" class="ai-feedback-box" style="display: none;"></div>
                @else
                    <p class="text-muted mb-0">Không có dữ liệu phân tích từ AI.</p>
                @endif
            </div>
        </div>

        {{-- ══════ Chi tiết từng câu ══════ --}}
        @if ($cauHois)
            <div class="result-detail-wrap">
                <div class="result-detail-heading">
                    <i class="fas fa-clipboard-list"></i> Chi tiết bài làm
                    <span class="result-detail-sub">{{ $result['so_cau_dung'] }}/{{ $result['tong_so_cau'] }} câu đúng</span>
                </div>

                @foreach ($cauHois as $idx => $cauHoi)
                    @php
                        $chiTiet      = $result['chi_tiet'][$cauHoi->id] ?? [];
                        $isCorrect    = $chiTiet['is_correct'] ?? false;
                        $selectedId   = $chiTiet['selected'] ?? null;
                        $tuLuanText   = $chiTiet['tu_luan_text'] ?? null;
                        $correctId    = $chiTiet['correct'] ?? null;
                        $diemCau      = $chiTiet['diem'] ?? 0;
                        $isTuLuan     = $cauHoi->loai_cau_hoi == 4;
                        $isDienKhuyet = $cauHoi->loai_cau_hoi == 3;
                        // Câu tự luận hoặc điền khuyết có text => không phải bỏ qua
                        $isSkipped    = ($isTuLuan || $isDienKhuyet) ? empty($tuLuanText) : ($selectedId === null);
                    @endphp

                    <div class="result-q-item {{ $isCorrect ? 'is-correct' : ($isSkipped ? 'is-skipped' : ($isTuLuan ? 'is-essay' : 'is-wrong')) }}"
                         style="{{ $isTuLuan && !$isSkipped && !$isCorrect ? 'border-left-color: #6366f1;' : '' }}">

                        {{-- Header câu hỏi --}}
                        <div class="result-q-top">
                            <div class="result-q-left">
                                <span class="result-q-num">{{ $idx + 1 }}</span>
                                @if ($isCorrect)
                                    <span class="result-q-status correct">
                                        <i class="fas fa-check"></i> Đúng &nbsp;+{{ $diemCau }} điểm
                                    </span>
                                @elseif ($isSkipped)
                                    <span class="result-q-status skipped">
                                        <i class="fas fa-minus"></i> Bỏ qua
                                    </span>
                                @elseif ($isTuLuan)
                                    <span class="result-q-status" style="background:#ede9fe; color:#6366f1;">
                                        <i class="fas fa-pen-nib"></i> Đã làm (Tự luận)
                                    </span>
                                @else
                                    <span class="result-q-status wrong">
                                        <i class="fas fa-times"></i> Sai
                                    </span>
                                @endif
                            </div>
                            @php
                                $mucDoLabel = match($cauHoi->muc_do) { 1=>'Nhận biết', 3=>'Vận dụng', default=>'Thông hiểu' };
                                $mucDoClass = match($cauHoi->muc_do) { 1=>'easy', 3=>'hard', default=>'mid' };
                            @endphp
                            <span class="q-level level-{{ $mucDoClass }}">{{ $mucDoLabel }}</span>
                        </div>

                        {{-- Nội dung câu hỏi --}}
                        <div class="result-q-content">
                            {!! $cauHoi->noi_dung !!}
                            @if($cauHoi->hinh_anh)
                                <div style="margin-top: 15px; text-align: center;">
                                    <img src="{{ asset('storage/' . $cauHoi->hinh_anh) }}" style="max-height: 300px; max-width: 100%; border-radius: 8px; border: 1px solid #e2e8f0;">
                                </div>
                            @endif
                        </div>

                        {{-- Đáp án --}}
                        @if($cauHoi->loai_cau_hoi == 4)
                            {{-- Tự luận: hiển thị bài làm và nhận xét AI --}}
                            <div class="result-q-explain" style="background-color: #f8fafc; border-color: #cbd5e1; margin-top: 15px;">
                                <strong><i class="fas fa-pen-nib"></i> Bài làm của bạn:</strong>
                                <div style="margin-top: 8px; white-space: pre-wrap; line-height: 1.7;">{{ ($chiTiet['tu_luan_text'] ?? null) ?: '(Không có câu trả lời)' }}</div>
                            </div>
                            @if(!empty($chiTiet['ai_feedback']))
                                <div class="result-q-explain" style="background-color: #fefce8; border-color: #fef08a; margin-top: 12px;">
                                    <strong style="color: #ca8a04;"><i class="fas fa-robot"></i> AI Nhận xét & Đánh giá:</strong>
                                    <div style="margin-top: 8px; white-space: pre-wrap; line-height: 1.7;">{{ $chiTiet['ai_feedback'] }}</div>
                                </div>
                            @endif
                        @elseif($cauHoi->loai_cau_hoi == 3)
                            {{-- Điền khuyết: Hiển thị bài làm và đáp án đúng --}}
                            <div class="result-q-explain" style="background-color: #f0fdf4; border-color: #bbf7d0; margin-top: 15px;">
                                <strong><i class="fas fa-edit"></i> Đáp án của bạn:</strong>
                                @php
                                    $userAnsArray = json_decode($tuLuanText, true) ?? [];
                                    $correctAnsArray = $cauHoi->dapAns->where('is_dung', 1)->values();
                                @endphp
                                <div style="margin-top: 10px; display: flex; flex-direction: column; gap: 8px;">
                                    @foreach($correctAnsArray as $idx => $cAns)
                                        @php
                                            $sAns = $userAnsArray[$idx] ?? '';
                                            $isMatch = trim(strtolower($sAns)) === trim(strtolower($cAns->noi_dung));
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="font-weight: 600; color: #64748b;">Ô {{ $idx + 1 }}:</span>
                                            <span style="padding: 4px 12px; border-radius: 6px; background: {{ $isMatch ? '#dcfce7' : '#fee2e2' }}; color: {{ $isMatch ? '#166534' : '#991b1b' }}; border: 1px solid {{ $isMatch ? '#bbf7d0' : '#fecaca' }};">
                                                {{ $sAns ?: '(Bỏ trống)' }}
                                            </span>
                                            @if(!$isMatch)
                                                <i class="fas fa-arrow-right" style="color: #64748b; font-size: 12px;"></i>
                                                <span style="color: #166534; font-weight: 600;">{{ $cAns->noi_dung }}</span>
                                            @else
                                                <i class="fas fa-check-circle" style="color: #22c55e;"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if(!empty($chiTiet['ai_feedback']))
                                <div class="result-q-explain" style="background-color: #fefce8; border-color: #fef08a; margin-top: 12px;">
                                    <strong style="color: #ca8a04;"><i class="fas fa-robot"></i> AI Nhận xét & Đánh giá:</strong>
                                    <div style="margin-top: 8px; white-space: pre-wrap; line-height: 1.7;">{{ $chiTiet['ai_feedback'] }}</div>
                                </div>
                            @endif
                        @else
                            <div class="result-q-options">
                                @foreach ($cauHoi->dapAns as $dapAnIdx => $dapAn)
                                    @php
                                        $letter  = ['A','B','C','D'][$dapAnIdx] ?? chr(65 + $dapAnIdx);
                                        $isUser  = ($dapAn->id == $selectedId);
                                        // Kiểm tra đáp án đúng (hỗ trợ cả mảng ID)
                                        $isRight = is_array($correctId) ? in_array($dapAn->id, $correctId) : ($dapAn->id == $correctId);
                                        $cls = $isRight ? 'opt-correct' : ($isUser && !$isRight ? 'opt-wrong' : '');
                                    @endphp
                                    <div class="result-opt-item {{ $cls }}">
                                        <span class="result-opt-letter">{{ $letter }}</span>
                                        <span class="result-opt-text">{!! $dapAn->noi_dung !!}</span>
                                        @if ($isRight)
                                            <i class="fas fa-check-circle result-opt-icon"></i>
                                        @elseif ($isUser && !$isRight)
                                            <i class="fas fa-times-circle result-opt-icon"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Giải thích --}}
                        @if (!empty($cauHoi->giai_thich))
                            <div class="result-q-explain">
                                <i class="fas fa-lightbulb"></i>
                                <strong>Giải thích:</strong> {!! $cauHoi->giai_thich !!}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="result-no-detail">
                <i class="fas fa-lock"></i>
                <p>Đề thi này không cho phép xem lại bài làm.</p>
            </div>
        @endif

    </div>
</div>

<script>
@if(isset($ketQuaThi) && $ketQuaThi?->trang_thai == 2)
    // Nếu AI vẫn đang chấm, thực hiện polling để check tình trạng
    document.addEventListener('DOMContentLoaded', function() {
        const loadingBox = document.getElementById('ai-loading');
        const resultBox = document.getElementById('ai-result-text');
        const url = '{{ route("client.exams.check_ai", $ketQuaThi?->id ?? 0) }}';

        // Hàm gọi API hỏi thăm backend
        function checkStatus() {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.trang_thai == 1) { // AI đã chấm xong
                        // Tải lại trang để cập nhật điểm số mới từ database
                        window.location.reload();
                    } else {
                        // Nếu chưa xong, 3 giây sau gọi lại hỏi tiếp
                        setTimeout(checkStatus, 3000); 
                    }
                })
                .catch(error => console.error('Lỗi khi check AI:', error));
        }

        // Đợi 1.5 giây sau khi tải trang xong mới bắt đầu hỏi thăm AI
        setTimeout(checkStatus, 1500); 
    });
@endif
</script>

<button class="scroll-top-btn" id="scrollTopBtn"><i class="fas fa-arrow-up"></i></button>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tô màu cú pháp code block bằng highlight.js
        if (typeof hljs !== 'undefined') {
            hljs.highlightAll();
        }
        
        // Render công thức Toán học MathJax
        if (window.MathJax && window.MathJax.typesetPromise) {
            MathJax.typesetPromise().catch((err) => console.log('MathJax error:', err));
        }
        
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        if (scrollTopBtn) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    scrollTopBtn.classList.add('show');
                } else {
                    scrollTopBtn.classList.remove('show');
                }
            });
            scrollTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    });
</script>
@endpush

@endsection
