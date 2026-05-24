@extends('Client.layouts.app')

@section('title', $monHoc->ten_mon_hoc . ' - IT Study Support')

@push('styles')
    <style>
        .ck-content p {
            margin: 0 0 1rem 0 !important;
            line-height: 1.8 !important;
        }

        .ck-content h2,
        .ck-content h3 {
            margin-top: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        .ck-content ul,
        .ck-content ol {
            margin-bottom: 1rem !important;
            padding-left: 1.5rem !important;
        }

        .ck-content img {
            max-width: 100% !important;
            border-radius: 12px !important;
            margin: 1rem 0 !important;
        }

        .ck-content blockquote {
            border-left: 4px solid var(--blue) !important;
            padding-left: 1rem !important;
            margin: 1rem 0 !important;
            font-style: italic !important;
            color: #64748b !important;
        }

        /* Remove old chapter accordion CSS from here */

        /* Remove old completed lessons CSS */

        /* Progress bar animation */
        .sidebar-prog-fill {
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── Nút "Đánh dấu hoàn thành" ── */
        .btn-complete-lesson {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-complete-lesson.pending {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .btn-complete-lesson.pending:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }

        .btn-complete-lesson.done {
            background: #f0fdf4;
            color: #10b981;
            border: 1.5px solid #bbf7d0;
            cursor: default;
        }

        /* ── Toast ── */
        .toast-notify {
            position: fixed;
            bottom: 80px;
            right: 30px;
            padding: 12px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideInRight 0.3s ease;
        }

        .toast-notify.success {
            background: #10b981;
            color: white;
        }

        .toast-notify.info {
            background: #3b82f6;
            color: white;
        }

        .toast-notify.error {
            background: #ef4444;
            color: white;
        }

        /* Quiz */
        .quiz-opt {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .quiz-opt:hover {
            transform: translateX(4px);
        }

        /* Code */
        .code-file-block {
            transition: all 0.3s ease;
        }

        .code-file-block:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Lesson animation */
        .lesson-card {
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        /* Scroll top */
        .scroll-top-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--blue), #1d4ed8);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .scroll-top-btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.5);
        }

        .scroll-top-btn.show {
            display: flex;
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 12px;
            background: #1e293b;
            color: white;
            font-size: 0.75rem;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
            margin-bottom: 8px;
            z-index: 1000;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
        }

        /* ── Video Block Premium Custom ── */
        .video-block {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            margin: 28px 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .video-block:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.07);
            border-color: #cbd5e1;
        }

        .video-block h3 {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0 !important;
            margin-bottom: 20px !important;
            padding-bottom: 14px;
            border-bottom: 2px solid #f1f5f9;
        }

        .video-block h3 i {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #ffe4e6 0%, #fecdd3 100%);
            color: #e11d48;
            border-radius: 10px;
            font-size: 1.1rem;
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            border: 4px solid #1e293b;
            background: #000000;
            aspect-ratio: 16 / 9;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .video-wrapper:hover {
            transform: scale(1.005);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        .video-wrapper iframe,
        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            outline: none;
        }

        .video-info-tip {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            padding: 12px 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 500;
            line-height: 1.5;
        }

        .video-info-tip i {
            color: #3b82f6;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* ── Document Block ── */
        .document-block {
            margin: 28px 0;
        }

        .document-block h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0 !important;
            margin-bottom: 16px !important;
        }

        .document-block h3 i {
            color: #3b82f6;
            font-size: 1.1rem;
        }

        .document-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
        }

        .document-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #ffffff;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }

        .document-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            transform: translateY(-2px);
        }

        .doc-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            font-size: 1.25rem;
            margin-right: 14px;
            flex-shrink: 0;
        }

        .doc-icon.pdf { background: #fee2e2; color: #ef4444; }
        .doc-icon.pptx { background: #ffedd5; color: #f97316; }
        .doc-icon.word { background: #e0f2fe; color: #0ea5e9; }
        .doc-icon.excel { background: #dcfce3; color: #10b981; }
        .doc-icon.zip { background: #f3e8ff; color: #8b5cf6; }
        .doc-icon.default { background: #f1f5f9; color: #64748b; }

        .doc-info {
            flex: 1;
            min-width: 0;
        }

        .doc-info h4 {
            margin: 0 0 4px 0;
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .doc-info span {
            font-size: 0.75rem;
            color: #64748b;
            display: block;
        }

        .doc-action {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .document-item:hover .doc-action {
            color: var(--blue);
        }

        @media (max-width: 640px) {
            .scroll-top-btn {
                bottom: 20px;
                right: 20px;
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .lesson-body {
                padding: 20px;
            }

            .quiz-opt {
                padding: 10px 14px;
            }

            .explain-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

    @php
        $color = $monHoc->mau_sac ?? '#3b82f6';
        $bgIcon = $color . '20';
        $hasImage = !empty($monHoc->hinh_anh);
        $levelText = 'Dễ';
        $levelIcon = '🌱';
        if ($monHoc->muc_do_mon_hoc == 2) {
            $levelText = 'Trung bình';
            $levelIcon = '📘';
        } elseif ($monHoc->muc_do_mon_hoc == 3) {
            $levelText = 'Khó';
            $levelIcon = '⚡';
        } elseif ($monHoc->muc_do_mon_hoc == 4) {
            $levelText = 'Rất khó';
            $levelIcon = '🔥';
        }

        // Gom tất cả bài học để tính prev/next
        $allLessons = collect();
        foreach ($monHoc->chuongHocs as $chuong) {
            foreach ($chuong->baiHocs as $bai) {
                $allLessons->push($bai);
            }
        }

        $currentIndex = 0;
        if ($activeBaiHoc) {
            $currentIndex = $allLessons->search(fn($item) => $item->id == $activeBaiHoc->id);
        }

        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        // Tiến độ thực sự từ controller
        $progressPercent = $progressPercent ?? 0;
        $baiHocDaXongIds = $baiHocDaXongIds ?? [];
        $tongBai = $allLessons->count();
        $soBaiDaXong = count($baiHocDaXongIds);
        $isCurrentDone = $activeBaiHoc && in_array($activeBaiHoc->id, $baiHocDaXongIds);
    @endphp

    {{-- ══ BREADCRUMB ══ --}}
    @include('Client.layouts.partials.breadcrumb')

    {{-- ══ SUBJECT HERO ══ --}}
    <div class="subject-hero"
        style="background: linear-gradient(135deg, #06132a 0%, {{ $color }}cc 60%, var(--navy-mid) 100%);">
        <div class="container">
            <div class="subject-hero-inner">
                <div class="subject-hero-left">
                    <div class="subject-hero-icon has-image"
                        style="color: {{ $color }}; background: transparent; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 2px solid {{ $color }}40;">
                        <img src="{{ !empty($monHoc->hinh_anh) ? asset('storage/' . $monHoc->hinh_anh) : asset('frontend/asset/images/default_subject.png') }}" alt="{{ $monHoc->ten_mon_hoc }}"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="subject-hero-text">
                        <h1>{{ $monHoc->ten_mon_hoc }}</h1>
                        <p>{{ $monHoc->mo_ta_ngan ?? 'Khám phá kiến thức và nâng cao kỹ năng của bạn với học phần này.' }}
                        </p>
                    </div>
                </div>
                <div class="subject-hero-meta">
                    @if ($monHoc->so_luong_bai_hoc)
                        <div class="hero-meta-chip"><i class="fas fa-book"></i> {{ $monHoc->so_luong_bai_hoc }} bài học
                        </div>
                    @endif
                    @if ($monHoc->so_luong_cau_hoi)
                        <div class="hero-meta-chip"><i class="fas fa-question-circle"></i> {{ $monHoc->so_luong_cau_hoi }}
                            câu hỏi</div>
                    @endif
                    <div class="hero-meta-chip"><i class="fas fa-signal"></i> {{ $levelIcon }} {{ $levelText }}
                    </div>
                    <div class="hero-meta-chip"><i class="fas fa-graduation-cap"></i> {{ $monHoc->so_tin_chi ?? 3 }} tín
                        chỉ</div>

                    {{-- ── TIẾN ĐỘ MINI TRÊN HERO ── --}}
                    @auth
                        @if ($tongBai > 0)
                            <div class="hero-meta-chip"
                                style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; gap: 8px;">
                                <i class="fas fa-chart-line"></i>
                                {{ $soBaiDaXong }}/{{ $tongBai }} bài &nbsp;·&nbsp;
                                <strong>{{ $progressPercent }}%</strong>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MAIN DETAIL ══ --}}
    <section class="detail-section">
        <div class="container">
            <div class="detail-layout">

                {{-- ── Sidebar ── --}}
                <aside class="chapters-sidebar">
                    <div class="sidebar-top">
                        <h3><i class="fas fa-book-open"></i> Nội dung học phần</h3>
                        <p>{{ mb_strtoupper($monHoc->ten_mon_hoc, 'UTF-8') }}</p>
                    </div>

                    <div class="chapters-list">
                        @forelse ($monHoc->chuongHocs as $chuong)
                            @php
                                // Đếm số bài đã xong trong chương này
                                $baiTrongChuong = $chuong->baiHocs->pluck('id')->toArray();
                                $baiXongTrongChuong = count(array_intersect($baiTrongChuong, $baiHocDaXongIds));
                                $tongBaiChuong = count($baiTrongChuong);
                                $chuongHoanThanh = $tongBaiChuong > 0 && $baiXongTrongChuong === $tongBaiChuong;
                            @endphp

                            <div class="chapter-item {{ $loop->first ? 'open' : '' }}">
                                <div class="chapter-header">
                                    <i class="fas fa-chevron-right ch-arrow"></i>
                                    <h4>{{ $chuong->ten_chuong }}</h4>

                                    {{-- Badge tiến độ chương --}}
                                    @if ($tongBaiChuong > 0)
                                        @php
                                            $pt = round(($baiXongTrongChuong / $tongBaiChuong) * 100);
                                        @endphp
                                        <span class="ch-progress-badge">
                                            {{ $pt }}%
                                        </span>
                                    @endif
                                </div>

                                <ul class="chapter-lessons">
                                    @foreach ($chuong->baiHocs as $bai)
                                        @php $isDone = in_array($bai->id, $baiHocDaXongIds); @endphp
                                        <li>
                                            <a href="{{ route('client.subjects.show', ['id' => $monHoc->id, 'bai_hoc_id' => $bai->id]) }}"
                                                class="{{ $activeBaiHoc && $activeBaiHoc->id == $bai->id ? 'active' : '' }} {{ $isDone ? 'completed' : '' }}"
                                                data-bai-id="{{ $bai->id }}">

                                                {{-- Icon: hoàn thành / đang học / chưa học --}}
                                                @if ($isDone)
                                                    <i class="fas fa-check-circle" style="color: #10b981; font-size: 14px;"></i>
                                                @elseif ($activeBaiHoc && $activeBaiHoc->id == $bai->id)
                                                    <i class="fas fa-play-circle"
                                                        style="color: var(--blue); font-size: 14px;"></i>
                                                @else
                                                    <i class="fas fa-play-circle" style="color: #64748b; font-size: 14px;"></i>
                                                @endif

                                                {{ $bai->ten_bai_hoc }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div style="padding: 40px 20px; text-align: center; color: #94a3b8;">
                                <i class="fas fa-folder-open"
                                    style="font-size: 2rem; margin-bottom: 12px; display: block;"></i>
                                <p>Chưa có chương học nào</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- ── THANH TIẾN ĐỘ TRONG SIDEBAR ── --}}
                    <div class="sidebar-progress">
                        <div class="sidebar-progress-title">
                            TIẾN ĐỘ HỌC TẬP
                            @if ($tongBai > 0)
                                <span id="sidebar-count-label"
                                    style="margin-left: auto; text-transform: none; font-weight: 500; font-size: 0.75rem; color: #1e293b;">
                                    {{ $soBaiDaXong }}/{{ $tongBai }} bài
                                </span>
                            @endif
                        </div>
                        <div class="sidebar-prog-bar">
                            <div class="sidebar-prog-fill" id="sidebar-prog-fill"
                                style="width: {{ $progressPercent }}%;
                                       background: {{ $progressPercent >= 100
                                           ? 'linear-gradient(90deg,#10b981,#059669)'
                                           : 'linear-gradient(90deg,var(--blue),#1d4ed8)' }}">
                            </div>
                        </div>
                        <div class="sidebar-prog-label" id="sidebar-prog-label">
                            @if ($progressPercent >= 100)
                                🎉 Hoàn thành 100%!
                            @elseif ($progressPercent > 0)
                                {{ $progressPercent }}% hoàn thành
                            @else
                                Chưa bắt đầu
                            @endif
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="sidebar-cta">
                        <a href="{{ route('client.practice.setup', ['mon_hoc_id' => $monHoc->id]) }}"
                            class="btn-cta-primary">
                            <i class="fas fa-play"></i> Tiếp tục luyện tập
                        </a>
                        <a href="{{ route('profile.history') }}" class="btn-cta-outline">
                            <i class="fas fa-chart-line"></i> Xem thống kê
                        </a>
                    </div>
                </aside>

                {{-- ── Main content ── --}}
                <main class="subject-main">
                    @if ($activeBaiHoc)
                        <div class="lesson-card" id="lesson-{{ $activeBaiHoc->id }}">

                            {{-- Header bài học --}}
                            <div class="lesson-card-header">
                                <div class="lesson-title">
                                    <i class="fas fa-book-open" style="color: var(--blue); margin-right: 8px;"></i>
                                    {{ $activeBaiHoc->ten_bai_hoc }}
                                </div>
                                <div class="lesson-header-actions">
                                    <button class="btn-lesson-sm" id="btn-bookmark" data-tooltip="Đánh dấu bài học">
                                        <i class="far fa-bookmark"></i> Đánh dấu
                                    </button>

                                    {{-- Nút hoàn thành bài --}}
                                    @auth
                                        <button id="btn-complete" data-bai-id="{{ $activeBaiHoc->id }}"
                                            class="btn-complete-lesson {{ $isCurrentDone ? 'done' : 'pending' }}"
                                            {{ $isCurrentDone ? 'disabled' : '' }}>
                                            @if ($isCurrentDone)
                                                <i class="fas fa-check-circle"></i> Đã hoàn thành
                                            @else
                                                <i class="fas fa-check"></i> Đánh dấu hoàn thành
                                            @endif
                                        </button>
                                    @endauth
                                </div>
                            </div>

                            <div class="lesson-body">

                                {{-- 1. Lý thuyết --}}
                                @if ($activeBaiHoc->noi_dung_ly_thuyet)
                                    <div class="theory-block ck-content">
                                        <h3><i class="fas fa-book-open"></i> Lý thuyết</h3>
                                        {!! $activeBaiHoc->noi_dung_ly_thuyet !!}
                                    </div>
                                @endif

                                {{-- 2. Video --}}
                                @if ($activeBaiHoc->video_url)
                                    <div class="video-block">
                                        <h3><i class="fas fa-video" style="color: #e11d48;"></i> Video bài giảng</h3>
                                        <div class="video-wrapper">
                                            @if (str_contains($activeBaiHoc->video_url, 'youtube.com') || str_contains($activeBaiHoc->video_url, 'youtu.be'))
                                                @php
                                                    $videoId = '';
                                                    if (
                                                        preg_match(
                                                            '/youtu\.be\/([^&]+)/',
                                                            $activeBaiHoc->video_url,
                                                            $matches,
                                                        )
                                                    ) {
                                                        $videoId = $matches[1];
                                                    } elseif (
                                                        preg_match('/v=([^&]+)/', $activeBaiHoc->video_url, $matches)
                                                    ) {
                                                        $videoId = $matches[1];
                                                    }
                                                @endphp
                                                @if ($videoId)
                                                    <iframe width="100%" height="480"
                                                        src="https://www.youtube.com/embed/{{ $videoId }}"
                                                        title="Video bài học" frameborder="0"
                                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                                @endif
                                            @elseif(str_contains($activeBaiHoc->video_url, 'vimeo.com'))
                                                @php $vimeoId = last(explode('/', $activeBaiHoc->video_url)); @endphp
                                                <iframe src="https://player.vimeo.com/video/{{ $vimeoId }}"
                                                    width="100%" height="480" frameborder="0"
                                                    allow="autoplay; fullscreen; picture-in-picture"
                                                    allowfullscreen></iframe>
                                            @else
                                                <video controls width="100%" height="480"
                                                    style="border-radius:12px;background:#000;">
                                                    <source src="{{ asset('storage/' . $activeBaiHoc->video_url) }}"
                                                        type="video/mp4">
                                                    Trình duyệt của bạn không hỗ trợ video.
                                                </video>
                                            @endif
                                        </div>
                                        <div class="video-info-tip">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Video hỗ trợ học tập trực quan và tiếp thu kiến thức tốt hơn. Hãy kết hợp lý thuyết và video bài giảng!</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- 3. Tài liệu đính kèm --}}
                                @php
                                    $taiLieuFiles = [];
                                    if (!empty($activeBaiHoc->tai_lieu_dinh_kem)) {
                                        if (is_array($activeBaiHoc->tai_lieu_dinh_kem)) {
                                            $taiLieuFiles = $activeBaiHoc->tai_lieu_dinh_kem;
                                        } elseif (is_string($activeBaiHoc->tai_lieu_dinh_kem) && str_starts_with(trim($activeBaiHoc->tai_lieu_dinh_kem), '[')) {
                                            $taiLieuFiles = json_decode($activeBaiHoc->tai_lieu_dinh_kem, true) ?? [];
                                        } else {
                                            $taiLieuFiles = [$activeBaiHoc->tai_lieu_dinh_kem];
                                        }
                                    }
                                @endphp

                                @if (count($taiLieuFiles) > 0)
                                    <div class="document-block">
                                        <h3><i class="fas fa-paperclip"></i> Tài liệu / File đính kèm</h3>
                                        <div class="document-list">
                                            @foreach ($taiLieuFiles as $filePath)
                                                @php
                                                    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                                                    $iconClass = 'default';
                                                    $faIcon = 'fa-file';
                                                    
                                                    if(in_array(strtolower($ext), ['pdf'])) { $iconClass = 'pdf'; $faIcon = 'fa-file-pdf'; }
                                                    elseif(in_array(strtolower($ext), ['pptx', 'ppt'])) { $iconClass = 'pptx'; $faIcon = 'fa-file-powerpoint'; }
                                                    elseif(in_array(strtolower($ext), ['doc', 'docx'])) { $iconClass = 'word'; $faIcon = 'fa-file-word'; }
                                                    elseif(in_array(strtolower($ext), ['xls', 'xlsx'])) { $iconClass = 'excel'; $faIcon = 'fa-file-excel'; }
                                                    elseif(in_array(strtolower($ext), ['zip', 'rar'])) { $iconClass = 'zip'; $faIcon = 'fa-file-archive'; }
                                                    
                                                    $fileName = basename($filePath);
                                                    
                                                    // Tính dung lượng file
                                                    $sizeStr = strtoupper($ext) . ' File';
                                                    $fullPath = storage_path('app/public/' . $filePath);
                                                    if (file_exists($fullPath)) {
                                                        $bytes = filesize($fullPath);
                                                        $sizeStr = number_format($bytes / 1048576, 2) . ' MB';
                                                    }
                                                @endphp
                                                <a href="{{ asset('storage/' . $filePath) }}" class="document-item" download>
                                                    <div class="doc-icon {{ $iconClass }}">
                                                        <i class="fas {{ $faIcon }}"></i>
                                                    </div>
                                                    <div class="doc-info">
                                                        <h4>{{ $fileName }}</h4>
                                                        <span>{{ $sizeStr }}</span>
                                                    </div>
                                                    <div class="doc-action">
                                                        <i class="fas fa-download"></i>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- 4. Code mẫu --}}
                                @if ($activeBaiHoc->ma_nguon_mau)
                                    <div class="code-block-wrap">
                                        <h3><i class="fas fa-code"></i> Ví dụ minh họa</h3>
                                        <div class="code-file-block">
                                            <div class="code-file-header">
                                                <span class="code-file-name">
                                                    <i class="fas fa-file-code"></i>
                                                    {{ $activeBaiHoc->ten_file_code ?? 'code_mau.' . $activeBaiHoc->ngon_ngu_code }}
                                                </span>
                                                <button class="btn-copy-code"
                                                    onclick="copyCode('code-block-{{ $activeBaiHoc->id }}')">
                                                    <i class="fas fa-copy"></i> Sao chép
                                                </button>
                                            </div>
                                            <pre><code id="code-block-{{ $activeBaiHoc->id }}" class="language-{{ $activeBaiHoc->ngon_ngu_code }}">{{ $activeBaiHoc->ma_nguon_mau }}</code></pre>
                                        </div>

                                        @if (is_array($activeBaiHoc->giai_thich_code) && count($activeBaiHoc->giai_thich_code) > 0)
                                            <div style="margin-top:16px;">
                                                <h4
                                                    style="font-size:0.85rem;font-weight:700;color:#475569;margin-bottom:12px;">
                                                    <i class="fas fa-lightbulb" style="color:#f59e0b;"></i> Giải thích chi
                                                    tiết
                                                </h4>
                                                <div class="explain-grid">
                                                    @foreach ($activeBaiHoc->giai_thich_code as $explain)
                                                        @if (isset($explain['dong']) && isset($explain['giai_thich']))
                                                            <div class="explain-item">
                                                                <span
                                                                    class="explain-line-num">{{ $explain['dong'] }}</span>
                                                                <p>{{ $explain['giai_thich'] }}</p>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- 4. Quiz nhanh --}}
                                @if ($activeBaiHoc->cauHois && $activeBaiHoc->cauHois->count() > 0)
                                    @php $quiz = $activeBaiHoc->cauHois->first(); @endphp
                                    <div class="quiz-block">
                                        <h3><i class="fas fa-question-circle"></i> Kiểm tra nhanh</h3>
                                        <div class="quiz-question">{!! $quiz->noi_dung !!}</div>
                                        <div class="quiz-opts">
                                            @foreach ($quiz->dapAns as $index => $ans)
                                                <label class="quiz-opt" data-opt-index="{{ $index }}">
                                                    <input type="radio" name="quick-quiz" value="{{ $ans->id }}"
                                                        data-correct="{{ $ans->is_dung ? 'true' : 'false' }}" />
                                                    <span class="quiz-opt-letter">{{ $ans->ky_hieu }}</span>
                                                    <span class="quiz-opt-text">{!! $ans->noi_dung !!}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="quiz-btns">
                                            <button class="btn-quiz" id="check-answer-btn">
                                                <i class="fas fa-check-circle"></i> Kiểm tra đáp án
                                            </button>
                                            @if ($quiz->giai_thich)
                                                <button class="btn-quiz" id="show-explanation-btn">
                                                    <i class="fas fa-lightbulb"></i> Xem giải thích
                                                </button>
                                            @endif
                                        </div>
                                        <div class="quiz-result-area" id="quiz-result"></div>
                                        @if ($quiz->giai_thich)
                                            <div id="quiz-explanation" style="display:none;">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <div style="flex:1;">
                                                        <strong>Giải thích chi tiết:</strong>
                                                        <div style="margin-top:8px;">{!! $quiz->giai_thich !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                            </div>
                        </div>

                        {{-- ── Lesson nav bar ── --}}
                        <div class="lesson-nav-bar">
                            @if ($prevLesson)
                                <a href="{{ route('client.subjects.show', ['id' => $monHoc->id, 'bai_hoc_id' => $prevLesson->id]) }}"
                                    class="btn-lesson-nav">
                                    <i class="fas fa-arrow-left"></i> Bài trước
                                </a>
                            @else
                                <button class="btn-lesson-nav" disabled>
                                    <i class="fas fa-arrow-left"></i> Bài trước
                                </button>
                            @endif

                            <div class="nav-dots-wrap">
                                <div class="nav-dots-label">
                                    <i class="fas fa-play" style="font-size:10px;color:var(--blue);"></i> Đang học
                                </div>
                                <div class="progress-dots">
                                    @for ($i = 0; $i < min(10, $allLessons->count()); $i++)
                                        <div class="dot {{ $i == $currentIndex ? 'active' : '' }}"></div>
                                    @endfor
                                </div>
                            </div>

                            @if ($nextLesson)
                                <a href="{{ route('client.subjects.show', ['id' => $monHoc->id, 'bai_hoc_id' => $nextLesson->id]) }}"
                                    class="btn-lesson-nav-primary" id="btn-next-lesson">
                                    Bài tiếp theo <i class="fas fa-arrow-right"></i>
                                </a>
                            @else
                                <button class="btn-lesson-nav-primary" disabled
                                    style="background:#10b981;box-shadow:none;">
                                    <i class="fas fa-trophy"></i> Hoàn thành
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-box-open"></i></div>
                            <h3>Chưa có bài học nào</h3>
                            <p>Học phần này đang được cập nhật nội dung.<br>Vui lòng quay lại sau nhé!</p>
                        </div>
                    @endif
                </main>

            </div>
        </div>
    </section>

    <button class="scroll-top-btn" id="scrollTopBtn"><i class="fas fa-arrow-up"></i></button>

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css" />
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                // ── Highlight.js ──
                if (typeof hljs !== 'undefined') hljs.highlightAll();

                // ── Copy code ──
                window.copyCode = function(id) {
                    const text = document.getElementById(id).innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        const btn = document.querySelector(`[onclick="copyCode('${id}')"]`);
                        const orig = btn.innerHTML;
                        btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
                        btn.style.background = "rgba(16,185,129,0.25)";
                        setTimeout(() => {
                            btn.innerHTML = orig;
                            btn.style.background = "";
                        }, 1800);
                    });
                };

                // ── Chapter accordion ──
                document.querySelectorAll(".chapter-header").forEach(header => {
                    header.addEventListener("click", () => {
                        header.closest(".chapter-item").classList.toggle("open");
                    });
                });

                // ── Quiz: chọn đáp án ──
                document.querySelectorAll(".quiz-opt").forEach(opt => {
                    opt.addEventListener("click", () => {
                        document.querySelectorAll(".quiz-opt").forEach(o => o.classList.remove(
                            "selected"));
                        opt.classList.add("selected");
                        opt.querySelector("input").checked = true;
                    });
                });

                // ── Quiz: kiểm tra đáp án ──
                const checkBtn = document.getElementById("check-answer-btn");
                const btnExplain = document.getElementById("show-explanation-btn");
                const resultArea = document.getElementById("quiz-result");
                const explainArea = document.getElementById("quiz-explanation");

                if (checkBtn) {
                    checkBtn.addEventListener("click", () => {
                        const selected = document.querySelector('input[name="quick-quiz"]:checked');
                        if (!selected) {
                            resultArea.innerHTML =
                                `<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i><span>Vui lòng chọn một đáp án!</span></div>`;
                            return;
                        }
                        resultArea.innerHTML = selected.dataset.correct === "true" ?
                            `<div class="alert alert-success"><i class="fas fa-check-circle"></i><span>✨ Chính xác! Bạn đã hiểu bài rồi!</span></div>` :
                            `<div class="alert alert-error"><i class="fas fa-times-circle"></i><span>Chưa đúng! Hãy xem giải thích nhé.</span></div>`;
                        resultArea.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    });
                }

                if (btnExplain && explainArea) {
                    btnExplain.addEventListener("click", () => {
                        const show = explainArea.style.display === "none" || !explainArea.style.display;
                        explainArea.style.display = show ? "block" : "none";
                        btnExplain.innerHTML = show ?
                            '<i class="fas fa-eye-slash"></i> Ẩn giải thích' :
                            '<i class="fas fa-lightbulb"></i> Xem giải thích';
                        if (show) explainArea.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                        if (window.MathJax) MathJax.typesetPromise();
                    });
                }

                // ── Bookmark ──
                const btnBookmark = document.getElementById("btn-bookmark");
                if (btnBookmark) {
                    const lessonId = {{ $activeBaiHoc->id ?? 0 }};
                    if (localStorage.getItem(`bookmark_${lessonId}`) === 'true') {
                        btnBookmark.innerHTML = `<i class="fas fa-bookmark" style="color:#eab308;"></i> Đã đánh dấu`;
                        btnBookmark.style.cssText = "background:#fef9c3;border-color:#fef08a;color:#a16207;";
                    }
                    btnBookmark.addEventListener("click", function() {
                        const icon = this.querySelector("i");
                        if (icon.classList.contains("far")) {
                            icon.className = "fas fa-bookmark";
                            this.innerHTML =
                                `<i class="fas fa-bookmark" style="color:#eab308;"></i> Đã đánh dấu`;
                            this.style.cssText = "background:#fef9c3;border-color:#fef08a;color:#a16207;";
                            localStorage.setItem(`bookmark_${lessonId}`, 'true');
                            showToast('Đã đánh dấu bài học', 'success');
                        } else {
                            this.innerHTML = `<i class="far fa-bookmark"></i> Đánh dấu`;
                            this.style.cssText = "";
                            localStorage.removeItem(`bookmark_${lessonId}`);
                            showToast('Đã bỏ đánh dấu', 'info');
                        }
                    });
                }

                // ════════════════════════════════════════════════════
                // ── NÚT "ĐÁNH DẤU HOÀN THÀNH" ── CẬP NHẬT TIẾN ĐỘ REALTIME
                // ════════════════════════════════════════════════════
                const btnComplete = document.getElementById("btn-complete");
                if (btnComplete) {
                    btnComplete.addEventListener("click", function() {
                        const baiHocId = this.dataset.baiId;
                        const originalContent = this.innerHTML;

                        // BẮT ĐẦU LOADING
                        this.disabled = true;
                        this.classList.add('loading');
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                        fetch("{{ route('client.tiendo.update') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    bai_hoc_id: baiHocId
                                })
                            })
                            .then(response => {
                                // Kiểm tra nếu phản hồi từ server không ok (Lỗi 500, 404, 419...)
                                if (!response.ok) {
                                    return response.text().then(text => {
                                        throw new Error(text)
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                this.classList.remove('loading');

                                if (data.success) {
                                    // 1. Cập nhật nút chính
                                    this.className = "btn-complete-lesson done";
                                    this.innerHTML = '<i class="fas fa-check-circle"></i> Đã hoàn thành';
                                    this.disabled = true;

                                    // 2. Đánh dấu bài học trong Sidebar
                                    const sidebarLink = document.querySelector(
                                        `.chapter-lessons a[data-bai-id="${baiHocId}"]`);
                                    if (sidebarLink) {
                                        sidebarLink.classList.add('completed');
                                        const iconEl = sidebarLink.querySelector('i, span.lesson-check');
                                        if (iconEl) {
                                            iconEl.outerHTML =
                                                `<span class="lesson-check"><i class="fas fa-check"></i></span>`;
                                        }
                                    }

                                    // 3. TÍNH TOÁN LẠI TIẾN ĐỘ
                                    const allLinks = document.querySelectorAll(
                                        '.chapter-lessons a[data-bai-id]');
                                    const completedLinks = document.querySelectorAll(
                                        '.chapter-lessons a.completed');
                                    const total = allLinks.length;
                                    const completed = completedLinks.length;
                                    const percentage = total > 0 ? Math.round((completed / total) * 100) :
                                        0;

                                    // 4. CẬP NHẬT GIAO DIỆN (Dùng selector an toàn hơn)
                                    const fill = document.getElementById('sidebar-prog-fill');
                                    const pctLabel = document.getElementById('sidebar-prog-label');
                                    const countLabel = document.getElementById('sidebar-count-label');

                                    if (fill) fill.style.width = percentage + '%';
                                    if (pctLabel) pctLabel.textContent = percentage + '% hoàn thành';

                                    // Cập nhật text X/Y BÀI (Tìm thẻ span chứa chữ BÀI)
                                    /* const labels = document.querySelectorAll('aside span, aside h3');
                                    labels.forEach(el => {
                                        if (el.textContent.includes('BÀI')) {
                                            el.textContent = `${completed}/${total} BÀI`;
                                        }
                                    }); */
                                    if (countLabel) {
                                        countLabel.textContent = `${completed}/${total} BÀI`;
                                    } else {
                                        // Phương án dự phòng nếu bạn quên chưa thêm ID hoặc sai tên ID
                                        const labels = document.querySelectorAll('aside span, aside h3');
                                        labels.forEach(el => {
                                            if (el.textContent.includes('BÀI')) {
                                                el.textContent = `${completed}/${total} BÀI`;
                                            }
                                        });
                                    }

                                    // 5. Cập nhật Badge của Chương
                                    if (sidebarLink) {
                                        const chapterItem = sidebarLink.closest('.chapter-item');
                                        const chBadge = chapterItem.querySelector('.ch-progress-badge');
                                        const chTotal = chapterItem.querySelectorAll('.chapter-lessons a')
                                            .length;
                                        const chDone = chapterItem.querySelectorAll(
                                            '.chapter-lessons a.completed').length;

                                        if (chBadge) {
                                            if (chDone === chTotal) {
                                                chBadge.innerHTML = '<i class="fas fa-check-circle"></i>';
                                                chBadge.style.cssText =
                                                    "background:#f0fdf4;color:#10b981;border-color:#bbf7d0;";
                                            } else {
                                                chBadge.textContent = `${chDone}/${chTotal}`;
                                            }
                                        }
                                    }
                                    showToast('🎉 Tuyệt vời! Đã cập nhật tiến độ.', 'success');
                                } else {
                                    throw new Error(data.message || 'Lỗi xử lý');
                                }
                            })
                            .catch(error => {
                                console.error("Lỗi chi tiết:", error);
                                this.classList.remove('loading');
                                this.disabled = false;
                                this.innerHTML = originalContent;

                                // Hiện thông báo lỗi cụ thể hơn để debug
                                showToast('Lỗi: Kiểm tra tab Console (F12) để xem chi tiết', 'error');
                            });
                    });
                }

                // ── Toast helper ──
                function showToast(message, type = 'success') {
                    const toast = document.createElement('div');
                    toast.className = `toast-notify ${type}`;
                    toast.innerHTML =
                        `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i> ${message}`;
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        toast.style.animation = 'slideOutRight 0.3s ease forwards';
                        setTimeout(() => toast.remove(), 300);
                    }, 2500);
                }

                // ── Scroll to top ──
                const scrollBtn = document.getElementById('scrollTopBtn');
                if (scrollBtn) {
                    window.addEventListener('scroll', () => {
                        scrollBtn.classList.toggle('show', window.scrollY > 300);
                    });
                    scrollBtn.addEventListener('click', () => window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    }));
                }

            });
        </script>
    @endpush

@endsection

{{-- Extra styles --}}
<style>
    .video-block {
        margin-bottom: 2.5rem;
        /* max-width: 720px;  /* đổi số này tuỳ ý */
        margin-left: 0;
        /* hoặc auto nếu muốn căn giữa */
        */
    }

    .video-wrapper {
        position: relative;
        padding-top: 56.25%;
        height: 0;
        border-radius: 16px;
        overflow: hidden;
        background: #000;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-bottom: 12px;
    }

    .video-wrapper iframe,
    .video-wrapper video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    .video-block h3 {
        margin-bottom: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        background: #fff;
        border-radius: 24px;
        border: 2px dashed #e2eaf6;
        box-shadow: 0 2px 16px rgba(37, 99, 235, 0.07);
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        background: linear-gradient(135deg, #f1f5f9, #e2eaf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #94a3b8;
    }

    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
        }

        50% {
            box-shadow: 0 4px 28px rgba(37, 99, 235, 0.65);
            transform: scale(1.03);
        }
    }

    @media (max-width: 768px) {
        .empty-state {
            padding: 50px 24px;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }
    }
</style>
