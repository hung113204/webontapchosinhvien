@extends('Admin.layouts.admin')
@section('title', isset($phienLuyenTap) ? 'Sửa Phiên Luyện Tập #' . $phienLuyenTap->id : 'Thêm Phiên Luyện Tập')

@section('content')

    <div style="padding: 0 4px;">

        {{-- ===== PAGE HEADER ===== --}}
        <div class="bf-page-header" style="margin-bottom: 24px;">
            <div>
                <h2 style="margin: 0; color: var(--gray-900); font-size: 1.4rem; font-weight: 700;">
                    {{ isset($phienLuyenTap) ? '✏️ Chỉnh sửa phiên luyện tập' : '➕ Thêm phiên luyện tập mới' }}
                </h2>
                <div class="breadcrumb" style="margin-top: 6px;">
                    <span>Admin</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="width:12px; height:12px;">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    <a href="{{ route('admin.phienluyentap.index') }}"
                        style="color: var(--primary); text-decoration: none;">Phiên luyện tập</a>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="width:12px; height:12px;">
                        <polyline points="9 18 15 12 9 6" />
                    </svg>
                    <span>{{ isset($phienLuyenTap) ? 'Chỉnh sửa #' . $phienLuyenTap->id : 'Tạo mới' }}</span>
                </div>
            </div>
            <a href="{{ route('admin.phienluyentap.index') }}" class="bf-btn-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="width:16px;height:16px;margin-right:5px;">
                    <path d="M19 12H5M12 5l-7 7 7 7" />
                </svg>
                Quay lại danh sách
            </a>
        </div>

        {{-- ===== THÔNG BÁO LỖI ===== --}}
        @if ($errors->any())
            <div class="bf-alert-error"
                style="margin-bottom: 20px; padding: 15px; background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; border-radius: 6px;">
                <strong>⚠ Vui lòng kiểm tra lại:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===== FORM ===== --}}
        <form action="{{ route('admin.phienluyentap.storeOrUpdate') }}" method="POST" id="phienForm">
            @csrf
            @if (isset($phienLuyenTap))
                <input type="hidden" name="id" value="{{ $phienLuyenTap->id }}">
            @endif

            <div class="bf-form-grid" style="display: grid; grid-template-columns: 1fr 320px; gap: 24px;">

                {{-- CỘT TRÁI: THÔNG TIN CHÍNH --}}
                <div class="bf-col-left">

                    {{-- CARD: Thông tin cơ bản --}}
                    <div class="bf-card"
                        style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
                        <h4 class="bf-card-title"
                            style="margin-bottom: 20px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-info-circle" style="color: var(--primary);"></i> Thông tin cơ bản
                        </h4>

                        <div class="bf-grid-2"
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            {{-- Người dùng --}}
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Người
                                    dùng <span style="color:red;">*</span></label>
                                <select name="user_id" class="bf-select"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                    required>
                                    <option value="">-- Chọn người dùng --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $phienLuyenTap->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->ho_ten }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Liên kết Bài kiểm tra --}}
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Liên
                                    kết Bài kiểm tra</label>
                                <select name="bai_kiem_tra_id" class="bf-select"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                                    <option value="">-- Luyện tập tự do --</option>
                                    @foreach ($baiKiemTras as $bkt)
                                        <option value="{{ $bkt->id }}"
                                            {{ old('bai_kiem_tra_id', $phienLuyenTap->bai_kiem_tra_id ?? '') == $bkt->id ? 'selected' : '' }}>
                                            {{ $bkt->ten_bai }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="bf-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            {{-- Môn học --}}
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Môn
                                    học <span style="color:red;">*</span></label>
                                <select name="mon_hoc_id" id="selectMonHoc" class="bf-select"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                    required>
                                    <option value="">-- Chọn môn học --</option>
                                    @foreach ($monHocs ?? [] as $mon)
                                        <option value="{{ $mon->id }}"
                                            {{ old('mon_hoc_id', $phienLuyenTap->mon_hoc_id ?? '') == $mon->id ? 'selected' : '' }}>
                                            {{ $mon->ten_mon_hoc }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chương học --}}
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Chương
                                    học</label>
                                <select name="chuong_hoc_id" id="selectChuongHoc" class="bf-select"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                                    <option value="">-- Toàn môn --</option>
                                    @foreach ($chuongHocs ?? [] as $ch)
                                        <option value="{{ $ch->id }}" data-mon="{{ $ch->mon_hoc_id }}"
                                            {{ old('chuong_hoc_id', $phienLuyenTap->chuong_hoc_id ?? '') == $ch->id ? 'selected' : '' }}>
                                            Ch.{{ $ch->thu_tu }}: {{ $ch->ten_chuong }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- CARD: Cấu hình phiên --}}
                    <div class="bf-card"
                        style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
                        <h4 class="bf-card-title" style="margin-bottom: 20px; font-weight: 700;">⚙️ Cấu hình và Kết quả</h4>

                        <div class="bf-grid-2"
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Số câu
                                    hỏi <span style="color:red;">*</span></label>
                                <input type="number" name="so_cau_hoi" class="bf-input"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                    value="{{ old('so_cau_hoi', $phienLuyenTap->so_cau_hoi ?? '') }}" required>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Chế
                                    độ làm bài</label>
                                <select name="che_do" class="bf-select"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                                    <option value="1"
                                        {{ old('che_do', $phienLuyenTap->che_do ?? 1) == 1 ? 'selected' : '' }}>🏃 Luyện
                                        tập tự do</option>
                                    <option value="2"
                                        {{ old('che_do', $phienLuyenTap->che_do ?? '') == 2 ? 'selected' : '' }}>📝 Thi thử
                                    </option>
                                    <option value="3"
                                        {{ old('che_do', $phienLuyenTap->che_do ?? '') == 3 ? 'selected' : '' }}>🔁 Ôn tập
                                        yếu</option>
                                </select>
                            </div>
                        </div>

                        <div class="bf-grid-2"
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Số
                                    câu đúng</label>
                                <input type="number" name="so_cau_dung" class="bf-input"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                    value="{{ old('so_cau_dung', $phienLuyenTap->so_cau_dung ?? 0) }}">
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Điểm
                                    số (thang 10)</label>
                                <input type="number" step="0.01" name="diem_so" class="bf-input"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                    value="{{ old('diem_so', $phienLuyenTap->diem_so ?? 0) }}">
                            </div>
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Phần
                                trăm đúng (%)</label>
                            <input type="number" step="0.01" name="phan_tram_dung" class="bf-input"
                                style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                value="{{ old('phan_tram_dung', $phienLuyenTap->phan_tram_dung ?? 0) }}"
                                placeholder="Tự động tính...">
                        </div>
                    </div>

                    {{-- CARD: Dữ liệu JSON --}}
                    <div class="bf-card"
                        style="background: #f8fafc; padding: 24px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                        <h4 class="bf-card-title" style="margin-bottom: 15px; font-weight: 700; color: #475569;">📦 Dữ
                            liệu kỹ thuật (JSON String)</h4>
                        <div class="bf-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="bf-form-group">
                                <label class="bf-label" style="display: block; font-weight: 600; margin-bottom: 8px;">Danh
                                    sách ID câu hỏi</label>
                                <textarea name="danh_sach_cau_hoi" class="bf-input" rows="4"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; font-family: monospace; font-size: 13px; background: white;">{{ old('danh_sach_cau_hoi', isset($phienLuyenTap) ? json_encode($phienLuyenTap->danh_sach_cau_hoi) : '') }}</textarea>
                                <small style="color: #64748b;">* Ví dụ: [1, 2, 15]</small>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-label">Kết quả chi tiết (JSON)</label>
                                <textarea name="ket_qua_chi_tiet" class="bf-input" rows="4"
                                    style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; font-family: monospace; font-size: 13px; background: white;">{{ old('ket_qua_chi_tiet', isset($phienLuyenTap) ? json_encode($phienLuyenTap->ket_qua_chi_tiet) : '') }}</textarea>
                                <small style="color: #64748b;">* Lưu vết đáp án đã chọn</small>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- CỘT PHẢI: TRẠNG THÁI --}}
                <div class="bf-col-right">
                    <div class="bf-card"
                        style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: sticky; top: 20px;">
                        <h4 class="bf-card-title" style="margin-bottom: 15px; font-weight: 700;">Trạng thái</h4>
                        <div class="bf-form-group" style="margin-bottom: 20px;">
                            <select name="trang_thai" class="bf-select"
                                style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;">
                                <option value="0"
                                    {{ old('trang_thai', $phienLuyenTap->trang_thai ?? 0) == 0 ? 'selected' : '' }}>⏳ Đang
                                    thực hiện</option>
                                <option value="1"
                                    {{ old('trang_thai', $phienLuyenTap->trang_thai ?? '') == 1 ? 'selected' : '' }}>✅ Hoàn
                                    thành</option>
                                <option value="2"
                                    {{ old('trang_thai', $phienLuyenTap->trang_thai ?? '') == 2 ? 'selected' : '' }}>❌ Bỏ
                                    dở</option>
                            </select>
                        </div>

                        <div class="bf-form-group" style="margin-bottom: 20px;">
                            <label class="bf-label">Bắt đầu lúc</label>
                            <input type="datetime-local" name="thoi_gian_bat_dau" class="bf-input"
                                style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd;"
                                value="{{ old('thoi_gian_bat_dau', isset($phienLuyenTap) ? $phienLuyenTap->thoi_gian_bat_dau?->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}">
                        </div>

                        <button type="submit" class="bf-btn-submit"
                            style="width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                            {{ isset($phienLuyenTap) ? 'Cập nhật phiên' : 'Tạo phiên mới' }}
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        // Lọc chương học theo môn học
        const selectMon = document.getElementById('selectMonHoc');
        const selectChuong = document.getElementById('selectChuongHoc');

        function filterChuong() {
            const monId = selectMon.value;
            Array.from(selectChuong.options).forEach(opt => {
                if (!opt.value) return;
                opt.style.display = (!monId || opt.dataset.mon === monId) ? '' : 'none';
            });
            if (selectChuong.options[selectChuong.selectedIndex]?.dataset.mon !== monId && selectChuong.value !== '') {
                selectChuong.value = '';
            }
        }
        selectMon?.addEventListener('change', filterChuong);
        filterChuong();

        const inputTong = document.querySelector('input[name="so_cau_hoi"]');
        const inputDung = document.querySelector('input[name="so_cau_dung"]');
        const inputDiem = document.querySelector('input[name="diem_so"]');
        const inputPhanTram = document.querySelector('input[name="phan_tram_dung"]');

        function autoCalc() {
            const tong = parseInt(inputTong.value) || 0;
            const dung = parseInt(inputDung.value) || 0;

            if (tong > 0) {
                if (dung > tong) {
                    inputDung.value = tong; // Không cho phép đúng > tổng
                    return autoCalc();
                }
                // Tự động tính điểm thang 10
                if (inputDiem) inputDiem.value = ((dung / tong) * 10).toFixed(2);
                // Tự động tính phần trăm
                if (inputPhanTram) inputPhanTram.value = ((dung / tong) * 100).toFixed(2);
            }
        }

        inputTong.addEventListener('input', autoCalc);
        inputDung.addEventListener('input', autoCalc);
    </script>
@endsection
