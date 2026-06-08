@extends('Admin.layouts.admin')
@section('title', 'Chi tiết Phiên Luyện Tập #' . $phienLuyenTap->id)

@section('content')
    <div style="padding: 0 4px;">

        <div class="bf-page-header" style="margin-bottom: 24px;">
            <div>
                <h2>📋 Chi tiết bài làm của sinh viên #{{ $phienLuyenTap->id }}</h2>
                <div class="breadcrumb">
                    <a href="{{ route('admin.phienluyentap.index') }}">Phiên luyện tập</a> →
                    <span>Chi tiết #{{ $phienLuyenTap->id }}</span>
                </div>
            </div>
            <a href="{{ route('admin.phienluyentap.index') }}" class="bf-btn-back">← Quay lại danh sách</a>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px;">

            {{-- Cột trái: Chi tiết câu hỏi --}}
            <div>
                <div class="bf-card" style="background: white; padding: 24px; border-radius: 8px; margin-bottom: 24px;">
                    <h4>👤 Thông tin sinh viên & phiên làm bài</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                        <div>
                            <strong>Người dùng:</strong><br>
                            {{ $phienLuyenTap->user->ho_ten ?? 'N/A' }} 
                            <small>({{ $phienLuyenTap->user->email ?? '' }})</small>
                        </div>
                        <div>
                            <strong>Môn học:</strong><br>
                            {{ $phienLuyenTap->monHoc->ten_mon_hoc ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                {{-- Bảng chi tiết câu hỏi --}}
                @if($chiTietCauHoi->count() > 0)
                    <div class="bf-card" style="background: white; padding: 24px; border-radius: 8px;">
                        <h4>📝 Chi tiết câu trả lời ({{ $chiTietCauHoi->count() }} câu)</h4>
                        
                        <div style="overflow-x: auto; margin-top: 15px;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="50">STT</th>
                                        <th>Câu hỏi</th>
                                        <th width="200">Sinh viên chọn</th>
                                        <th width="200">Đáp án đúng</th>
                                        <th width="80">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $ketQua = $phienLuyenTap->ket_qua_chi_tiet;
                                        if (is_string($ketQua)) $ketQua = json_decode($ketQua, true) ?? [];
                                    @endphp

                                    @foreach($chiTietCauHoi as $index => $cau)
                                        @php
                                            $userAns = $ketQua[$cau->id] ?? null;
                                            $selected = $userAns['selected'] ?? null;
                                            $dapAnChonHtml = '<i style="color:#94a3b8">Không chọn</i>';
                                            $dapAnDungHtml = 'N/A';

                                            if ($cau->loai_cau_hoi == 3 || $cau->loai_cau_hoi == 4) {
                                                // Tự luận & Điền khuyết
                                                if ($selected !== null && trim((string)$selected) !== '' && $selected !== '[]') {
                                                    $decoded = json_decode($selected, true);
                                                    if (is_array($decoded)) {
                                                        $dapAnChonHtml = nl2br(htmlspecialchars(implode(', ', $decoded)));
                                                    } else {
                                                        $dapAnChonHtml = nl2br(htmlspecialchars($selected));
                                                    }
                                                }
                                                
                                                if ($cau->loai_cau_hoi == 4) {
                                                    $dapAnDungObj = $cau->dapAns->firstWhere('is_dung', 1);
                                                    $dapAnDungHtml = $dapAnDungObj ? $dapAnDungObj->noi_dung : ($cau->giai_thich ?: 'N/A');
                                                } else {
                                                    $dapAnDungHtml = $cau->dapAns->where('is_dung', 1)->pluck('noi_dung')->implode(', ');
                                                }
                                            } else {
                                                // Trắc nghiệm & Đúng/Sai
                                                $dapAnChonObj = $selected ? $cau->dapAns->firstWhere('id', $selected) : null;
                                                if ($dapAnChonObj) {
                                                    $dapAnChonHtml = $dapAnChonObj->noi_dung;
                                                }
                                                $dapAnDungObj = $cau->dapAns->firstWhere('is_dung', 1);
                                                if ($dapAnDungObj) {
                                                    $dapAnDungHtml = $dapAnDungObj->noi_dung;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{!! Str::limit(strip_tags($cau->noi_dung), 110) !!}</td>
                                            <td>{!! $dapAnChonHtml !!}</td>
                                            <td style="color:#10b981">
                                                {!! $dapAnDungHtml !!}
                                            </td>
                                            <td>
                                                @if($userAns)
                                                    <span class="badge {{ $userAns['is_correct'] ? 'badge-easy' : 'badge-hard' }}">
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
                @else
                    <p>Phiên này không có câu hỏi nào.</p>
                @endif
            </div>

            {{-- Cột phải: Tóm tắt kết quả --}}
            <div>
                <div class="bf-card" style="background: white; padding: 24px; border-radius: 8px; position: sticky; top: 20px;">
                    <h4>📊 Kết quả tổng quát</h4>
                    
                    @if($phienLuyenTap->trang_thai == 1)
                        <div style="text-align: center; margin: 20px 0;">
                            <h1 style="margin:0; color:#10b981; font-size: 3rem;">
                                {{ number_format($phienLuyenTap->diem_so ?? 0, 1) }}đ
                            </h1>
                            <p style="margin:8px 0 0;">
                                Đúng {{ $phienLuyenTap->so_cau_dung ?? 0 }} / {{ $phienLuyenTap->so_cau_hoi ?? 0 }} câu
                            </p>
                        </div>
                    @else
                        <p style="color:#f59e0b; text-align:center; font-weight:600;">
                            Phiên chưa hoàn thành
                        </p>
                    @endif

                    <hr style="margin:20px 0;">
                    
                    <p><strong>Trạng thái:</strong> 
                        @php
                            $tt = [0 => '⏳ Đang làm', 1 => '✅ Hoàn thành', 2 => '❌ Bỏ dở'];
                        @endphp
                        {{ $tt[$phienLuyenTap->trang_thai] ?? 'N/A' }}
                    </p>
                    <p><strong>Thời gian bắt đầu:</strong> 
                        {{ $phienLuyenTap->thoi_gian_bat_dau?->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection