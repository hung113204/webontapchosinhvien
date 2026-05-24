@extends('Admin.layouts.admin')
@section('title', 'Quản lý tiến độ học sinh')

@section('content')
@php
    $summary = $summary ?? [
        'total_students' => count($stats),
        'avg_progress' => count($stats) > 0 ? round(collect($stats)->avg('progress'), 1) : 0,
        'avg_score' => count($stats) > 0 ? round(collect($stats)->avg('avg_score'), 1) : 0,
        'active_students' => collect($stats)->whereNotNull('last_activity')->count(),
        'completed_lessons' => collect($stats)->sum('completed_count'),
    ];
@endphp

<div class="progress-page">
    <div class="progress-hero">
        {{-- <div>
            <div class="eyebrow">Quản lý học tập</div>
            <h1>Tiến độ học sinh</h1>
            <p>Theo dõi mức hoàn thành bài học, điểm luyện tập và hoạt động gần nhất của toàn bộ học sinh.</p>
        </div> --}}
        <div class="hero-metric">
            <span>Tổng bài học</span>
            <strong>{{ number_format($totalLessons) }}</strong>
        </div>
    </div>

    <div class="metric-grid">
        <div class="metric-card accent-blue">
            <div class="metric-icon"><i class="fas fa-users"></i></div>
            <div>
                <span>Học sinh</span>
                <strong>{{ number_format($summary['total_students']) }}</strong>
            </div>
        </div>
        <div class="metric-card accent-green">
            <div class="metric-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span>Tiến độ TB</span>
                <strong>{{ $summary['avg_progress'] }}%</strong>
            </div>
        </div>
        <div class="metric-card accent-amber">
            <div class="metric-icon"><i class="fas fa-star"></i></div>
            <div>
                <span>Điểm luyện tập TB</span>
                <strong>{{ $summary['avg_score'] }}</strong>
            </div>
        </div>
        <div class="metric-card accent-slate">
            <div class="metric-icon"><i class="fas fa-clock"></i></div>
            <div>
                <span>Có hoạt động</span>
                <strong>{{ number_format($summary['active_students']) }}</strong>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-toolbar">
            <div>
                <h2>Bảng quản lý tiến độ</h2>
                <p>{{ number_format($summary['completed_lessons']) }} lượt bài học đã hoàn thành trên hệ thống.</p>
            </div>
            <div class="toolbar-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="studentSearch" placeholder="Tìm theo tên, email, mã sinh viên">
                </div>
                <select id="progressFilter" class="filter-select">
                    <option value="all">Tất cả tiến độ</option>
                    <option value="high">Từ 80% trở lên</option>
                    <option value="medium">Từ 40% đến 79%</option>
                    <option value="low">Dưới 40%</option>
                    <option value="inactive">Chưa hoạt động</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table progress-table mb-0" id="globalTable">
                <thead>
                    <tr>
                        <th>Hạng</th>
                        <th>Học sinh</th>
                        <th>Tiến độ</th>
                        <th class="text-center">Bài đã xong</th>
                        <th class="text-center">Điểm TB</th>
                        <th class="text-center">Phiên luyện tập</th>
                        <th>Hoạt động cuối</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats as $index => $item)
                        @php
                            $name = $item->user->ho_ten ?? $item->user->name ?? 'Học sinh';
                            $initial = mb_strtoupper(mb_substr($name, 0, 1));
                            $scoreClass = $item->avg_score >= 8 ? 'score-good' : ($item->avg_score >= 5 ? 'score-mid' : 'score-low');
                            $progressLevel = $item->last_activity ? ($item->progress >= 80 ? 'high' : ($item->progress >= 40 ? 'medium' : 'low')) : 'inactive';
                        @endphp
                        <tr class="student-row" data-progress="{{ $item->progress }}" data-level="{{ $progressLevel }}">
                            <td>
                                <span class="rank-badge {{ $index < 3 ? 'top-rank' : '' }}">#{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="student-cell">
                                    <div class="student-avatar">{{ $initial }}</div>
                                    <div class="student-meta">
                                        <strong>{{ $name }}</strong>
                                        <span>{{ $item->user->email }}</span>
                                        @if(!empty($item->user->ma_sv))
                                            <small>{{ $item->user->ma_sv }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="progress-cell">
                                    <div class="progress-info">
                                        <span>{{ $item->progress }}%</span>
                                        <small>{{ $item->progress >= 80 ? 'Tốt' : ($item->progress >= 40 ? 'Đang học' : 'Cần theo dõi') }}</small>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $item->progress }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="pill">{{ number_format($item->completed_count) }} / {{ number_format($totalLessons) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="score {{ $scoreClass }}">{{ $item->avg_score }}</span>
                            </td>
                            <td class="text-center">
                                <span class="pill neutral">{{ number_format($item->practice_count ?? 0) }}</span>
                            </td>
                            <td>
                                @if($item->last_activity)
                                    <span class="last-active"><i class="far fa-clock"></i>{{ $item->last_activity->diffForHumans() }}</span>
                                @else
                                    <span class="muted-text">Chưa có dữ liệu</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.tiendo.index', ['user_id' => $item->user->id]) }}" class="detail-btn">
                                    Chi tiết <i class="fas fa-chevron-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-user-graduate"></i>
                                    <strong>Chưa có học sinh nào</strong>
                                    <span>Khi học sinh tham gia học tập, dữ liệu tiến độ sẽ hiển thị tại đây.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('studentSearch');
        const progressFilter = document.getElementById('progressFilter');
        const rows = Array.from(document.querySelectorAll('.student-row'));

        function applyFilters() {
            const keyword = (searchInput.value || '').toLowerCase().trim();
            const level = progressFilter.value;

            rows.forEach(row => {
                const matchesKeyword = row.innerText.toLowerCase().includes(keyword);
                const matchesLevel = level === 'all' || row.dataset.level === level;
                row.style.display = matchesKeyword && matchesLevel ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', applyFilters);
        progressFilter.addEventListener('change', applyFilters);
    });
</script>
@endpush

<style>
    .progress-page {
        padding: 24px;
        max-width: 1480px;
        margin: 0 auto;
        color: #1f2937;
    }

    .progress-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 22px;
    }

    .eyebrow {
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .progress-hero h1 {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        color: #111827;
    }

    .progress-hero p {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 15px;
        max-width: 680px;
    }

    .hero-metric {
        min-width: 170px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-left: 4px solid #2563eb;
        border-radius: 8px;
        padding: 14px 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
    }

    .hero-metric span,
    .metric-card span,
    .panel-toolbar p {
        display: block;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .hero-metric strong {
        display: block;
        color: #111827;
        font-size: 26px;
        line-height: 1.1;
        margin-top: 6px;
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .05);
    }

    .metric-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
    }

    .metric-card strong {
        display: block;
        font-size: 25px;
        color: #111827;
        line-height: 1.1;
        margin-top: 6px;
    }

    .accent-blue .metric-icon { background: #eff6ff; color: #2563eb; }
    .accent-green .metric-icon { background: #ecfdf5; color: #059669; }
    .accent-amber .metric-icon { background: #fffbeb; color: #d97706; }
    .accent-slate .metric-icon { background: #f1f5f9; color: #475569; }

    .panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .panel-toolbar {
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: center;
        border-bottom: 1px solid #eef2f7;
    }

    .panel-toolbar h2 {
        margin: 0 0 5px;
        font-size: 18px;
        font-weight: 800;
        color: #111827;
    }

    .toolbar-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-box {
        width: 310px;
        height: 42px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0 13px;
        color: #94a3b8;
    }

    .search-box input,
    .filter-select {
        border: 0;
        outline: 0;
        background: transparent;
        color: #334155;
        font-size: 14px;
        width: 100%;
    }

    .filter-select {
        width: 180px;
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        padding: 0 12px;
    }

    .progress-table thead th {
        background: #f8fafc;
        border: 0;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        padding: 13px 18px;
        white-space: nowrap;
    }

    .progress-table tbody td {
        vertical-align: middle;
        border-color: #eef2f7;
        padding: 16px 18px;
    }

    .progress-table tbody tr:hover {
        background: #f8fafc;
    }

    .rank-badge,
    .pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 28px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: 13px;
        font-weight: 800;
    }

    .rank-badge.top-rank {
        background: #fef3c7;
        color: #92400e;
    }

    .student-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 245px;
    }

    .student-avatar {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        font-weight: 900;
        flex: 0 0 auto;
    }

    .student-meta strong,
    .student-meta span,
    .student-meta small {
        display: block;
    }

    .student-meta strong {
        color: #111827;
        font-size: 14px;
        max-width: 230px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-meta span,
    .student-meta small {
        color: #64748b;
        font-size: 12px;
    }

    .progress-cell {
        min-width: 230px;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .progress-info span {
        color: #2563eb;
        font-weight: 900;
        font-size: 14px;
    }

    .progress-info small {
        color: #64748b;
        font-size: 12px;
    }

    .progress-track {
        height: 8px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #2563eb, #22c55e);
    }

    .pill.neutral {
        background: #f8fafc;
        color: #334155;
    }

    .score {
        display: inline-flex;
        min-width: 44px;
        height: 32px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-weight: 900;
    }

    .score-good { background: #ecfdf5; color: #047857; }
    .score-mid { background: #fffbeb; color: #b45309; }
    .score-low { background: #fef2f2; color: #dc2626; }

    .last-active {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: #475569;
        font-size: 13px;
        white-space: nowrap;
    }

    .muted-text {
        color: #94a3b8;
        font-size: 13px;
    }

    .detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 36px;
        padding: 0 14px;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        color: #2563eb;
        font-weight: 800;
        text-decoration: none !important;
        white-space: nowrap;
    }

    .detail-btn:hover {
        background: #2563eb;
        border-color: #2563eb;
        color: #fff;
    }

    .empty-state {
        min-height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #64748b;
        text-align: center;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 38px;
    }

    .empty-state strong {
        color: #111827;
        font-size: 18px;
    }

    @media (max-width: 1100px) {
        .metric-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .panel-toolbar,
        .progress-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .toolbar-actions {
            width: 100%;
        }

        .search-box {
            flex: 1;
            width: auto;
        }
    }

    @media (max-width: 640px) {
        .progress-page {
            padding: 16px;
        }

        .progress-hero h1 {
            font-size: 26px;
        }

        .metric-grid,
        .toolbar-actions {
            grid-template-columns: 1fr;
            display: grid;
        }

        .filter-select,
        .search-box {
            width: 100%;
        }
    }
</style>
@endsection
