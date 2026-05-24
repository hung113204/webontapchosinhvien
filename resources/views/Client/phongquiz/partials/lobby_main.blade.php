        {{-- ============ VIEW 1: LOBBY MAIN ============ --}}
        <div class="qv-central-card" id="qv-view-lobby" style="animation: qvSlideUp 0.3s ease;">
            <div class="qv-group-icon-wrap">
                <svg viewBox="0 0 120 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="mainGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#3b82f6"/>
                            <stop offset="100%" stop-color="#2563eb"/>
                        </linearGradient>
                        <linearGradient id="subGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#60a5fa"/>
                            <stop offset="100%" stop-color="#3b82f6"/>
                        </linearGradient>
                        <linearGradient id="subGradient2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#93c5fd"/>
                            <stop offset="100%" stop-color="#60a5fa"/>
                        </linearGradient>
                    </defs>
                    <circle cx="60" cy="28" r="18" fill="url(#mainGradient)"/>
                    <path d="M30 88 C30 60 42 48 60 48 C78 48 90 60 90 88" fill="url(#mainGradient)"/>
                    
                    <circle cx="25" cy="38" r="14" fill="url(#subGradient1)"/>
                    <path d="M0 88 C0 68 12 58 25 58 C35 58 45 68 45 88" fill="url(#subGradient1)"/>
                    
                    <circle cx="95" cy="38" r="14" fill="url(#subGradient2)"/>
                    <path d="M75 88 C75 68 85 58 95 58 C108 58 120 68 120 88" fill="url(#subGradient2)"/>
                </svg>
            </div>

            <h1 class="qv-title">Quiz Realtime theo phòng</h1>
            <p class="qv-subtitle">Tạo phòng, mời bạn bè, cùng chơi trắc nghiệm đồng bộ realtime.</p>

            {{-- Action Buttons --}}
            <div class="qv-btn-row">
                <button class="qv-btn qv-btn-blue" onclick="openCreateModal()">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tạo phòng
                </button>

                <button class="qv-btn qv-btn-white" onclick="openJoinModal()">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Tham gia phòng
                </button>

                <button class="qv-btn qv-btn-gray" onclick="alert('Chức năng vào ngẫu nhiên đang được phát triển!')">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="8" height="8" rx="1.5"></rect><rect x="14" y="2" width="8" height="8" rx="1.5"></rect>
                        <rect x="2" y="14" width="8" height="8" rx="1.5"></rect><rect x="14" y="14" width="8" height="8" rx="1.5"></rect>
                        <circle cx="6" cy="6" r="1" fill="currentColor"></circle><circle cx="18" cy="6" r="1" fill="currentColor"></circle>
                        <circle cx="6" cy="18" r="1" fill="currentColor"></circle><circle cx="18" cy="18" r="1" fill="currentColor"></circle>
                    </svg>
                    Vào ngẫu nhiên
                </button>
            </div>

            <div class="qv-main-content-wrapper">
                {{-- Leaderboard Area --}}
                <div class="qv-leaderboard-title">
                    <span>🏆</span>
                    <span>Top leaderboard (global)</span>
                </div>

                <div class="qv-tabs">
                    <button class="qv-tab" data-period="day" onclick="switchLeaderboard('day', this)">Ngày</button>
                    <button class="qv-tab active" data-period="week" onclick="switchLeaderboard('week', this)">Tuần</button>
                    <button class="qv-tab" data-period="month" onclick="switchLeaderboard('month', this)">Tháng</button>
                </div>

                <div class="qv-leaderboard-card" id="qv-leaderboard-list">
                    <!-- Leaderboard rows inject realtime here -->
                </div>
                
                <div class="qv-leaderboard-title" style="margin-top: 40px;">
                    <i class="fa-solid fa-door-open text-sky-600 mr-2 md:mr-3 text-xl md:text-2xl"></i>
                    <span>Danh sách phòng thi đấu đang mở</span>
                </div>

                <div class="qv-leaderboard-card" style="margin-bottom: 40px; border: 1.5px solid #bae6fd; background: #f0f9ff; box-shadow: 0 4px 15px rgba(14, 165, 233, 0.05);">
                    @if($activeRooms->isEmpty())
                        <div style="padding: 40px 20px; text-align: center; color: #0284c7; font-weight: 600; font-size: 17px;">
                            <span style="font-size: 24px; display: block; margin-bottom: 8px;">✨</span>
                            Hiện chưa có phòng thi đấu nào đang mở.<br><span style="font-weight: 500; font-size: 15px; color: #64748b;">Hãy là người đầu tiên tạo phòng để so tài cùng bạn bè nhé!</span>
                        </div>
                    @else
                        @foreach($activeRooms as $room)
                            <div class="qv-lb-row" style="cursor: pointer; background: white; margin: 10px; border-radius: 14px; border: 1px solid #e0f2fe; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center;" onclick="quickJoinRoom('{{ $room->ma_phong }}')">
                                <div style="display: flex; align-items: center; gap: 14px;">
                                    <div class="qv-lb-rank" style="background: linear-gradient(135deg, #0284c7, #0369a1); color: white; font-weight: 800; width: auto; padding: 8px 16px; border-radius: 10px; font-size: 16px; letter-spacing: 0.5px; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);">
                                        PIN: {{ $room->ma_phong }}
                                    </div>
                                    <div class="qv-lb-name" style="text-align: left;">
                                        <div style="font-weight: 800; color: #0f172a; font-size: 18px;">
                                            {{ $room->ten_phong ?: 'Phòng đấu #' . $room->ma_phong }}
                                        </div>
                                        <div style="font-size: 15px; color: #64748b; font-weight: 500; margin-top: 3px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                            Môn học: <strong style="color: #2563eb;">{{ $room->monHoc->ten_mon_hoc }}</strong>
                                            <span style="color: #cbd5e1;">•</span>
                                            Chủ phòng: <strong style="color: #475569;">{{ $room->chuPhong->ho_ten ?? 'Hệ thống' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="text-align: right;">
                                        <div style="font-size: 17px; font-weight: 800; color: #1e293b;">
                                            👥 {{ $room->thanh_vien_count }} người chơi
                                        </div>
                                        <div style="font-size: 13px; font-weight: 800; color: {{ $room->trang_thai == 1 ? '#16a34a' : '#ea580c' }}; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;">
                                            ● {{ $room->trang_thai == 1 ? 'Đang chờ' : 'Đang đấu' }}
                                        </div>
                                    </div>
                                    
                                    @if($room->trang_thai == 1)
                                        <button class="qv-btn qv-btn-blue" style="padding: 12px 24px; font-size: 15px; border-radius: 10px; box-shadow: 0 4px 10px rgba(30, 136, 229, 0.2);" onclick="event.stopPropagation(); quickJoinRoom('{{ $room->ma_phong }}')">
                                            Vào chơi
                                        </button>
                                    @else
                                        <button class="qv-btn" style="padding: 12px 24px; font-size: 15px; border-radius: 10px; background: #f1f5f9; color: #94a3b8; cursor: not-allowed;" disabled>
                                            Đang đấu
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
