@extends('Client.layouts.app')

@section('title', 'Ho so hoc tap - IT Study Support')

@section('content')
    @php
        $completionRate = is_numeric($stats['ty_le_hoan_thanh'] ?? null) ? (float) $stats['ty_le_hoan_thanh'] : 0;
        $averageScore = is_numeric($stats['diem_trung_binh'] ?? null) ? (float) $stats['diem_trung_binh'] : 0;
        $studyHours = is_numeric($stats['tong_gio_hoc_tap'] ?? null) ? (float) $stats['tong_gio_hoc_tap'] : 0;
        $totalQuestions = is_numeric($stats['tong_cau_da_lam'] ?? null) ? (int) $stats['tong_cau_da_lam'] : 0;

        // Chuyển tổng giờ học (số thực) thành định dạng "Xh Yp"
        $studyHoursInt = floor($studyHours);
        $studyMinutes  = round(($studyHours - $studyHoursInt) * 60);
        $studyHoursLabel = $studyHoursInt . 'h' . ($studyMinutes > 0 ? ' ' . $studyMinutes . 'p' : '');
    @endphp

    <section class="profile-section">
        <div class="container">
            <div class="profile-layout">
                <aside class="profile-sidebar">
                    <div class="profile-avatar-block">
                        <div class="profile-avatar-wrap">
                            @if (Auth::user()->avatar_url)
                                <img src="{{ Storage::url(Auth::user()->avatar_url) }}" alt="Avatar" />
                            @else
                                <div style="width:84px; height:84px; border-radius:50%; background:rgba(255,255,255,0.25); border:3px solid rgba(255,255,255,0.25); display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:800; color:#fff; margin:0 auto;">
                                    {{ mb_strtoupper(mb_substr(Auth::user()->ho_ten, 0, 2)) }}
                                </div>
                            @endif
                            <a href="{{ route('profile.index') }}" class="avatar-edit-btn" title="Cap nhat thong tin">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                        </div>
                        <div class="profile-name">{{ Auth::user()->ho_ten }}</div>
                        <div class="profile-msv">Ma SV: {{ Auth::user()->ma_sv }}</div>
                        <div class="profile-faculty">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="profile-quick-stats">
                        <div class="pqs-item">
                            <span class="pqs-value">{{ $completionRate }}%</span>
                            <span class="pqs-label">Hoan thanh</span>
                        </div>
                        <div class="pqs-item">
                            <span class="pqs-value">{{ number_format($averageScore, 1) }}</span>
                            <span class="pqs-label">Diem TB</span>
                        </div>
                        <div class="pqs-item">
                            <span class="pqs-value">{{ $studyHoursLabel }}</span>
                            <span class="pqs-label">Hoc tap</span>
                        </div>
                    </div>

                    <nav class="profile-side-nav">
                        <a href="#" class="active" data-tab="dashboard">
                            <i class="fas fa-chart-line"></i> Tong quan
                        </a>
                        <a href="#" data-tab="progress">
                            <i class="fas fa-tasks"></i> Tien do hoc tap
                        </a>
                        <a href="#" data-tab="results">
                            <i class="fas fa-chart-bar"></i> Ket qua
                        </a>
                        <a href="#" data-tab="achievements">
                            <i class="fas fa-award"></i> Thanh tich
                        </a>
                    </nav>

                    <div class="profile-badges-block" style="margin-top: 20px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none;">
                        <div class="profile-badges-title" style="color: white; border-bottom-color: rgba(255,255,255,0.1);">
                            <i class="fas fa-trophy"></i> Vi the cua ban
                        </div>
                        <div style="padding: 15px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span>Xep hang:</span>
                                <span style="font-weight: 800; font-size: 1.2rem;">#{{ $comparison['rank'] }}/{{ $comparison['total_users'] }}</span>
                            </div>
                            <div style="font-size: 12px; opacity: 0.8; margin-bottom: 5px;">So voi trung binh he thong:</div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <div style="flex-grow: 1; height: 6px; background: rgba(255,255,255,0.2); border-radius: 3px;">
                                    <div style="width: {{ $comparison['system_avg_progress'] }}%; height: 100%; background: #fff; border-radius: 3px; position: relative;">
                                        <div style="position: absolute; right: -5px; top: -5px; width: 10px; height: 10px; background: #fbbf24; border-radius: 50%; border: 2px solid #fff;" title="Trung binh"></div>
                                    </div>
                                </div>
                                <span style="font-size: 11px;">{{ $comparison['system_avg_progress'] }}%</span>
                            </div>
                            <div style="font-size: 11px; font-style: italic;">Bạn đang {{ $stats['ty_le_hoan_thanh'] >= $comparison['system_avg_progress'] ? 'cao hơn' : 'thấp hơn' }} mức trung bình.</div>
                        </div>
                    </div>

                    <div class="profile-badges-block">
                        <div class="profile-badges-title">
                            <i class="fas fa-medal"></i> Huy hieu dat duoc
                        </div>
                        <div class="badges-row">
                            <div class="badge-chip" title="Hoc vien moi">
                                <i class="fas fa-seedling"></i>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" style="display:block;">
                        @csrf
                        <button type="submit" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Dang xuat
                        </button>
                    </form>
                </aside>

                <main class="profile-main">
                    <div class="profile-tab-content active" id="tab-dashboard">
                        <div class="profile-section-heading">
                            <h1><i class="fas fa-chart-line"></i> Tong quan hoc tap</h1>
                            <p>Thong ke chi tiet qua trinh hoc tap cua ban</p>
                        </div>

                        <div class="profile-stats-grid">
                            <div class="profile-stat-card">
                                <div class="psc-icon blue"><i class="fas fa-book-open"></i></div>
                                <div>
                                    <div class="psc-value">{{ $stats['tong_so_bai'] ?? 0 }}</div>
                                    <div class="psc-label">Bai thi da lam</div>
                                </div>
                            </div>
                            <div class="profile-stat-card">
                                <div class="psc-icon green"><i class="fas fa-question-circle"></i></div>
                                <div>
                                    <div class="psc-value">{{ number_format($totalQuestions) }}</div>
                                    <div class="psc-label">Cau hoi da lam</div>
                                </div>
                            </div>
                            <div class="profile-stat-card">
                                <div class="psc-icon gold"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="psc-value">{{ $studyHoursLabel }}</div>
                                    <div class="psc-label">Tong thoi gian hoc</div>
                                </div>
                            </div>
                            <div class="profile-stat-card">
                                <div class="psc-icon purple"><i class="fas fa-chart-line"></i></div>
                                <div>
                                    <div class="psc-value">{{ number_format($averageScore, 1) }}</div>
                                    <div class="psc-label">Diem trung binh</div>
                                </div>
                            </div>
                        </div>

                        <div class="charts-row">
                            <div class="chart-card">
                                <h3><i class="fas fa-chart-pie"></i> Phan bo hoc tap</h3>
                                <div class="chart-wrap">
                                    <canvas id="subjectDistributionChart"></canvas>
                                </div>
                            </div>
                            <div class="chart-card">
                                <h3><i class="fas fa-chart-line"></i> Tien do diem so</h3>
                                <div class="chart-wrap">
                                    <canvas id="progressChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-tab-content" id="tab-progress">
                        <div class="profile-section-heading">
                            <h1><i class="fas fa-tasks"></i> Tien do theo hoc phan</h1>
                            <p>Dua tren so luong cau hoi ban da luyen tap thuc te</p>
                        </div>

                        <div class="subject-prog-list">
                            @forelse ($tienDoMonHoc as $item)
                                @php
                                    $progressPercent = is_numeric($item['phan_tram'] ?? null) ? max(0, min(100, (float) $item['phan_tram'])) : 0;
                                @endphp
                                <div class="subject-prog-card">
                                    <div class="spc-top">
                                        <div class="spc-left">
                                            <div class="spc-icon icon-blue">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="spc-info">
                                                <h3>{{ $item['ten_mon_hoc'] }}</h3>
                                                <p>{{ $item['so_bai'] }} phien luyen tap</p>
                                            </div>
                                        </div>
                                        <div class="spc-pct">{{ $progressPercent }}%</div>
                                    </div>
                                    <div class="spc-bar">
                                        <div class="spc-bar-fill" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="subject-prog-card">
                                    <div class="spc-info">
                                        <h3>Chua co du lieu</h3>
                                        <p>Ban chua co phien luyen tap nao.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="profile-tab-content" id="tab-results">
                        <div class="profile-section-heading">
                            <h1><i class="fas fa-chart-bar"></i> Ket qua thi</h1>
                            <p>Thong ke lich su cac bai kiem tra truc tuyen</p>
                        </div>
                        <div class="results-card">
                            <div class="results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>De thi</th>
                                            <th>Ngay thi</th>
                                            <th>Diem so</th>
                                            <th>Thoi gian</th>
                                            <th>Chi tiet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ketQuaThis as $kq)
                                            @php
                                                $examScore = is_numeric($kq->diem ?? null) ? (float) $kq->diem : 0;
                                                $rawDuration = $kq->tong_thoi_gian_lam ?? 0;
                                                $totalSeconds    = is_numeric($rawDuration) ? (int) $rawDuration : 0;
                                                $durationMinutes = floor($totalSeconds / 60);
                                                $durationSeconds = $totalSeconds % 60;
                                                $durationLabel   = $durationMinutes . 'p'
                                                    . ($durationSeconds > 0 ? ' ' . $durationSeconds . 's' : '');
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ data_get($kq, 'baiKiemTra.ten_bai', '-') }}</strong>
                                                    <div style="font-size:11px; color:#94a3b8">
                                                        {{ data_get($kq, 'baiKiemTra.monHoc.ten_mon_hoc', '') }}
                                                    </div>
                                                </td>
                                                <td>{{ $kq->thoi_gian_nop_bai?->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="score-badge {{ $examScore >= 5 ? 'good' : 'low' }}">
                                                        {{ number_format($examScore, 1) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="time-badge">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ $durationLabel }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('client.exams.result', $kq->id) }}" class="btn-view-detail">
                                                        <span>Xem</span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" style="text-align:center">Chua co du lieu thi</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.profile-side-nav a[data-tab]');
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

            const MON_LABELS = @json($monData->keys() ?? []);
            const MON_DATA = @json($monData->values() ?? []);
            const WEEK_LABELS = @json($weekLabels ?? []);
            const WEEK_SCORES = @json($weekScores ?? []);

            new Chart(document.getElementById('subjectDistributionChart'), {
                type: 'doughnut',
                data: {
                    labels: MON_LABELS.length ? MON_LABELS : ['Chua co du lieu'],
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
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            new Chart(document.getElementById('progressChart'), {
                type: 'line',
                data: {
                    labels: WEEK_LABELS,
                    datasets: [{
                        label: 'Diem TB',
                        data: WEEK_SCORES,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        fill: true,
                        tension: 0.4
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10
                        }
                    }
                }
            });
        });
    </script>
@endsection
