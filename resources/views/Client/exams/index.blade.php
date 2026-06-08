{{-- resources/views/Client/exams/index.blade.php --}}
@extends('Client.layouts.app')

@section('title', 'Thi thử – IT Study Support')

@section('content')
    <div class="exam-page-header">
        <div class="container">
            <div class="breadcrumb-transparent">
                @php
                    $breadcrumbs = [
                        ['label' => 'Trang chủ', 'url' => route('home')],
                        ['label' => 'Thi thử', 'url' => '']
                    ];
                @endphp
                @include('Client.layouts.partials.breadcrumb', ['breadcrumbs' => $breadcrumbs])
            </div>
            <div class="exam-page-title">
                <div class="exam-page-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="exam-page-text">
                    <h1>Thi thử</h1>
                    <p>Mô phỏng thi thật với thời gian và áp lực phòng thi — luyện tập để tự tin hơn</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════ FILTER BAR ══════════════════════ --}}
    <section class="exam-section">
        <div class="container">
            <form method="GET" action="{{ route('client.exams.index') }}" class="filter-bar mb-4">
                <div class="filter-item">
                    <i class="fas fa-search filter-icon"></i>
                    <input type="text" name="tu_khoa" value="{{ request('tu_khoa') }}" placeholder="Tìm kiếm đề thi..."
                        class="filter-input" />
                </div>
                <div class="filter-item">
                    <i class="fas fa-book filter-icon"></i>
                    <select name="mon_hoc_id" class="filter-select">
                        <option value="">-- Môn học --</option>
                        @foreach ($monHocs as $mh)
                            <option value="{{ $mh->id }}" @selected(request('mon_hoc_id') == $mh->id)>
                                {{ $mh->ten_mon_hoc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <i class="fas fa-sort-amount-down filter-icon"></i>
                    <select name="sort" class="filter-select">
                        <option value="">-- Sắp xếp --</option>
                        <option value="moi" @selected(request('sort') == 'moi')>Mới nhất</option>
                        <option value="az" @selected(request('sort') == 'az')>A → Z</option>
                        <option value="za" @selected(request('sort') == 'za')>Z → A</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </form>

            {{-- ══════════════════ DANH SÁCH ĐỀ THI ══════════════════ --}}
            @if ($baiKiemTras->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Chưa có đề thi nào. Vui lòng quay lại sau.</p>
                </div>
            @else
                <div class="exams-grid">
                    @foreach ($baiKiemTras as $de)
                        @php
                            $history = $lichSuThi[$de->id] ?? [];
                            $lanTotNhat = collect($history)->sortByDesc('diem')->first();
                            $now = \Carbon\Carbon::now();
                            $conHan = !$de->thoi_gian_ket_thuc || $now->lt($de->thoi_gian_ket_thuc);
                        @endphp

                        <div class="exam-card">
                            <div class="exam-card-header">
                                <div class="exam-card-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="exam-tags">
                                    <span class="tag time">
                                        <i class="fas fa-clock"></i> {{ $de->thoi_gian_phut }} phút
                                    </span>
                                    <span class="tag questions">
                                        <i class="fas fa-file-alt"></i> {{ $de->cau_hois_count }} câu
                                    </span>
                                    @if (!$conHan)
                                        <span class="tag level-hard">Đã đóng</span>
                                    @elseif ($de->thoi_gian_bat_dau && $now->lt($de->thoi_gian_bat_dau))
                                        <span class="tag level-mid">Chưa mở</span>
                                    @else
                                        <span class="tag level-easy">Đang diễn ra</span>
                                    @endif
                                </div>
                            </div>

                            <h3>{{ $de->ten_bai }}</h3>
                            <p>{{ $de->mo_ta ?? 'Đề thi tổng hợp kiến thức môn ' . $de->monHoc->ten_mon_hoc }}</p>

                            {{-- Thống kê --}}
                            <div class="exam-stats-row">
                                <div class="exam-stat">
                                    <div class="exam-stat-icon"><i class="fas fa-users"></i></div>
                                    <div class="exam-stat-info">
                                        <span class="exam-stat-value">{{ number_format($de->so_luot_thi) }}</span>
                                        <span class="exam-stat-label">lượt thi</span>
                                    </div>
                                </div>
                                <div class="exam-stat">
                                    <div class="exam-stat-icon"><i class="fas fa-chart-line"></i></div>
                                    <div class="exam-stat-info">
                                        <span class="exam-stat-value">{{ number_format($de->diem_trung_binh, 1) }}/10</span>
                                        <span class="exam-stat-label">điểm TB</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Lịch sử thi --}}
                            <div class="exam-history">
                                <div class="exam-history-title">
                                    <i class="fas fa-history"></i> Lịch sử thi của bạn
                                </div>
                                @if (empty($history))
                                    <div class="no-history">
                                        <i class="fas fa-exclamation-circle"></i> Bạn chưa thi lần nào
                                    </div>
                                @else
                                    @foreach (array_slice($history, 0, 3) as $h)
                                        <div class="exam-history-item">
                                            {{ \Carbon\Carbon::parse($h['thoi_gian_nop_bai'])->format('d/m/Y') }}
                                            <span class="exam-history-score">{{ $h['diem'] }}/10</span>
                                            @php
                                                $giay = \Carbon\Carbon::parse($h['thoi_gian_vao_thi'])->diffInSeconds(
                                                    $h['thoi_gian_nop_bai'],
                                                );
                                            @endphp
                                            <span class="exam-history-time">{{ floor($giay / 60) }} phút</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <a href="{{ route('client.exams.show', $de->id) }}" class="btn-start">
                                <i class="fas fa-play-circle"></i> Xem đề & Bắt đầu thi
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4 d-flex justify-items-center justify-content-between align-items-center">
                    {{ $baiKiemTras->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </section>

@endsection

@push('styles')
<style>
/* Ẩn phần phân trang mặc định dành cho mobile của Bootstrap nếu không dùng */
.d-sm-none { display: none !important; }

/* Flexbox layout cho phần text và phần nút bấm */
.d-sm-flex { 
    display: flex !important; 
    padding: 10px;
    align-items: center; 
    justify-content: space-between; 
    flex-wrap: wrap;
    gap: 15px;
    width: 100%;
}

/* Ẩn d-none ban đầu (Bootstrap ẩn đi trên mobile, nhưng ta muốn hiện) */
.d-none.d-sm-flex {
    display: flex !important;
}

/* Style cho chữ "Showing 1 to..." */
.text-muted { color: #64748b; font-size: 14px; margin: 0; }
.small { font-size: 14px; }

/* Style cho danh sách nút bấm */
.pagination {
    display: flex;
    padding-left: 0;
    list-style: none;
    margin: 0;
    gap: 5px;
}

.page-item .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.page-item.active .page-link {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #fff;
}

.page-item.disabled .page-link {
    color: #cbd5e1;
    background: #f8fafc;
    pointer-events: none;
}

.page-item:not(.active):not(.disabled) .page-link:hover {
    background: #f1f5f9;
    color: #1e293b;
    border-color: #cbd5e1;
}

.page-link:focus {
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    outline: none;
}

/* Custom Premium Filter Bar */
.filter-bar {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 16px !important;
    background: #ffffff !important;
    padding: 24px !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
    align-items: center !important;
    margin-bottom: 35px !important;
}

.filter-item {
    position: relative !important;
    flex: 1 !important;
    min-width: 220px !important;
    display: flex !important;
    align-items: center !important;
}

.filter-item .filter-icon {
    position: absolute !important;
    left: 16px !important;
    color: #94a3b8 !important;
    font-size: 15px !important;
    pointer-events: none !important;
    z-index: 5 !important;
}

.filter-item .filter-input,
.filter-item .filter-select {
    width: 100% !important;
    height: 52px !important;
    padding: 0 16px 0 46px !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 14px !important;
    font-size: 14.5px !important;
    color: #1e293b !important;
    outline: none !important;
    transition: all 0.25s ease !important;
    background: #ffffff !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    box-sizing: border-box !important;
}

/* Custom Arrow for select elements */
.filter-item:has(.filter-select)::after {
    content: "\f078" !important;
    font-family: "Font Awesome 5 Free" !important;
    font-weight: 900 !important;
    position: absolute !important;
    right: 18px !important;
    color: #94a3b8 !important;
    font-size: 12px !important;
    pointer-events: none !important;
    z-index: 5 !important;
}

.filter-item .filter-input:focus,
.filter-item .filter-select:focus {
    border-color: #3b82f6 !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12) !important;
}

.btn-filter {
    height: 52px !important;
    padding: 0 32px !important;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 14px !important;
    font-weight: 700 !important;
    font-size: 15px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    cursor: pointer !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25) !important;
    white-space: nowrap !important;
}

.btn-filter:hover {
    transform: translateY(-1.5px) !important;
    box-shadow: 0 8px 22px rgba(37, 99, 235, 0.35) !important;
    filter: brightness(1.05) !important;
}

.btn-filter:active {
    transform: translateY(0) !important;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .filter-bar {
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 20px !important;
        gap: 14px !important;
        border-radius: 18px !important;
    }
    
    .filter-item {
        width: 100% !important;
        min-width: 0 !important;
    }
    
    .filter-item .filter-input,
    .filter-item .filter-select {
        height: 50px !important;
        font-size: 14px !important;
    }
    
    .btn-filter {
        width: 100% !important;
        height: 50px !important;
        font-size: 15px !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
