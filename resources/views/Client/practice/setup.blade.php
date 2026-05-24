{{-- resources/views/Client/practice/setup.blade.php --}}
@extends('Client.layouts.app')

@section('title', 'Thiết lập Luyện tập - ' . $monHoc->ten_mon_hoc)

@push('styles')
    <style>
        
        .practice-page-header {
            background: #fff;
           /*  padding: 48px 0 52px; */
            position: relative;
            overflow: hidden;
        }

        .practice-page-header::before {
            content: "";
            position: absolute;
            top: -80px;
            right: -60px;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .practice-page-title {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }

        .practice-page-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--green), var(--blue));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.35);
        }

        .practice-page-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #000;
            letter-spacing: -0.4px;
        }

        .practice-page-header p {
            color: #000;
            font-size: 0.97rem;
            margin-left: 0;
        }

        /* Layout */
        .practice-section {
            padding: 40px 0 80px;
        }

        .practice-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 28px;
            align-items: start;
        }

        /* Sidebar */
        .practice-sidebar {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2eaf6;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: sticky;
            top: 140px;
        }

        .practice-sidebar-top {
            background: linear-gradient(135deg, var(--navy-dark), var(--navy));
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .practice-sidebar-icon {
            font-size: 1.8rem;
            color: var(--white);
        }

        .practice-sidebar-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .practice-sidebar-top h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            margin: 0;
            text-align: left;
        }

        .practice-sidebar-top p {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            text-align: left;
        }

        .subject-pick-list {
            padding: 16px 0;
        }

        .subject-pick-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
        }

        .subject-pick-item.active {
            background: linear-gradient(90deg, #eff6ff 0%, #fff 100%);
            border-left-color: var(--blue);
        }

        .pick-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: all 0.25s;
        }

        .subject-pick-item.active .pick-icon {
            transform: scale(1.05);
        }

        .pick-info h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .subject-pick-item.active .pick-info h4 {
            color: var(--blue);
        }

        .pick-info p {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Main Card */
        .settings-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .settings-card:hover {
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.05);
        }

        .card-header-custom {
            padding: 32px 32px 0;
        }

        .card-header-custom h3 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header-custom h3 i {
            color: var(--blue);
            font-size: 1.4rem;
        }

        .card-header-custom p {
            font-size: 0.9rem;
            color: #64748b;
            margin-top: 8px;
            margin-bottom: 0;
        }

        .settings-form {
            padding: 32px;
        }

        /* Form Elements */
        .setting-group {
            margin-bottom: 28px;
        }

        .setting-label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .setting-label i {
            color: var(--blue);
            font-size: 1rem;
        }

        .custom-select {
            width: 100%;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.95rem;
            color: #334155;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            transition: all 0.2s;
        }

        .custom-select:hover,
        .custom-select:focus {
            border-color: var(--blue);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Grid Layout cho 2 và 3 cột */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
        }

        /* Mode Options */
        .mode-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .mode-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px;
            cursor: pointer;
            background: #ffffff;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .mode-card.active {
            border-color: var(--blue);
            background: #f0f7ff;
        }

        .mode-card-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .mode-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .mode-icon-blue {
            background: #e0f2fe;
            color: var(--blue);
        }

        .mode-icon-purple {
            background: #f3e8ff;
            color: #a855f7;
        }

        .mode-title-wrap {
            flex: 1;
        }

        .mode-title {
            display: block;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .mode-card.active .mode-title {
            color: var(--blue);
        }

        .badge-recommend {
            background: #dcfce3;
            color: #166534;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            display: inline-block;
        }

        .mode-radio {
            font-size: 1.4rem;
            color: #cbd5e1;
            margin-top: -4px;
        }

        .mode-card .active-icon { display: none; }
        .mode-card .inactive-icon { display: block; }

        .mode-card.active .mode-radio { color: var(--blue); }
        .mode-card.active .active-icon { display: block; }
        .mode-card.active .inactive-icon { display: none; }

        .mode-desc {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .mode-features {
            list-style: none;
            padding: 0;
            margin: 0;
            margin-top: auto;
        }

        .mode-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 12px;
        }

        .mode-features li:last-child {
            margin-bottom: 0;
        }

        .mode-features li i {
            font-size: 1.1rem;
        }

        /* Tip box */
        .tip-box {
            background: #f0f7ff;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 32px;
        }

        .tip-box i {
            color: var(--blue);
            font-size: 1.4rem;
            margin-top: 2px;
        }

        .tip-box p {
            margin: 0;
            font-size: 0.9rem;
            color: var(--blue);
            line-height: 1.5;
        }

        .tip-box p strong {
            font-weight: 700;
        }

        /* Submit Action Wrap */
        .submit-action-wrap {
            text-align: center;
            padding-top: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-effect {
            position: relative;
            display: inline-block;
        }

        .action-effect::before,
        .action-effect::after {
            content: '';
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%233b82f6' stroke-width='2' stroke-linecap='round'%3E%3Cpath d='M1 12h4M19 12h4M4 4l3 3M17 17l3 3M4 20l3-3M17 7l3-3'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            opacity: 0.5;
        }

        .action-effect::before {
            left: -40px;
        }

        .action-effect::after {
            right: -40px;
            transform: translateY(-50%) scaleX(-1);
        }

        .btn-submit-pill {
            background: var(--blue);
            color: #fff;
            border: none;
            border-radius: 9999px;
            padding: 16px 40px;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
        }

        .btn-submit-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(37, 99, 235, 0.35);
            background: #2563eb;
        }

        .btn-submit-pill i:first-child {
            background: #fff;
            color: var(--blue);
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .submit-subtext {
            margin-top: 16px;
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Alert Styles */
        .alert-setup {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger-setup {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
        }

        .alert-danger-setup i {
            font-size: 1.1rem;
        }

        .alert-danger-setup ul {
            margin: 0;
            padding-left: 20px;
        }

        /* Info Chip */
        .info-chip {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 12px 16px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.8rem;
            color: #475569;
        }

        .info-chip i {
            color: var(--blue);
            font-size: 1rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .practice-layout {
                grid-template-columns: 280px 1fr;
                gap: 24px;
            }
        }

        @media (max-width: 900px) {
            .practice-layout {
                grid-template-columns: 1fr;
            }

            .practice-sidebar {
                position: static;
                margin-bottom: 20px;
            }

            .practice-page-header p {
                margin-left: 0;
            }

            .practice-page-title {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 640px) {
            .practice-page-header h1 {
                font-size: 1.5rem;
            }

            .form-grid-2, .form-grid-3 {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .mode-options {
                grid-template-columns: 1fr;
            }

            .settings-form {
                padding: 20px;
            }

            .card-header-custom {
                padding: 20px 20px 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="practice-page-header">
        <div class="container">
            <div class="breadcrumb-transparent">
                @php
                    $breadcrumbs = [
                        ['label' => 'Trang chủ', 'url' => route('home')],
                        ['label' => 'Luyện tập', 'url' => route('client.practice.index')],
                        ['label' => 'Thiết lập']
                    ];
                @endphp
                @include('Client.layouts.partials.breadcrumb', ['breadcrumbs' => $breadcrumbs])
            </div>
            <div class="practice-page-title">
                <div class="practice-page-icon">
                    <i class="fas fa-sliders-h"></i>
                </div>
                <div>
                    <h1>Thiết lập Luyện tập</h1>
                    <p>Tùy chỉnh phạm vi kiến thức và mức độ câu hỏi để đạt hiệu quả ôn tập tốt nhất</p>
                </div>
            </div>
        </div>
    </div>

    <section class="practice-section">
        <div class="container">
            <div class="practice-layout">
                {{-- Sidebar --}}
                <aside class="practice-sidebar">
                    <div class="practice-sidebar-top">
                        <i class="fas fa-graduation-cap practice-sidebar-icon"></i>
                        <div class="practice-sidebar-text">
                            <h3>Học phần hiện tại</h3>
                            <p>Bạn đang luyện tập với môn học</p>
                        </div>
                    </div>
                    <div class="subject-pick-list">
                        <div class="subject-pick-item active">
                            <div class="pick-icon"
                                style="background: {{ $monHoc->mau_sac ?? '#3b82f6' }}20; color: {{ $monHoc->mau_sac ?? '#3b82f6' }}">
                                <i class="{{ $monHoc->icon_class ?? 'fas fa-book' }}"></i>
                            </div>
                            <div class="pick-info">
                                <h4>{{ $monHoc->ten_mon_hoc }}</h4>
                                <p>
                                    <i class="fas fa-layer-group"></i> {{ $monHoc->chuongHocs->count() }} chương kiến thức
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #e2e8f0; padding: 20px;">
                        <div class="info-chip" style="margin: 0;">
                            <i class="fas fa-lightbulb"></i>
                            <span>Chọn chế độ phù hợp để tối ưu hiệu quả học tập. Chế độ "Vừa học vừa làm" phù hợp với người mới
                                bắt đầu.</span>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="practice-main">
                    <div class="settings-card">
                        <div class="card-header-custom">
                            <h3>
                                <i class="fas fa-cog"></i>
                                Tùy chỉnh tham số luyện tập
                            </h3>
                            <p>Điều chỉnh các thông số dưới đây để tạo bộ câu hỏi phù hợp với nhu cầu của bạn.</p>
                        </div>

                        <form action="{{ route('client.practice.generate') }}" method="POST" class="settings-form">
                            @csrf
                            <input type="hidden" name="mon_hoc_id" value="{{ $monHoc->id }}">

                            {{-- Phạm vi kiến thức --}}
                            <div class="setting-group">
                                <div class="setting-label">
                                    <i class="fas fa-book-open"></i>
                                    <span>Phạm vi kiến thức</span>
                                </div>
                                <select name="chuong_id" class="custom-select" required>
                                    <option value="all">Tất cả các chương ({{ $monHoc->chuongHocs->count() }} chương)
                                    </option>
                                    @foreach ($monHoc->chuongHocs as $chuong)
                                        <option value="{{ $chuong->id }}"
                                            {{ old('chuong_id') == $chuong->id ? 'selected' : '' }}>
                                            {{ Str::limit($chuong->ten_chuong, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Mức độ, Loại câu hỏi và Số lượng --}}
                            <div class="form-grid-3">
                                <div class="setting-group">
                                    <div class="setting-label">
                                        <i class="fas fa-chart-line"></i>
                                        <span>Mức độ câu hỏi</span>
                                    </div>
                                    <select name="muc_do" class="custom-select" required>
                                        <option value="all">Ngẫu nhiên (Trộn đều)</option>
                                        <option value="1" {{ old('muc_do') == '1' ? 'selected' : '' }}>Dễ - Cơ bản</option>
                                        <option value="2" {{ old('muc_do') == '2' ? 'selected' : '' }}>Trung bình - Vận dụng</option>
                                        <option value="3" {{ old('muc_do') == '3' ? 'selected' : '' }}>Khó - Nâng cao</option>
                                    </select>
                                </div>

                                <div class="setting-group">
                                    <div class="setting-label">
                                        <i class="fas fa-tags"></i>
                                        <span>Loại câu hỏi</span>
                                    </div>
                                    <select name="loai_cau_hoi" class="custom-select" required>
                                        <option value="all">Tất cả các loại</option>
                                        <option value="objective" {{ old('loai_cau_hoi') == 'objective' ? 'selected' : '' }}>Trắc nghiệm & Đúng/Sai</option>
                                        <option value="subjective" {{ old('loai_cau_hoi') == 'subjective' ? 'selected' : '' }}>Tự luận & Điền khuyết</option>
                                    </select>
                                </div>

                                <div class="setting-group">
                                    <div class="setting-label">
                                        <i class="fas fa-list-ol"></i>
                                        <span>Số lượng câu hỏi</span>
                                    </div>
                                    <select name="so_luong" class="custom-select" required>
                                        <option value="5" {{ old('so_luong') == '5' ? 'selected' : '' }}>5 câu (Khởi động)</option>
                                        <option value="10" {{ old('so_luong') == '10' ? 'selected' : '' }}>10 câu (Luyện nhanh)</option>
                                        <option value="20" {{ old('so_luong', '20') == '20' ? 'selected' : '' }}>20 câu (Tiêu chuẩn)</option>
                                        <option value="40" {{ old('so_luong') == '40' ? 'selected' : '' }}>40 câu (Ôn tập chuyên sâu)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Chế độ luyện tập --}}
                            <div class="setting-group">
                                <div class="setting-label">
                                    <i class="fas fa-toggle-on"></i>
                                    <span>Chế độ luyện tập</span>
                                </div>
                                <div class="mode-options">
                                    <label class="mode-card {{ old('che_do', 'learn') == 'learn' ? 'active' : '' }}" id="mode-learn-card">
                                        <input type="radio" name="che_do" value="learn" {{ old('che_do', 'learn') == 'learn' ? 'checked' : '' }} style="display: none;">
                                        
                                        <div class="mode-card-header">
                                            <div class="mode-icon mode-icon-blue">
                                                <i class="fas fa-graduation-cap"></i>
                                            </div>
                                            <div class="mode-title-wrap">
                                                <strong class="mode-title">Vừa học vừa làm</strong>
                                                <span class="badge-recommend">Khuyên dùng</span>
                                            </div>
                                            <div class="mode-radio">
                                                <i class="fas fa-check-circle active-icon"></i>
                                                <i class="far fa-circle inactive-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <p class="mode-desc">Xem đáp án và giải thích ngay sau mỗi câu hỏi, phù hợp để học và hiểu sâu kiến thức.</p>
                                        
                                        <ul class="mode-features">
                                            <li><i class="fas fa-check-circle" style="color: var(--blue);"></i> Xem đáp án ngay lập tức</li>
                                            <li><i class="fas fa-check-circle" style="color: var(--blue);"></i> Giải thích chi tiết cho từng câu</li>
                                            <li><i class="fas fa-check-circle" style="color: var(--blue);"></i> Theo dõi tiến trình học tập</li>
                                        </ul>
                                    </label>

                                    <label class="mode-card {{ old('che_do') == 'test' ? 'active' : '' }}" id="mode-test-card">
                                        <input type="radio" name="che_do" value="test" {{ old('che_do') == 'test' ? 'checked' : '' }} style="display: none;">
                                        
                                        <div class="mode-card-header">
                                            <div class="mode-icon mode-icon-purple">
                                                <i class="fas fa-stopwatch"></i>
                                            </div>
                                            <div class="mode-title-wrap">
                                                <strong class="mode-title">Làm như thi thật</strong>
                                            </div>
                                            <div class="mode-radio">
                                                <i class="fas fa-check-circle active-icon"></i>
                                                <i class="far fa-circle inactive-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <p class="mode-desc">Hoàn thành toàn bộ bài thi, chấm điểm và đánh giá sau khi nộp bài, tạo cảm giác như thi thực tế.</p>
                                        
                                        <ul class="mode-features">
                                            <li><i class="fas fa-cog" style="color: #a855f7;"></i> Không xem đáp án trong quá trình làm bài</li>
                                            <li><i class="fas fa-cog" style="color: #a855f7;"></i> Chấm điểm và đánh giá cuối bài</li>
                                            <li><i class="fas fa-cog" style="color: #a855f7;"></i> Rèn luyện kỹ năng làm bài thi</li>
                                        </ul>
                                    </label>
                                </div>
                            </div>

                            <div class="tip-box">
                                <i class="far fa-lightbulb"></i>
                                <p><strong>Mẹo:</strong> Bạn nên chọn chế độ "Vừa học vừa làm" khi mới bắt đầu để nắm vững kiến thức, sau đó chuyển sang "Làm như thi thật" để kiểm tra năng lực.</p>
                            </div>

                            <div class="submit-action-wrap">
                                <div class="action-effect">
                                    <button type="submit" class="btn-submit-pill">
                                        <i class="fas fa-play"></i> Bắt đầu luyện tập <i class="fas fa-arrow-right" style="font-size: 14px; margin-left: 4px;"></i>
                                    </button>
                                </div>
                                <p class="submit-subtext">Tạo đề và bắt đầu làm bài ngay</p>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function changeMode(mode) {
            const learnCard = document.getElementById('mode-learn-card');
            const testCard = document.getElementById('mode-test-card');

            if (mode === 'learn') {
                learnCard.classList.add('active');
                testCard.classList.remove('active');
            } else {
                testCard.classList.add('active');
                learnCard.classList.remove('active');
            }
        }

        // Thêm event listeners cho các radio buttons
        document.querySelectorAll('input[name="che_do"]').forEach(radio => {
            radio.addEventListener('change', function() {
                changeMode(this.value);
            });
        });
    </script>
@endpush
