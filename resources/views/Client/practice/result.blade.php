{{-- resources/views/Client/practice/result.blade.php --}}
@extends('Client.layouts.app')

@section('title', 'Kết quả – Luyện tập')

@section('content')

@php
    // Tính xếp loại từ điểm số (thang 10)
    $thangDiem = 10;
    $diem = $result['diem'];
    if ($diem >= 9) {
        $xepLoai = ['label' => 'Xuất sắc',   'class' => 'excellent'];
    } elseif ($diem >= 7) {
        $xepLoai = ['label' => 'Giỏi',        'class' => 'good'];
    } elseif ($diem >= 5) {
        $xepLoai = ['label' => 'Trung bình',  'class' => 'fair'];
    } else {
        $xepLoai = ['label' => 'Cần cố gắng', 'class' => 'poor'];
    }
    $thoiGianLam = 0;
    if ($phien->thoi_gian_phut > 0) {
        $thoiGianLam = (int) $phien->thoi_gian_phut;
    } elseif ($phien->thoi_gian_bat_dau && $phien->thoi_gian_ket_thuc) {
        $thoiGianLam = (int) \Carbon\Carbon::parse($phien->thoi_gian_bat_dau)
            ->diffInSeconds(\Carbon\Carbon::parse($phien->thoi_gian_ket_thuc));
    }
@endphp

<div class="exam-result-page">
    <div class="container">

        {{-- ══════ Thẻ điểm tổng quan ══════ --}}
        <div class="result-hero {{ $xepLoai['class'] }}">
            <div class="result-hero-inner">
                <div class="result-score-ring">
                    <svg viewBox="0 0 120 120" width="140" height="140">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="8"/>
                        @php
                            $pct    = min($diem / $thangDiem, 1);
                            $circ   = 2 * M_PI * 52;
                            $offset = $circ * (1 - $pct);
                        @endphp
                        <circle cx="60" cy="60" r="52" fill="none" stroke="white" stroke-width="8"
                                stroke-linecap="round"
                                stroke-dasharray="{{ $circ }}"
                                stroke-dashoffset="{{ $offset }}"
                                transform="rotate(-90 60 60)"/>
                    </svg>
                    <div class="result-score-center">
                        <span class="result-score-num">{{ number_format($diem, 1) }}</span>
                        <span class="result-score-den">/{{ $thangDiem }}</span>
                    </div>
                </div>
                <div class="result-hero-info">
                    <div class="result-badge-xep-loai">
                        @if(in_array($xepLoai['class'], ['excellent','good']))
                            <i class="fas fa-trophy"></i>
                        @elseif($xepLoai['class'] === 'fair')
                            <i class="fas fa-medal"></i>
                        @else
                            <i class="fas fa-redo-alt"></i>
                        @endif
                        {{ $xepLoai['label'] }}
                    </div>
                    <h2 class="result-exam-name">{{ $monHoc->ten_mon_hoc ?? 'Bài luyện tập' }}</h2>
                    <p class="result-subject-name">
                        <i class="fas fa-book"></i> {{ $monHoc->ten_mon_hoc }}
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
                            @php
                                $h = floor($thoiGianLam / 3600);
                                $m = floor(($thoiGianLam % 3600) / 60);
                                $s = $thoiGianLam % 60;
                                if ($h > 0) {
                                    $timeDisplay = $h . 'g' . str_pad($m, 2, '0', STR_PAD_LEFT) . 'p' . str_pad($s, 2, '0', STR_PAD_LEFT) . 's';
                                } elseif ($m > 0) {
                                    $timeDisplay = $m . 'p' . str_pad($s, 2, '0', STR_PAD_LEFT) . 's';
                                } else {
                                    $timeDisplay = $s . 's';
                                }
                            @endphp
                            <span class="result-chip-val">{{ $timeDisplay }}</span>
                            <span class="result-chip-lbl">Thời gian</span>
                        </div>
                    </div>

                    <div class="result-action-btns">
                        <a href="{{ route('client.practice.setup', $phien->mon_hoc_id) }}" class="btn-result-retake">
                            <i class="fas fa-redo"></i> Luyện tập lại
                        </a>
                        <a href="{{ route('client.practice.index') }}" class="btn-result-back">
                            <i class="fas fa-list"></i> Danh sách môn học
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════ Chi tiết từng câu ══════ --}}
        @if ($cauHois)
            <div class="result-detail-wrap" style="margin-top: 24px;">
                <div class="result-detail-heading">
                    <i class="fas fa-clipboard-list"></i> Chi tiết bài làm
                    <span class="result-detail-sub">{{ $result['so_cau_dung'] }}/{{ $result['tong_so_cau'] }} câu đúng</span>
                </div>

                @foreach ($cauHois as $idx => $cauHoi)
                    @php
                        $chiTiet    = $result['chi_tiet'][$cauHoi->id] ?? [];
                        $isCorrect  = $chiTiet['is_correct'] ?? false;
                        $selectedId = $chiTiet['selected'] ?? null;
                        $correctId  = $chiTiet['correct'] ?? null;
                        $isSkipped  = $selectedId === null;
                    @endphp

                    <div class="result-q-item {{ $isCorrect ? 'is-correct' : ($isSkipped ? 'is-skipped' : 'is-wrong') }}">

                        {{-- Header câu hỏi --}}
                        <div class="result-q-top">
                            <div class="result-q-left">
                                <span class="result-q-num">{{ $idx + 1 }}</span>
                                @if ($isCorrect)
                                    <span class="result-q-status correct">
                                        <i class="fas fa-check"></i> Đúng
                                    </span>
                                @elseif ($isSkipped)
                                    <span class="result-q-status skipped">
                                        <i class="fas fa-minus"></i> Bỏ qua
                                    </span>
                                @else
                                    <span class="result-q-status wrong">
                                        <i class="fas fa-times"></i> Sai
                                    </span>
                                @endif
                            </div>
                            @php
                                $mucDoLabel = match($cauHoi->muc_do) { 1 => 'Dễ', 3 => 'Khó', default => 'Trung bình' };
                                $mucDoClass = match($cauHoi->muc_do) { 1 => 'easy', 3 => 'hard', default => 'mid' };
                            @endphp
                            <span class="q-level level-{{ $mucDoClass }}">{{ $mucDoLabel }}</span>
                        </div>

                        {{-- Nội dung câu hỏi --}}
                        <div class="result-q-content">{!! $cauHoi->noi_dung !!}</div>

                        {{-- Đáp án --}}
                        @if($cauHoi->loai_cau_hoi == 4)
                            <div class="result-q-explain" style="background-color: #f8fafc; border-color: #cbd5e1; margin-top: 15px;">
                                <strong>Bài làm của bạn:</strong>
                                <div style="margin-top: 8px; white-space: pre-wrap;">{{ $selectedId ?: '(Không có câu trả lời)' }}</div>
                            </div>
                            @php
                                $aiFeedbackText = $chiTiet['ai_feedback'] ?? '';
                                if (is_string($aiFeedbackText)) {
                                    $decodedFeedback = json_decode($aiFeedbackText, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedFeedback)) {
                                        if (isset($decodedFeedback['feedback'])) {
                                            $aiFeedbackText = $decodedFeedback['feedback'];
                                        } else {
                                            $labelMap = [
                                                'Strengths' => 'Ưu điểm',
                                                'Weaknesses & Errors' => 'Lỗi sai & Hạn chế',
                                                'Suggestions for improvement' => 'Gợi ý cải thiện',
                                            ];
                                            $aiFeedbackText = collect($decodedFeedback)
                                                ->map(fn($value, $key) => '[' . ($labelMap[$key] ?? $key) . ']: ' . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value))
                                                ->implode("\n\n");
                                        }
                                    }
                                } elseif (is_array($aiFeedbackText)) {
                                    $aiFeedbackText = json_encode($aiFeedbackText, JSON_UNESCAPED_UNICODE);
                                }
                            @endphp
                            @if(!empty($aiFeedbackText))
                                <div class="result-q-explain" style="background-color: #fefce8; border-color: #fef08a; margin-top: 15px;">
                                    <strong style="color: #ca8a04;"><i class="fas fa-robot"></i> AI Nhận xét & Đánh giá:</strong>
                                    <div style="margin-top: 8px; white-space: pre-wrap; line-height: 1.6;">{{ $aiFeedbackText }}</div>
                                </div>
                            @endif
                        @elseif($cauHoi->loai_cau_hoi == 3)
                            {{-- Điền khuyết --}}
                            <div class="result-q-explain" style="background-color: #f0fdf4; border-color: #bbf7d0; margin-top: 15px;">
                                <strong><i class="fas fa-edit"></i> Đáp án của bạn:</strong>
                                @php
                                    $userAnsArray = json_decode($selectedId, true) ?? [];
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
                            @php
                                $aiFeedbackText = $chiTiet['ai_feedback'] ?? '';
                                if (is_string($aiFeedbackText)) {
                                    $decodedFeedback = json_decode($aiFeedbackText, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedFeedback)) {
                                        if (isset($decodedFeedback['feedback'])) {
                                            $aiFeedbackText = $decodedFeedback['feedback'];
                                        } else {
                                            $labelMap = [
                                                'Strengths' => 'Ưu điểm',
                                                'Weaknesses & Errors' => 'Lỗi sai & Hạn chế',
                                                'Suggestions for improvement' => 'Gợi ý cải thiện',
                                            ];
                                            $aiFeedbackText = collect($decodedFeedback)
                                                ->map(fn($value, $key) => '[' . ($labelMap[$key] ?? $key) . ']: ' . (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value))
                                                ->implode("\n\n");
                                        }
                                    }
                                } elseif (is_array($aiFeedbackText)) {
                                    $aiFeedbackText = json_encode($aiFeedbackText, JSON_UNESCAPED_UNICODE);
                                }
                            @endphp
                            @if(!empty($aiFeedbackText))
                                <div class="result-q-explain" style="background-color: #fefce8; border-color: #fef08a; margin-top: 15px;">
                                    <strong style="color: #ca8a04;"><i class="fas fa-robot"></i> AI Nhận xét & Đánh giá:</strong>
                                    <div style="margin-top: 8px; white-space: pre-wrap; line-height: 1.6;">{{ $aiFeedbackText }}</div>
                                </div>
                            @endif
                        @else
                            <div class="result-q-options">
                                @foreach ($cauHoi->dapAns as $dapAnIdx => $dapAn)
                                    @php
                                        $letter  = ['A','B','C','D'][$dapAnIdx] ?? chr(65 + $dapAnIdx);
                                        $isUser  = ($dapAn->id == $selectedId);
                                        $isRight = ($dapAn->id == $correctId);
                                        $cls     = $isRight ? 'opt-correct' : ($isUser && !$isRight ? 'opt-wrong' : '');
                                    @endphp
                                    <div class="result-opt-item {{ $cls }}">
                                        <span class="result-opt-letter">{{ $letter }}</span>
                                        <span class="result-opt-text">
                                            {!! $dapAn->noi_dung !!}
                                            @if($dapAn->hinh_anh)
                                                <div style="margin-top: 10px;">
                                                    <img src="{{ asset('storage/' . $dapAn->hinh_anh) }}" style="max-height: 150px; border-radius: 6px; border: 1px solid #e2e8f0; display: block; max-width: 100%;">
                                                </div>
                                            @endif
                                        </span>
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
        @endif

    </div>
</div>

@endsection
