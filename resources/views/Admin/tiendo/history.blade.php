@extends('Admin.layouts.admin')
@section('title', 'Lịch sử luyện tập')

@section('content')
<style>
    .history-header {
        background: white;
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #eef2f6;
    }

    .stats-mini {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .stat-mini-card {
        flex: 1;
        min-width: 150px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-mini-icon {
        width: 48px;
        height: 48px;
        background: #eef2ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
    }

    .history-table-container {
        background: white;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        overflow: hidden;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th {
        text-align: left;
        padding: 16px 20px;
        background: #f9fafb;
        font-weight: 600;
        color: #374151;
        font-size: 13px;
        border-bottom: 1px solid #eef2f6;
    }

    .history-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        color: #4b5563;
    }

    .history-table tr:hover {
        background: #f9fafb;
    }

    .score-cell {
        font-weight: 600;
    }

    .score-high {
        color: #10b981;
    }

    .score-medium {
        color: #f59e0b;
    }

    .score-low {
        color: #ef4444;
    }

    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid #eef2f6;
    }

    .filter-bar {
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-input {
        padding: 10px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        min-width: 200px;
    }

    .subject-badge {
        background: #eef2ff;
        color: #4f46e5;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
</style>

<div class="history-header">
    <div class="stats-mini">
        <div class="stat-mini-card">
            <div class="stat-mini-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: #111827;">{{ $history->total() }}</div>
                <div style="font-size: 13px; color: #6b7280;">Tổng số phiên luyện tập</div>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: #111827;">
                    {{ number_format($history->avg('diem_so') ?? 0, 1) }}
                </div>
                <div style="font-size: 13px; color: #6b7280;">Điểm trung bình</div>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-icon">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
            </div>
            <div>
                <div style="font-size: 24px; font-weight: 700; color: #111827;">
                    {{ number_format($history->avg('phan_tram_dung') ?? 0, 0) }}%
                </div>
                <div style="font-size: 13px; color: #6b7280;">Tỉ lệ đúng trung bình</div>
            </div>
        </div>
    </div>
</div>

<div class="filter-bar">
    <input type="text" id="searchInput" class="filter-input" placeholder="🔍 Tìm kiếm theo môn học..." 
           onkeyup="filterTable()">
    <select id="scoreFilter" class="filter-input" onchange="filterTable()">
        <option value="">Tất cả điểm số</option>
        <option value="high">Điểm cao (≥ 8)</option>
        <option value="medium">Điểm trung bình (5-7.9)</option>
        <option value="low">Điểm thấp (&lt; 5)</option>
    </select>
</div>

<div class="history-table-container">
    <table class="history-table" id="historyTable">
        <thead>
            <tr>
                <th>STT</th>
                <th>Môn học</th>
                <th>Thời gian</th>
                <th>Số câu hỏi</th>
                <th>Điểm số</th>
                <th>Tỉ lệ đúng</th>
                <th>Xếp loại</th>
            </tr>
        </thead>
        <tbody>
            @forelse($history as $index => $phien)
                @php
                    $scoreClass = 'score-high';
                    $rank = 'Xuất sắc';
                    if($phien->diem_so >= 8) {
                        $scoreClass = 'score-high';
                        $rank = 'Xuất sắc';
                    } elseif($phien->diem_so >= 5) {
                        $scoreClass = 'score-medium';
                        $rank = 'Đạt';
                    } else {
                        $scoreClass = 'score-low';
                        $rank = 'Cần cải thiện';
                    }
                @endphp
                <tr class="history-row" data-subject="{{ strtolower($phien->monHoc->ten_mon_hoc ?? '') }}" 
                    data-score="{{ $phien->diem_so }}">
                    <td>{{ $history->firstItem() + $index }}</td>
                    <td>
                        <span class="subject-badge">
                            {{ $phien->monHoc->ten_mon_hoc ?? 'Không xác định' }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        {{ $phien->created_at->format('d/m/Y H:i') }}
                        <div style="font-size: 11px; color: #9ca3af;">{{ $phien->created_at->diffForHumans() }}</div>
                    </td>
                    <td>{{ $phien->so_cau_hoi ?? 'N/A' }}</td>
                    <td class="score-cell {{ $scoreClass }}">{{ number_format($phien->diem_so, 1) }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="flex: 1; background: #e5e7eb; border-radius: 10px; height: 6px; width: 80px;">
                                <div style="width: {{ $phien->phan_tram_dung }}%; background: {{ $phien->phan_tram_dung >= 70 ? '#10b981' : ($phien->phan_tram_dung >= 50 ? '#f59e0b' : '#ef4444') }}; height: 100%; border-radius: 10px;"></div>
                            </div>
                            <span>{{ number_format($phien->phan_tram_dung, 0) }}%</span>
                        </div>
                    </td>
                    <td>
                        <span style="padding: 4px 12px; background: {{ $scoreClass === 'score-high' ? '#d1fae5' : ($scoreClass === 'score-medium' ? '#fed7aa' : '#fee2e2') }}; border-radius: 20px; font-size: 12px; font-weight: 500;">
                            {{ $rank }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px 20px;">
                        <div class="empty-state" style="max-width: 400px; margin: 0 auto;">
                            <div class="empty-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="white" stroke-width="2">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                            </div>
                            <h3 style="font-size: 18px; color: #111827; margin-bottom: 8px;">Chưa có dữ liệu luyện tập</h3>
                            <p style="color: #6b7280;">Hãy bắt đầu luyện tập để theo dõi kết quả của bạn.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($history->hasPages())
        <div class="pagination-wrapper">
            {{ $history->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<script>
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const scoreFilter = document.getElementById('scoreFilter').value;
        const rows = document.querySelectorAll('.history-row');
        
        rows.forEach(row => {
            const subject = row.getAttribute('data-subject');
            const score = parseFloat(row.getAttribute('data-score'));
            
            let showRow = true;
            
            // Filter by subject
            if(searchTerm && !subject.includes(searchTerm)) {
                showRow = false;
            }
            
            // Filter by score
            if(showRow && scoreFilter) {
                if(scoreFilter === 'high' && score < 8) showRow = false;
                else if(scoreFilter === 'medium' && (score < 5 || score >= 8)) showRow = false;
                else if(scoreFilter === 'low' && score >= 5) showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
</script>
@endsection