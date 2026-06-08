<div style="display: grid; grid-template-columns: 1fr 380px; gap: 24px;">

    {{-- Cột trái: Chi tiết câu hỏi --}}
    <div>
        <div class="bf-card" style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px;">
            <h4 style="margin-top:0; color:#1e293b; font-size:16px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">👤 Thông tin sinh viên & phiên làm bài</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; font-size:14px;">
                <div>
                    <strong style="color:#64748b;">Người dùng:</strong><br>
                    <span style="font-weight:600; color:#1e293b;">{{ $phienLuyenTap->user->ho_ten ?? 'N/A' }}</span>
                    <div style="color: #64748b; font-size: 12px;">({{ $phienLuyenTap->user->email ?? '' }})</div>
                </div>
                <div>
                    <strong style="color:#64748b;">Môn học:</strong><br>
                    <span style="font-weight:600; color:#1e293b;">{{ $phienLuyenTap->monHoc->ten_mon_hoc ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Bảng chi tiết câu hỏi --}}
        @php
            $ketQua = $phienLuyenTap->ket_qua_chi_tiet;
            if (is_string($ketQua)) $ketQua = json_decode($ketQua, true) ?? [];
        @endphp

        @if($chiTietCauHoi->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 20px;">
                @foreach($chiTietCauHoi as $index => $cau)
                    @php
                        $userAns = $ketQua[$cau->id] ?? null;
                        $selected = $userAns['selected'] ?? null;
                        $isCorrect = $userAns['is_correct'] ?? false;
                        
                        $statusColor = $isCorrect ? '#10B981' : '#EF4444';
                        $statusText  = $isCorrect ? '✓ Đúng' : ($selected !== null ? '✗ Sai' : '⏳ Chưa trả lời');
                        if ($selected === null) $statusColor = '#94a3b8';
                    @endphp

                    <div style="border: 1px solid #E5E7EB; border-radius: 12px; padding: 20px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 16px; margin-bottom: 15px;">
                            <div style="flex:1;">
                                <h4 style="margin: 0; color: #1e293b; font-size: 15px; font-weight:600; line-height:1.5;">
                                    <span style="background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; font-size:12px; margin-right:6px; font-weight:bold;">Câu {{ $index + 1 }}</span>
                                    {!! $cau->noi_dung !!}
                                </h4>
                            </div>
                            <div style="background: {{ $statusColor }}; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 600; font-size: 12px; white-space: nowrap; flex-shrink: 0;">
                                {{ $statusText }}
                            </div>
                        </div>

                        {{-- Hiển thị các lựa chọn của câu hỏi --}}
                        @if($cau->loai_cau_hoi == 1 || $cau->loai_cau_hoi == 2)
                            <div style="display: flex; flex-direction: column; gap: 8px; margin: 15px 0;">
                                @foreach($cau->dapAns as $daIdx => $da)
                                    @php
                                        $letter = chr(65 + $daIdx);
                                        $isSelected = ($da->id == $selected);
                                        $isAnswerCorrect = $da->is_dung;
                                        
                                        $bgStyle = 'background: #f8fafc; border: 1px solid #e2e8f0; color: #334155;';
                                        $iconHTML = '';
                                        
                                        if ($isAnswerCorrect) {
                                            $bgStyle = 'background: #ecfdf5; border: 1px solid #10b981; color: #065f46; font-weight: 500;';
                                            $iconHTML = '<i class="fas fa-check-circle" style="color: #10b981; margin-left: auto; font-size: 16px;"></i>';
                                        } elseif ($isSelected) {
                                            $bgStyle = 'background: #fef2f2; border: 1px solid #ef4444; color: #991b1b;';
                                            $iconHTML = '<i class="fas fa-times-circle" style="color: #ef4444; margin-left: auto; font-size: 16px;"></i>';
                                        }
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 8px; {{ $bgStyle }}">
                                        <span style="font-weight: bold; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.05); border-radius: 50%; font-size: 12px; flex-shrink: 0;">{{ $letter }}</span>
                                        <div style="flex: 1; font-size: 13.5px;">{!! $da->noi_dung !!}</div>
                                        {!! $iconHTML !!}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Tự luận hoặc Điền khuyết --}}
                            @php
                                $dapAnChonText = 'Không chọn';
                                if ($selected !== null && trim((string)$selected) !== '' && $selected !== '[]') {
                                    $decoded = json_decode($selected, true);
                                    if (is_array($decoded)) {
                                        $dapAnChonText = implode(', ', $decoded);
                                    } else {
                                        $dapAnChonText = $selected;
                                    }
                                }
                                
                                if ($cau->loai_cau_hoi == 4) {
                                    $dapAnDungObj = $cau->dapAns->firstWhere('is_dung', 1);
                                    $dapAnDungText = $dapAnDungObj ? $dapAnDungObj->noi_dung : ($cau->giai_thich ?: 'N/A');
                                } else {
                                    $dapAnDungText = $cau->dapAns->where('is_dung', 1)->pluck('noi_dung')->implode(', ');
                                }
                            @endphp
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 15px 0; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div>
                                    <strong style="font-size: 12px; color: #64748b;">Sinh viên chọn:</strong>
                                    <div style="margin-top: 6px; padding: 8px 12px; background: white; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 13.5px; color: {{ $isCorrect ? '#065f46' : '#991b1b' }};">
                                        {{ $dapAnChonText }}
                                    </div>
                                </div>
                                <div>
                                    <strong style="font-size: 12px; color: #64748b;">Đáp án đúng:</strong>
                                    <div style="margin-top: 6px; padding: 8px 12px; background: white; border-radius: 6px; border: 1px solid #a7f3d0; font-size: 13.5px; color: #065f46; font-weight: 500;">
                                        {{ $dapAnDungText }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Giải thích đáp án --}}
                        @if(!empty($cau->giai_thich))
                            <div style="padding: 12px 16px; background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px; margin-top: 15px; font-size:13px;">
                                <strong style="color: #1e40af; font-size: 12px; display: block; margin-bottom: 4px;"><i class="fas fa-info-circle"></i> Giải thích:</strong>
                                <div style="color: #1e40af; line-height: 1.5;">{!! $cau->giai_thich !!}</div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid #e2e8f0;">
                <p style="color: #64748b; margin: 0;">Phiên này không có câu hỏi nào.</p>
            </div>
        @endif
    </div>

    {{-- Cột phải: Tóm tắt kết quả --}}
    <div>
        <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; position: sticky; top: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <h4 style="margin-top:0; color:#1e293b; font-size:16px; border-bottom:1px solid #f1f5f9; padding-bottom:10px;">📊 Kết quả tổng quát</h4>
            
            @if($phienLuyenTap->trang_thai == 1)
                <div style="text-align: center; margin: 24px 0;">
                    <h1 style="margin:0; color:#10b981; font-size: 3rem; font-weight:800; font-family:'Outfit', sans-serif;">
                        {{ number_format($phienLuyenTap->diem_so ?? 0, 1) }}đ
                    </h1>
                    <div style="margin-top:8px; font-weight:600; color:#475569; font-size:14px;">
                        Đúng {{ $phienLuyenTap->so_cau_dung ?? 0 }} / {{ $phienLuyenTap->so_cau_hoi ?? 0 }} câu
                    </div>
                </div>
            @else
                <div style="text-align: center; margin: 24px 0; color:#f59e0b; font-weight:600; font-size:15px; padding:15px; background:#fffbeb; border-radius:8px; border:1px dashed #f59e0b;">
                    ⚠️ Phiên chưa hoàn thành
                </div>
            @endif

            <hr style="margin:20px 0; border:0; border-top:1px solid #f1f5f9;">
            
            <div style="display:flex; flex-direction:column; gap:12px; font-size:13.5px; color:#475569;">
                <div style="display:flex; justify-content:space-between;">
                    <strong>Trạng thái:</strong>
                    <span>
                        @php
                            $tt = [0 => '⏳ Đang làm', 1 => '✅ Hoàn thành', 2 => '❌ Bỏ dở'];
                        @endphp
                        {{ $tt[$phienLuyenTap->trang_thai] ?? 'N/A' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <strong>Thời gian bắt đầu:</strong>
                    <span>{{ $phienLuyenTap->thoi_gian_bat_dau?->format('d/m/Y H:i') }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <strong>Chế độ:</strong>
                    <span>
                        {{ $phienLuyenTap->che_do == 1 ? 'Luyện tập' : ($phienLuyenTap->che_do == 2 ? 'Thi thử' : 'Ôn yếu') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
