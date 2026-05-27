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

    <section class="section" style="background: #f0f7ff">
        <div class="container">
            @php $courseCategory = $danhMucTrangChu->where('loai_danh_muc', 'course_list')->first(); @endphp
            @if($courseCategory)
                <div class="section-header">
                    <!-- <div class="section-tag">{{ $courseCategory->tieu_de }}</div> -->
                    <h2>{{ $courseCategory->tieu_de }}</h2>
                    <p>{{ $courseCategory->mo_ta }}</p>
                </div>
            @else
                <div class="section-header">
                    <div class="section-tag">Học phần</div>
                    <h2>Học phần nổi bật</h2>
                    <p>Các môn học quan trọng trong chương trình đào tạo CNTT</p>
                </div>
            @endif

            <div class="subjects-grid">
                {{-- Kiểm tra xem có dữ liệu môn học không --}}
                @if (isset($monHocNoiBat) && $monHocNoiBat->isNotEmpty())
                    @foreach ($monHocNoiBat as $mon)
                        @php
                            $imageUrl = !empty($mon->hinh_anh) ? asset('storage/' . $mon->hinh_anh) : asset('frontend/asset/images/default_subject.png');
                            $brandColor = $mon->mau_sac ?? '#3b82f6';
                        @endphp
                        <div class="subject-item-card"
                            style="--subject-brand-color: {{ $brandColor }};"
                            data-banner-height="large">
                            {{-- Banner --}}
                            <div class="subject-card-banner has-image">
                                <img class="banner-bg-img" src="{{ $imageUrl }}"
                                    alt="{{ $mon->ten_mon_hoc }}">

                                {{-- <span class="banner-badge">
                                    <i class="fas fa-gem"></i> MIỄN PHÍ
                                </span> --}}

                                <span class="banner-medal">
                                    @if (!empty($mon->icon_class))
                                        <i class="{{ $mon->icon_class }}"></i>
                                    @else
                                        {{-- <i class="fas fa-certificate"></i> --}}
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
                                            1 => 'Dễ',
                                            2 => 'Trung bình',
                                            3 => 'Khó',
                                            4 => 'Rất khó',
                                            default => 'Dễ',
                                        };
                                    @endphp
                                    <span class="badge-level {{ $levelClass }}">{{ $levelText }}</span>
                                </div>

                                <p>{{ \Illuminate\Support\Str::limit($mon->mo_ta_ngan ?? 'Chưa có mô tả cho học phần này.', 85) }}
                                </p>

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
                                    {{-- <span class="subject-stat-chip">
                                        <i class="fas fa-question-circle"></i>
                                        {{ number_format($mon->cau_hois_count ?? 0) }} câu
                                    </span> --}}
                                </div>

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
                @else
                    <div class="no-data-placeholder"
                        style="grid-column: 1/-1; text-align: center; padding: 60px 24px; background: #fff; border-radius: 20px;">
                        <i class="fas fa-folder-open"
                            style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                        <h3 style="color: #1e293b; margin-bottom: 8px;">Chưa có học phần nổi bật</h3>
                        <p style="color: #64748b;">Hiện tại chưa có học phần nổi bật nào trong hệ thống.</p>
                    </div>
                @endif
            </div>

        </div>
    </section>
    @include('Client.home.sections')

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
