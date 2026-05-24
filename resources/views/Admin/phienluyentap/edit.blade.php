@extends('Admin.layouts.admin')
@section('title', 'Chỉnh sửa Phiên Luyện Tập #' . $phienLuyenTap->id)

@section('content')
    <div style="padding: 0 4px;">
        {{-- PAGE HEADER (Giữ nguyên như bản trước) --}}
        <div class="bf-page-header" style="margin-bottom: 24px;">
            <h2>✏️ Chỉnh sửa phiên luyện tập #{{ $phienLuyenTap->id }}</h2>
            <a href="{{ route('admin.phienluyentap.index') }}" class="bf-btn-back">Quay lại danh sách</a>
        </div>

        <form action="{{ route('admin.phienluyentap.storeOrUpdate') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $phienLuyenTap->id }}">

            <div class="bf-form-grid" style="display: grid; grid-template-columns: 1fr 320px; gap: 24px;">
                <div class="bf-col-left">
                    {{-- CARD: Thông tin cơ bản --}}
                    <div class="bf-card" style="background: white; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
                        <h4 class="bf-card-title">ℹ️ Thông tin phiên làm bài</h4>
                        <div class="bf-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="bf-form-group">
                                <label class="bf-label">Người dùng</label>
                                <input type="text" class="bf-input" style="background: #f1f5f9;"
                                    value="{{ $phienLuyenTap->user->ho_ten ?? 'N/A' }}" readonly>
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-label">Môn học</label>
                                <input type="text" class="bf-input" style="background: #f1f5f9;"
                                    value="{{ $phienLuyenTap->monHoc->ten_mon_hoc ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- BẢNG CHI TIẾT BÀI LÀM - ĐÃ SỬA LỖI DÒNG 139 --}}
                    @if (isset($chiTietCauHoi) && $chiTietCauHoi->count() > 0)
                        <div class="bf-card"
                            style="background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px;">
                            <h4 class="bf-card-title">📝 Chi tiết nội dung câu hỏi</h4>
                            <div style="overflow-x: auto; margin-top: 15px;">
                                <table class="data-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #f8fafc; text-align: left; font-size: 0.85rem;">
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 40px;">STT
                                            </th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0;">Câu hỏi</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 150px;">SV
                                                chọn</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 150px;">Đáp
                                                án đúng</th>
                                            <th style="padding: 12px; border-bottom: 2px solid #e2e8f0; width: 80px;">KQ
                                            </th>
                                        </tr>
                                    </thead>
                                    {{-- Trong file edit.blade.php --}}
                                    <tbody>
                                        @php
                                            // Đảm bảo dữ liệu kết quả là mảng
                                            $kqChiTiet = $phienLuyenTap->ket_qua_chi_tiet;
                                            if (is_string($kqChiTiet)) {
                                                $kqChiTiet = json_decode($kqChiTiet, true) ?? [];
                                            }
                                        @endphp

                                        @foreach ($chiTietCauHoi as $index => $cau)
                                            @php
                                                // Truy cập an toàn vào ID câu hỏi trong mảng kết quả
                                                $userAns = $kqChiTiet[$cau->id] ?? null;
                                                $correctAns = $cau->dapAns->firstWhere('is_dung', 1);
                                                $selectedAns =
                                                    $userAns && isset($userAns['selected'])
                                                        ? $cau->dapAns->firstWhere('id', $userAns['selected'])
                                                        : null;
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{!! Str::limit(strip_tags($cau->noi_dung), 100) !!}</td>
                                                <td>{!! $selectedAns ? $selectedAns->noi_dung : '<i style="color:#94a3b8">Bỏ trống</i>' !!}</td>
                                                <td style="color: #059669; font-weight: 600;">{!! $correctAns ? $correctAns->noi_dung : 'N/A' !!}</td>
                                                <td>
                                                    @if ($userAns)
                                                        <span
                                                            class="badge {{ $userAns['is_correct'] ? 'badge-easy' : 'badge-hard' }}">
                                                            {{ $userAns['is_correct'] ? 'Đúng' : 'Sai' }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-draft">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- DỮ LIỆU JSON DEBUG (Để ẩn hoặc Readonly) --}}
                    <div class="bf-card"
                        style="background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px dashed #cbd5e1; opacity: 0.7;">
                        <h4 class="bf-card-title">🛠 Dữ liệu hệ thống (JSON)</h4>
                        <div class="bf-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <textarea name="danh_sach_cau_hoi" class="bf-input" rows="3" readonly>{{ is_array($phienLuyenTap->danh_sach_cau_hoi) ? json_encode($phienLuyenTap->danh_sach_cau_hoi) : $phienLuyenTap->danh_sach_cau_hoi }}</textarea>
                            <textarea name="ket_qua_chi_tiet" class="bf-input" rows="3" readonly>{{ is_array($phienLuyenTap->ket_qua_chi_tiet) ? json_encode($phienLuyenTap->ket_qua_chi_tiet) : $phienLuyenTap->ket_qua_chi_tiet }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI (SUBMIT & TRẠNG THÁI) --}}
                <div class="bf-col-right">
                    <div class="bf-card"
                        style="background: white; padding: 20px; border-radius: 8px; position: sticky; top: 20px;">
                        <div class="bf-form-group" style="margin-bottom: 15px;">
                            <label class="bf-label">Trạng thái</label>
                            <select name="trang_thai" class="bf-select">
                                <option value="0" {{ $phienLuyenTap->trang_thai == 0 ? 'selected' : '' }}>⏳ Đang làm
                                </option>
                                <option value="1" {{ $phienLuyenTap->trang_thai == 1 ? 'selected' : '' }}>✅ Hoàn thành
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="bf-btn-submit"
                            style="width: 100%; padding: 12px; background: #2563eb; color: white; border: none; border-radius: 6px; font-weight: 700;">
                            Lưu thay đổi
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
