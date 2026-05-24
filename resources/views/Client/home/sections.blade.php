{{-- ══════════════════════════════════════════════
   SECTION: Ôn tập nhanh + Luyện tập theo chủ đề
══════════════════════════════════════════════ --}}

<section class="section ontap-luyen-section" style="background: #f0f7ff; padding: 72px 0;">
    <div class="container">
        <div class="ontap-luyen-grid">

            {{-- ── Ôn tập nhanh ── --}}
            <div class="quick-review-box slide-from-left">
                <div class="qr-header">
                    <div class="qr-header-icon"><i class="fas fa-bolt"></i></div>
                    <div>
                        <h3>Ôn tập nhanh</h3>
                        <p>Luyện tập ngẫu nhiên 10 câu hỏi trong vòng 5 phút để kiểm tra kiến thức của bạn.</p>
                    </div>
                </div>

                <div class="qr-form">
                    <form action="{{ route('client.practice.setup', '') }}" method="GET" class="qr-form"
                        id="form-on-tap-nhanh">
                        <div class="qr-select-wrap">
                            <label>Chọn môn học</label>
                            <div class="qr-select">
                                <select name="mon_hoc_id" required>
                                    <option value="">-- Chọn môn học --</option>
                                    @foreach ($danhSachMon as $mon)
                                        <option value="{{ $mon->id }}">{{ $mon->ten_mon_hoc }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>

                        <div class="qr-select-wrap">
                            <label>Chọn mức độ</label>
                            <div class="qr-select">
                                <select name="muc_do">
                                    <option value="">-- Tất cả mức độ --</option>
                                    <option value="1">Dễ</option>
                                    <option value="2">Trung bình</option>
                                    <option value="3">Khó</option>
                                </select>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-start-quick" style="border:none; width:100%;">
                            <i class="fas fa-play-circle"></i> Bắt đầu ngay
                        </button>
                    </form>
                    {{-- <div class="qr-select-wrap">
                        <label>Chọn mức độ</label>
                        <div class="qr-select">
                            <select name="muc_do">
                                <option value="">-- Tất cả mức độ --</option>
                                <option value="1">Dễ</option>
                                <option value="2">Trung bình</option>
                                <option value="3">Khó</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <a href="{{ url('/luyen-tap/nhanh') }}" class="btn-start-quick">
                        <i class="fas fa-play-circle"></i> Bắt đầu ngay
                    </a> --}}
                </div>

                <div class="qr-illustration">
                    <i class="fas fa-stopwatch"></i>
                </div>
            </div>

            {{-- ── Luyện tập theo chủ đề ── --}}
            <div class="topic-practice-box slide-from-right">
                <div class="topic-header-row">
                    <h3><i class="fas fa-layer-group"></i> Luyện tập theo chủ đề</h3>
                    <a href="{{ url('/luyen-tap') }}" class="link-xem-tat-ca">Xem tất cả <i
                            class="fas fa-arrow-right"></i></a>
                </div>

                <div class="topics-grid">
                    @foreach ($chuDe as $cd)
                        <a href="{{ route('client.practice.setup', $cd->id) }}" class="topic-card">
                            <div class="topic-icon"
                                style="background: {{ $cd->mau_sac }}1a; color: {{ $cd->mau_sac }};">
                                <i class="{{ $cd->icon_class ?? 'fas fa-book' }}"></i>
                            </div>
                            <div class="topic-name">{{ $cd->ten_mon_hoc }}</div>
                            <div class="topic-count">{{ $cd->cau_hois_count }} câu hỏi</div>
                            <div class="topic-action">Luyện ngay <i class="fas fa-chevron-right"></i></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section exams-section" style="background: #fff; padding: 72px 0;">
    <div class="container">
        @php $examCategory = $danhMucTrangChu->where('loai_danh_muc', 'exam_list')->first(); @endphp
        @if($examCategory)
            <div class="section-header">
                <!-- <div class="section-tag" style="background: rgba(59, 130, 246, 0.1); color: var(--blue); border-color: rgba(59, 130, 246, 0.3);">{{ $examCategory->tieu_de }}</div> -->
                <h2>{{ $examCategory->tieu_de }}</h2>
                <p>{{ $examCategory->mo_ta }}</p>
            </div>
        @else
            <div class="section-header">
                <div class="section-tag" style="background: rgba(59, 130, 246, 0.1); color: var(--blue); border-color: rgba(59, 130, 246, 0.3);">Đề thi</div>
                <h2>Đề thi mới nhất</h2>
                <p>Tổng hợp các đề thi trắc nghiệm mới cập nhật, bám sát chương trình học</p>
            </div>
        @endif

        <div class="exams-grid">
            @forelse ($deThu as $de)
                @php
                    $history = $lichSuThi[$de->id] ?? [];
                    $lanTotNhat = collect($history)->sortByDesc('diem')->first();
                    $now = \Carbon\Carbon::now();
                    $conHan = !$de->thoi_gian_ket_thuc || $now->lt($de->thoi_gian_ket_thuc);
                @endphp

                <div class="exam-card">
                    <div class="exam-card-header">
                        <div class="exam-card-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="exam-tags">
                            <span class="tag time">
                                <i class="fas fa-clock"></i> {{ $de->thoi_gian_phut }} phút
                            </span>
                            <span class="tag questions">
                                {{ $de->cau_hois_count }} câu
                            </span>
                            @if (!$conHan)
                                <span class="tag level-hard">Đã đóng</span>
                            @elseif ($de->thoi_gian_bat_dau && $now->lt($de->thoi_gian_bat_dau))
                                <span class="tag level-mid">Chưa mở</span>
                            @else
                                <span class="tag level-easy">Đang diễn ra</span>
                            @endif
                        </div>
                    </div>

                    <h3>{{ $de->ten_bai }}</h3>
                    <p>{{ $de->mo_ta ?? 'Đề thi tổng hợp kiến thức môn ' . $de->monHoc->ten_mon_hoc }}</p>

                    {{-- Thống kê --}}
                    <div class="exam-stats-row">
                        <div class="exam-stat">
                            <div class="exam-stat-icon"><i class="fas fa-users"></i></div>
                            <div class="exam-stat-info">
                                <span class="exam-stat-value">{{ number_format($de->so_luot_thi) }}</span>
                                <span class="exam-stat-label">lượt thi</span>
                            </div>
                        </div>
                        <div class="exam-stat">
                            <div class="exam-stat-icon"><i class="fas fa-chart-line"></i></div>
                            <div class="exam-stat-info">
                                <span class="exam-stat-value">{{ number_format($de->diem_trung_binh, 1) }}/10</span>
                                <span class="exam-stat-label">điểm TB</span>
                            </div>
                        </div>
                    </div>

                    {{-- Lịch sử thi --}}
                    <div class="exam-history">
                        <div class="exam-history-title">
                            <i class="fas fa-history"></i> Lịch sử thi của bạn
                        </div>
                        @if (empty($history))
                            <div class="no-history">
                                <i class="fas fa-exclamation-circle"></i> Bạn chưa thi lần nào
                            </div>
                        @else
                            @foreach (array_slice($history, 0, 1) as $h)
                                <div class="exam-history-item">
                                    {{ \Carbon\Carbon::parse($h['thoi_gian_nop_bai'])->format('d/m/Y') }}
                                    <span class="exam-history-score">{{ $h['diem'] }}/10</span>
                                    @php
                                        $giay = \Carbon\Carbon::parse($h['thoi_gian_vao_thi'])->diffInSeconds(
                                            $h['thoi_gian_nop_bai'],
                                        );
                                    @endphp
                                    <span class="exam-history-time">{{ floor($giay / 60) }} phút</span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <a href="{{ route('client.exams.show', $de->id) }}" class="btn-start">
                        <i class="fas fa-play-circle"></i> Xem đề & Bắt đầu thi
                    </a>
                </div>
            @empty
                <div class="no-data-placeholder"
                    style="grid-column: 1/-1; text-align: center; padding: 60px 24px; background: #fff; border-radius: 20px; border: 1.5px solid #e2e8f0; width: 100%;">
                    <i class="fas fa-folder-open"
                        style="font-size: 3rem; color: #cbd5e1; margin-bottom: 16px; display: block;"></i>
                    <h3 style="color: #1e293b; margin-bottom: 8px;">Chưa có đề thi nào</h3>
                    <p style="color: #64748b;">Hiện tại chưa có đề thi nào trong hệ thống.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
{{-- ══════════════════════════════════════════════
   SECTION: Tiến độ học tập (Redesign)
   Layout: 3 card ngang — 1.5fr | 1fr | 1.5fr
══════════════════════════════════════════════ --}}

<section class="section tiendo-section">
    <div class="container">
        <div class="tiendo-3col-grid">

            {{-- ══ CỘT 1: TIẾN ĐỘ HỌC TẬP ══ --}}
            <div class="td-card td-progress-card">
                <div class="td-card-header">
                    <div class="td-header-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="td-card-title">Tiến độ học tập</h3>
                </div>

                <div class="td-progress-list">
                    @php
                        $tienDoMau = [
                            ['ten' => 'Cấu trúc dữ liệu và Giải thuật', 'phan_tram' => 35, 'mau' => '#3b82f6'],
                            ['ten' => 'Lập trình hướng đối tượng (C++)', 'phan_tram' => 28, 'mau' => '#8b5cf6'],
                            ['ten' => 'Cơ sở dữ liệu', 'phan_tram' => 22, 'mau' => '#06b6d4'],
                            ['ten' => 'Mạng máy tính', 'phan_tram' => 18, 'mau' => '#10b981'],
                        ];
                    @endphp

                    @foreach ($tienDoMon ?? $tienDoMau as $mon)
                        <div class="td-progress-item">
                            <div class="td-progress-meta">
                                <span class="td-subject-name">{{ $mon['ten'] }}</span>
                                <span class="td-subject-pct"
                                    style="color: {{ $mon['mau'] }};">{{ $mon['phan_tram'] }}%</span>
                            </div>
                            <div class="td-bar-track">
                                <div class="td-bar-fill tiendo-fill" data-width="{{ $mon['phan_tram'] }}"
                                    style="width: 0%; background: {{ $mon['mau'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ CỘT 2: GIỮ VỮNG PHONG ĐỘ ══ --}}
            <div class="td-card td-streak-card">
                <div class="td-streak-inner">
                    <div class="td-streak-badge">
                        <!-- Phóng to ảnh streak1.png -->
                        <img src="{{ asset('frontend/asset/images/streak1.png') }}" alt="Streak Fire"
                            style="width: 50px; height: 50px; object-fit: contain;">
                    </div>

                    <p class="td-streak-label">Giữ vững phong độ</p>
                    <p class="td-streak-sub">Bạn đã học tiến đạt</p>

                    <div class="td-streak-number">
                        @auth
                            {{ $streak ?? 5 }}
                        @else
                            —
                        @endauth
                    </div>
                    <p class="td-streak-unit">ngày</p>

                    <div class="td-streak-check">
                        @auth
                            <div class="td-check-icon">
                                <!-- Thay icon Font Awesome bằng ảnh -->
                                <img src="{{ asset('frontend/asset/images/streak.png') }}" alt="Streak icon"
                                    class="td-streak-img">
                            </div>
                            <span class="td-streak-text">Cố gắng lên! Bạn đang làm rất tốt.</span>
                        @else
                            <a href="{{ url('/dang-nhap') }}" class="td-login-link">
                                Đăng nhập để theo dõi <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- ══ CỘT 3: TÀI LIỆU NỔI BẬT + AI ══ --}}
            <div class="td-card td-docs-card">
                {{-- Header hàng trên --}}
                <div class="td-docs-header">
                    <h3 class="td-card-title">Tài liệu nổi bật</h3>
                    <a href="{{ url('/tai-lieu') }}" class="td-link-more">
                        Xem tất cả <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Layout 2 cột: danh sách + box AI --}}
                <div class="td-docs-body">
                    {{-- Danh sách tài liệu --}}
                    <div class="td-docs-list">
                        @php
                            $taiLieuMau = [
                                ['ten' => 'Tổng hợp công thức và thuật toán', 'size' => '2.4 MB', 'mau' => '#ef4444'],
                                ['ten' => 'Bài tập C++ lời giải chi tiết', 'size' => '1.8 MB', 'mau' => '#f97316'],
                                ['ten' => 'Tổng hợp câu hỏi SQL', 'size' => '1.3 MB', 'mau' => '#3b82f6'],
                            ];
                        @endphp

                        @foreach ($taiLieuMoi ?? $taiLieuMau as $tl)
                            <a href="{{ url('/tai-lieu/' . ($tl['id'] ?? '')) }}" class="td-doc-item">
                                <div class="td-doc-icon" style="color: {{ $tl['mau'] ?? '#3b82f6' }};">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="td-doc-info">
                                    <div class="td-doc-name">{{ $tl['ten'] }}</div>
                                    <div class="td-doc-meta">PDF · {{ $tl['size'] ?? '' }}</div>
                                </div>
                                <div class="td-doc-dl">
                                    <i class="fas fa-download"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Box AI --}}
                    <div class="td-ai-box">
                        <div class="td-ai-icon">
                            <!-- Chèn ảnh vào đây -->
                            <img src="{{ asset('./frontend/asset/images/t2.png') }}"
                                style="width:60px;height:60px;object-fit:contain;border-radius:6px;" alt="AI Icon">
                        </div>
                        <div class="td-ai-title">Hỏi đáp với AI</div>
                        <div class="td-ai-sub">Bạn có thắc mắc? AI sẽ giúp bạn giải đáp mọi thắc mắc!</div>
                        <a href="{{ route('client.ai.index') }}" class="td-ai-btn">
                            Hỏi ngay
                        </a>
                    </div>
                </div>
            </div>

        </div>{{-- /.tiendo-3col-grid --}}
    </div>
</section>

{{-- ══════════════════════════════════════════════
   SECTION: Câu hỏi thường gặp (FAQ)
══════════════════════════════════════════════ --}}
<section class="section faq-section">
    <div class="faq-bg-circle-1"></div>
    <div class="faq-bg-circle-2"></div>
    <div class="faq-container">
        <div class="faq-header">
            <h2>Câu hỏi thường gặp</h2>
            <p>Giải đáp những thắc mắc phổ biến về IT Study Support</p>
        </div>
        <div class="faq-list">
            <div class="faq-item">
                <div class="faq-question">
                    <span>IT Study Support có miễn phí không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    IT Study Support cung cấp các tính năng cốt lõi hoàn toàn miễn phí. Tuy nhiên, chúng tôi cũng có các gói trả phí với nhiều tính năng nâng cao.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>Tôi cần đăng ký để sử dụng không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Bạn có thể xem một số tài liệu cơ bản mà không cần đăng ký. Tuy nhiên, để lưu tiến độ học tập, làm bài kiểm tra và theo dõi thống kê, bạn cần tạo một tài khoản.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>Có app mobile không?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Hiện tại IT Study Support hoạt động tốt nhất trên nền tảng web (tương thích trên cả điện thoại). Ứng dụng di động đang trong quá trình phát triển.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>Dữ liệu học tập của tôi được lưu ở đâu?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Tất cả dữ liệu của bạn được lưu trữ an toàn trên hệ thống máy chủ đám mây bảo mật của chúng tôi.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>Streak và XP hoạt động như thế nào?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Streak tăng khi bạn có hoạt động học tập liên tiếp mỗi ngày. XP là điểm kinh nghiệm bạn nhận được khi hoàn thành các bài tập và đề thi.
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <span>Làm sao để liên hệ hỗ trợ?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Bạn có thể liên hệ với đội ngũ hỗ trợ qua email hung113204@gmail.com hoặc qua fanpage chính thức của chúng tôi.
                </div>
            </div>
        </div>
    </div>
</section>
{{-- ══════════════════════════════════════════════
   SECTION: Tiến độ của bạn
══════════════════════════════════════════════ --}}
{{-- <section class="section tiendo-section" style="background: #f0f7ff; padding: 72px 0;">
    <div class="container">
        <div class="tiendo-header-row">
            <div class="tiendo-title-wrap">
                <div class="tiendo-title-icon"><i class="fas fa-chart-line"></i></div>
                <h2>Tiến độ của bạn</h2>
            </div>
            <a href="{{ url('/ca-nhan/tien-do') }}" class="link-xem-tat-ca">Xem chi tiết <i
                    class="fas fa-arrow-right"></i></a>
        </div>

        <div class="tiendo-grid">
            {{-- Progress cards --}}
{{-- <div class="tiendo-cards-wrap">
                @php
                    $tienDoMau = [
                        ['ten' => 'Cấu trúc dữ liệu và giải thuật', 'phan_tram' => 75, 'mau' => '#8b5cf6'],
                        ['ten' => 'Lập trình C/C++', 'phan_tram' => 90, 'mau' => '#3b82f6'],
                        ['ten' => 'Cơ sở dữ liệu', 'phan_tram' => 15, 'mau' => '#06b6d4'],
                        ['ten' => 'Kỹ thuật lập trình', 'phan_tram' => 5, 'mau' => '#f59e0b'],
                    ];
                @endphp

                @foreach ($tienDoMon ?? $tienDoMau as $mon)
                    <div class="tiendo-card">
                        <div class="tiendo-card-name">{{ $mon['ten'] }}</div>
                        <div class="tiendo-bar-wrap">
                            <div class="tiendo-bar">
                                <div class="tiendo-fill" data-width="{{ $mon['phan_tram'] }}"
                                    style="width: 0%; background: {{ $mon['mau'] ?? '#3b82f6' }};">
                                </div>
                            </div>
                            <span class="tiendo-pct"
                                style="color: {{ $mon['mau'] ?? '#3b82f6' }};">{{ $mon['phan_tram'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Streak / Motivational card --}}
{{--  @auth
                <div class="streak-card">
                    <div class="streak-fire"><i class="fas fa-fire"></i></div>
                    <div class="streak-text">
                        <strong>Giữ vững phong độ!</strong>
                        <span>Bạn đã học <span class="streak-days">{{ $streak ?? 12 }}</span> ngày liên tiếp 🔥</span>
                    </div>
                    <a href="{{ url('/ca-nhan/tien-do') }}" class="btn-xem-thong-ke">Xem thống kê chi tiết <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            @else
                <div class="streak-card streak-card--guest">
                    <div class="streak-fire"><i class="fas fa-user-graduate"></i></div>
                    <div class="streak-text">
                        <strong>Theo dõi tiến độ học tập</strong>
                        <span>Đăng nhập để xem tiến độ học tập và streak của bạn!</span>
                    </div>
                    <a href="{{ url('/dang-nhap') }}" class="btn-xem-thong-ke">Đăng nhập ngay <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            @endauth
        </div>
    </div>
</section> --}}

{{-- ══════════════════════════════════════════════
   SECTION: Tài liệu mới cập nhật
══════════════════════════════════════════════ --}}
{{-- <section class="section tailieu-section" style="background: #fff; padding: 72px 0;">
    <div class="container">
        <div class="tailieu-header-row">
            <div class="tailieu-title-wrap">
                <div class="tailieu-title-icon"><i class="fas fa-file-download"></i></div>
                <h2>Tài liệu mới cập nhật</h2>
            </div>
            <a href="{{ url('/tai-lieu') }}" class="link-xem-tat-ca">Xem tất cả <i
                    class="fas fa-arrow-right"></i></a>
        </div>

        <div class="tailieu-grid">
            @php
                $taiLieuMau = [
                    [
                        'ten' => 'Tóm tắt Cấu trúc dữ liệu',
                        'so_trang' => '45 trang',
                        'loai' => 'pdf',
                        'mau' => '#ef4444',
                        'mo_ta' => 'Tóm tắt toàn bộ lý thuyết CTDL',
                    ],
                    [
                        'ten' => 'Bài tập C++ cơ bản',
                        'so_trang' => '30 bài tập',
                        'loai' => 'pdf',
                        'mau' => '#f97316',
                        'mo_ta' => 'Bài tập có lời giải chi tiết',
                    ],
                    [
                        'ten' => 'SQL tổng hợp',
                        'so_trang' => '50 câu hỏi',
                        'loai' => 'pdf',
                        'mau' => '#3b82f6',
                        'mo_ta' => 'Câu hỏi ôn tập SQL thực hành',
                    ],
                    [
                        'ten' => 'Thuật toán sắp xếp',
                        'so_trang' => '20 trang',
                        'loai' => 'pdf',
                        'mau' => '#8b5cf6',
                        'mo_ta' => 'Minh họa trực quan từng bước',
                    ],
                    [
                        'ten' => 'Đề thi giữa kỳ 2024',
                        'so_trang' => 'Đề + đáp án',
                        'loai' => 'pdf',
                        'mau' => '#10b981',
                        'mo_ta' => 'Đề thi chính thức có đáp án',
                    ],
                ];
            @endphp

            @foreach ($taiLieuMoi ?? $taiLieuMau as $tl)
                <a href="{{ url('/tai-lieu/' . ($tl['id'] ?? '')) }}" class="tailieu-card">
                    <div class="tailieu-icon" style="background: {{ $tl['mau'] ?? '#3b82f6' }}1a;">
                        <i class="fas fa-file-pdf" style="color: {{ $tl['mau'] ?? '#3b82f6' }};"></i>
                    </div>
                    <div class="tailieu-info">
                        <div class="tailieu-name">{{ $tl['ten'] }}</div>
                        <div class="tailieu-meta">{{ $tl['mo_ta'] ?? '' }}</div>
                        <div class="tailieu-size">{{ $tl['so_trang'] }}</div>
                    </div>
                    <div class="tailieu-download">
                        <i class="fas fa-download"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section> --}}

<script>
    document.getElementById('form-on-tap-nhanh').addEventListener('submit', function(e) {
        e.preventDefault();
        const monId = this.querySelector('[name=mon_hoc_id]').value;
        const mucDo = this.querySelector('[name=muc_do]').value;
        if (!monId) return;
        let url = `/luyen-tap/thiet-lap/${monId}`;
        if (mucDo) url += `?muc_do=${mucDo}`;
        window.location.href = url;
    });

    // ── Animate tiến độ bars khi scroll đến ──
    (function() {
        const fills = document.querySelectorAll('.tiendo-fill');
        if (!fills.length) return;

        function animateFill(fill) {
            const target = fill.getAttribute('data-width');
            if (!target) return;

            // Reset về 0 ngay lập tức trước khi chạy hiệu ứng
            fill.style.transition = 'none';
            fill.style.width = '0%';

            // Dùng double requestAnimationFrame để trình duyệt kịp nhận diện trạng thái width: 0%
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    fill.style.transition = 'width 1.4s cubic-bezier(0.22, 1, 0.36, 1)';
                    fill.style.width = target + '%';
                });
            });
        }

        function resetFill(fill) {
            // Đưa về 0% khi khuất khỏi màn hình để sẵn sàng cho lần cuộn tới
            fill.style.transition = 'none';
            fill.style.width = '0%';
        }

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        // Khi nhìn thấy: chạy hiệu ứng tăng thanh tiến độ
                        animateFill(entry.target);
                    } else {
                        // Khi khuất màn hình: reset về 0 (Xóa bỏ unobserve tại đây)
                        resetFill(entry.target);
                    }
                });
            }, {
                threshold: 0.2,
                rootMargin: '0px 0px -40px 0px'
            });

            fills.forEach(function(fill) {
                observer.observe(fill);
            });
        } else {
            fills.forEach(animateFill);
        }
    })();
    // ── Slide-in khi scroll ──
    (function() {
        const slideEls = document.querySelectorAll('.slide-from-left, .slide-from-right');
        if (!slideEls.length) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Khi lăn chuột đến: thêm class để hiện ra
                    entry.target.classList.add('slide-in-visible');
                } else {
                    // Khi lăn chuột qua khỏi: xóa class để có thể trượt lại lần sau
                    entry.target.classList.remove('slide-in-visible');
                }
            });
        }, {
            threshold: 0.15, // Giảm xuống một chút để nhạy hơn khi cuộn
            rootMargin: '0px 0px -50px 0px'
        });

        slideEls.forEach(function(el) {
            observer.observe(el);
        });
    })();

    // ── FAQ Accordion ──
    document.addEventListener('DOMContentLoaded', function() {
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(q => {
            q.addEventListener('click', () => {
                const item = q.parentElement;
                
                // Đóng các câu hỏi khác (nếu muốn)
                const isActive = item.classList.contains('active');
                document.querySelectorAll('.faq-item').forEach(other => {
                    other.classList.remove('active');
                });
                
                // Mở câu hỏi hiện tại nếu trước đó nó chưa mở
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
</script>
