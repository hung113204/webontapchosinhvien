@extends('Admin.layouts.admin')
@section('title', 'Chỉnh sửa bài kiểm tra')

@section('content')
    <style>
        .edit-container {
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 24px;
            align-items: start;
        }

        /* Form bên trái */
        .edit-form-panel {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Preview bên phải */
        .preview-sidebar {
            position: sticky;
            top: 100px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .preview-header {
            padding: 20px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        .preview-header h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
        }

        .preview-meta {
            font-size: 13px;
            opacity: 0.9;
        }

        .preview-body {
            max-height: 500px;
            overflow-y: auto;
            padding: 16px;
        }

        .preview-question {
            padding: 12px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 3px solid #6366f1;
        }

        .preview-question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .preview-question-number {
            font-weight: 600;
            color: #6366f1;
        }

        .preview-question-content {
            font-size: 14px;
            color: #374151;
            line-height: 1.5;
        }

        .preview-empty {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .preview-empty svg {
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-easy {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-hard {
            background: #fee2e2;
            color: #991b1b;
        }

        .multi-select-container {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px;
            background: white;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            min-height: 45px;
        }

        .tags-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chapter-tag {
            background: #f3f4f6;
            color: #374151;
            padding: 6px 12px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            border: 1px solid #e5e7eb;
        }

        .remove-tag {
            cursor: pointer;
            color: #ef4444;
            font-weight: bold;
            font-size: 16px;
            line-height: 1;
        }

        .remove-tag:hover {
            color: #dc2626;
        }

        .form-select-inline {
            border: none;
            outline: none;
            color: #6366f1;
            font-weight: 600;
            cursor: pointer;
            padding: 4px;
            background: transparent;
        }

        @media (max-width: 1200px) {
            .edit-container {
                grid-template-columns: 1fr;
            }

            .preview-sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger"
            style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="edit-container">
        <!-- Panel trái - Form -->
        <div class="edit-form-panel">
            <div class="card-header" style="padding: 0 0 24px 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px;">
                <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">
                    📝 Chỉnh sửa bài kiểm tra
                </h3>
                <p style="color: #6b7280; font-size: 14px; margin: 8px 0 0 0;">
                    {{ $baiKiemTra->ten_bai }}
                </p>
            </div>

            <form id="exam-form" method="POST" action="{{ route('admin.baikiemtra.save', $baiKiemTra->id) }}">
                @csrf
                <input type="hidden" name="id" value="{{ $baiKiemTra->id }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Tên bài kiểm tra <span class="required">*</span></label>
                        <input type="text" name="ten_bai" id="ten_bai" class="form-input"
                            value="{{ old('ten_bai', $baiKiemTra->ten_bai) }}" required />
                    </div>

                    <div class="form-group">
                        <label>Môn học <span class="required">*</span></label>
                        <select class="form-select" name="mon_hoc_id" id="mon-hoc-select" required>
                            <option value="">Chọn môn học</option>
                            @foreach ($monHocs as $monHoc)
                                <option value="{{ $monHoc->id }}"
                                    {{ $baiKiemTra->mon_hoc_id == $monHoc->id ? 'selected' : '' }}>
                                    {{ $monHoc->ten_mon_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Mô tả bài kiểm tra</label>
                        <textarea name="mo_ta" id="mo_ta" class="form-input"
                            placeholder="VD: Bài kiểm tra giữa kỳ, bao gồm các câu hỏi về..."
                            rows="3" style="resize: vertical;">{{ old('mo_ta', $baiKiemTra->mo_ta) }}</textarea>
                        <span class="form-help">Không bắt buộc. Mô tả sẽ hiển thị với sinh viên trước khi vào thi.</span>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label>Chương học áp dụng <span class="required">*</span></label>
                        <div class="multi-select-container">
                            <div class="tags-wrapper" id="selected-chapters"></div>
                            <select class="form-select-inline" id="chapter-dropdown">
                                <option value="">+ Thêm chương...</option>
                            </select>
                        </div>
                        <input type="hidden" name="chuong_hoc_ids" id="chuong_hoc_ids_input"
                            value="{{ $baiKiemTra->chuong_hoc_ids }}" />
                    </div>
                </div>

                <div style="margin-top: 24px;">
                    <h4 style="margin-bottom: 16px; font-size: 16px; font-weight: 600;">⏰ Cấu hình thời gian</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Thời gian làm bài (phút)</label>
                            <input type="number" name="thoi_gian_phut" class="form-input"
                                value="{{ $baiKiemTra->thoi_gian_phut }}" required />
                        </div>
                        <div class="form-group">
                            <label>Thời gian bắt đầu</label>
                            <input type="datetime-local" name="thoi_gian_bat_dau" class="form-input"
                                value="{{ $baiKiemTra->thoi_gian_bat_dau ? date('Y-m-d\TH:i', strtotime($baiKiemTra->thoi_gian_bat_dau)) : '' }}"
                                required />
                        </div>
                        <div class="form-group">
                            <label>Thời gian kết thúc</label>
                            <input type="datetime-local" name="thoi_gian_ket_thuc" class="form-input"
                                value="{{ $baiKiemTra->thoi_gian_ket_thuc ? date('Y-m-d\TH:i', strtotime($baiKiemTra->thoi_gian_ket_thuc)) : '' }}"
                                required />
                        </div>
                    </div>
                </div>

                <div style="margin-top: 24px">
                    <h4 style="margin-bottom: 16px; font-size: 16px; font-weight: 600;">🎯 Cấu hình bài thi</h4>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Số lần làm bài</label>
                            <input type="number" name="so_lan_lam_bai" class="form-input"
                                value="{{ $baiKiemTra->so_lan_lam_bai }}" min="1" required />
                        </div>
                        <div class="form-group">
                            <label>Cách tính điểm</label>
                            <select class="form-select" name="cach_tinh_diem" required>
                                <option value="0" {{ $baiKiemTra->cach_tinh_diem == 0 ? 'selected' : '' }}>Lấy điểm
                                    cao nhất</option>
                                <option value="1" {{ $baiKiemTra->cach_tinh_diem == 1 ? 'selected' : '' }}>Lấy điểm
                                    lần cuối</option>
                                <option value="2" {{ $baiKiemTra->cach_tinh_diem == 2 ? 'selected' : '' }}>Trung bình
                                    cộng</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 24px">
                    <h4 style="margin-bottom: 16px; font-size: 16px; font-weight: 600;">🌐 Trạng thái bài kiểm tra</h4>
                    <div class="form-group">
                        <select class="form-select" name="trang_thai" required>
                            <option value="0" {{ $baiKiemTra->trang_thai == 0 ? 'selected' : '' }}>
                                📝 Bản nháp (Chưa công bố)
                            </option>
                            <option value="1" {{ $baiKiemTra->trang_thai == 1 ? 'selected' : '' }}>
                                ✅ Đang mở (Sinh viên có thể làm)
                            </option>
                            <option value="2" {{ $baiKiemTra->trang_thai == 2 ? 'selected' : '' }}>
                                🚫 Đã đóng (Không cho phép làm)
                            </option>
                        </select>
                        <small class="text-muted">Chọn trạng thái hiển thị của bài kiểm tra</small>
                    </div>
                </div>

                <div style="margin-top: 24px">
                    <h4 style="margin-bottom: 16px; font-size: 16px; font-weight: 600;">🎛️ Tùy chọn nâng cao</h4>
                    <div class="checkbox-grid">
                        <label class="checkbox-item">
                            <input type="checkbox" name="xem_diem" value="1"
                                {{ old('xem_diem', $baiKiemTra->xem_diem) ? 'checked' : '' }} />
                            <span>Cho phép xem điểm</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="xem_bai_lam" value="1"
                                {{ old('xem_bai_lam', $baiKiemTra->xem_bai_lam) ? 'checked' : '' }} />
                            <span>Cho phép xem bài làm</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="dao_cau_hoi" value="1"
                                {{ old('dao_cau_hoi', $baiKiemTra->dao_cau_hoi) ? 'checked' : '' }} />
                            <span>Đảo thứ tự câu hỏi</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="dao_dap_an" value="1"
                                {{ old('dao_dap_an', $baiKiemTra->dao_dap_an) ? 'checked' : '' }} />
                            <span>Đảo thứ tự đáp án</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="nop_khi_chuyen_tab" value="1"
                                {{ old('nop_khi_chuyen_tab', $baiKiemTra->nop_khi_chuyen_tab) ? 'checked' : '' }} />
                            <span>Tự động nộp khi chuyển tab</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 32px; display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('admin.baikiemtra.index') }}" class="btn btn-secondary">Hủy bỏ</a>

                    <button type="button" id="btn-edit-questions" class="btn btn-primary"
                        style="background: #8b5cf6;">
                        Sửa danh sách câu hỏi
                    </button>

                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>

        <!-- Panel phải - Preview -->
        <div class="preview-sidebar">
            <div class="preview-header">
                <h4>
                    @if ($baiKiemTra->tu_dong_lay_de == 1)
                        🎲 Đề thi tự động
                    @else
                        ✍️ Đề thi thủ công
                    @endif
                </h4>
                <div class="preview-meta">
                    <div>📝 <strong id="question-count">{{ $baiKiemTra->cauHois->count() }}</strong> câu hỏi</div>
                    <div>⏱️ {{ $baiKiemTra->thoi_gian_phut }} phút</div>
                </div>
            </div>

            <div class="preview-body" id="preview-container">
                @if ($baiKiemTra->cauHois->count() > 0)
                    @foreach ($baiKiemTra->cauHois as $index => $cauHoi)
                        <div class="preview-question">
                            <div class="preview-question-header">
                                <span class="preview-question-number">Câu {{ $index + 1 }}</span>
                                @php
                                    $badgeClass =
                                        $cauHoi->muc_do == 1
                                            ? 'badge-easy'
                                            : ($cauHoi->muc_do == 2
                                                ? 'badge-medium'
                                                : 'badge-hard');
                                    $badgeText = $cauHoi->muc_do == 1 ? 'Nhận biết' : ($cauHoi->muc_do == 2 ? 'Thông hiểu' : 'Vận dụng');
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </div>
                            <div class="preview-question-content">
                                {!! Str::limit(strip_tags($cauHoi->noi_dung), 100) !!}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="preview-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p style="margin: 0; font-size: 14px; color: #6b7280;">
                            Chưa có câu hỏi nào
                        </p>
                        <small style="color: #9ca3af;">Click "Sửa danh sách câu hỏi" để thêm</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Nạp dữ liệu chương học cũ
                window.selectedChapters = {!! json_encode($currentChapters ?? []) !!};
                renderSelectedChapters();

                // Load chương học khi chọn môn
                $('#mon-hoc-select').on('change', function() {
                    const monHocId = $(this).val();
                    if (!monHocId) return;

                    fetch(`{{ url('/admin/baikiemtra/api/chuong-hoc') }}/${monHocId}`)
                        .then(res => res.json())
                        .then(response => {
                            let html = '<option value="">+ Thêm chương...</option>';
                            const data = response.success ? response.data : response;

                            if (Array.isArray(data)) {
                                data.forEach(c => {
                                    html += `<option value="${c.id}">${c.ten_chuong}</option>`;
                                });
                            }

                            $('#chapter-dropdown').html(html);
                        })
                        .catch(err => {
                            console.error('Lỗi load chương:', err);
                        });
                }).trigger('change');

                // Thêm chương học
                $('#chapter-dropdown').on('change', function() {
                    const id = $(this).val();
                    const name = $("#chapter-dropdown option:selected").text();
                    if (id && !window.selectedChapters[id]) {
                        window.selectedChapters[id] = name;
                        renderSelectedChapters();
                    }
                    $(this).val('');
                });

                // Xóa chương học
                window.removeChapter = function(id) {
                    delete window.selectedChapters[id];
                    renderSelectedChapters();
                };

                // Render danh sách chương đã chọn
                function renderSelectedChapters() {
                    const container = $('#selected-chapters');
                    container.empty();
                    const ids = Object.keys(window.selectedChapters);

                    ids.forEach(id => {
                        container.append(`
                            <div class="chapter-tag">
                                <span>${window.selectedChapters[id]}</span>
                                <span class="remove-tag" onclick="removeChapter(${id})">×</span>
                            </div>
                        `);
                    });

                    $('#chuong_hoc_ids_input').val(ids.join(','));
                }

                // Nút "Sửa danh sách câu hỏi"
                $('#btn-edit-questions').on('click', function() {
                    window.location.href =
                    "{{ route('admin.baikiemtra.soanthao') }}?id={{ $baiKiemTra->id }}";
                });
            });
        </script>
    @endpush
@endsection
