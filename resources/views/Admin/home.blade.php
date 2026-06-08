@extends('Admin.layouts.admin') 
@section('title', 'Dashboard') 

@section('content')
<section class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Tổng sinh viên</div>
            <div class="stat-value">{{ number_format($tongSinhVien) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-check-circle"></i> <span>Hoạt động trên hệ thống</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Tổng đề thi</div>
            <div class="stat-value">{{ number_format($tongBaiKiemTra) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-sync-alt"></i> <span>Cập nhật liên tục</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10" />
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Ngân hàng câu hỏi</div>
            <div class="stat-value">{{ number_format($tongCauHoi) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-database"></i> <span>Dữ liệu thực tế</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="20" x2="18" y2="10" />
                <line x1="12" y1="20" x2="12" y2="4" />
                <line x1="6" y1="20" x2="6" y2="14" />
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Điểm trung bình chung</div>
            <div class="stat-value">{{ number_format($diemTrungBinh, 1) }}</div>
            <div class="stat-change positive">
                <i class="fas fa-chart-line"></i> <span>Trên thang điểm 10</span>
            </div>
        </div>
    </div>
</section>

<section class="charts-section">
    <div class="chart-card chart-large">
        <div class="card-header">
            <h3>Lượt thi 7 ngày qua</h3>
        </div>
        <div class="chart-placeholder">
            <div class="chart-bars">
                @foreach($chartData as $data)
                    @php 
                        // Tính toán % chiều cao cột biểu đồ
                        $height = ($data['count'] / $maxChartCount) * 100; 
                        if($height < 5) $height = 5; // Cột tối thiểu 5% cho đỡ bị mất hình
                    @endphp
                    <div class="bar {{ $loop->last ? 'active' : '' }}" style="height: {{ $height }}%">
                        <div class="bar-value">{{ $data['count'] > 0 ? $data['count'] : '' }}</div>
                    </div>
                @endforeach
            </div>
            <div class="chart-labels">
                @foreach($chartData as $data)
                    <span style="font-size: 11px;">{{ mb_substr($data['day_label'], 0, 8) }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <div class="chart-card">
        <div class="card-header">
            <h3>Phân bố mức độ câu hỏi</h3>
        </div>
        <div class="difficulty-pie-chart" style="position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; padding-top: 10px;">
            <svg viewBox="0 0 100 100" style="width: 200px; height: 200px; transform: rotate(-90deg); filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.04)); cursor: pointer;">
                @php
                    $r = 36;
                    $c = 2 * M_PI * $r;
                    
                    $l_de = ($phanTramDe / 100) * $c;
                    $l_tb = ($phanTramTb / 100) * $c;
                    $l_kho = ($phanTramKho / 100) * $c;
                @endphp
                
                <!-- Nền -->
                <circle cx="50" cy="50" r="36" fill="none" stroke="#f3f4f6" stroke-width="14" />
                
                <!-- Nhận biết -->
                @if($phanTramDe > 0)
                <circle cx="50" cy="50" r="36" fill="none" stroke="#10b981" stroke-width="14"
                        stroke-dasharray="{{ $l_de }} {{ $c }}" stroke-dashoffset="0"
                        onmousemove="showChartTooltip(event, 'Nhận biết', '{{ $phanTramDe }}%', '#10b981')" 
                        onmouseout="hideChartTooltip()" style="transition: stroke-width 0.2s;"></circle>
                @endif
                
                <!-- Thông hiểu -->
                @if($phanTramTb > 0)
                <circle cx="50" cy="50" r="36" fill="none" stroke="#f59e0b" stroke-width="14"
                        stroke-dasharray="{{ $l_tb }} {{ $c }}" stroke-dashoffset="{{ -$l_de }}"
                        onmousemove="showChartTooltip(event, 'Thông hiểu', '{{ $phanTramTb }}%', '#f59e0b')" 
                        onmouseout="hideChartTooltip()" style="transition: stroke-width 0.2s;"></circle>
                @endif
                
                <!-- Vận dụng -->
                @if($phanTramKho > 0)
                <circle cx="50" cy="50" r="36" fill="none" stroke="#ef4444" stroke-width="14"
                        stroke-dasharray="{{ $l_kho }} {{ $c }}" stroke-dashoffset="{{ -($l_de + $l_tb) }}"
                        onmousemove="showChartTooltip(event, 'Vận dụng', '{{ $phanTramKho }}%', '#ef4444')" 
                        onmouseout="hideChartTooltip()" style="transition: stroke-width 0.2s;"></circle>
                @endif
            </svg>

            <!-- Tooltip nổi -->
            <div id="chart-custom-tooltip" style="position: fixed; background: #262626; color: white; padding: 8px 12px; border-radius: 6px; font-size: 13px; font-family: 'Inter', sans-serif; pointer-events: none; opacity: 0; transition: opacity 0.1s; z-index: 1000; display: flex; flex-direction: column; gap: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <div id="tooltip-title" style="font-weight: 600;">Tiêu đề</div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span id="tooltip-color" style="width: 10px; height: 10px; display: inline-block;"></span>
                    <span id="tooltip-value">0</span>
                </div>
            </div>

            <script>
                function showChartTooltip(e, title, value, color) {
                    const tooltip = document.getElementById('chart-custom-tooltip');
                    document.getElementById('tooltip-title').innerText = title;
                    document.getElementById('tooltip-value').innerText = value;
                    document.getElementById('tooltip-color').style.background = color;
                    
                    tooltip.style.opacity = '1';
                    tooltip.style.left = (e.clientX + 15) + 'px';
                    tooltip.style.top = (e.clientY + 15) + 'px';
                    
                    // Hiệu ứng hover dày lên
                    e.target.style.strokeWidth = '18';
                }
                function hideChartTooltip() {
                    document.getElementById('chart-custom-tooltip').style.opacity = '0';
                    // Reset độ dày
                    document.querySelectorAll('.difficulty-pie-chart svg circle:not(:first-child)').forEach(c => {
                        c.style.strokeWidth = '14';
                    });
                }
            </script>

            <div class="difficulty-legend" style="display: flex; gap: 16px; margin-top: 30px; width: 100%; justify-content: center; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #f0fdf4; border-radius: 20px; border: 1px solid #bbf7d0;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600; color: #166534;">Nhận biết ({{ $phanTramDe }}%)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #fffbeb; border-radius: 20px; border: 1px solid #fde68a;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600; color: #92400e;">Thông hiểu ({{ $phanTramTb }}%)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; background: #fef2f2; border-radius: 20px; border: 1px solid #fecaca;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="font-size: 13px; font-weight: 600; color: #991b1b;">Vận dụng ({{ $phanTramKho }}%)</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="activity-section">
    <div class="activity-card">
        <div class="card-header">
            <h3>Đề thi mới tạo</h3>
            <a href="{{ route('admin.baikiemtra.index') }}" class="view-all">Xem tất cả &rarr;</a>
        </div>
        <div class="activity-list">
            @forelse($baiKiemTraMoi as $bai)
                <div class="activity-item">
                    <div class="activity-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 22px; height: 22px; color: white;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ $bai->ten_bai }}</div>
                        <div class="activity-meta" style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                {{ $bai->monHoc->ten_mon_hoc ?? 'Môn chung' }}
                            </span>
                            <span class="separator">•</span>
                            <span style="display: flex; align-items: center; gap: 4px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                {{ $bai->thoi_gian_lam_bai ?? $bai->thoi_gian ?? 45 }} phút
                            </span>
                            <span class="separator">•</span>
                            <span style="display: flex; align-items: center; gap: 4px; color: #8b5cf6; font-weight: 500;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 14px; height: 14px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                Tạo {{ $bai->created_at ? $bai->created_at->diffForHumans() : 'Gần đây' }}
                            </span>
                        </div>
                    </div>
                    <div class="activity-status {{ $bai->trang_thai ? 'status-active' : 'status-scheduled' }}">
                        {{ $bai->trang_thai ? 'Đang mở' : 'Đang đóng' }}
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #9ca3af; padding: 20px;">Chưa có đề thi nào.</div>
            @endforelse
        </div>
    </div>

    <div class="activity-card">
        <div class="card-header">
            <h3>Sinh viên xuất sắc (Điểm TB)</h3>
            <a href="{{ route('admin.users.index') }}" class="view-all">Quản lý user &rarr;</a>
        </div>
        <div class="top-students">
            @forelse($topSinhVien as $index => $sv)
                <div class="student-item">
                    <div class="student-rank">{{ $index + 1 }}</div>
                    <div class="student-avatar" style="text-transform: uppercase;">
                        {{ mb_substr($sv->ho_ten, 0, 2) }}
                    </div>
                    <div class="student-info">
                        <div class="student-name">{{ $sv->ho_ten }}</div>
                        <div class="student-id" style="display: flex; align-items: center; gap: 4px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><circle cx="12" cy="10" r="3"></circle><path d="M7 20v-2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"></path></svg>
                            {{ $sv->ma_sv }}
                        </div>
                    </div>
                    <div class="student-score">{{ number_format($sv->diem_tb, 1) }}</div>
                </div>
            @empty
                <div style="text-align: center; color: #9ca3af; padding: 20px;">Chưa có dữ liệu làm bài thi.</div>
            @endforelse
        </div>
    </div>
</section>
@endsection