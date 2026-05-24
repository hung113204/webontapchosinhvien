@extends('Admin.layouts.admin')
@section('title', 'Tiến độ học tập của ' . $user->name)

@section('content')
<div class="bf-container" style="padding: 24px; max-width: 1400px; margin: 0 auto;">

    <!-- Header & Back Button -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px;">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2" style="font-size: 13px;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.tiendo.students') }}" style="color: #64748b;">Chọn học sinh</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tiến độ cá nhân</li>
                </ol>
            </nav>
            <h2 style="font-size: 32px; color: #1e293b; font-weight: 800; margin: 0;">
                Hồ sơ học tập: <span style="color: #4f46e5;">{{ $user->name }}</span>
            </h2>
            <p style="color: #64748b; margin-top: 4px;">Chi tiết quá trình rèn luyện và kết quả thi cử.</p>
        </div>
        <a href="{{ route('admin.tiendo.students') }}" class="btn btn-outline-secondary" style="border-radius: 10px; font-weight: 600;">
            <i class="fas fa-chevron-left mr-1"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Môn học Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 24px; margin-bottom: 40px;">
        @forelse($dataTienDo as $item)
        <div class="subject-card">
            <div class="subject-header">
                <div class="icon-box">
                    <i class="{{ $item['icon'] }}"></i>
                </div>
                <div class="subject-info">
                    <h3>{{ $item['mon_hoc'] }}</h3>
                    <span>{{ $item['so_bai_da_xong'] }} / {{ $item['tong_bai'] }} bài hoàn thành</span>
                </div>
            </div>

            <div class="progress-section">
                <div class="progress-meta">
                    <span>Tiến độ học tập</span>
                    <span class="pct">{{ $item['tien_do_ly_thuyet'] }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $item['tien_do_ly_thuyet'] }}%"></div>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="val text-success">{{ $item['diem_trung_binh'] }}</div>
                    <div class="lab">Điểm TB</div>
                </div>
                <div class="stat-box">
                    <div class="val text-info">{{ $item['ti_le_dung'] }}%</div>
                    <div class="lab">Tỉ lệ đúng</div>
                </div>
            </div>

            <a href="{{ route('admin.tiendo.show', ['user_id' => $user->id, 'mon_hoc_id' => $item['id']]) }}" class="btn-explore">
                Xem chi tiết bài học <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        @empty
            <div class="empty-subjects">
                <i class="fas fa-book-open"></i>
                <strong>Chưa có môn học có bài học</strong>
                <span>Khi môn học có bài học hoạt động, tiến độ sẽ hiển thị tại đây.</span>
            </div>
        @endforelse
    </div>

    @if($dataTienDo->hasPages())
        <div class="subject-pagination">
            {{ $dataTienDo->links() }}
        </div>
    @endif

    <!-- Charts & Activity -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div class="analysis-card">
            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Biểu đồ phong độ (10 phiên gần nhất)</h3>
            <div style="height: 380px;">
                <canvas id="chartTienDo"></canvas>
            </div>
        </div>

        <div class="analysis-card">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Hoạt động gần đây</h3>
            <div class="activity-list">
                @forelse($lichSuLuyenTap as $ls)
                <div class="activity-item">
                    <div class="activity-info">
                        <strong>Luyện tập môn {{ $ls->monHoc->ten_mon_hoc ?? 'N/A' }}</strong>
                        <small>{{ $ls->created_at->format('H:i - d/m/Y') }}</small>
                    </div>
                    <div class="activity-score">
                        {{ $ls->diem_so }} <small>đ</small>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #94a3b8; padding: 60px 0;">
                    <i class="fas fa-ghost mb-2" style="font-size: 32px;"></i>
                    <p>Chưa có lịch sử luyện tập</p>
                </div>
                @endforelse
            </div>
            @if(count($lichSuLuyenTap) > 0)
                <a href="{{ route('admin.tiendo.history', $user->id) }}" class="btn-history">
                    Xem toàn bộ lịch sử
                </a>
            @endif
        </div>
    </div>
</div>

<style>
    .subject-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .subject-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .subject-header {
        display: flex;
        gap: 16px;
        align-items: center;
        margin-bottom: 24px;
    }
    .icon-box {
        width: 56px;
        height: 56px;
        background: #f1f5f9;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #4f46e5;
    }
    .subject-info h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    .subject-info span {
        font-size: 13px;
        color: #64748b;
    }

    .progress-section {
        margin-bottom: 24px;
    }
    .progress-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }
    .pct {
        color: #4f46e5;
    }
    .progress-bar-container {
        height: 8px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #4f46e5, #818cf8);
        border-radius: 4px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 16px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 20px;
    }
    .stat-box {
        text-align: center;
    }
    .stat-box .val {
        font-size: 24px;
        font-weight: 800;
    }
    .stat-box .lab {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 700;
    }

    .btn-explore {
        display: block;
        text-align: center;
        padding: 12px;
        background: #f8fafc;
        border-radius: 12px;
        color: #1e293b;
        font-weight: 700;
        text-decoration: none !important;
        transition: 0.2s;
    }
    .btn-explore:hover {
        background: #1e293b;
        color: white;
    }

    .analysis-card {
        background: white;
        border-radius: 20px;
        padding: 28px;
        border: 1px solid #e2e8f0;
    }
    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 24px;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .activity-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .activity-info strong {
        display: block;
        font-size: 14px;
        color: #334155;
    }
    .activity-info small {
        color: #94a3b8;
    }
    .activity-score {
        background: #ecfdf5;
        color: #10b981;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 800;
        font-size: 18px;
    }

    .btn-history {
        display: block;
        margin-top: 20px;
        text-align: center;
        color: #4f46e5;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
    }

    .subject-pagination {
        display: flex;
        justify-content: center;
        margin: -12px 0 40px;
    }

    .empty-subjects {
        grid-column: 1 / -1;
        min-height: 220px;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        color: #64748b;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-align: center;
    }

    .empty-subjects i {
        color: #cbd5e1;
        font-size: 34px;
    }

    .empty-subjects strong {
        color: #1e293b;
        font-size: 18px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartTienDo').getContext('2d');
        const labels = @json($lichSuLuyenTap->reverse()->pluck('created_at')->map(fn($d) => $d->format('d/m')));
        const data   = @json($lichSuLuyenTap->reverse()->pluck('diem_so'));

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Điểm số',
                    data: data,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        max: 10,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 2 }
                    },
                    x: { grid: { display: false } }
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 }
                    }
                }
            }
        });
    });
</script>
@endsection
