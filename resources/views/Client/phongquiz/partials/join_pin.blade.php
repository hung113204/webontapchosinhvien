        {{-- ============ VIEW 3: INLINE JOIN PIN (Direct sibling in qv-main-panel) ============ --}}
        <div id="qv-view-join" style="display: none; animation: qvSlideUp 0.3s ease; max-width: 600px; margin: 40px auto 0; background: #e8f4fc; border-radius: 24px; padding: 45px 50px; box-shadow: 0 10px 40px rgba(59, 130, 246, 0.08); position: relative;">
            <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 35px; position: relative;">
                <button type="button" class="qv-back-btn" onclick="showLobbyView()">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Quay lại
                </button>
                <h2 style="font-size: 28px; font-weight: 800; color: #334155; margin: 0;">Tham gia phòng</h2>
            </div>

            @if ($errors->any())
                <div class="pq-error-box" style="margin-bottom: 24px; background: #fef2f2; border: 1px solid #fca5a5; padding: 16px; border-radius: 12px; color: #b91c1c; display: flex; align-items: flex-start; gap: 12px; font-size: 15px;">
                    <span style="font-size: 20px;">⚠️</span>
                    <div style="flex: 1; text-align: left;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('client.phongquiz.join.post') }}" method="POST" autocomplete="off">
                @csrf
                <div style="margin-bottom: 28px; text-align: left;">
                    <label style="display: block; font-weight: 800; color: #334155; margin-bottom: 12px; font-size: 17px;">Mã phòng (6 ký tự)</label>
                    <input type="text" name="ma_phong" id="ma_phong_input" maxlength="6" pattern="[0-9]*" inputmode="numeric" required
                        placeholder="VD : A2BC3D"
                        style="width: 100%; padding: 18px 24px; font-size: 18px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 600; letter-spacing: 2px; text-align: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s;"
                        onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';"
                        onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>

                <div style="margin-bottom: 40px; text-align: left;">
                    <label style="display: block; font-weight: 800; color: #334155; margin-bottom: 12px; font-size: 17px;">Nickname</label>
                    <input type="text" name="ho_ten" id="ho_ten_input" required
                        placeholder="Nhập nickname của bạn"
                        value="{{ session('quiz_nickname', optional(auth()->user())->ho_ten ?? '') }}"
                        style="width: 100%; padding: 18px 24px; font-size: 18px; border-radius: 14px; border: none; background: #f8fafc; color: #334155; outline: none; font-weight: 500; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); box-sizing: border-box; transition: all 0.2s;"
                        onfocus="this.style.background='white'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02), 0 0 0 4px rgba(30, 136, 229, 0.1)';"
                        onblur="this.style.background='#f8fafc'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';">
                </div>

                <button type="submit" style="width: 100%; padding: 20px; font-size: 20px; font-weight: 800; background: #10b981; color: white; border: none; border-radius: 100px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                    Vào phòng
                </button>
            </form>
        </div>
