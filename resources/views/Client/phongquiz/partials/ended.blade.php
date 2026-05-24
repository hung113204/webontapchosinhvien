@php
    $matchId = md5($room->id . $room->created_at);
@endphp
<div id="student-ended" class="qv-ended-card" style="display: none;">
    <div class="qv-ended-trophy">
        <i class="fas fa-trophy text-9xl text-yellow-400 drop-shadow-md"></i>
    </div>
    <h2>Kết quả ván đấu</h2>
    <p class="qv-ended-subtitle">
        Phòng <strong class="code">{{ $room->ma_phong }}</strong> • {{ $room->monHoc->ten_mon_hoc }} • @if($room->muc_do_cau_hoi == 1) Dễ @elseif($room->muc_do_cau_hoi == 2) Trung bình @else Khó @endif
    </p>
    <p class="qv-ended-matchid">matchId: {{ $matchId }}</p>

    <div class="qv-ended-leaderboard-card">
        <div class="qv-ended-lb-header">
            <span class="left">XẾP HẠNG</span>
            <span class="right">TỔNG ĐIỂM</span>
        </div>
        <div class="qv-ended-lb-list" id="ended-leaderboard-list">
            <!-- Dynamic rows injected here -->
        </div>
    </div>

    <div class="qv-ended-actions">
        <a href="{{ route('client.phongquiz.join') }}" class="qv-ended-btn-exit" onclick="leaveRoom(event)">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Thoát
        </a>
        
        <a href="{{ route('client.phongquiz.join') }}" class="qv-ended-btn-lobby" onclick="leaveRoom(event)">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
            </svg>
            Về phòng chờ
        </a>
    </div>
</div>
