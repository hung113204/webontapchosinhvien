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

    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); display: flex; align-items: center; gap: 16px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-icon.indigo { background: rgba(79, 70, 229, 0.1); color: #4F46E5; }
        .stat-icon.green { background: rgba(16, 185, 129, 0.1); color: #059669; }
        .stat-icon.yellow { background: rgba(245, 158, 11, 0.1); color: #D97706; }
        .stat-icon.red { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
        .stat-val { font-size: 26px; font-weight: 700; color: #111827; line-height: 1; }
        .stat-lbl { font-size: 13px; color: #6B7280; margin-top: 4px; }
        
        .filters-section { background: white; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .filter-group { display: grid; gap: 16px; align-items: end; }
        .search-box { position: relative; display: flex; align-items: center; }
        .search-box input { width: 100%; padding: 10px 12px 10px 40px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; }
        .form-select { width: 100%; padding: 10px 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; }

        .table-section { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead th { background: #F9FAFB; padding: 12px; text-align: left; font-weight: 600; font-size: 13px; color: #6B7280; border-bottom: 2px solid #E5E7EB; }
        .data-table tbody td { padding: 14px 12px; border-bottom: 1px solid #F3F4F6; font-size: 14px; }

        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px); }
        .modal-overlay.show { display: flex; align-items: center; justify-content: center; }
        .modal-card { background: white; border-radius: 12px; width: 90%; max-width: 960px; max-height: 90vh; overflow-y: auto; }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; }
        
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-indigo { background: rgba(79, 70, 229, 0.1); color: #4F46E5; }
        .score-bar-bg { flex: 1; height: 7px; background: #F3F4F6; border-radius: 10px; overflow: hidden; }
        .score-bar-fill { height: 100%; border-radius: 10px; }
        .score-bar-fill.high { background: #10B981; }
        .score-bar-fill.mid { background: #F59E0B; }
        .score-bar-fill.low { background: #EF4444; }

        /* ===== PAGINATION ===== */
        .pagination-wrapper nav { display: flex; justify-content: center; }
        .pagination-wrapper .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .pagination-wrapper .pagination li { list-style: none; }
        .pagination-wrapper .pagination li a,
        .pagination-wrapper .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1;
        }
        .pagination-wrapper .pagination li a:hover {
            background: #eef2ff;
            border-color: #c7d2fe;
            color: #4338ca;
        }
        .pagination-wrapper .pagination li.active span,
        .pagination-wrapper .pagination li.active a {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-color: #4f46e5;
            color: #fff;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.35);
        }
        .pagination-wrapper .pagination li.disabled span {
            background: #f9fafb;
            border-color: #f3f4f6;
            color: #d1d5db;
            cursor: not-allowed;
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
                                
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 12px; background: #F9FAFB; border-radius: 6px; margin: 12px 0;">
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Đáp án sinh viên chọn:</strong>
                                        <p style="margin: 4px 0 0 0; color: #111827; padding: 6px; background: white; border-radius: 4px;">${q.dap_an_chon}</p>
                                    </div>
                                    <div>
                                        <strong style="font-size: 12px; color: #6B7280;">Đáp án đúng:</strong>
                                        <p style="margin: 4px 0 0 0; color: #059669; font-weight: bold; padding: 6px; background: white; border-radius: 4px;">${q.dap_an_dung}</p>
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
                                
                                ${q.giai_thich && q.giai_thich !== '—' ? `
                                    <div style="padding: 12px; background: #DBEAFE; border-left: 4px solid #3B82F6; border-radius: 4px; margin-top: 12px;">
                                        <strong style="color: #1E40AF; font-size: 12px;">📚 Giải thích:</strong>
                                        <p style="margin: 6px 0 0 0; color: #1E40AF;">${q.giai_thich}</p>
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