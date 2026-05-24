{{-- resources/views/Client/exams/show.blade.php --}}
@extends('Client.layouts.app')

@section('title', $baiKiemTra->ten_bai . ' – Thi thử')

@section('content')

    @php
        $now = \Carbon\Carbon::now();
    @endphp

    {{-- ══════════════ PAGE HEADER ══════════════ --}}
    <div class="exam-page-header">
        <div class="container">
            <div class="exam-page-title">
                <div class="exam-page-icon">
                    <i class="fas fa-stopwatch"></i>
                </div>
                <h1>{{ $baiKiemTra->ten_bai }}</h1>
            </div>
            <p>{{ $baiKiemTra->monHoc->ten_mon_hoc }}
                @if ($baiKiemTra->mo_ta)
                    &nbsp;–&nbsp; {{ $baiKiemTra->mo_ta }}
                @endif
            </p>
        </div>
    </div>

    {{-- ══════════════ NỘI DUNG CHÍNH ══════════════ --}}
    <section class="exam-section">
        <div class="container">
            <div style="display:grid; grid-template-columns: 1fr 340px; gap:28px; align-items:start;">

                {{-- ══ CỘT TRÁI: thông tin đề + nút thi ══ --}}
                <div style="display:flex; flex-direction:column; gap:22px;">

                    {{-- Thông tin tổng quan --}}
                    <div class="exam-card" style="gap:20px;">

                        {{-- Header card --}}
                        <div class="exam-card-header">
                            <div class="exam-card-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="exam-tags">
                                <span class="tag time">
                                    <i class="fas fa-clock"></i> {{ $baiKiemTra->thoi_gian_phut }} phút
                                </span>
                                <span class="tag questions">
                                    {{ $baiKiemTra->cau_hois_count }} câu
                                </span>
                                @if (!$coTheThi)
                                    <span class="tag level-hard"><i class="fas fa-lock"></i> Đã đóng</span>
                                @elseif ($baiKiemTra->thoi_gian_bat_dau && $now->lt($baiKiemTra->thoi_gian_bat_dau))
                                    <span class="tag level-mid"><i class="fas fa-clock"></i> Chưa mở</span>
                                @else
                                    <span class="tag level-easy"><i class="fas fa-circle"></i> Đang diễn ra</span>
                                @endif
                            </div>
                        </div>

                        <h3 style="font-size:1.2rem; font-weight:800; color:#1e293b; margin:0;">
                            {{ $baiKiemTra->ten_bai }}
                        </h3>

                        @if ($baiKiemTra->mo_ta)
                            <p style="font-size:0.9rem; color:#64748b; line-height:1.7; margin:0;">
                                {{ $baiKiemTra->mo_ta }}
                            </p>
                        @endif

                        {{-- Số liệu 4 ô --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">

                            <div class="exam-stat"
                                style="background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e8f0fb;">
                                <div class="exam-stat-icon" style="background:#dbeafe;">
                                    <i class="fas fa-clock" style="color:var(--blue);"></i>
                                </div>
                                <div class="exam-stat-info">
                                    <span class="exam-stat-value">{{ $baiKiemTra->thoi_gian_phut }} phút</span>
                                    <span class="exam-stat-label">Thời gian làm bài</span>
                                </div>
                            </div>

                            <div class="exam-stat"
                                style="background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e8f0fb;">
                                <div class="exam-stat-icon" style="background:#d1fae5;">
                                    <i class="fas fa-question-circle" style="color:#059669;"></i>
                                </div>
                                <div class="exam-stat-info">
                                    <span class="exam-stat-value">{{ $baiKiemTra->cau_hois_count }} câu</span>
                                    <span class="exam-stat-label">Số câu hỏi</span>
                                </div>
                            </div>

                            <div class="exam-stat"
                                style="background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e8f0fb;">
                                <div class="exam-stat-icon" style="background:#fefce8;">
                                    <i class="fas fa-redo" style="color:#ca8a04;"></i>
                                </div>
                                <div class="exam-stat-info">
                                    <span class="exam-stat-value">
                                        {{ $soLanDaLam }}/{{ $baiKiemTra->so_lan_lam_bai }}
                                    </span>
                                    <span class="exam-stat-label">Lần đã thi / Tối đa</span>
                                </div>
                            </div>

                            <div class="exam-stat"
                                style="background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e8f0fb;">
                                <div class="exam-stat-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="exam-stat-info">
                                    <span class="exam-stat-value">{{ number_format($baiKiemTra->so_luot_thi) }}</span>
                                    <span class="exam-stat-label">Tổng lượt thi</span>
                                </div>
                            </div>
                        </div>

                        {{-- Thời gian mở / đóng --}}
                        @if ($baiKiemTra->thoi_gian_bat_dau || $baiKiemTra->thoi_gian_ket_thuc)
                            <div
                                style="background:#f8fafc; border-radius:12px; padding:14px 16px; border:1px solid #e8f0fb; display:flex; gap:24px; flex-wrap:wrap;">
                                @if ($baiKiemTra->thoi_gian_bat_dau)
                                    <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem;">
                                        <i class="fas fa-calendar-check" style="color:var(--green);"></i>
                                        <span style="color:#64748b;">Mở lúc:</span>
                                        <strong
                                            style="color:#1e293b;">{{ $baiKiemTra->thoi_gian_bat_dau->format('H:i – d/m/Y') }}</strong>
                                    </div>
                                @endif
                                @if ($baiKiemTra->thoi_gian_ket_thuc)
                                    <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem;">
                                        <i class="fas fa-calendar-times" style="color:#ef4444;"></i>
                                        <span style="color:#64748b;">Đóng lúc:</span>
                                        <strong
                                            style="color:#1e293b;">{{ $baiKiemTra->thoi_gian_ket_thuc->format('H:i – d/m/Y') }}</strong>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Cấu hình bài thi --}}
                    <div class="exam-card" style="gap:16px;">
                        <h3
                            style="font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#475569; margin:0;">
                            <i class="fas fa-sliders-h"></i> Cấu hình đề thi
                        </h3>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                            @php
                                $configs = [
                                    [
                                        'icon' => 'fa-random',
                                        'label' => 'Đảo thứ tự câu hỏi',
                                        'val' => $baiKiemTra->dao_cau_hoi,
                                    ],
                                    [
                                        'icon' => 'fa-shuffle',
                                        'label' => 'Đảo thứ tự đáp án',
                                        'val' => $baiKiemTra->dao_dap_an,
                                    ],
                                    [
                                        'icon' => 'fa-star',
                                        'label' => 'Xem điểm sau khi thi',
                                        'val' => $baiKiemTra->xem_diem,
                                    ],
                                    [
                                        'icon' => 'fa-eye',
                                        'label' => 'Xem bài làm sau thi',
                                        'val' => $baiKiemTra->xem_bai_lam,
                                    ],
                                    [
                                        'icon' => 'fa-exclamation-triangle',
                                        'label' => 'Nộp khi chuyển tab',
                                        'val' => $baiKiemTra->nop_khi_chuyen_tab,
                                    ],
                                ];
                            @endphp
                            @foreach ($configs as $cfg)
                                <div
                                    style="display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; background:{{ $cfg['val'] ? '#f0fdf4' : '#f8fafc' }}; border:1px solid {{ $cfg['val'] ? '#bbf7d0' : '#e2eaf6' }};">
                                    <i class="fas {{ $cfg['icon'] }}"
                                        style="color:{{ $cfg['val'] ? 'var(--green)' : '#94a3b8' }}; width:16px; text-align:center;"></i>
                                    <span
                                        style="font-size:0.82rem; color:{{ $cfg['val'] ? '#166534' : '#94a3b8' }}; font-weight:600;">
                                        {{ $cfg['label'] }}
                                    </span>
                                    <span
                                        style="margin-left:auto; font-size:0.75rem; font-weight:700; color:{{ $cfg['val'] ? 'var(--green)' : '#cbd5e1' }};">
                                        {{ $cfg['val'] ? 'BẬT' : 'TẮT' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        @if ($coTheThi)
                            @auth
                                <a href="{{ route('client.exams.start', $baiKiemTra->id) }}" class="btn-start"
                                    style="flex:1; min-width:200px;"
                                    onclick="return confirm('Bạn có chắc muốn bắt đầu thi?\n\nSau khi bắt đầu, đồng hồ sẽ chạy và không thể dừng lại!')">
                                    <i class="fas fa-play-circle"></i> Bắt đầu thi ngay
                                </a>
                            @else
                                <a href="{{ route('client.login') }}" class="btn-start"
                                    style="flex:1; min-width:200px; background:linear-gradient(135deg,var(--green),#059669);">
                                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để thi
                                </a>
                            @endauth
                        @else
                            <button class="btn-start"
                                style="flex:1; min-width:200px; background:#94a3b8; cursor:not-allowed; box-shadow:none;"
                                disabled>
                                <i class="fas fa-lock"></i> {{ $lyDoKhongThe }}
                            </button>
                        @endif

                        <a href="{{ route('client.exams.index') }}"
                            style="display:flex; align-items:center; gap:8px; background:#fff; border:1.5px solid #e2eaf6; color:#374151; font-size:0.9rem; font-weight:600; padding:13px 22px; border-radius:50px; transition:all 0.2s; white-space:nowrap;">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                </div>{{-- /cột trái --}}

                {{-- ══ CỘT PHẢI: lịch sử thi ══ --}}
                <div class="exam-card" style="gap:16px; position:sticky; top:160px;">
                    <div class="exam-history-title" style="margin-bottom:0;">
                        <i class="fas fa-history"></i> Lịch sử thi của bạn
                    </div>

                    @auth
                        @if ($lichSuThi->isEmpty())
                            <div class="no-history"
                                style="flex-direction:column; align-items:center; padding:24px 0; text-align:center;">
                                <i class="fas fa-inbox" style="font-size:2rem; margin-bottom:8px; color:#e2e8f0;"></i>
                                <p style="margin:0;">Bạn chưa thi lần nào</p>
                            </div>
                        @else
                            {{-- Điểm tốt nhất --}}
                            @php $diemCao = $lichSuThi->max('diem'); @endphp
                            <div
                                style="background:linear-gradient(135deg,var(--navy-dark),var(--navy)); border-radius:12px; padding:16px 18px; display:flex; align-items:center; gap:14px;">
                                <div
                                    style="width:44px;height:44px;background:var(--gold);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-trophy" style="color:var(--navy-dark);font-size:1.1rem;"></i>
                                </div>
                                <div>
                                    <div
                                        style="font-size:0.72rem;color:rgba(255,255,255,0.5);font-weight:600;text-transform:uppercase;letter-spacing:0.4px;">
                                        Điểm cao nhất</div>
                                    <div style="font-size:1.4rem;font-weight:800;color:var(--gold);line-height:1.2;">
                                        {{ number_format($diemCao, 1) }}<span
                                            style="font-size:0.9rem;color:rgba(255,255,255,0.4);font-weight:500;">/10</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Danh sách lần thi --}}
                            <div class="exam-history" style="gap:0; padding:0; background:transparent; border:none;">
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    @foreach ($lichSuThi as $idx => $h)
                                        @php
                                            $dColor =
                                                $h->diem >= 8
                                                    ? 'var(--green)'
                                                    : ($h->diem >= 5
                                                        ? 'var(--gold)'
                                                        : '#ef4444');
                                        @endphp
                                        <div class="exam-history-item"
                                            style="border-bottom:1px solid #f1f5f9; padding:10px 0; display:flex; align-items:center; gap:10px;">
                                            <div
                                                style="width:28px;height:28px;border-radius:50%;background:#f0f7ff;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:800;color:var(--blue);flex-shrink:0;">
                                                {{ $idx + 1 }}
                                            </div>
                                            <div style="flex:1; min-width:0;">
                                                <div style="font-size:0.78rem;color:#64748b;">
                                                    {{ \Carbon\Carbon::parse($h->thoi_gian_nop_bai ?? $h->thoi_gian_vao_thi)->format('H:i d/m/Y') }}
                                                </div>
                                                <div style="font-size:0.75rem;color:#94a3b8;">
                                                    <i class="fas fa-stopwatch"></i>
                                                    {{ floor($h->thoi_gian_lam / 60) }}p {{ $h->thoi_gian_lam % 60 }}s
                                                </div>
                                            </div>
                                            <div
                                                style="font-size:1rem;font-weight:800;color:{{ $dColor }};flex-shrink:0;">
                                                {{ number_format($h->diem, 1) }}<span
                                                    style="font-size:0.75rem;color:#94a3b8;font-weight:500;">/10</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="no-history"
                            style="flex-direction:column; align-items:center; padding:24px 0; text-align:center;">
                            <i class="fas fa-user-lock" style="font-size:2rem; margin-bottom:8px; color:#e2e8f0;"></i>
                            <p style="margin:0; margin-bottom:12px;">Đăng nhập để xem lịch sử</p>
                            <a href="{{ route('client.login') }}" class="btn-start" style="font-size:0.82rem; padding:9px 20px;">
                                <i class="fas fa-sign-in-alt"></i> Đăng nhập
                            </a>
                        </div>
                    @endauth
                </div>{{-- /cột phải --}}

            </div>
        </div>
    </section>

@endsection
