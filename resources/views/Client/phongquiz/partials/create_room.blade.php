        {{-- ============ VIEW 2: INLINE CREATE ROOM (Direct sibling in qv-main-panel) ============ --}}
        <div id="qv-view-create" style="display: none; animation: qvSlideUp 0.3s ease; max-width: 1000px; margin: 40px auto 0; background: white; border-radius: 24px; padding: 45px; box-shadow: 0 10px 40px rgba(59, 130, 246, 0.05); border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 18px;">
                <button class="qv-fullscreen-btn" onclick="showLobbyView()" style="border-color: #94a3b8; color: #475569; padding: 8px 16px; font-size: 13px; background: #f8fafc;">
                    ← Quay lại sảnh
                </button>
                <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; text-align: center;">Tạo phòng thi đấu mới</h2>
                <div style="width: 120px;" class="hide-mobile"></div>
            </div>

            <div id="create-error-box" class="pq-error-box" style="display:none;"></div>

            <form id="create-room-form" autocomplete="off" style="text-align: left; max-width: 650px; margin: 0 auto;">
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:14px;font-weight:700;color:#334155;margin-bottom:8px;">Tên phòng thi đấu <span style="color:#94a3b8;font-weight:500;">(tùy chọn)</span></label>
                    <input type="text" name="ten_phong" id="input_ten_phong" placeholder="VD: Thách đấu cấu trúc dữ liệu giải thuật" style="width:100%;padding:13px 16px;background:#f8fafc;border:2.5px solid #e2e8f0;border-radius:12px;font-size:15px;color:#1e293b;outline:none;font-family:inherit;box-sizing:border-box;transition:all 0.2s;" onfocus="this.style.borderColor='#1e88e5'; this.style.background='white';" onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                </div>

                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:14px;font-weight:700;color:#334155;margin-bottom:8px;">Môn học trắc nghiệm thi đấu <span style="color:#e53935;">*</span></label>
                    <select name="mon_hoc_id" id="select_mon_hoc" required style="width:100%;padding:13px 16px;background:#f8fafc;border:2.5px solid #e2e8f0;border-radius:12px;font-size:15px;color:#1e293b;outline:none;font-family:inherit;cursor:pointer;box-sizing:border-box;transition:all 0.2s;" onfocus="this.style.borderColor='#1e88e5'; this.style.background='white';" onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                        <option value="">-- Chọn môn học --</option>
                        @foreach($monHocs as $mh)
                            <option value="{{ $mh->id }}">{{ $mh->ten_mon_hoc }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
                    <div>
                        <label style="display:block;font-size:14px;font-weight:700;color:#334155;margin-bottom:8px;">Độ khó câu hỏi</label>
                        <select name="muc_do_cau_hoi" style="width:100%;padding:13px 16px;background:#f8fafc;border:2.5px solid #e2e8f0;border-radius:12px;font-size:15px;outline:none;font-family:inherit;box-sizing:border-box;cursor:pointer;">
                            <option value="">Hỗn hợp</option>
                            <option value="1">Dễ</option>
                            <option value="2">Trung bình</option>
                            <option value="3">Khó</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:14px;font-weight:700;color:#334155;margin-bottom:8px;">Tổng số câu hỏi</label>
                        <input type="number" name="tong_so_cau" value="10" min="5" max="50" style="width:100%;padding:13px 16px;background:#f8fafc;border:2.5px solid #e2e8f0;border-radius:12px;font-size:15px;outline:none;box-sizing:border-box;font-family:inherit;transition:all 0.2s;" onfocus="this.style.borderColor='#1e88e5'; this.style.background='white';" onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                    </div>
                </div>

                <div style="margin-bottom:28px;">
                    <label style="display:block;font-size:14px;font-weight:700;color:#334155;margin-bottom:8px;">Thời gian đếm ngược mỗi câu (giây)</label>
                    <input type="number" name="thoi_gian_tra_loi_cau_hoi" value="30" min="10" max="120" style="width:100%;padding:13px 16px;background:#f8fafc;border:2.5px solid #e2e8f0;border-radius:12px;font-size:15px;outline:none;box-sizing:border-box;font-family:inherit;transition:all 0.2s;" onfocus="this.style.borderColor='#1e88e5'; this.style.background='white';" onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc';">
                </div>

                <button type="submit" id="create-submit-btn" class="pq-submit-btn" style="background: linear-gradient(135deg, #2da44e, #238636); box-shadow: 0 4px 12px rgba(45, 164, 78, 0.25); max-width: 280px; margin: 0 auto;">
                    <span>Khởi tạo phòng ngay</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>
        </div>
