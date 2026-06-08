@extends('Client.layouts.app')

@section('title', 'Hồ sơ học tập - IT Study Support')

@section('content')
    @php
        $completionRate = is_numeric($stats['ty_le_hoan_thanh'] ?? null) ? (float) $stats['ty_le_hoan_thanh'] : 0;
        $averageScore = is_numeric($stats['diem_trung_binh'] ?? null) ? (float) $stats['diem_trung_binh'] : 0;
        $studyHours = is_numeric($stats['tong_gio_hoc_tap'] ?? null) ? (float) $stats['tong_gio_hoc_tap'] : 0;
        $totalQuestions = is_numeric($stats['tong_cau_da_lam'] ?? null) ? (int) $stats['tong_cau_da_lam'] : 0;

        $studyHoursInt = floor($studyHours);
        $studyMinutes  = round(($studyHours - $studyHoursInt) * 60);
        $studyHoursLabel = $studyHoursInt . 'h' . ($studyMinutes > 0 ? ' ' . $studyMinutes . 'p' : '');
    @endphp

    <style>
        .h-tab-link {
            padding: 12px 4px;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }
        .h-tab-link:hover {
            color: #4f46e5;
        }
        .h-tab-link.active {
            font-weight: 600;
            color: #4f46e5;
            border-bottom: 2px solid #4f46e5;
        }
        .profile-tab-content {
            display: none;
        }
        .profile-tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .subject-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .subject-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }
        .practice-nest {
            background: #f8fafc;
            border-radius: 8px;
            padding: 12px 16px;
            border: 1px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>

    <section class="profile-section-clean">
        <div class="container">
            <div class="profile-grid-layout">
                
                <!-- Sidebar (Tài khoản) -->
                @include('Client.account.partials.sidebar')

                <div class="profile-main-area">
                    <!-- Header -->
                    <div class="profile-header-top" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h2 style="font-size: 1.3rem; font-weight: 700; color: #1e293b; margin: 0 0 5px 0;">Hồ sơ học tập</h2>
                            <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Tổng hợp tiến độ – lịch sử thi – phân tích điểm yếu theo đề/chủ đề.</p>
                        </div>
                        <div style="display: flex; gap: 10px;">
                           <!--  <button class="btn-avatar-outline" style="padding: 8px 16px; font-size: 0.9rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Bộ lọc</button>
                            <button class="btn-avatar-outline" style="padding: 8px 16px; font-size: 0.9rem; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; cursor: pointer;">Xuất báo cáo</button> -->
                            <button class="btn-avatar-primary" style="padding: 8px 16px; font-size: 0.9rem; background: #4f46e5; color: #fff; border: none; border-radius: 6px; cursor: pointer;" onclick="location.reload()">Làm mới</button>
                        </div>
                    </div>

                    <!-- User Info & Goal Card -->
                    <div class="p-card" style="margin-bottom: 20px; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #f1f5f9;">
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Storage::url(Auth::user()->avatar_url) }}" alt="Avatar" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;" />
                            @else
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; color: #64748b;">
                                    {{ mb_strtoupper(mb_substr(Auth::user()->ho_ten, 0, 2)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-weight: 600; font-size: 1.1rem; color: #1e293b; margin-bottom: 4px;">{{ Auth::user()->ho_ten }} - {{ Auth::user()->email }}</div>
                                <div style="color: #64748b; font-size: 0.9rem;">Mã SV: {{ Auth::user()->ma_sv }} • Tổng lượt thi: {{ $stats['tong_so_bai'] ?? 0 }}</div>
                            </div>
                        </div>

                        <div style="background: #f8fafc; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #e2e8f0;">
                            <div style="font-size: 0.9rem; color: #334155;">
                                <strong style="color: #4f46e5;">Đang tập trung:</strong> Cải thiện điểm số trung bình (Gợi ý hệ thống)
                            </div>
                            <div style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                Mục tiêu: Điểm TB ≥ 8.0
                            </div>
                        </div>
                    </div>

                    <!-- Horizontal Tabs Navigation -->
                    <div class="horizontal-tabs" style="display: flex; border-bottom: 1px solid #e2e8f0; margin-bottom: 24px; gap: 32px; padding: 0 5px;">
                        <a href="#" class="h-tab-link active" data-tab="dashboard">
                            <i class="fas fa-chart-line" style="margin-right: 6px;"></i> Tổng quan
                        </a>
                        <a href="#" class="h-tab-link" data-tab="progress">
                            <i class="fas fa-tasks" style="margin-right: 6px;"></i> Tiến độ học tập
                        </a>
                        <a href="#" class="h-tab-link" data-tab="results">
                            <i class="fas fa-chart-bar" style="margin-right: 6px;"></i> Kết quả
                        </a>
                        <a href="#" class="h-tab-link" data-tab="achievements">
                            <i class="fas fa-award" style="margin-right: 6px;"></i> Thành tích
                        </a>
                    </div>

                    <!-- Tab Contents -->
                    <div class="profile-tab-content active" id="tab-dashboard">
                        <!-- Stats Grid -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                            <div class="p-card" style="padding: 20px; text-align: center; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">Điểm TB (30 ngày)</div>
                                <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ number_format($averageScore, 1) }}<span style="font-size: 0.9rem; color: #94a3b8; font-weight: 500;">/10</span></div>
                            </div>
                            <div class="p-card" style="padding: 20px; text-align: center; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">Bài thi đã làm</div>
                                <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $stats['tong_so_bai'] ?? 0 }}</div>
                            </div>
                            <div class="p-card" style="padding: 20px; text-align: center; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">Thời gian học</div>
                                <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $studyHoursLabel }}</div>
                            </div>
                            <div class="p-card" style="padding: 20px; text-align: center; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <div style="color: #64748b; font-size: 0.85rem; margin-bottom: 8px;">Hoàn thành</div>
                                <div style="font-size: 2rem; font-weight: 700; color: #1e293b;">{{ $completionRate }}%</div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="charts-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            <div class="p-card" style="padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <h3 style="font-size: 1rem; color: #1e293b; margin-top: 0; margin-bottom: 20px;"><i class="fas fa-chart-line" style="color: #4f46e5; margin-right: 8px;"></i> Tiến độ điểm số</h3>
                                <div class="chart-wrap" style="height: 300px;">
                                    <canvas id="progressChart"></canvas>
                                </div>
                            </div>
                            <div class="p-card" style="padding: 20px; border-radius: 12px; border: 1px solid #f1f5f9;">
                                <h3 style="font-size: 1rem; color: #1e293b; margin-top: 0; margin-bottom: 20px;"><i class="fas fa-chart-pie" style="color: #4f46e5; margin-right: 8px;"></i> Phân bổ học tập</h3>
                                <div class="chart-wrap" style="height: 300px;">
                                    <canvas id="subjectDistributionChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-tab-content" id="tab-progress">
                        <div class="p-card" style="padding: 24px; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <h3 style="font-size: 1.2rem; color: #1e293b; margin-top: 0; margin-bottom: 20px;"><i class="fas fa-tasks" style="color: #4f46e5; margin-right: 8px;"></i> Tiến độ theo học phần</h3>
                            
                            <div class="subject-prog-list" style="display: flex; flex-direction: column; gap: 20px;">
                                @forelse ($tienDoMonHoc->where('has_progress', true) as $item)
                                    @php
                                        $progressPercent = is_numeric($item['phan_tram'] ?? null) ? max(0, min(100, (float) $item['phan_tram'])) : 0;
                                    @endphp
                                    <div class="subject-card">
                                        <!-- Subject Title & Learning Progress Header -->
                                        <div>
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                <div style="display: flex; align-items: center; gap: 12px;">
                                                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);">
                                                        <i class="fas fa-graduation-cap"></i>
                                                    </div>
                                                    <div>
                                                        <h4 style="margin: 0; color: #1e293b; font-size: 1.1rem; font-weight: 600;">{{ $item['ten_mon_hoc'] }}</h4>
                                                        <p style="margin: 4px 0 0; color: #64748b; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                                            <i class="fas fa-book-reader" style="color: #6366f1;"></i>
                                                            Tiến độ bài học: <strong style="color: #334155;">{{ $item['bai_da_xong'] }}/{{ $item['tong_bai'] }}</strong> bài học hoàn thành
                                                        </p>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <div style="font-weight: 700; color: #4f46e5; font-size: 1.15rem;">{{ $progressPercent }}%</div>
                                                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 500; margin-top: 2px;">Hoàn thành học phần</div>
                                                </div>
                                            </div>
                                            <!-- Progress Bar for Learning Progress -->
                                            <div style="height: 10px; background: #f1f5f9; border-radius: 5px; overflow: hidden; position: relative;">
                                                <div style="height: 100%; width: {{ $progressPercent }}%; background: linear-gradient(90deg, #4f46e5, #6366f1); border-radius: 5px; transition: width 0.5s ease-in-out;"></div>
                                            </div>
                                        </div>

                                        <!-- Nested Practice Progress Block -->
                                        <div class="practice-nest">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="font-size: 0.85rem; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 6px;">
                                                    <i class="fas fa-dumbbell" style="color: #8b5cf6;"></i>
                                                    Tiến độ luyện tập môn học
                                                </span>
                                                <span style="font-size: 0.85rem; font-weight: 700; color: #8b5cf6;">{{ $item['tien_do_luyen_tap_percent'] }}%</span>
                                            </div>
                                            <!-- Practice Progress Bar -->
                                            <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                                <div style="height: 100%; width: {{ $item['tien_do_luyen_tap_percent'] }}%; background: linear-gradient(90deg, #8b5cf6, #a78bfa); border-radius: 3px; transition: width 0.5s ease-in-out;"></div>
                                            </div>
                                            <!-- Practice Stats Details -->
                                            <div style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                                <span style="display: flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-history" style="color: #94a3b8;"></i>
                                                    Số phiên: <strong style="color: #475569;">{{ $item['so_phien_luyen_tap'] }}</strong>
                                                </span>
                                                <span style="display: flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-check-circle" style="color: #94a3b8;"></i>
                                                    Số câu đã làm: <strong style="color: #475569;">{{ $item['tong_cau_luyen_tap'] }}</strong>
                                                </span>
                                                <span style="display: flex; align-items: center; gap: 4px;">
                                                    <i class="fas fa-star" style="color: #fbbf24;"></i>
                                                    Điểm trung bình: <strong style="color: #475569;">{{ $item['diem_tb_luyen_tap'] }}/10</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align: center; padding: 40px; color: #64748b;">
                                        Chưa có dữ liệu tiến độ.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="profile-tab-content" id="tab-results">
                        <div class="p-card" style="padding: 24px; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <h3 style="font-size: 1.2rem; color: #1e293b; margin-top: 0; margin-bottom: 20px;"><i class="fas fa-chart-bar" style="color: #4f46e5; margin-right: 8px;"></i> Kết quả thi gần đây</h3>
                            
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #f1f5f9; text-align: left;">
                                        <th style="padding: 12px; color: #64748b; font-weight: 600;">Đề thi</th>
                                        <th style="padding: 12px; color: #64748b; font-weight: 600;">Ngày thi</th>
                                        <th style="padding: 12px; color: #64748b; font-weight: 600;">Điểm số</th>
                                        <th style="padding: 12px; color: #64748b; font-weight: 600;">Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($ketQuaThis as $kq)
                                        @php
                                            $examScore = is_numeric($kq->diem ?? null) ? (float) $kq->diem : 0;
                                        @endphp
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td style="padding: 12px;">
                                                <strong style="color: #1e293b;">{{ data_get($kq, 'baiKiemTra.ten_bai', '-') }}</strong>
                                            </td>
                                            <td style="padding: 12px; color: #64748b;">{{ $kq->thoi_gian_nop_bai?->format('d/m/Y') }}</td>
                                            <td style="padding: 12px;">
                                                <span style="display: inline-block; padding: 4px 10px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; {{ $examScore >= 5 ? 'background: #dcfce7; color: #166534;' : 'background: #fee2e2; color: #991b1b;' }}">
                                                    {{ number_format($examScore, 1) }}
                                                </span>
                                            </td>
                                            <td style="padding: 12px;">
                                                <a href="{{ route('client.exams.result', $kq->id) }}" style="color: #4f46e5; text-decoration: none; font-weight: 500; font-size: 0.9rem;">Xem <i class="fas fa-arrow-right"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">Chưa có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="profile-tab-content" id="tab-achievements">
                        <div class="p-card" style="padding: 24px; border-radius: 12px; border: 1px solid #f1f5f9;">
                            <h3 style="font-size: 1.2rem; color: #1e293b; margin-top: 0; margin-bottom: 20px;"><i class="fas fa-award" style="color: #4f46e5; margin-right: 8px;"></i> Thành tích & Huy hiệu</h3>
                            
                            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <!-- Rank Block -->
                                <div style="flex: 1; min-width: 250px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border-radius: 12px; padding: 20px;">
                                    <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px;"><i class="fas fa-trophy"></i> Vị thế của bạn</div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                        <span>Xếp hạng:</span>
                                        <span style="font-weight: 800; font-size: 1.2rem;">#{{ $comparison['rank'] ?? 1 }}/{{ $comparison['total_users'] ?? 1 }}</span>
                                    </div>
                                    <div style="font-size: 12px; opacity: 0.8; margin-bottom: 5px;">So với trung bình hệ thống:</div>
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                        <div style="flex-grow: 1; height: 6px; background: rgba(255,255,255,0.2); border-radius: 3px;">
                                            <div style="width: {{ $comparison['system_avg_progress'] ?? 0 }}%; height: 100%; background: #fff; border-radius: 3px; position: relative;">
                                                <div style="position: absolute; right: -5px; top: -5px; width: 10px; height: 10px; background: #fbbf24; border-radius: 50%; border: 2px solid #fff;" title="Trung bình"></div>
                                            </div>
                                        </div>
                                        <span style="font-size: 11px;">{{ $comparison['system_avg_progress'] ?? 0 }}%</span>
                                    </div>
                                </div>

                                <!-- Badges Block -->
                                <div style="flex: 1; min-width: 250px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;">
                                    <div style="font-size: 1.1rem; font-weight: 600; margin-bottom: 15px; color: #1e293b;"><i class="fas fa-medal"></i> Huy hiệu</div>
                                    <div style="display: flex; gap: 15px;">
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;" title="Học viên mới">
                                            <i class="fas fa-seedling"></i>
                                        </div>
                                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;" title="Chưa mở khóa">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switching Logic
            const tabLinks = document.querySelectorAll('.h-tab-link');
            tabLinks.forEach((link) => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tab = link.dataset.tab;
                    tabLinks.forEach((l) => l.classList.remove('active'));
                    document.querySelectorAll('.profile-tab-content').forEach((c) => c.classList.remove('active'));
                    link.classList.add('active');
                    document.getElementById('tab-' + tab)?.classList.add('active');
                });
            });

            // Charts
            const MON_LABELS = @json($monData->keys() ?? []);
            const MON_DATA = @json($monData->values() ?? []);
            const WEEK_LABELS = @json($weekLabels ?? []);
            const WEEK_SCORES = @json($weekScores ?? []);

            new Chart(document.getElementById('subjectDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: MON_LABELS.length ? MON_LABELS : ['Chưa có dữ liệu'],
                    datasets: [{
                        data: MON_DATA.length ? MON_DATA : [1],
                        backgroundColor: ['#6366f1', '#22c55e', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            new Chart(document.getElementById('progressChart'), {
                type: 'line',
                data: {
                    labels: WEEK_LABELS,
                    datasets: [{
                        label: 'Điểm Trung Bình',
                        data: WEEK_SCORES,
                        borderColor: '#4f46e5',
                        borderWidth: 3,
                        backgroundColor: (context) => {
                            const ctx = context.chart.ctx;
                            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
                            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.05)');
                            return gradient;
                        },
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f46e5',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, max: 10 }
                    }
                }
            });
        });
    </script>
@endsection
