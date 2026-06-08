@extends('Admin.layouts.admin')
@section('title', 'Kết quả bài thi')

@section('header_action')
    <button class="btn btn-success" onclick="exportExcel()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #059669; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" y1="15" x2="12" y2="3" />
        </svg>
        <span style="font-weight: 500;">Xuất Excel</span>
    </button>
@endsection

@section('content')

    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <!-- MathJax config -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
                processEscapes: true
            },
            options: {
                ignoreHtmlClass: 'tex2jax_ignore',
                processHtmlClass: 'tex2jax_process'
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <link rel="stylesheet" href="{{ asset('backend/asset/css/ketquathi.css') }}">
    <style>
        /* Premium Code Block Styling */
        #detailContent pre {
            background: #1e1e2f !important;
            color: #abb2bf !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            padding: 16px 20px !important;
            border-radius: 8px !important;
            overflow-x: auto !important;
            font-family: 'JetBrains Mono', 'Consolas', 'Monaco', monospace !important;
            font-size: 14px !important;
            line-height: 1.6 !important;
            margin: 14px 0 !important;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12) !important;
        }
        #detailContent code {
            font-family: 'JetBrains Mono', 'Consolas', 'Monaco', monospace !important;
            font-size: 14px;
        }
        #detailContent pre code {
            background: transparent !important;
            color: inherit !important;
            padding: 0 !important;
            font-family: 'JetBrains Mono', 'Consolas', 'Monaco', monospace !important;
            font-size: 14px !important;
        }
    </style>

    {{-- Thống kê nhanh --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon indigo"><i class="fas fa-file-alt"></i></div>
            <div>
                <div class="stat-val">{{ $thongKe->total() }}</div>
                <div class="stat-lbl">Tổng số bài thi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-val">{{ $thongKe->sum('so_sv_thi') }}</div>
                <div class="stat-lbl">Lượt sinh viên thi</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="stat-val">{{ $thongKe->count() > 0 ? number_format($thongKe->avg('diem_trung_binh'), 1) : '—' }}</div>
                <div class="stat-lbl">Điểm trung bình</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-trophy"></i></div>
            <div>
                <div class="stat-val">{{ $thongKe->max('diem_cao_nhat') ?? '—' }}</div>
                <div class="stat-lbl">Điểm cao nhất</div>
            </div>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <section class="filters-section">
        <form action="{{ route('admin.ketquathi.index') }}" method="GET">
            <div class="filter-group" style="grid-template-columns: 2fr 1fr auto auto;">
                <div class="filter-item">
                    <label>Tìm kiếm</label>
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Tên bài kiểm tra, môn học..." value="{{ $request->search ?? '' }}">
                    </div>
                </div>
                <div class="filter-item">
                    <label>Môn học</label>
                    <select name="mon_hoc_id" class="form-select">
                        <option value="">Tất cả môn học</option>
                        @foreach ($dsMonHoc as $mon)
                            <option value="{{ $mon->id }}" {{ ($request->mon_hoc_id ?? '') == $mon->id ? 'selected' : '' }}>
                                {{ $mon->ten_mon_hoc }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-item">
                    <button type="submit" class="btn btn-primary" style="background:#4F46E5; color:white; padding:10px 20px; border-radius:8px; border:none;">Lọc dữ liệu</button>
                </div>
                <div class="filter-item">
                    <a href="{{ route('admin.ketquathi.index') }}" class="btn btn-secondary" style="background:#F3F4F6; padding:10px 20px; border-radius:8px; text-decoration:none; color:black;">Reset</a>
                </div>
            </div>
        </form>
    </section>

    {{-- Bảng dữ liệu --}}
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách kết quả thi</h3>
                    <span class="count-badge">Dữ liệu hệ thống</span>
                </div>
            </div>
            <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Bài kiểm tra</th>
                        <th>Môn học</th>
                        <th style="text-align:center;">Số SV đã thi</th>
                        <th>Điểm trung bình</th>
                        <th style="text-align:center;">Điểm cao nhất</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($thongKe as $index => $row)
                        @php
                            $tb = floatval($row->diem_trung_binh);
                            $scoreClass = $tb >= 7.5 ? 'high' : ($tb >= 5 ? 'mid' : 'low');
                            $pct = ($tb / 10) * 100;
                        @endphp
                        <tr onclick="openExamModal({{ $row->bai_thi_id }})" style="cursor:pointer;">
                            <td>{{ $thongKe->firstItem() + $index }}</td>
                            <td><strong>{{ $row->ten_bai }}</strong></td>
                            <td><span class="badge badge-indigo">{{ $row->ten_mon_hoc }}</span></td>
                            <td style="text-align:center;">{{ $row->so_sv_thi }} SV</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="score-bar-bg"><div class="score-bar-fill {{ $scoreClass }}" style="width:{{ $pct }}%"></div></div>
                                    <span style="font-weight:bold;">{{ number_format($tb, 1) }}</span>
                                </div>
                            </td>
                            <td style="text-align:center; color:#059669; font-weight:bold;">{{ number_format($row->diem_cao_nhat, 1) }}</td>
                            <td onclick="event.stopPropagation()">
                                <button class="btn-view" onclick="openExamModal({{ $row->bai_thi_id }})" style="border:none; background:#EEF2FF; color:#4F46E5; padding:6px 10px; border-radius:6px; cursor:pointer;">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center; padding:30px;">Không có dữ liệu bài thi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <div class="showing-info">
                Hiển thị từ {{ $thongKe->firstItem() ?? 0 }} đến {{ $thongKe->lastItem() ?? 0 }}
                trong tổng số {{ $thongKe->total() }} kết quả
            </div>
            <div class="pagination-wrapper">
                {{ $thongKe->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
        </div>
    </section>

    {{-- Modal Danh sách SV --}}
    <div id="modalExam" class="modal-overlay" onclick="closeModal('modalExam')">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="examModalTitle">Danh sách sinh viên</h3>
                <button onclick="closeModal('modalExam')" style="border:none; background:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div id="examStudentLoading" style="text-align:center; display:none;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>
                <div id="examStudentTable">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th style="text-align:center;">Điểm</th>
                                <th style="text-align:center;">Số câu đúng</th>
                                <th style="text-align:center;">Thời gian</th>
                                <th width="80">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody id="examStudentBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Chi tiết bài làm sinh viên --}}
    <div id="modalDetail" class="modal-overlay" onclick="closeModal('modalDetail')">
        <div class="modal-card" style="max-width: 1200px;" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="detailModalTitle">Chi tiết bài làm</h3>
                <button onclick="closeModal('modalDetail')" style="border:none; background:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body" style="padding:24px; max-height: 80vh; overflow-y: auto;">
                <div id="detailLoading" style="text-align:center; padding:40px; display:none;">
                    <i class="fas fa-spinner fa-spin fa-2x" style="color:#4F46E5;"></i>
                    <p style="margin-top:12px; color:#6B7280;">Đang tải chi tiết...</p>
                </div>
                <div id="detailContent">
                    {{-- Thông tin tổng quát --}}
                    <div style="background: #F9FAFB; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Mã SV:</strong>
                            <div id="detail_maSv" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Sinh viên:</strong>
                            <div id="detail_tenSv" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Bài thi:</strong>
                            <div id="detail_tenBai" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Môn học:</strong>
                            <div id="detail_monHoc" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Điểm:</strong>
                            <div id="detail_diem" style="font-size: 16px; font-weight: bold; margin-top: 4px; color: #059669;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Số câu đúng:</strong>
                            <div id="detail_cauDung" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Tỷ lệ:</strong>
                            <div id="detail_tyLe" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Tổng thời gian:</strong>
                            <div id="detail_thongGian" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Thời gian vào thi:</strong>
                            <div id="detail_gioVao" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                        <div>
                            <strong style="color: #6B7280; font-size: 12px;">Thời gian nộp bài:</strong>
                            <div id="detail_gioNop" style="font-size: 14px; margin-top: 4px;">—</div>
                        </div>
                    </div>

                    {{-- Chi tiết từng câu hỏi --}}
                    <div id="detailQuestionsContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ✅ FIX: Dùng route() của Laravel thay vì hardcode URL
        const AJAX_URL     = "{{ route('admin.ketquathi.ajax.danh-sach', ['bai_thi_id' => '__ID__']) }}";
        const DETAIL_URL   = "{{ route('admin.ketquathi.detail', ['id' => '__ID__']) }}";
        const EXPORT_URL   = "{{ route('admin.ketquathi.export') }}";

        // ✅ FIX: Helper headers dùng chung — thêm X-Requested-With để Laravel nhận diện AJAX
        function ajaxHeaders() {
            return {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? ''
            };
        }

        function openExamModal(baiThiId) {
            const loading = document.getElementById('examStudentLoading');
            const body    = document.getElementById('examStudentBody');
            
            loading.style.display = 'block';
            body.innerHTML = '';
            document.getElementById('modalExam').classList.add('show');

            const url = AJAX_URL.replace('__ID__', baiThiId);

            fetch(url, { headers: ajaxHeaders() })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                loading.style.display = 'none';
                
                if (!data.success) {
                    body.innerHTML = `<tr><td colspan="6" style="text-align:center; color:red;">Lỗi: ${data.message}</td></tr>`;
                    return;
                }
                
                document.getElementById('examModalTitle').textContent = data.ten_bai;
                
                let html = '';
                if (data.sinh_viens && Array.isArray(data.sinh_viens)) {
                    data.sinh_viens.forEach(sv => {
                        html += `<tr>
                            <td><code>${sv.ma_sv}</code></td>
                            <td>${sv.ho_ten}</td>
                            <td style="text-align:center; font-weight:bold;">${sv.diem}</td>
                            <td style="text-align:center;">${sv.so_cau_dung}/${sv.tong_so_cau}</td>
                            <td style="text-align:center;">${sv.tong_thoi_gian} phút</td>
                            <td>
                                <button onclick="openStudentModal(${sv.ket_qua_id})" style="border:none; background:#F3F4F6; padding:4px 8px; border-radius:4px; cursor:pointer;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </td>
                        </tr>`;
                    });
                }
                body.innerHTML = html || '<tr><td colspan="6" style="text-align:center;">Chưa có dữ liệu.</td></tr>';
            })
            .catch(err => {
                loading.style.display = 'none';
                body.innerHTML = `<tr><td colspan="6" style="text-align:center; color:red;">Lỗi: ${err.message}</td></tr>`;
            });
        }

        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
        
        function openStudentModal(ketQuaId) {
            const loading = document.getElementById('detailLoading');
            const content = document.getElementById('detailContent');
            
            loading.style.display = 'block';
            content.style.display = 'none';
            document.getElementById('modalDetail').classList.add('show');

            // ✅ FIX: Dùng URL từ route Laravel, thêm đủ headers AJAX
            const url = DETAIL_URL.replace('__ID__', ketQuaId);

            fetch(url, { headers: ajaxHeaders() })
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                loading.style.display = 'none';
                
                if (!data.success) {
                    content.innerHTML = `<div style="color:red; padding:20px; text-align:center; background:#fee; border-radius:8px;"><strong>❌ Lỗi:</strong><br>${data.message}</div>`;
                    content.style.display = 'block';
                    return;
                }
                
                document.getElementById('detailModalTitle').textContent = `Chi tiết bài làm - ${data.ten_sinh_vien}`;
                document.getElementById('detail_maSv').textContent      = data.ma_sv;
                document.getElementById('detail_tenSv').textContent     = data.ten_sinh_vien;
                document.getElementById('detail_tenBai').textContent    = data.ten_bai;
                document.getElementById('detail_monHoc').textContent    = data.ten_mon_hoc;
                document.getElementById('detail_diem').textContent      = data.diem;
                document.getElementById('detail_cauDung').textContent   = data.so_cau_dung + '/' + data.tong_so_cau;
                document.getElementById('detail_tyLe').textContent      = data.ty_le + '%';
                document.getElementById('detail_thongGian').textContent = data.tong_thoi_gian;
                document.getElementById('detail_gioVao').textContent    = data.thoi_gian_vao;
                document.getElementById('detail_gioNop').textContent    = data.thoi_gian_nop;
                
                const container = document.getElementById('detailQuestionsContainer');
                let questionsHTML = '';
                
                if (data.chi_tiet && Array.isArray(data.chi_tiet) && data.chi_tiet.length > 0) {
                    data.chi_tiet.forEach((q, idx) => {
                        const statusColor = q.is_correct_flag ? '#10B981' : '#EF4444';
                        const statusText  = q.is_correct_flag ? '✓ Đúng' : '✗ Sai';
                        
                        let bodyHTML = '';
                        
                        if ((q.loai_cau_hoi === 1 || q.loai_cau_hoi === 2) && q.ds_dap_an && Array.isArray(q.ds_dap_an) && q.ds_dap_an.length > 0) {
                            bodyHTML += `<div style="display: flex; flex-direction: column; gap: 8px; margin: 12px 0;">`;
                            q.ds_dap_an.forEach((da, daIdx) => {
                                const letter = String.fromCharCode(65 + daIdx); // A, B, C, D...
                                
                                // Check if user selected this answer
                                let isSelected = false;
                                if (q.dap_an_chon_ids && Array.isArray(q.dap_an_chon_ids)) {
                                    isSelected = q.dap_an_chon_ids.includes(da.id);
                                } else {
                                    isSelected = (da.id == q.dap_an_chon_id);
                                }
                                
                                const isCorrect = da.is_dung;
                                
                                let bgStyle = 'background: #F9FAFB; border: 1px solid #E5E7EB; color: #374151;';
                                let iconHTML = '';
                                
                                if (isCorrect) {
                                    bgStyle = 'background: #ECFDF5; border: 1px solid #10B981; color: #065F46; font-weight: 500;';
                                    iconHTML = `<i class="fas fa-check-circle" style="color: #10B981; margin-left: auto; font-size: 16px;"></i>`;
                                } else if (isSelected) {
                                    bgStyle = 'background: #FEF2F2; border: 1px solid #EF4444; color: #991B1B;';
                                    iconHTML = `<i class="fas fa-times-circle" style="color: #EF4444; margin-left: auto; font-size: 16px;"></i>`;
                                }
                                
                                bodyHTML += `
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; ${bgStyle}">
                                        <span style="font-weight: bold; width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 50%; font-size: 13px; flex-shrink: 0;">${letter}</span>
                                        <div style="flex: 1; font-size: 14px;">${da.noi_dung}</div>
                                        ${da.hinh_anh ? `<div style="margin-left: 10px;"><img src="${da.hinh_anh}" style="max-height: 60px; border-radius: 4px; border: 1px solid #cbd5e1;"></div>` : ''}
                                        ${iconHTML}
                                    </div>
                                `;
                            });
                            bodyHTML += `</div>`;
                            
                            // Thêm thông tin thời gian & bookmark phía dưới
                            bodyHTML += `
                                <div style="display: flex; gap: 24px; font-size: 12px; color: #6B7280; margin-top: 12px; padding-top: 10px; border-top: 1px dashed #E5E7EB;">
                                    <div><i class="far fa-clock" style="margin-right: 4px;"></i> Thời gian trả lời: <span style="color: #111827; font-weight: 500;">${q.thoi_gian}</span></div>
                                    <div><i class="far fa-bookmark" style="margin-right: 4px;"></i> Đánh dấu xem lại: <span style="color: #111827; font-weight: 500;">${q.is_marked}</span></div>
                                </div>
                            `;
                        } else {
                            // Điền khuyết (3) hoặc Tự luận (4) hoặc fallback
                            bodyHTML += `
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 12px; background: #F9FAFB; border-radius: 6px; margin: 12px 0;">
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Đáp án sinh viên chọn:</strong>
                                        <p style="margin: 4px 0 0 0; color: #111827; padding: 6px; background: white; border-radius: 4px; border: 1px solid #E5E7EB;">${q.dap_an_chon}</p>
                                    </div>
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Đáp án đúng:</strong>
                                        <p style="margin: 4px 0 0 0; color: #059669; font-weight: bold; padding: 6px; background: white; border-radius: 4px; border: 1px solid #A7F3D0;">${q.dap_an_dung}</p>
                                    </div>
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Thời gian trả lời:</strong>
                                        <p style="margin: 4px 0 0 0; color: #111827;">${q.thoi_gian}</p>
                                    </div>
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Đánh dấu xem lại:</strong>
                                        <p style="margin: 4px 0 0 0; color: #111827;">${q.is_marked}</p>
                                    </div>
                                </div>
                            `;
                        }
                        
                        questionsHTML += `
                            <div style="border: 1px solid #E5E7EB; border-radius: 8px; padding: 16px; margin-bottom: 16px; background: white;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <div style="flex:1;">
                                        <h4 style="margin: 0 0 8px 0; color: #111827; font-size: 14px;">
                                            <strong>Câu ${idx + 1}:</strong> ${q.cau_hoi}
                                        </h4>
                                        ${q.hinh_anh ? `<img src="${q.hinh_anh}" style="max-width: 300px; max-height: 200px; border-radius: 4px; margin-top: 8px;">` : ''}
                                    </div>
                                    <div style="background: ${statusColor}; color: white; padding: 8px 14px; border-radius: 6px; font-weight: bold; white-space: nowrap; flex-shrink: 0; margin-left: 12px;">
                                        ${statusText}
                                    </div>
                                </div>
                                
                                ${bodyHTML}
                                
                                ${q.giai_thich && q.giai_thich !== '—' ? `
                                    <div style="padding: 12px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; margin-top: 12px;">
                                        <strong style="color: #1E40AF; font-size: 12px;">📚 Giải thích:</strong>
                                        <div style="margin: 6px 0 0 0; color: #1E40AF;">${q.giai_thich}</div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    });
                } else {
                    questionsHTML = '<div style="text-align:center; padding:20px; color:#6B7280; background:#f5f5f5; border-radius:8px;"><i class="fas fa-info-circle"></i> Không có dữ liệu chi tiết câu hỏi.</div>';
                }
                
                container.innerHTML = questionsHTML;
                content.style.display = 'block';
                
                // Tô màu cú pháp bằng highlight.js cho code blocks trong modal
                if (typeof hljs !== 'undefined') {
                    container.querySelectorAll('pre code').forEach((el) => {
                        hljs.highlightElement(el);
                    });
                }

                // Render công thức Toán học MathJax
                if (window.MathJax && window.MathJax.typesetPromise) {
                    MathJax.typesetPromise([container]).catch((err) => console.log('MathJax error:', err));
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                content.innerHTML = `<div style="color:red; padding:20px; text-align:center; background:#fee; border-radius:8px;"><strong>❌ Lỗi:</strong><br>${err.message}</div>`;
                content.style.display = 'block';
            });
        }
        
        function exportExcel() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = EXPORT_URL + '?' + params.toString();
        }
    </script>
@endsection