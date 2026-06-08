        {{-- ============ VIEW 2: INLINE CREATE ROOM (Direct sibling in qv-main-panel) ============ --}}
        <div id="qv-view-create" style="display: none; animation: qvSlideUp 0.3s ease; max-width: 1000px; margin: 40px auto 0; background: white; border-radius: 24px; padding: 45px; box-shadow: 0 10px 40px rgba(59, 130, 246, 0.05); border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 18px; position: relative;">
                <button type="button" class="qv-back-btn" onclick="showLobbyView()">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Quay lại
                </button>
                <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0; text-align: center;">Tạo phòng thi đấu mới</h2>
            </div>

            <div id="create-error-box" class="pq-error-box" style="display:none;"></div>

            <form id="create-room-form" autocomplete="off" style="text-align: left; max-width: 850px; margin: 0 auto;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 24px;">
                    <!-- Cột Trái -->
                    <div>
                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:10px;">Tên phòng thi đấu <span style="color:#94a3b8;font-weight:500;">(tùy chọn)</span></label>
                            <input type="text" name="ten_phong" id="input_ten_phong" placeholder="VD: Thách đấu cấu trúc dữ liệu giải thuật" style="width: 100%; padding: 18px 24px; font-size: 17px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; font-family: inherit; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';" onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                        </div>
        
                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:10px;">Môn học trắc nghiệm thi đấu <span style="color:#e53935;">*</span></label>
                            <select name="mon_hoc_id" id="select_mon_hoc" required style="width: 100%; padding: 18px 24px; font-size: 17px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; font-family: inherit; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s; cursor: pointer;" onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';" onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                                <option value="">-- Chọn môn học --</option>
                                @foreach($monHocs as $mh)
                                    <option value="{{ $mh->id }}">{{ $mh->ten_mon_hoc }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cột Phải -->
                    <div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                            <div>
                                <label style="display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:10px;">Độ khó câu hỏi</label>
                                <select name="muc_do_cau_hoi" style="width: 100%; padding: 18px 24px; font-size: 17px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; font-family: inherit; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s; cursor: pointer;" onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';" onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                                    <option value="">Hỗn hợp</option>
                                    <option value="1">Nhận biết</option>
                                    <option value="2">Thông hiểu</option>
                                    <option value="3">Vận dụng</option>
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:10px;">Tổng số câu</label>
                                <input type="number" name="tong_so_cau" value="10" min="5" max="50" style="width: 100%; padding: 18px 24px; font-size: 17px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; font-family: inherit; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';" onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                            </div>
                        </div>
        
                        <div style="margin-bottom:20px;">
                            <label style="display:block;font-size:16px;font-weight:700;color:#334155;margin-bottom:10px;">Thời gian đếm ngược mỗi câu (giây)</label>
                            <input type="number" name="thoi_gian_tra_loi_cau_hoi" value="30" min="10" max="120" style="width: 100%; padding: 18px 24px; font-size: 17px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; font-family: inherit; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';" onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                        </div>
                    </div>
                </div>

                <button type="submit" id="create-submit-btn" class="pq-submit-btn" style="background: linear-gradient(135deg, #2da44e, #238636); box-shadow: 0 4px 12px rgba(45, 164, 78, 0.25); max-width: 360px; margin: 10px auto 0; padding: 18px 24px; font-size: 18px; border-radius: 100px; display: flex; justify-content: center; align-items: center; gap: 10px; color: white; font-weight: 800; border: none; cursor: pointer;">
                    <span>Khởi tạo phòng ngay</span>
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>
        </div>
