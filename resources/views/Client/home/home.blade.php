@extends('Client.layouts.app')

@section('title', 'Trang chủ - IT Study Support Khoa CNTT')

@section('styles')
    <link rel="stylesheet" href="{{ asset('frontend/asset/css/subject-brand-hover.css') }}">
@endsection

@section('content')
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-graduation-cap"></i> Dành cho sinh viên Khoa CNTT
                </div>
                <h2>
                    Ôn tập <span class="hl">hiệu quả hơn</span><br />với hệ thống thông
                    minh
                </h2>
                <p>
                    Tài liệu, bài tập và đề thi thử được biên soạn bám sát chương trình
                    đào tạo. Học mọi lúc, mọi nơi — theo dõi tiến độ tức thì.
                </p>
                <div class="hero-actions">
                    <a href="{{ url('/hoc-phan') }}" class="btn-hero-primary"><i class="fas fa-play-circle"></i> Bắt đầu học
                        ngay</a>
                    <a href="{{ url('/hoc-phan') }}" class="btn-hero-secondary"><i class="fas fa-search"></i> Khám phá học
                        phần</a>
                </div>

                {{-- Feature badges --}}
                <div class="hero-features">
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="ti ti-users"></i>
                        </div>
                        <div class="hero-feature-text">
                            <strong>{{ number_format($thongKe['tong_sinh_vien']) }}+</strong>
                            <span>Sinh viên tin dùng</span>
                        </div>
                    </div>
                    <div class="hero-feature-divider"></div>
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="ti ti-book-2"></i>
                        </div>
                        <div class="hero-feature-text">
                            <strong>{{ number_format($thongKe['tong_mon_hoc']) }}+</strong>
                            <span>Học phần</span>
                        </div>
                    </div>
                    <div class="hero-feature-divider"></div>
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="ti ti-help-circle"></i>
                        </div>
                        <div class="hero-feature-text">
                            <strong>{{ number_format($thongKe['tong_cau_hoi']) }}+</strong>
                            <span>Câu hỏi ôn tập</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Visual: ảnh sinh viên + floating card --}}
            <div class="hero-visual">
                {{-- Floating card tiến độ --}}
                {{--  <div class="hero-card-main">
                    <div class="card-head">
                        <div class="card-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span>Tiến độ học tập</span>
                    </div>
                    <div class="subject-pills">
                        <div class="pill active"><i class="fab fa-cuttlefish"></i> C/C++</div>
                        <div class="pill"><i class="fas fa-database"></i> CSDL</div>
                        <div class="pill"><i class="fas fa-sitemap"></i> CTDL</div>
                        <div class="pill"><i class="fas fa-code"></i> Web</div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Lập trình C/C++</span><span>72%</span></div>
                        <div class="progress-bar"><div class="progress-fill" style="width: 72%"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Cơ sở dữ liệu</span><span>45%</span></div>
                        <div class="progress-bar"><div class="progress-fill" style="width: 45%"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Mạng máy tính</span><span>60%</span></div>
                        <div class="progress-bar"><div class="progress-fill" style="width: 60%"></div></div>
                    </div>
                    <div class="progress-item">
                        <div class="progress-label"><span>Lập trình Web</span><span>88%</span></div>
                        <div class="progress-bar"><div class="progress-fill" style="width: 88%"></div></div>
                    </div>
                </div> --}}

                {{-- Ảnh sinh viên --}}
                <img
                    src="{{ asset('./frontend/asset/images/hero151.png') }}"
                    alt="Sinh viên CNTT"
                    class="hero-student-img"
                >

                {{-- Float badge trophy --}}
                {{-- @auth
                    <div class="float-badge">
                        <div class="icon"><i class="fas fa-trophy"></i></div>
                        <div class="info">
                            <div class="val">{{ $diemTuanNay ?? 0 }} điểm</div>
                            <div class="desc">Tuần này của bạn</div>
                        </div>
                    </div>
                @else
                    <div class="float-badge">
                        <div class="icon"><i class="fas fa-trophy"></i></div>
                        <div class="info">
                            <div class="val">156 điểm</div>
                            <div class="desc">Đăng nhập để xem</div>
                        </div>
                    </div>
                @endauth --}}
                {{-- <div class="float-badge">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="info">
                        <div class="val">{{ number_format($thongKe['tong_sinh_vien']) }}+</div>
                        <div class="desc">Sinh viên đã tham gia</div>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

    @php
        $allCategories = collect();
        
        // 1. Thêm Môn học nổi bật (is_featured = 1) vào đầu danh sách nếu có
        if(isset($monHocNoiBat) && $monHocNoiBat->isNotEmpty()) {
            $allCategories->push((object)[
                'tieu_de' => 'Học phần nổi bật',
                'mo_ta' => 'Những khóa học xuất sắc được chọn lọc dành riêng cho bạn',
                'monHocs' => $monHocNoiBat
            ]);
        }
        
        // 2. Gộp thêm các danh mục động từ Admin
        if(isset($danhMucTrangChu)) {
            foreach($danhMucTrangChu->where('loai_danh_muc', 'course_list') as $courseCategory) {
                if($courseCategory->monHocs && $courseCategory->monHocs->isNotEmpty()) {
                    $allCategories->push($courseCategory);
                }
            }
        }
    @endphp

    {{-- VÒNG LẶP GỘP: Lặp qua Môn học nổi bật + Danh mục cấu hình từ Admin --}}
    @foreach($allCategories as $courseCategory)
        <section class="section" style="background: #f8fafc; padding: 60px 0; border-bottom: 1px solid #e2e8f0;">
            <div class="container">
                
                {{-- Tiêu đề khối --}}
                <div class="section-header">
                    <h2>{{ $courseCategory->tieu_de }}</h2>
                    <p>{{ $courseCategory->mo_ta }}</p>
                </div>

                <div class="subjects-grid">
                    {{-- VÒNG LẶP CON: Lặp qua các môn học thuộc danh mục này --}}
                    @foreach ($courseCategory->monHocs as $mon)
                        @php
                            $imageUrl = !empty($mon->hinh_anh) ? asset('storage/' . $mon->hinh_anh) : asset('frontend/asset/images/default_subject.png');
                            $brandColor = $mon->mau_sac ?? '#3b82f6';
                        @endphp
                        <div class="subject-item-card"
                            style="--subject-brand-color: {{ $brandColor }};"
                            data-banner-height="large">
                            
                            {{-- Banner --}}
                            <div class="subject-card-banner has-image">
                                <img class="banner-bg-img" src="{{ $imageUrl }}" alt="{{ $mon->ten_mon_hoc }}">
                                <span class="banner-medal">
                                    @if (!empty($mon->icon_class))
                                        <i class="{{ $mon->icon_class }}"></i>
                                    @endif
                                </span>
                            </div>

                            {{-- Card Body --}}
                            <div class="subject-card-body">
                                <div class="card-header-row">
                                    <h3>{{ \Illuminate\Support\Str::limit($mon->ten_mon_hoc, 35) }}</h3>
                                    @php
                                        $levelClass = match ($mon->muc_do_mon_hoc ?? 1) {
                                            2 => 'intermediate',
                                            3, 4 => 'advanced',
                                            default => 'basic',
                                        };
                                        $levelText = match ($mon->muc_do_mon_hoc ?? 1) {
                                            1 => 'Nhập môn',
                                            2 => 'Cơ sở ngành',
                                            3 => 'Chuyên ngành',
                                            4 => 'Chuyên sâu',
                                            default => 'Nhập môn',
                                        };
                                    @endphp
                                    <span class="badge-level {{ $levelClass }}">{{ $levelText }}</span>
                                </div>

                                <p>{{ \Illuminate\Support\Str::limit($mon->mo_ta_ngan ?? 'Chưa có mô tả cho học phần này.', 85) }}</p>

                                <div class="subject-stat-row">
                                    <span class="subject-stat-chip">
                                        <i class="fas fa-bookmark"></i> {{ $mon->ma_mon_hoc ?? 'Mã môn' }}
                                    </span>
                                    <span class="subject-stat-chip">
                                        <i class="fas fa-graduation-cap"></i> {{ $mon->so_tin_chi ?? 3 }} TC
                                    </span>
                                    <span class="subject-stat-chip">
                                        <i class="fas fa-play-circle"></i>
                                        {{ number_format($mon->tong_bai_hoc ?? 0) }} bài học
                                    </span>
                                </div>

                                {{-- Tiến độ học tập động tính từ Controller --}}
                                @php
                                    $pct = $mon->progress_percent ?? 0;
                                    $pctText = $pct > 0 ? $pct . '%' : 'Chưa bắt đầu';
                                    $fillClass = $pct > 0 ? '' : 'zero';
                                    $brandColor = $mon->mau_sac ?? '#3b82f6';
                                @endphp
                                <div class="subject-progress-wrap">
                                    <div class="subject-progress-top">
                                        <span class="subject-progress-label">Tiến độ học tập</span>
                                        <span class="subject-progress-pct">{{ $pctText }}</span>
                                    </div>
                                    <div class="subject-progress-bar">
                                        <div class="subject-progress-fill {{ $fillClass }}"
                                            style="width: {{ $pct }}%; background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}dd);">
                                        </div>
                                    </div>
                                </div>

                                <div class="subject-card-actions">
                                    <a href="{{ url('/hoc-phan/' . $mon->id) }}" class="btn-detail-link">
                                        <span class="btn-detail-text">BẮT ĐẦU NGAY</span>
                                        <span class="btn-detail-circle">
                                            <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                </div>
            </div>
        </section>
@endforeach

{{-- Trường hợp không có bất kỳ danh mục nào có môn học --}}
@if($danhMucTrangChu->where('loai_danh_muc', 'course_list')->flatMap->monHocs->isEmpty())
    <section class="section" style="background: #f8fafc">
        <div class="container">
            <div class="no-data-placeholder" style="text-align: center; padding: 60px 24px; background: #fff; border-radius: 20px;">
                <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                <h3 style="color: #1e293b; margin-bottom: 8px;">Chưa có học phần hiển thị</h3>
                <p style="color: #64748b;">Hiện tại chưa có học phần nào được cấu hình hiển thị trên trang chủ.</p>
            </div>
        </div>
    </section>
@endif
    @include('Client.home.sections')

    <section class="section" style="background: #f8fafc; padding: 72px 0;">
        <div class="container">
            @php $examCategory = $danhMucTrangChu->where('loai_danh_muc', 'exam_list')->first(); @endphp
            <div class="section-header">
                <div class="section-tag" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 99px; padding: 4px 16px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-block; margin-bottom: 12px; letter-spacing: 0.5px;">Đề thi</div>
                @if($examCategory)
                    <h2>{{ $examCategory->tieu_de }}</h2>
                    <p>{{ $examCategory->mo_ta }}</p>
                @else
                    <h2>Đề thi thử mới nhất</h2>
                    <p>Thử sức với các đề thi trắc nghiệm được cập nhật liên tục từ hệ thống.</p>
                @endif
            </div>

            @if ($deThu->isEmpty())
                <div class="empty-state" style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 16px;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <p style="color: #64748b; margin: 0;">Chưa có đề thi nào. Vui lòng quay lại sau.</p>
                </div>
            @else
                <div class="home-exams-grid">
                    @foreach ($deThu as $de)
                        @php
                            $history = $lichSuThi[$de->id] ?? [];
                            $now = \Carbon\Carbon::now();
                            $conHan = !$de->thoi_gian_ket_thuc || $now->lt($de->thoi_gian_ket_thuc);
                        @endphp

                        <div class="home-exam-card">
                            <div class="hec-header">
                                <div class="hec-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="hec-tags">
                                    <div class="hec-tags-top">
                                        <span class="hec-tag-time"><i class="fas fa-clock"></i> {{ $de->thoi_gian_phut }} phút</span>
                                        <span class="hec-tag-qs"><i class="fas fa-file-alt"></i> {{ $de->cau_hois_count }} câu</span>
                                    </div>
                                    @if (!$conHan)
                                        <span class="hec-badge closed">Đã đóng</span>
                                    @elseif ($de->thoi_gian_bat_dau && $now->lt($de->thoi_gian_bat_dau))
                                        <span class="hec-badge upcoming">Chưa mở</span>
                                    @else
                                        <span class="hec-badge active">Đang diễn ra</span>
                                    @endif
                                </div>
                            </div>

                            <h3 class="hec-title">{{ $de->ten_bai }}</h3>
                            <p class="hec-desc">{{ $de->mo_ta ?? 'Đề thi tổng hợp kiến thức môn ' . ($de->monHoc->ten_mon_hoc ?? '') }}</p>

                            <div class="hec-stats">
                                <div class="hec-stat-item">
                                    <div class="hec-stat-icon"><i class="fas fa-user-friends"></i></div>
                                    <div class="hec-stat-info">
                                        <strong>{{ number_format($de->so_luot_thi) }}</strong>
                                        <span>lượt thi</span>
                                    </div>
                                </div>
                                <div class="hec-stat-item">
                                    <div class="hec-stat-icon"><i class="fas fa-chart-line"></i></div>
                                    <div class="hec-stat-info">
                                        <strong>{{ number_format($de->diem_trung_binh, 1) }}/10</strong>
                                        <span>điểm TB</span>
                                    </div>
                                </div>
                            </div>

                            <div class="hec-history">
                                <div class="hec-history-title">
                                    <i class="fas fa-history"></i> Lịch sử thi của bạn
                                </div>
                                @if (empty($history))
                                    <div class="hec-history-empty">
                                        <i class="fas fa-exclamation-circle"></i> Bạn chưa thi lần nào
                                    </div>
                                @else
                                    <div class="hec-history-list">
                                        @foreach (array_slice($history, 0, 3) as $h)
                                            @php
                                                $giay = \Carbon\Carbon::parse($h['thoi_gian_vao_thi'])->diffInSeconds($h['thoi_gian_nop_bai']);
                                            @endphp
                                            <div class="hec-history-row">
                                                <span class="h-date">{{ \Carbon\Carbon::parse($h['thoi_gian_nop_bai'])->format('d/m/Y') }}</span>
                                                <span class="h-score {{ $h['diem'] >= 5 ? 'pass' : 'fail' }}">{{ $h['diem'] }}/10</span>
                                                <span class="h-time">{{ floor($giay / 60) }} phút</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <a href="{{ route('client.exams.show', $de->id) }}" class="hec-btn">
                                <i class="fas fa-play-circle"></i> Xem đề & Bắt đầu thi
                            </a>
                        </div>
                    @endforeach
                </div>

                <div style="text-align: center; margin-top: 40px;">
                    <a href="{{ route('client.exams.index') }}" class="btn-action" style="padding: 12px 30px; background: #fff; color: #1e293b; border: 1.5px solid #cbd5e1; border-radius: 99px; font-weight: 600; text-decoration: none; display: inline-block; transition: all 0.2s;">
                        Xem toàn bộ đề thi <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                    </a>
                </div>
            @endif
        </div>
    </section>

    @push('styles')
    <style>
        .home-exams-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }

        .home-exam-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .home-exam-card:hover {
            box-shadow: 0 10px 30px rgba(37,99,235,0.08);
            border-color: #bfdbfe;
            transform: translateY(-4px);
        }

        .hec-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .hec-icon {
            width: 52px;
            height: 52px;
            background: #2563eb;
            color: #ffffff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 16px rgba(37,99,235,0.25);
        }

        .hec-tags {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .hec-tags-top {
            display: flex;
            gap: 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .hec-tag-time {
            color: #2563eb;
        }

        .hec-tag-qs {
            color: #10b981;
        }

        .hec-tags-top i {
            margin-right: 4px;
        }

        .hec-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hec-badge.active {
            background: #dcfce3;
            color: #16a34a;
        }
        .hec-badge.upcoming {
            background: #fef3c7;
            color: #d97706;
        }
        .hec-badge.closed {
            background: #fee2e2;
            color: #dc2626;
        }

        .hec-title {
            font-size: 17px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hec-desc {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hec-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .hec-stat-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hec-stat-icon {
            width: 36px;
            height: 36px;
            background: #f0f7ff;
            color: #3b82f6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .hec-stat-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .hec-stat-info strong {
            font-size: 15px;
            font-weight: 800;
            color: #1e293b;
        }

        .hec-stat-info span {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .hec-history {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            flex: 1;
        }

        .hec-history-title {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hec-history-empty {
            font-size: 13px;
            color: #94a3b8;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hec-history-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .hec-history-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            font-weight: 600;
        }

        .h-date {
            color: #64748b;
        }

        .h-score.pass {
            color: #10b981;
        }

        .h-score.fail {
            color: #f43f5e;
        }

        .h-time {
            color: #94a3b8;
            font-weight: 500;
        }

        .hec-btn {
            background: #2563eb;
            color: #ffffff;
            border-radius: 99px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: auto;
        }

        .hec-btn:hover {
            background: #1d4ed8;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(37,99,235,0.3);
            transform: translateY(-2px);
        }
    </style>
    @endpush
        </div>
    </section>

    <section class="section faq-section" style="background: #f8fafc; padding: 72px 0;">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <div class="faq-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: 2rem; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Câu hỏi thường gặp</h2>
                <p style="color: #64748b; font-size: 1.05rem;">Giải đáp những thắc mắc phổ biến về IT Study Support</p>
            </div>
            <div class="faq-list" style="display: flex; flex-direction: column; gap: 16px;">
                @if(isset($faqs) && $faqs->isNotEmpty())
                    @foreach($faqs as $faq)
                        <div class="faq-item" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; transition: all 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                            <div class="faq-question" style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; font-weight: 600; color: #1e293b; font-size: 1.05rem;">
                                <span>{{ $faq->question }}</span>
                                <i class="fas fa-chevron-down" style="color: #94a3b8; transition: transform 0.3s;"></i>
                            </div>
                            <div class="faq-answer" style="padding: 0 24px; max-height: 0; overflow: hidden; transition: all 0.3s ease-in-out; color: #64748b; font-size: 0.95rem; line-height: 1.6;">
                                <div style="padding-bottom: 20px;">{!! $faq->answer !!}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div style="text-align: center; color: #64748b; padding: 20px;">Chưa có câu hỏi thường gặp nào.</div>
                @endif
            </div>
        </div>
    </section>

    @push('styles')
    <style>
        .faq-item.active {
            border-color: #8b5cf6 !important;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.1) !important;
        }
        .faq-item.active .faq-question {
            color: #8b5cf6 !important;
        }
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
            color: #8b5cf6 !important;
        }
        .faq-item.active .faq-answer {
            max-height: 500px !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(q => {
                q.addEventListener('click', () => {
                    const item = q.parentElement;
                    const isActive = item.classList.contains('active');
                    
                    document.querySelectorAll('.faq-item').forEach(other => {
                        other.classList.remove('active');
                    });
                    
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
    @endpush
    <section class="features-section">
        <div class="container" style="max-width: 1260px; margin: 0 auto; padding: 0 28px">
            <div class="features-cta-content">
                <h2>Bắt đầu luyện thi ngay hôm nay</h2>
                <a href="{{ url('/thi-thu') }}" class="btn-features-cta">
                    <i class="fas fa-rocket"></i> Thi thử miễn phí ngay
                </a>
            </div>
        </div>
    </section>
@endsection
