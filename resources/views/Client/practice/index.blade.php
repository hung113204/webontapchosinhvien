@extends('Client.layouts.app')

@section('title', 'Trung tâm Luyện tập - IT Study')



@section('content')
    {{-- ══ PAGE HEADER ══ --}}
    <div class="practice-page-header">
        <div class="container">
            <div class="breadcrumb-transparent">
                @php
                    $breadcrumbs = [
                        ['label' => 'Trang chủ', 'url' => route('home')],
                        ['label' => 'Luyện tập', 'url' => '']
                    ];
                @endphp
                @include('Client.layouts.partials.breadcrumb', ['breadcrumbs' => $breadcrumbs])
            </div>

            <div class="practice-page-title">
                <div class="practice-page-icon">
                    <i class="fas fa-pencil-alt"></i>
                </div>
                <div class="practice-page-text">
                    <h1>Trung tâm Luyện tập</h1>
                    <p>Lựa chọn học phần bạn muốn ôn tập — Cấu hình đề linh hoạt, bám sát danh mục đào tạo.</p>
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
                    <input type="text" id="search-practice" placeholder="Tìm kiếm học phần luyện tập..."
                        autocomplete="off">
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
                @foreach ($monHocs as $index => $mon)
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
                        $pctText = $pct > 0 ? $pct . '%' : 'Chưa luyện tập';
                        $fillClass = $pct > 0 ? '' : 'zero';
                        $hasImage = !empty($mon->hinh_anh);

                        // Tạo màu gradient ngẫu nhiên nhưng đẹp cho banner không có ảnh
                        $gradients = [
                            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                            'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
                        ];
                        $gradientIndex = $index % count($gradients);
                    @endphp

                    @php
                        $imageUrl = !empty($mon->hinh_anh) ? asset('storage/' . $mon->hinh_anh) : asset('frontend/asset/images/default_subject.png');
                    @endphp
                    <div class="subject-item-card" data-danhmuc="{{ $mon->danh_muc_id }}"
                        data-mucdo="{{ $mon->muc_do_mon_hoc }}" data-id="{{ $mon->id }}"
                        data-banner-height="large">

                        {{-- ── BANNER (CÓ THỂ TÙY CHỈNH CHIỀU CAO) ── --}}
                        <div class="subject-card-banner has-image">
                            <img class="banner-bg-img" src="{{ $imageUrl }}"
                                alt="{{ $mon->ten_mon_hoc }}">

                            {{-- Badge miễn phí --}}
                            {{-- <span class="banner-badge">
                                <i class="fas fa-gem"></i> MIỄN PHÍ
                            </span> --}}

                            {{-- Icon/Huy chương --}}
                            <span class="banner-medal">
                                @if (!empty($mon->icon_class))
                                    <i class="{{ $mon->icon_class }}"></i>
                                @else
                                    {{-- <i class="fas fa-certificate"></i> --}}
                                @endif
                            </span>

                            {{--  {{-- Nội dung banner --}}
                            {{-- <div class="banner-org">
                                KHOA CÔNG NGHỆ THÔNG TIN
                            </div>
                            <div class="banner-subject-name">
                                {{ Str::upper($mon->ten_mon_hoc) }}
                            </div>
                            <div class="banner-year">
                                NIÊN KHÓA 2024 - 2025
                            </div> --}}
                        </div>

                        {{-- ── CARD BODY ── --}}
                        <div class="subject-card-body">
                            <div class="card-header-row">
                                <h3>{{ Str::limit($mon->ten_mon_hoc, 35) }}</h3>
                                <span class="badge-level {{ $levelClass }}">{{ $levelText }}</span>
                            </div>

                            <p>{{ Str::limit($mon->mo_ta_ngan ?? 'Chưa có mô tả cho học phần này.', 85) }}</p>

                            <div class="subject-stat-row">
                                <span class="subject-stat-chip">
                                    <i class="fas fa-bookmark"></i> {{ $mon->ma_mon_hoc ?? 'Mã môn' }}
                                </span>
                                <span class="subject-stat-chip">
                                    <i class="fas fa-question-circle"></i> {{ number_format($mon->cau_hois_count ?? 0) }}
                                    câu
                                </span>
                                {{-- <span class="subject-stat-chip">
                                    <i class="fas fa-clock"></i> 45 phút
                                </span> --}}
                            </div>

                            <div class="subject-progress-wrap">
                                {{-- <div class="subject-progress-top">
                                    <span class="subject-progress-label">Tiến độ luyện tập</span>
                                    <span class="subject-progress-pct">{{ $pctText }}</span>
                                </div> --}}
                                {{-- <div class="subject-progress-bar">
                                    <div class="subject-progress-fill {{ $fillClass }}"
                                        style="width: {{ $pct }}%; background: linear-gradient(90deg, {{ $brandColor }}, {{ $brandColor }}dd);">
                                    </div>
                                </div> --}}
                            </div>

                            <div class="subject-card-actions">
                                <a href="{{ route('client.subjects.show', $mon->id) }}" class="btn-theory">
                                    <i class="fas fa-book-open"></i> Lý thuyết
                                </a>
                                <a href="{{ route('client.practice.setup', ['mon_hoc_id' => $mon->id]) }}"
                                    class="btn-practice"
                                    style="background: linear-gradient(135deg, {{ $brandColor }} 0%, {{ $brandColor }}dd 100%);">
                                    <i class="fas fa-pencil-alt"></i> Luyện tập
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
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
        const searchInput = document.getElementById('search-practice');
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
            document.getElementById('no-results').style.display = visible.length === 0 ? 'block' : 'none';
        }

        searchInput.addEventListener('input', applyFilters);
        mhFilter.addEventListener('change', applyFilters);
        mdFilter.addEventListener('change', applyFilters);
        clearBtn.addEventListener('click', () => {
            searchInput.value = '';
            mhFilter.value = 'all';
            mdFilter.value = 'all';
            applyFilters();
        });
    </script>
@endpush
