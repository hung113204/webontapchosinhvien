{{-- resources/views/Client/exams/show.blade.php --}}
@extends('Client.layouts.app')

@section('title', $baiKiemTra->ten_bai . ' – Thi thử')

@push('styles')
    <style>
        .exam-action-row {
            display: flex;
            justify-content: center;
            gap: 16px;
            align-items: center;
            margin-top: 22px;
        }

        .exam-action-row .btn-start.exam-start-main {
            width: auto;
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 24px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(180deg, #2f6cf0 0%, #2456dd 100%);
            color: #fff;
            font-size: 0.88rem;
            font-weight: 800;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.16);
            transition: all 0.2s ease;
        }

        .leaderboard-scroll {
            max-height: 400px;
            overflow-y: auto;
        }

        .leaderboard-trophy-icon {
            font-size: 1.8rem;
            color: #fde047;
            transform-origin: center;
            animation: trophyPulse 1.35s ease-in-out infinite;
            will-change: transform;
        }

        @keyframes trophyPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.18);
            }
        }

        .leaderboard-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .leaderboard-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .leaderboard-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .leaderboard-scroll::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .exam-action-row .btn-start.exam-start-main i {
            width: 16px;
            height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            line-height: 1;
        }

        .exam-action-row .btn-start.exam-start-main:hover {
            transform: translateY(-1px);
            background: linear-gradient(180deg, #3b7cff 0%, #255ee7 100%);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.22);
        }

        .exam-action-row .btn-start.exam-start-main:disabled,
        .exam-action-row .btn-start.exam-start-main.is-disabled {
            background: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .exam-back-link {
            min-height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 10px 20px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #dbe5f2;
            color: #1f2937;
            font-size: 0.88rem;
            font-weight: 800;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.03);
            transition: all 0.2s ease;
        }

        .exam-back-link:hover {
            color: var(--blue);
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }

        .exam-grid-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }

        .exam-right-col {
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: sticky;
            top: 120px;
            max-height: calc(100vh - 140px);
            overflow-y: auto;
            padding-right: 4px;
        }

        @media (max-width: 991px) {
            .exam-grid-layout {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .exam-right-col {
                position: static;
                max-height: none;
                overflow-y: visible;
                padding-right: 0;
            }
        }

        @media (max-width: 640px) {
            .exam-action-row {
                flex-direction: column;
                width: 100%;
            }

            .exam-action-row .btn-start.exam-start-main,
            .exam-back-link {
                width: 100%;
            }
        }

        /* Modal Styles */
        .config-modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            z-index: 9999; display: flex; align-items: center; justify-content: center;
            opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        .config-modal-overlay.active { opacity: 1; visibility: visible; }
        .config-modal {
            background: #fff; border-radius: 16px; width: 100%; max-width: 650px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); overflow: hidden;
            margin: 20px;
        }
        .config-modal-overlay.active .config-modal { transform: translateY(0) scale(1); }
        .config-modal-header {
            padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex;
            align-items: center; justify-content: space-between;
        }
        .config-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 800; color: #1e293b; }
        .config-modal-close {
            background: none; border: none; color: #94a3b8; font-size: 1.2rem;
            cursor: pointer; transition: color 0.2s; padding: 0;
        }
        .config-modal-close:hover { color: #ef4444; }
        .config-modal-body { padding: 24px; }
        .config-modal-footer {
            padding: 16px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: flex-end; gap: 12px;
        }
        .btn-cancel {
            padding: 10px 20px; border-radius: 8px; background: #fff; border: 1px solid #cbd5e1;
            color: #475569; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
        }
        .btn-cancel:hover { background: #f1f5f9; color: #1e293b; border-color: #94a3b8; }
        .btn-confirm {
            padding: 10px 20px; border-radius: 8px; background: var(--blue); border: none;
            color: #fff; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;
            text-decoration: none; display: inline-flex; align-items: center;
        }
        .btn-confirm:hover { background: #2563eb; color: #fff; }
    </style>
@endpush

@section('content')

    @php
        $now = \Carbon\Carbon::now();
    @endphp

    {{-- ══════════════ PAGE HEADER ══════════════ --}}
    <div class="exam-page-header">
        <div class="container">
            <div class="exam-page-title">
                <div class="exam-page-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="exam-page-text">
                    <h1>{{ $baiKiemTra->ten_bai }}</h1>
                    <p>{{ $baiKiemTra->monHoc->ten_mon_hoc }}
                        @if ($baiKiemTra->mo_ta)
                            &nbsp;–&nbsp; {{ $baiKiemTra->mo_ta }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════ NỘI DUNG CHÍNH ══════════════ --}}
    <section class="exam-section">
        <div class="container">
            <div class="exam-grid-layout">

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
                                <span class="tag questions"><i class="fas fa-file-alt"></i>
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

                    {{-- LỊCH SỬ THI --}}
                    <div class="exam-card" style="gap:16px;">
                        <h3
                            style="font-size:0.85rem; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#475569; margin:0; padding-bottom: 8px;">
                            <i class="fas fa-history"></i> Lịch sử thi của bạn
                        </h3>

                        @auth
                            @if ($lichSuThi->isEmpty())
                                <table style="width: 100%; border-collapse: collapse; margin-top: 4px;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #1e293b;">
                                            <th style="padding: 12px 8px; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Thời gian</th>
                                            <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Số câu đúng</th>
                                            <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Điểm số</th>
                                            <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Xếp loại</th>
                                            <th style="padding: 12px 8px; text-align: right; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Ngày làm bài</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div style="text-align: center; padding: 32px 0; color: #64748b; font-size: 0.95rem;">
                                    Chưa có lịch sử làm bài
                                </div>
                            @else
                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; border-collapse: collapse; margin-top: 4px; min-width: 500px;">
                                        <thead>
                                            <tr style="border-bottom: 1px solid #1e293b;">
                                                <th style="padding: 12px 8px; text-align: left; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Thời gian</th>
                                                <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Số câu đúng</th>
                                                <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Điểm số</th>
                                                <th style="padding: 12px 8px; text-align: center; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Xếp loại</th>
                                                <th style="padding: 12px 8px; text-align: right; font-weight: 700; color: #1e293b; font-size: 0.9rem;">Ngày làm bài</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($lichSuThi as $h)
                                                @php
                                                    $diem = $h->diem;
                                                    if ($diem >= 8) {
                                                        $xepLoai = 'Giỏi';
                                                        $color = '#10b981'; // green
                                                    } elseif ($diem >= 6.5) {
                                                        $xepLoai = 'Khá';
                                                        $color = '#3b82f6'; // blue
                                                    } elseif ($diem >= 5) {
                                                        $xepLoai = 'Trung bình';
                                                        $color = '#f59e0b'; // orange
                                                    } else {
                                                        $xepLoai = 'Yếu';
                                                        $color = '#ef4444'; // red
                                                    }
                                                @endphp
                                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                                    <td style="padding: 12px 8px; text-align: left; font-size: 0.85rem; color: #475569;">
                                                        {{ floor($h->thoi_gian_lam / 60) }}p {{ $h->thoi_gian_lam % 60 }}s
                                                    </td>
                                                    <td style="padding: 12px 8px; text-align: center; font-size: 0.85rem; color: #475569; font-weight: 600;">
                                                        {{ $h->so_cau_dung }} / {{ $h->tong_so_cau }}
                                                    </td>
                                                    <td style="padding: 12px 8px; text-align: center; font-size: 0.95rem; font-weight: 800; color: {{ $color }};">
                                                        {{ number_format($diem, 1) }}
                                                    </td>
                                                    <td style="padding: 12px 8px; text-align: center; font-size: 0.85rem; font-weight: 600; color: {{ $color }};">
                                                        {{ $xepLoai }}
                                                    </td>
                                                    <td style="padding: 12px 8px; text-align: right; font-size: 0.85rem; color: #64748b;">
                                                        {{ \Carbon\Carbon::parse($h->thoi_gian_nop_bai ?? $h->thoi_gian_vao_thi)->format('H:i d/m/Y') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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
                    </div>

                    {{-- Nút hành động --}}
                    <div class="exam-action-row">
                        @if ($coTheThi)
                            @auth
                                <button type="button" class="btn-start exam-start-main" onclick="document.getElementById('configModal').classList.add('active')">
                                    <i class="fas fa-play-circle"></i> Bắt đầu thi ngay
                                </button>
                            @else
                                <a href="{{ route('client.login') }}" class="btn-start exam-start-main">
                                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để thi
                                </a>
                            @endauth
                        @else
                            <button class="btn-start exam-start-main is-disabled" disabled>
                                <i class="fas fa-lock"></i> {{ $lyDoKhongThe }}
                            </button>
                        @endif

                        <a href="{{ route('client.exams.index') }}" class="exam-back-link">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>

                </div>{{-- /cột trái --}}

                {{-- ══ CỘT PHẢI: lịch sử thi & bảng xếp hạng ══ --}}
                <div class="exam-right-col custom-scrollbar">
                    {{-- BẢNG XẾP HẠNG THI THỬ --}}
                    @if(isset($topScorers) && $topScorers->count() > 0)
                    <div class="exam-card" style="padding:0; overflow:hidden; gap:0;">
                        <div style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%); padding: 20px 24px; color: white; display:flex; align-items:center; gap: 14px;">
                            <i class="fas fa-trophy leaderboard-trophy-icon"></i>
                            <div>
                                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: white; line-height: 1.3;">Bảng xếp hạng<br>thi thử</h3>
                                <p style="margin: 4px 0 0; font-size: 0.8rem; font-weight: 600; opacity: 0.9;">Top 10 thí sinh xuất sắc</p>
                            </div>
                        </div>
                        <div class="leaderboard-scroll" style="display: flex; flex-direction: column;">
                            @foreach($topScorers as $idx => $scorer)
                            @php
                                $words = explode(' ', trim($scorer->sinhVien->name ?? 'User'));
                                $initials = mb_strtoupper(mb_substr($words[0], 0, 1));
                                if (count($words) > 1) {
                                    $initials .= mb_strtoupper(mb_substr(end($words), 0, 1));
                                }
                                $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
                                $bgColor = $colors[strlen($scorer->sinhVien->name ?? 'a') % count($colors)];
                            @endphp
                            <div style="display: flex; align-items: center; padding: 12px 20px; border-bottom: 1px solid #f1f5f9; gap: 14px; background: {{ $idx < 3 ? '#fffbeb' : '#fff' }};">
                                <div style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; border-radius: 8px; flex-shrink: 0;
                                    @if($idx == 0) background: #fef08a; color: #854d0e; box-shadow: 0 4px 10px rgba(234,179,8,0.2);
                                    @elseif($idx == 1) background: #e2e8f0; color: #475569;
                                    @elseif($idx == 2) background: #fed7aa; color: #9a3412;
                                    @else color: #64748b; font-size: 1rem;
                                    @endif
                                ">
                                    @if($idx == 0)
                                        <i class="fas fa-medal" style="color: #ca8a04;"></i>
                                    @elseif($idx == 1)
                                        <i class="fas fa-medal" style="color: #64748b;"></i>
                                    @elseif($idx == 2)
                                        <i class="fas fa-medal" style="color: #b45309;"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>
                                <div style="width: 38px; height: 38px; background: {{ $bgColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                                    {{ $initials }}
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $scorer->sinhVien->name ?? 'Người dùng ẩn danh' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                        {{ number_format($scorer->diem, 1) }} điểm
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif



                </div>{{-- /cột phải --}}

            </div>
        </div>
    </section>

    <div class="config-modal-overlay" id="configModal">
        <div class="config-modal">
            <div class="config-modal-header">
                <h3>Xác nhận bắt đầu thi</h3>
                <button class="config-modal-close" onclick="document.getElementById('configModal').classList.remove('active')"><i class="fas fa-times"></i></button>
            </div>
            <div class="config-modal-body">
                <div style="display:flex; align-items:flex-start; gap:10px; margin-bottom: 20px; padding: 12px 16px; background: #eff6ff; border-radius: 8px; color: #1e40af; font-size: 0.85rem; font-weight: 500; line-height: 1.5;">
                    <i class="fas fa-info-circle" style="margin-top: 3px; font-size: 1rem;"></i> 
                    <div>
                        Vui lòng kiểm tra kỹ cấu hình đề thi bên dưới.<br>
                        <strong>Lưu ý:</strong> Sau khi xác nhận, thời gian làm bài sẽ được tính ngay lập tức.
                    </div>
                </div>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    @php
                        $configs = [
                            [ 'icon' => 'fa-random', 'label' => 'Đảo câu hỏi', 'val' => $baiKiemTra->dao_cau_hoi ],
                            [ 'icon' => 'fa-shuffle', 'label' => 'Đảo đáp án', 'val' => $baiKiemTra->dao_dap_an ],
                            [ 'icon' => 'fa-star', 'label' => 'Xem điểm', 'val' => $baiKiemTra->xem_diem ],
                            [ 'icon' => 'fa-eye', 'label' => 'Xem bài làm', 'val' => $baiKiemTra->xem_bai_lam ],
                            [ 'icon' => 'fa-exclamation-triangle', 'label' => 'Nộp khi chuyển tab', 'val' => $baiKiemTra->nop_khi_chuyen_tab ],
                        ];
                    @endphp
                    @foreach ($configs as $cfg)
                        <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:8px; background:{{ $cfg['val'] ? '#f0fdf4' : '#f8fafc' }}; border:1px solid {{ $cfg['val'] ? '#bbf7d0' : '#e2eaf6' }};">
                            <i class="fas {{ $cfg['icon'] }}" style="color:{{ $cfg['val'] ? 'var(--green)' : '#94a3b8' }}; width:14px; text-align:center;"></i>
                            <span style="font-size:0.8rem; color:{{ $cfg['val'] ? '#166534' : '#94a3b8' }}; font-weight:600;">{{ $cfg['label'] }}</span>
                            <span style="margin-left:auto; font-size:0.7rem; font-weight:700; color:{{ $cfg['val'] ? 'var(--green)' : '#cbd5e1' }};">{{ $cfg['val'] ? 'BẬT' : 'TẮT' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="config-modal-footer">
                <button type="button" class="btn-cancel" onclick="document.getElementById('configModal').classList.remove('active')">Huỷ bỏ</button>
                <a href="{{ route('client.exams.start', $baiKiemTra->id) }}" class="btn-confirm">Vào thi ngay <i class="fas fa-arrow-right" style="margin-left: 6px;"></i></a>
            </div>
        </div>
    </div>

@endsection
