        {{-- ============ VIEW 3: INLINE JOIN PIN (Direct sibling in qv-main-panel) ============ --}}
        <div id="qv-view-join" style="display: none; animation: qvSlideUp 0.3s ease; max-width: 600px; margin: 40px auto 0; background: white; border-radius: 24px; padding: 45px; box-shadow: 0 10px 40px rgba(59, 130, 246, 0.05); border: 1px solid #e2e8f0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 18px;">
                <button class="qv-fullscreen-btn" onclick="showLobbyView()" style="border-color: #94a3b8; color: #475569; padding: 8px 16px; font-size: 13px; background: #f8fafc;">
                    ← Quay lại sảnh
                </button>
                <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; text-align: center;">Tham gia bằng mã PIN</h2>
                <div style="width: 120px;" class="hide-mobile"></div>
            </div>

            <p style="font-size: 15px; color: #64748b; font-weight: 500; margin-bottom: 28px; text-align: center;">Nhập mã PIN 6 chữ số để gia nhập trận đấu realtime!</p>

            @if ($errors->any())
                <div class="pq-error-box" style="max-width: 450px; margin: 0 auto 20px;">
                    <span>⚠️</span>
                    <div style="flex: 1; text-align: left;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('client.phongquiz.join.post') }}" method="POST" autocomplete="off" style="max-width: 450px; margin: 0 auto;">
                @csrf
                <input type="text" name="ma_phong" id="ma_phong_input" maxlength="6" pattern="[0-9]*" inputmode="numeric" required
                    placeholder="MÃ PIN"
                    class="pq-pin-input"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                <button type="submit" class="pq-submit-btn" style="max-width: 280px; margin: 24px auto 0;">
                    <span>Vào phòng chơi</span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" />
                    </svg>
                </button>
            </form>
        </div>
