@extends('Client.layouts.app')

@section('title', 'Tất cả học phần - IT Study Support')

@section('styles')
    {{-- BEGIN SUBJECT BRAND HOVER CSS - doi mau ten va nut theo mau backend --}}
    <link rel="stylesheet" href="{{ asset('frontend/asset/css/subject-brand-hover.css') }}">
    {{-- END SUBJECT BRAND HOVER CSS --}}
@endsection

@push('meta')
    <meta name="base-url" content="{{ url('') }}">
@endpush

@section('content')
    {{-- ══ PAGE HEADER (Với Breadcrumb gộp chung) ══ --}}
    <div class="subjects-page-header">
        <div class="container">
            <div class="breadcrumb-transparent">
                @php
                    $breadcrumbs = [
                        ['label' => 'Trang chủ', 'url' => route('home')],
                        ['label' => 'Học phần', 'url' => '']
                    ];
                @endphp
                @include('Client.layouts.partials.breadcrumb', ['breadcrumbs' => $breadcrumbs])
            </div>

            <div class="subjects-page-title">
                <div class="subjects-page-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="subjects-page-text">
                    <h1>Tất cả học phần</h1>
                    <p>Lựa chọn học phần bạn muốn ôn tập — theo dõi tiến độ tức thì</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ MAIN SECTION ══ --}}
    <section class="subjects-section">
        <div class="container">

            {{-- ── THANH BỘ LỌC ── --}}
            <div class="filter-bar">
                <div class="filter-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-subject" placeholder="Tìm kiếm học phần..." autocomplete="off">
                </div>

                <div class="filter-selects">
                    <div class="filter-select-wrap">
                        <i class="fas fa-layer-group"></i>
                        <select id="mon-hoc-filter">
                            <option value="all">Tất cả môn học</option>
                            @foreach ($monHocs ?? [] as $mh)
                                <option value="{{ $mh->id }}">{{ $mh->ten_mon_hoc }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-select-wrap">
                        <i class="fas fa-signal"></i>
                        <select id="muc-do-filter">
                            <option value="all">Tất cả mức độ</option>
                            <option value="1">Dễ</option>
                            <option value="2">Trung bình</option>
                            <option value="3">Khó</option>
                        </select>
                    </div>

                    <button class="btn-clear-filters" id="clear-filters">
                        <i class="fas fa-times"></i> Xóa lọc
                    </button>
                </div>
            </div>

            {{-- ── LƯỚI DANH SÁCH MÔN HỌC ── --}}
            <div class="subjects-list-grid" id="subjects-grid">
                @forelse($monHocs as $index => $mon)
                    @php
                        $levelClass = match ($mon->muc_do_mon_hoc) {
                            2 => 'intermediate',
                            3, 4 => 'advanced',
                            default => 'basic',
                        };
                        $levelText = match ($mon->muc_do_mon_hoc) {
                            1 => 'Dễ',
                            2 => 'Trung bình',
                            3 => 'Khó',
                            4 => 'Rất khó',
                            default => 'Dễ',
                        };
                        $brandColor = $mon->mau_sac ?? '#3b82f6';
                        $pct = $mon->progress_percent ?? 0;
                        $pctText = $pct > 0 ? $pct . '%' : 'Chưa bắt đầu';
                        $fillClass = $pct > 0 ? '' : 'zero';
                        $imageUrl = !empty($mon->hinh_anh) ? asset('storage/' . $mon->hinh_anh) : asset('frontend/asset/images/default_subject.png');
                    @endphp

                    {{-- BEGIN SUBJECT BRAND HOVER - mau hover lay tu cot mau_sac backend --}}
                    <a href="{{ route('client.subjects.show', $mon->id) }}" class="subject-item-card"
                        style="--subject-brand-color: {{ $brandColor }};"
                        data-danhmuc="{{ $mon->danh_muc_id }}" data-mucdo="{{ $mon->muc_do_mon_hoc }}"
                        data-id="{{ $mon->id }}" data-banner-height="large">
                    {{-- END SUBJECT BRAND HOVER --}}

                        {{-- ── BANNER ── --}}
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
                                    {{--  <i class="fas fa-certificate"></i> --}}
                                @endif
                            </span>

                            {{-- <div class="banner-org">
                                KHOA CÔNG NGHỆ THÔNG TIN
                            </div>
                            <div class="banner-subject-name">
                                {{ Str::upper(Str::limit($mon->ten_mon_hoc, 25)) }}
                            </div>
                            <div class="banner-year">
                                NIÊN KHÓA 2024 - 2025
                            </div> --}}
                        </div>

                        {{-- ── CARD BODY ── --}}
                        <div class="subject-card-body">
                            <div class="card-header-row">
                                {{-- SUBJECT BRAND HOVER TARGET: ten mon hoc doi mau khi hover card --}}
                                <h3>{{ Str::limit($mon->ten_mon_hoc, 35) }}</h3>
                                <span class="badge-level {{ $levelClass }}">{{ $levelText }}</span>
                            </div>

                            <p>{{ Str::limit($mon->mo_ta_ngan ?? 'Chưa có mô tả cho học phần này.', 85) }}</p>

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
                                    <i class="fas fa-question-circle"></i> {{ number_format($mon->cau_hois_count ?? 0) }}
                                    câu
                                </span> --}}
                            </div>

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
                                {{-- SUBJECT BRAND HOVER TARGET: nut doi mau khi hover card --}}
                                <span class="btn-theory">
                                    <i class="fas fa-book-open"></i> Lý thuyết
                                </span>
                                {{-- <span class="btn-practice"
                                    style="background: linear-gradient(135deg, {{ $brandColor }} 0%, {{ $brandColor }}dd 100%);">
                                    <i class="fas fa-pencil-alt"></i> Luyện tập
                                </span> --}}
                            </div>
                        </div>

                    </a>
                @empty
                    <div class="no-data-placeholder"
                        style="grid-column: 1/-1; text-align: center; padding: 60px 24px; background: #fff; border-radius: 20px;">
                        <i class="fas fa-folder-open"
                            style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                        <h3 style="color: #1e293b; margin-bottom: 8px;">Chưa có học phần</h3>
                        <p style="color: #64748b;">Hiện tại chưa có học phần nào trong hệ thống.</p>
                    </div>
                @endforelse
            </div>

            <div class="no-results" id="no-results">
                <i class="fas fa-search"></i>
                <p>Không tìm thấy học phần phù hợp.</p>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const searchInput = document.getElementById('search-subject');
        const mhFilter = document.getElementById('mon-hoc-filter');
        const mdFilter = document.getElementById('muc-do-filter');
        const clearBtn = document.getElementById('clear-filters');
        const grid = document.getElementById('subjects-grid');
        const cards = Array.from(document.querySelectorAll('.subject-item-card'));

        function applyFilters() {
            const search = searchInput.value.toLowerCase().trim();
            const mhId = mhFilter.value;
            const md = mdFilter.value;

            let visible = cards.filter(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() ?? '';
                const cardMhId = card.dataset.id;
                const cardMd = card.dataset.mucdo;
                return (!search || name.includes(search)) &&
                    (mhId === 'all' || cardMhId == mhId) &&
                    (md === 'all' || cardMd == md);
            });

            cards.forEach(c => c.style.display = 'none');
            visible.forEach(c => {
                c.style.display = 'flex';
                grid.appendChild(c);
            });

            const noResults = document.getElementById('no-results');
            if (noResults) {
                noResults.style.display = visible.length === 0 ? 'block' : 'none';
            }
        }

        // Debounce cho input
        let debounceTimer;
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(applyFilters, 300);
            });
        }

        if (mhFilter) mhFilter.addEventListener('change', applyFilters);
        if (mdFilter) mdFilter.addEventListener('change', applyFilters);

        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                if (mhFilter) mhFilter.value = 'all';
                if (mdFilter) mdFilter.value = 'all';
                applyFilters();
            });
        }

        function updateProgressOnCards(progressData) {
            if (!Array.isArray(progressData)) return;

            progressData.forEach(prog => {
                const card = document.querySelector(`.subject-item-card[data-id="${prog.id}"]`);
                if (!card) return;

                const pctLabel = card.querySelector('.subject-progress-pct');
                const fillBar = card.querySelector('.subject-progress-fill');

                if (pctLabel) {
                    pctLabel.textContent = prog.text || (prog.percent > 0 ? prog.percent + '%' : 'Chưa bắt đầu');
                }
                if (fillBar) {
                    fillBar.style.width = prog.percent + '%';
                    if (prog.percent > 0) {
                        fillBar.classList.remove('zero');
                    } else {
                        fillBar.classList.add('zero');
                    }
                }
            });
        }
        // ── Init khi trang load ──
        function initProgress() {
            // Chỉ fetch tiến độ nếu user đã đăng nhập
            @auth
            // 1. Ưu tiên lấy từ sessionStorage (dữ liệu mới nhất từ trang show)
            let progressData = [];
            const saved = sessionStorage.getItem('progressUpdate');
            if (saved) {
                try {
                    progressData = JSON.parse(saved);
                    sessionStorage.removeItem('progressUpdate'); // xóa sau khi dùng để tránh lặp
                } catch (e) {}
            }

            // 2. Nếu không có trong session → fetch lại từ server (an toàn)
            if (progressData.length === 0) {
                fetch("{{ route('client.tiendo.getAll') }}")
                    .then(res => res.json())
                    .then(result => {
                        if (result.success && result.data) {
                            updateProgressOnCards(result.data);
                        }
                    })
                    .catch(() => {});
            } else {
                updateProgressOnCards(progressData);
            }
        @endauth
        }

        // ── Gọi init khi DOM ready ──
        document.addEventListener('DOMContentLoaded', initProgress);

        // ── LẮNG NGHE EVENT CẬP NHẬT TIẾN ĐỘ TỪ TRANG SHOW ──
        window.addEventListener('progressUpdated', function(e) {
            const progressData = e.detail; // Array of { id, percent, text }

            if (!Array.isArray(progressData)) return;

            progressData.forEach(prog => {
                // Tìm card có data-id = prog.id
                const card = document.querySelector(`.subject-item-card[data-id="${prog.id}"]`);
                if (!card) return;

                // Cập nhật phần tử tiến độ
                const pctLabel = card.querySelector('.subject-progress-pct');
                const fillBar = card.querySelector('.subject-progress-fill');

                if (pctLabel) {
                    pctLabel.textContent = prog.text;
                }
                if (fillBar) {
                    fillBar.style.width = prog.percent + '%';
                    // Xóa class 'zero' nếu có tiến độ
                    if (prog.percent > 0) {
                        fillBar.classList.remove('zero');
                    }
                }
            });
        });
    </script>
@endpush
