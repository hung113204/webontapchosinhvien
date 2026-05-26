<div id="student-lobby" class="qv-lobby-new">
    <div class="qv-lobby-header">
        <a href="{{ route('client.phongquiz.join') }}" class="qv-lobby-back" onclick="leaveRoom(event)">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Rời phòng
        </a>
        <div class="qv-lobby-code-badge">
            <span class="label">MÃ PHÒNG</span>
            <div class="code-row">
                <span class="code">{{ $room->ma_phong }}</span>
                <svg viewBox="0 0 24 24" width="20" height="20" fill="#64748b" style="cursor:pointer;" onclick="navigator.clipboard.writeText('{{ $room->ma_phong }}')">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="qv-lobby-content">
        <div class="qv-lobby-center">
            <div class="qv-lobby-icon-pulse">
                <i class="fas fa-users-cog" style="font-size: 80px; color: #0284c7;"></i>
            </div>
            <h1 class="qv-lobby-title-large">Lobby</h1>
            
            <div class="qv-lobby-info-badges">
                <div class="qv-info-badge">
                    <span id="lobby-subject">{{ $room->monHoc->ten_mon_hoc ?? 'Môn học' }}</span>
                </div>
                <div class="qv-info-badge">
                    <span id="lobby-level">
                        @if($room->muc_do_cau_hoi == 1)
                            Mức độ: Dễ
                        @elseif($room->muc_do_cau_hoi == 2)
                            Mức độ: Trung bình
                        @elseif($room->muc_do_cau_hoi == 3)
                            Mức độ: Khó
                        @else
                            Tất cả mức độ
                        @endif
                    </span>
                </div>
                <div class="qv-info-badge">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    <span id="lobby-member-count">{{ $room->thanhVien->count() }}/50</span>
                </div>
            </div>
        </div>

        <div class="qv-lobby-two-column">
            <div class="qv-lobby-column">
                <div class="qv-lobby-col-header">
                    <div class="qv-lobby-section-title">Người chơi</div>
                    <div class="qv-lobby-section-count" id="lobby-player-count">{{ $room->thanhVien->count() }} người</div>
                </div>
                <div class="qv-lobby-players-list" id="lobby-players-list">
                    @foreach($room->thanhVien as $index => $m)
                        @php
                            $isMe = $m->user_id === $member->user_id;
                            $rankDisplay = $index + 1;
                            $avatarClass = $index === 0 ? '' : 'avatar-normal';
                            $avatarIcon = $index === 0 ? '👑' : $rankDisplay;
                            $memberName = $m->biem_danh ?: ($m->user->ho_ten ?? 'Học sinh');
                        @endphp
                        <div class="qv-lobby-player-item">
                            <div style="display: flex; align-items: center;">
                                <div class="qv-lobby-player-avatar {{ $avatarClass }}">{{ $avatarIcon }}</div>
                                <div class="qv-lobby-player-info">
                                    <p class="qv-lobby-player-name">{{ $memberName }}@if($isMe) <span style="font-size:13px;color:#64748b;font-weight:600;">(bạn)</span>@endif</p>
                                    <div class="qv-lobby-player-status">
                                        <span class="dot"></span> Online
                                    </div>
                                </div>
                            </div>
                            <div class="qv-lobby-player-rank">#{{ $rankDisplay }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="qv-lobby-column">
                <div class="qv-lobby-section-title" style="margin-bottom: 24px;">Trạng thái</div>
                <div class="qv-lobby-status-box">
                    <div class="qv-lobby-status-message" id="lobby-status-message">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="#3b82f6" fill="none" stroke-width="2.5" style="animation: spin 2s linear infinite;">
                            <circle cx="12" cy="12" r="10" stroke-dasharray="30 30" stroke-linecap="round"></circle>
                        </svg>
                        Đang chờ Host bắt đầu...
                    </div>
                </div>
                @if($room->chu_phong_id === $member->user_id)
                    <button id="host-start-btn" onclick="startRoomByHost()" class="qv-lobby-play-btn">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="white" stroke="none">
                            <polygon points="5 3 19 12 5 21 5 3" />
                        </svg>
                        Chơi ngay
                    </button>
                @else
                    <button class="qv-lobby-play-btn" disabled style="opacity: 0.6; cursor: not-allowed;">
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="white" stroke="none">
                            <polygon points="5 3 19 12 5 21 5 3" />
                        </svg>
                        Chờ phát
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
