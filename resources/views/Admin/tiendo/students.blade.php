@extends('Admin.layouts.admin')
@section('title', 'Quản lý Tiến độ - Chọn học sinh')

@section('content')
<div class="bf-container" style="padding: 24px; max-width: 1400px; margin: 0 auto;">
    <!-- Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 800; color: #1e293b; margin: 0;">Tiến độ học tập</h1>
            <p style="color: #64748b; font-size: 16px; margin-top: 4px;">Chọn một học sinh để phân tích tiến độ chi tiết.</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <a href="{{ route('admin.tiendo.global') }}" class="btn-premium">
                <i class="fas fa-chart-bar"></i> Xem tiến độ tổng hợp
            </a>
        </div>
    </div>

    <!-- User Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
        @forelse($users as $user)
            <div class="student-card">
                <div class="card-top">
                    <div class="avatar-wrap">
                        <div class="avatar-circle">
                            {{ strtoupper(substr($user->ho_ten ?? $user->name, 0, 1)) }}
                        </div>
                    </div>
                    <div class="user-info">
                        <h3 class="user-name text-truncate" title="{{ $user->ho_ten ?? $user->name }}">{{ $user->ho_ten ?? $user->name }}</h3>
                        <p class="user-email text-truncate">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="card-progress">
                    <div class="progress-label">
                        <span>Tiến độ hoàn thành</span>
                        <span class="progress-value">{{ $user->overall_progress }}%</span>
                    </div>
                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $user->overall_progress }}%"></div>
                    </div>
                </div>

                <div class="card-stats">
                    <div class="stat-item">
                        <div class="stat-value">{{ $user->avg_practice_score }}</div>
                        <div class="stat-label">Điểm TB</div>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <div class="stat-value">
                            @php
                                $completedCount = \App\Models\TienDoBaiHoc::where('user_id', $user->id)->where('trang_thai', 2)->count();
                            @endphp
                            {{ $completedCount }}
                        </div>
                        <div class="stat-label">Bài đã xong</div>
                    </div>
                </div>

                <div class="card-actions">
                    <a href="{{ route('admin.tiendo.index', ['user_id' => $user->id]) }}" class="btn-detail">
                        Phân tích chi tiết <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
                <div style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"><i class="fas fa-users-slash"></i></div>
                <p style="color: #64748b; font-size: 18px; font-weight: 500;">Chưa có học sinh nào tham gia hệ thống.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top: 40px; display: flex; justify-content: center;">
        {{ $users->links() }}
    </div>
</div>

<style>
    /* Premium Design System */
    .btn-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        border: none;
    }
    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .student-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .card-top {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-weight: 800;
        font-size: 24px;
        border: 2px solid #fff;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .user-name {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        max-width: 180px;
    }
    .user-email {
        font-size: 13px;
        color: #64748b;
        margin: 4px 0 0 0;
        max-width: 180px;
    }

    .card-progress {
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
    }
    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .progress-value {
        color: #4f46e5;
    }
    .progress-bar-bg {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #4f46e5, #818cf8);
        border-radius: 3px;
        transition: width 1s ease-out;
    }

    .card-stats {
        display: flex;
        justify-content: space-between;
        padding: 0 8px;
    }
    .stat-item {
        text-align: center;
        flex: 1;
    }
    .stat-value {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
    }
    .stat-label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 4px;
    }
    .stat-divider {
        width: 1px;
        background: #f1f5f9;
        margin: 4px 0;
    }

    .btn-detail {
        display: block;
        width: 100%;
        padding: 12px;
        background: #f1f5f9;
        color: #4f46e5;
        text-align: center;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.2s;
        font-size: 14px;
    }
    .btn-detail:hover {
        background: #4f46e5;
        color: white;
    }
</style>
@endsection
