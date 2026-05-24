@extends('Admin.layouts.admin')
@section('title', 'Điều phối Trận đấu Quiz')

@section('content')
<div class="game-container" style="max-width: 1200px; margin: 0 auto; font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    {{-- MÀN HÌNH CHỜ (LOBBY - TRẠNG THÁI 1) --}}
    <div id="state-lobby" class="game-state-card" style="display: none; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); padding: 40px 20px; text-align: center; color: white; position: relative;">
            <div style="font-size: 16px; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; opacity: 0.9; margin-bottom: 8px;">MÃ PIN PHÒNG CHƠI</div>
            <div id="lobby-pin" style="font-size: 72px; font-weight: 800; letter-spacing: 4px; line-height: 1; text-shadow: 0 4px 10px rgba(0,0,0,0.15); font-family: monospace;">------</div>
            <p style="margin-top: 15px; font-size: 16px; font-weight: 500; opacity: 0.9;">Mời học sinh truy cập mục <strong style="text-decoration: underline;">Học viên -> Phòng Quiz Realtime</strong> và nhập mã PIN trên để tham gia.</p>
            
            <div style="position: absolute; bottom: -25px; left: 50%; transform: translateX(-50%); background: #10b981; color: white; padding: 10px 24px; border-radius: 50px; font-weight: 700; font-size: 14px; box-shadow: 0 4px 10px rgba(16,185,129,0.3); display: flex; align-items: center; gap: 8px;">
                <span class="pulse-dot" style="width: 8px; height: 8px; background: white; border-radius: 50%;"></span>
                ĐANG CHỜ HỌC SINH THAM GIA
            </div>
        </div>

        <div style="padding: 60px 40px 40px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px;">
                <h4 style="margin: 0; font-size: 20px; font-weight: 700; color: #1f2937; display: flex; align-items: center; gap: 10px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#4f46e5" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    Danh sách học sinh (<span id="lobby-count">0</span>)
                </h4>
                
                <button id="btn-start-game" onclick="startGame()" class="btn btn-primary" 
                    style="padding: 12px 28px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 16px; cursor: pointer; box-shadow: 0 4px 15px rgba(16,185,129,0.25); display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" stroke="none">
                        <polygon points="5 3 19 12 5 21 5 3" />
                    </svg>
                    Bắt đầu trận đấu
                </button>
            </div>

            <div id="lobby-players-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; min-height: 150px;">
                {{-- Players are filled here dynamically --}}
            </div>
            <div id="lobby-empty-players" style="text-align: center; color: #9ca3af; padding: 40px 0; font-style: italic;">
                Chưa có học sinh nào tham gia. Mã PIN phòng chơi đang hiển thị, hãy mời học sinh tham gia ngay!
            </div>
        </div>
    </div>


    {{-- MÀN HÌNH THI ĐẤU (PLAYING - TRẠNG THÁI 2) --}}
    <div id="state-playing" class="game-state-card" style="display: none; display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        {{-- Phần bên trái: Câu hỏi và Bảng thống kê đáp án --}}
        <div style="background: white; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); padding: 30px; border: 1px solid #e5e7eb; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <span id="play-question-num" style="background: #e0e7ff; color: #4338ca; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 14px;">Câu hỏi 1 / 10</span>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    <span id="play-timer" style="font-size: 24px; font-weight: 800; color: #ef4444; font-family: monospace;">30</span>
                    <span style="font-size: 14px; color: #ef4444; font-weight: 600;">giây</span>
                </div>
            </div>

            <h2 id="play-question-text" style="font-size: 24px; font-weight: 800; color: #111827; line-height: 1.4; margin: 10px 0 20px;">Câu hỏi tải ở đây...</h2>
            
            <div id="play-question-image-container" style="display: none; text-align: center; margin-bottom: 25px; background: #f9fafb; padding: 10px; border-radius: 12px; border: 1px dashed #e5e7eb;">
                <img id="play-question-image" src="" style="max-height: 250px; border-radius: 8px; object-fit: contain;">
            </div>

            {{-- Kahoot-style grid options --}}
            <div id="play-options-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                {{-- Options rendered dynamically --}}
            </div>

            {{-- Visual Bar Chart (Chỉ hiện khi Hết giờ / Show Answer) --}}
            <div id="play-chart-container" style="display: none; background: #f9fafb; padding: 25px; border-radius: 12px; border: 1px solid #e5e7eb; margin-top: 10px;">
                <h4 style="margin: 0 0 20px; font-size: 16px; font-weight: 700; color: #374151; display: flex; align-items: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10" /><line x1="12" y1="20" x2="12" y2="4" /><line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                    Thống kê đáp án học sinh đã chọn
                </h4>
                <div id="play-chart-bars" style="display: flex; align-items: flex-end; justify-content: space-around; height: 180px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb; margin-bottom: 15px;">
                    {{-- Bars rendered dynamically --}}
                </div>
            </div>

            <div style="margin-top: auto; border-top: 1px solid #f3f4f6; padding-top: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div style="font-weight: 600; color: #4b5563; font-size: 15px;">
                    Đã nộp bài: <span id="play-submit-count" style="color: #4f46e5; font-size: 18px; font-weight: 700;">0</span> / <span id="play-total-count" style="font-weight: 700;">0</span> người chơi
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button id="btn-force-end" onclick="forceEndQuestion()" class="btn btn-secondary" style="padding: 10px 18px; border: 1px solid #d1d5db; background: white; border-radius: 6px; font-weight: 600; cursor: pointer; color: #4b5563;">
                        Kết thúc câu hỏi
                    </button>
                    <button id="btn-next-question" onclick="nextQuestion()" class="btn btn-primary" style="padding: 10px 22px; background: #4f46e5; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; display: none; align-items: center; gap: 6px;">
                        <span>Câu hỏi tiếp theo</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="9 18 15 12 9 6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Phần bên phải: Bảng xếp hạng trực tiếp vòng này --}}
        <div style="background: white; border-radius: 16px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); padding: 25px; border: 1px solid #e5e7eb; display: flex; flex-direction: column;">
            <h3 style="margin: 0 0 20px; font-size: 18px; font-weight: 700; color: #111827; display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f59e0b" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                Bảng xếp hạng live
            </h3>
            
            <div id="play-leaderboard-list" style="display: flex; flex-direction: column; gap: 10px; overflow-y: auto; max-height: 450px; padding-right: 5px;">
                {{-- Leaderboard items filled dynamically --}}
            </div>
        </div>
    </div>


    {{-- MÀN HÌNH KẾT THÚC (ENDED - TRẠNG THÁI 3) --}}
    <div id="state-ended" class="game-state-card" style="display: none; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 40px; border: 1px solid #e5e7eb; text-align: center;">
        <div style="font-size: 50px;">🏆</div>
        <h2 style="font-size: 32px; font-weight: 800; color: #111827; margin: 10px 0 5px;">Trận đấu đã kết thúc!</h2>
        <p style="color: #6b7280; font-size: 16px; margin-bottom: 40px;">Xin chúc mừng tất cả các tuyển thủ đã hoàn thành chặng thi đấu xuất sắc.</p>

        {{-- Elegant 3D Celebration Podium --}}
        <div style="display: flex; justify-content: center; align-items: flex-end; height: 280px; margin-bottom: 50px; gap: 20px; padding-bottom: 10px;">
            {{-- 2nd Place --}}
            <div id="podium-2nd" style="display: flex; flex-direction: column; align-items: center; width: 140px; display: none;">
                <div class="podium-avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #cbd5e1; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569; font-size: 18px; margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">2</div>
                <div class="podium-name" style="font-weight: 700; color: #1f2937; margin-bottom: 5px; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">Tên SV 2</div>
                <div class="podium-score" style="font-weight: 600; color: #4b5563; font-size: 12px; margin-bottom: 10px;">0 đ</div>
                <div style="height: 120px; background: linear-gradient(to top, #94a3b8, #cbd5e1); width: 100%; border-radius: 8px 8px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">2</div>
            </div>

            {{-- 1st Place (Middle - Tallest) --}}
            <div id="podium-1st" style="display: flex; flex-direction: column; align-items: center; width: 160px; display: none;">
                <div style="font-size: 24px; line-height: 1; margin-bottom: 2px; animation: bounce 1s infinite alternate;">👑</div>
                <div class="podium-avatar" style="width: 70px; height: 70px; border-radius: 50%; border: 4px solid #f59e0b; background: #fef3c7; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #d97706; font-size: 20px; margin-bottom: 10px; box-shadow: 0 6px 12px rgba(245,158,11,0.2);">1</div>
                <div class="podium-name" style="font-weight: 800; color: #111827; margin-bottom: 5px; font-size: 15px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">Tên SV 1</div>
                <div class="podium-score" style="font-weight: 700; color: #b45309; font-size: 13px; margin-bottom: 10px;">0 đ</div>
                <div style="height: 160px; background: linear-gradient(to top, #d97706, #f59e0b); width: 100%; border-radius: 8px 8px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 42px; font-weight: 800; text-shadow: 0 2px 5px rgba(0,0,0,0.15);">1</div>
            </div>

            {{-- 3rd Place --}}
            <div id="podium-3rd" style="display: flex; flex-direction: column; align-items: center; width: 140px; display: none;">
                <div class="podium-avatar" style="width: 60px; height: 60px; border-radius: 50%; border: 3px solid #b45309; background: #ffedd5; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #b45309; font-size: 18px; margin-bottom: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">3</div>
                <div class="podium-name" style="font-weight: 700; color: #1f2937; margin-bottom: 5px; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">Tên SV 3</div>
                <div class="podium-score" style="font-weight: 600; color: #4b5563; font-size: 12px; margin-bottom: 10px;">0 đ</div>
                <div style="height: 90px; background: linear-gradient(to top, #a16207, #d97706); width: 100%; border-radius: 8px 8px 0 0; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">3</div>
            </div>
        </div>

        <div style="max-width: 600px; margin: 0 auto 30px; border-top: 1px solid #f3f4f6; padding-top: 30px;">
            <h4 style="margin: 0 0 15px; font-weight: 700; color: #374151; font-size: 16px;">Bảng điểm chi tiết</h4>
            <div id="ended-rank-list" style="display: flex; flex-direction: column; gap: 8px;">
                {{-- Loaded dynamically --}}
            </div>
        </div>

        <a href="{{ route('admin.phongquiz.index') }}" class="btn btn-primary" style="padding: 12px 30px; font-size: 15px; font-weight: 700; border-radius: 8px; background: #4f46e5; border: none; color: white; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(79,70,229,0.2);">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Quay lại danh sách phòng
        </a>
    </div>
</div>

<script>
    const ma_phong = "{{ $room->ma_phong }}";
    let current_state = null;
    let room_data = null;
    let timer_interval = null;
    let seconds_left = 0;
    let is_revealed = false; // Trạng thái đã công bố đáp án / biểu đồ của câu hỏi hiện tại

    // Màu sắc và ký hiệu Kahoot-style cho 4 đáp án
    const option_styles = [
        { class: 'opt-red', color: '#ef4444', icon: '▲' },
        { class: 'opt-blue', color: '#3b82f6', icon: '◆' },
        { class: 'opt-yellow', color: '#f59e0b', icon: '●' },
        { class: 'opt-green', color: '#10b981', icon: '■' }
    ];

    function fetchStatus() {
        fetch(`/admin/phong-quiz/room/${ma_phong}/status`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                
                room_data = data;
                const state = data.room.trang_thai;
                
                // Chuyển màn hình tương ứng với trạng thái
                switchState(state);
                
                // Cập nhật dữ liệu cho từng màn hình
                if (state === 1) {
                    updateLobby(data);
                } else if (state === 2) {
                    updatePlaying(data);
                } else if (state === 3) {
                    updateEnded(data);
                }
            })
            .catch(err => console.error("Lỗi đồng bộ Realtime: ", err));
    }

    // Khởi động vòng lặp đồng bộ Realtime bằng polling 1.5 giây
    const status_poll_interval = setInterval(fetchStatus, 1500);
    // Chạy lần đầu ngay lập tức
    fetchStatus();

    function switchState(state) {
        if (current_state === state) return;
        current_state = state;

        document.getElementById('state-lobby').style.display = state === 1 ? 'block' : 'none';
        document.getElementById('state-playing').style.display = state === 2 ? 'grid' : 'none';
        document.getElementById('state-ended').style.display = state === 3 ? 'block' : 'none';

        // Xóa countdown timer cũ nếu chuyển bang
        if (state !== 2) {
            clearInterval(timer_interval);
            timer_interval = null;
        }
    }

    // ==========================================
    // 1. XỬ LÝ MÀN HÌNH CHỜ (LOBBY)
    // ==========================================
    function updateLobby(data) {
        document.getElementById('lobby-pin').textContent = data.room.ma_phong;
        document.getElementById('lobby-count').textContent = data.members.length;

        const grid = document.getElementById('lobby-players-grid');
        const emptyAlert = document.getElementById('lobby-empty-players');
        
        if (data.members.length === 0) {
            grid.style.display = 'none';
            emptyAlert.style.display = 'block';
            return;
        }

        grid.style.display = 'grid';
        emptyAlert.style.display = 'none';

        // Vẽ danh sách học sinh
        grid.innerHTML = data.members.map(player => {
            const avatar = player.avatar 
                ? `<img src="${player.avatar}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">`
                : `<div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">${player.name.substring(0, 2).toUpperCase()}</div>`;

            return `
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: all 0.2s;">
                    ${avatar}
                    <strong style="color: #334155; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">${player.name}</strong>
                    <span style="font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 50px; background: ${player.is_online ? '#d1fae5; color: #065f46;' : '#f3f4f6; color: #6b7280;'}">
                        ${player.is_online ? 'Sẵn sàng' : 'Offline'}
                    </span>
                </div>
            `;
        }).join('');
    }

    function startGame() {
        if (!confirm('Bạn có muốn bắt đầu trận đấu ngay bây giờ?')) return;
        
        const btn = document.getElementById('btn-start-game');
        btn.disabled = true;

        fetch(`/admin/phong-quiz/room/${ma_phong}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                switchState(2);
                fetchStatus();
            } else {
                alert(data.message);
                btn.disabled = false;
            }
        });
    }

    // ==========================================
    // 2. XỬ LÝ MÀN HÌNH THI ĐẤU (PLAYING)
    // ==========================================
    let active_question_id = null;

    function updatePlaying(data) {
        const q = data.current_question;
        if (!q) return;

        // Reset nếu đây là một câu hỏi hoàn toàn mới
        if (active_question_id !== q.id) {
            active_question_id = q.id;
            is_revealed = false;
            
            // Xóa/reset giao diện cho câu hỏi mới
            document.getElementById('play-chart-container').style.display = 'none';
            document.getElementById('btn-force-end').style.display = 'block';
            document.getElementById('btn-next-question').style.display = 'none';

            // Kích hoạt Countdown Timer đếm ngược tại chỗ
            seconds_left = data.seconds_remaining;
            document.getElementById('play-timer').textContent = seconds_left;
            
            clearInterval(timer_interval);
            timer_interval = setInterval(() => {
                seconds_left = Math.max(0, seconds_left - 1);
                document.getElementById('play-timer').textContent = seconds_left;
                if (seconds_left === 0) {
                    clearInterval(timer_interval);
                    revealAnswer(); // Hết giờ -> Tự động vẽ biểu đồ và hiện kết quả đúng
                }
            }, 1000);
        }

        // Nếu tất cả học sinh đã nộp bài sớm -> kết thúc đếm ngược và reveal luôn
        if (data.all_answered && !is_revealed && seconds_left > 0) {
            clearInterval(timer_interval);
            seconds_left = 0;
            document.getElementById('play-timer').textContent = 0;
            revealAnswer();
        }

        // Cập nhật text câu hỏi
        document.getElementById('play-question-num').textContent = `Câu hỏi ${q.number} / ${data.room.tong_so_cau}`;
        document.getElementById('play-question-text').innerHTML = q.noi_dung;

        // Ảnh câu hỏi
        const imgCont = document.getElementById('play-question-image-container');
        const imgTag = document.getElementById('play-question-image');
        if (q.hinh_anh) {
            imgTag.src = q.hinh_anh;
            imgCont.style.display = 'block';
        } else {
            imgCont.style.display = 'none';
        }

        // Vẽ các đáp án Kahoot Grid
        const grid = document.getElementById('play-options-grid');
        grid.innerHTML = q.choices.map((choice, i) => {
            const style = option_styles[i % 4];
            
            // Xác định class highlight nếu đã công bố kết quả
            let revealBorder = 'border: 2px solid transparent;';
            let checkmark = '';
            let opacity = 'opacity: 1;';
            
            if (is_revealed) {
                if (choice.is_dung) {
                    revealBorder = 'border: 4px solid #10b981; box-shadow: 0 0 15px rgba(16,185,129,0.3);';
                    checkmark = '<span style="background: white; color: #10b981; width: 22px; height: 22px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; margin-left: auto;">✓</span>';
                } else {
                    opacity = 'opacity: 0.45;';
                }
            }

            return `
                <div style="background: ${style.color}; color: white; padding: 18px 20px; border-radius: 12px; display: flex; align-items: center; gap: 12px; font-size: 16px; font-weight: 700; ${revealBorder} ${opacity} transition: all 0.3s ease;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        ${choice.ky_hieu}
                    </div>
                    <span>${choice.noi_dung}</span>
                    ${checkmark}
                </div>
            `;
        }).join('');

        // Cập nhật số học sinh nộp bài
        document.getElementById('play-submit-count').textContent = data.answered_users.length;
        document.getElementById('play-total-count').textContent = data.members.filter(m => m.is_online).length;

        // Vẽ bảng xếp hạng Live
        const lbList = document.getElementById('play-leaderboard-list');
        lbList.innerHTML = data.members.map((player, idx) => {
            // Check nếu người này đã trả lời câu hiện tại
            const hasAnswered = data.answered_users.includes(player.user_id);
            const answeredIndicator = hasAnswered 
                ? `<span style="background: #d1fae5; color: #065f46; font-size: 11px; padding: 2px 8px; border-radius: 20px; font-weight: 600;">✓ Đã nộp</span>`
                : `<span style="background: #fff7ed; color: #c2410c; font-size: 11px; padding: 2px 8px; border-radius: 20px; font-weight: 600;">Suy nghĩ...</span>`;

            // Huy chương cho Top 3
            let medal = `<span style="font-weight: 700; color: #64748b; min-width: 20px;">#${idx+1}</span>`;
            if (idx === 0) medal = '🥇';
            else if (idx === 1) medal = '🥈';
            else if (idx === 2) medal = '🥉';

            return `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; background: ${idx === 0 ? '#fffbeb; border: 1px solid #fef3c7;' : '#f8fafc; border: 1px solid #f1f5f9;'} border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: all 0.2s;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        ${medal}
                        <strong style="color: #334155; font-size: 14px;">${player.name}</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        ${answeredIndicator}
                        <span style="font-weight: 700; color: #1e40af; font-size: 14px;">${player.score} đ</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    // Force dừng thời gian câu hỏi và công bố kết quả sớm
    function forceEndQuestion() {
        clearInterval(timer_interval);
        seconds_left = 0;
        document.getElementById('play-timer').textContent = 0;
        revealAnswer();
    }

    // Công bố đáp án + Vẽ biểu đồ biểu quyết
    function revealAnswer() {
        if (is_revealed) return;
        is_revealed = true;

        document.getElementById('btn-force-end').style.display = 'none';
        document.getElementById('btn-next-question').style.display = 'flex';

        // Tải lại status một lần cuối để lấy full stats
        fetch(`/admin/phong-quiz/room/${ma_phong}/status`)
            .then(res => res.json())
            .then(data => {
                room_data = data;
                
                // Show container biểu đồ
                const chartContainer = document.getElementById('play-chart-container');
                chartContainer.style.display = 'block';

                const q = data.current_question;
                const stats = data.answer_stats || [];
                const maxVote = Math.max(...stats.map(s => s.count), 1); // Tránh chia cho 0

                // Vẽ các cột biểu đồ
                const barsContainer = document.getElementById('play-chart-bars');
                barsContainer.innerHTML = q.choices.map((choice, i) => {
                    const style = option_styles[i % 4];
                    const choiceStat = stats.find(s => s.id === choice.id) || { count: 0 };
                    const heightPercent = (choiceStat.count / maxVote) * 100;
                    
                    let indicator = choice.is_dung ? '🏆' : '';

                    return `
                        <div style="display: flex; flex-direction: column; align-items: center; width: 70px; height: 100%; justify-content: flex-end;">
                            <span style="font-weight: 700; font-size: 14px; color: #374151; margin-bottom: 5px;">${choiceStat.count}</span>
                            <div style="height: ${heightPercent}%; width: 38px; background: ${style.color}; border-radius: 6px 6px 0 0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: height 0.5s ease-out; position: relative;">
                                ${choice.is_dung ? '<div style="position: absolute; top:-25px; left:50%; transform:translateX(-50%); font-size:16px;">✓</div>' : ''}
                            </div>
                            <span style="font-weight: 700; color: white; background: ${style.color}; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-top: 8px; font-size: 12px; font-family: monospace;">
                                ${choice.ky_hieu}
                            </span>
                        </div>
                    `;
                }).join('');

                // Cập nhật highlight lại grid câu hỏi
                updatePlaying(data);
            });
    }

    // Chuyển sang câu hỏi tiếp theo
    function nextQuestion() {
        const btn = document.getElementById('btn-next-question');
        btn.disabled = true;

        fetch(`/admin/phong-quiz/room/${ma_phong}/next`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.ended) {
                    switchState(3);
                    fetchStatus();
                } else {
                    is_revealed = false;
                    btn.disabled = false;
                    fetchStatus();
                }
            } else {
                alert(data.message);
                btn.disabled = false;
            }
        });
    }


    // ==========================================
    // 3. XỬ LÝ MÀN HÌNH KẾT THÚC (ENDED)
    // ==========================================
    function updateEnded(data) {
        // Dừng polling status khi game đã kết thúc
        clearInterval(status_poll_interval);

        const members = data.members;

        // Vẽ 3D Podium
        if (members.length >= 1) {
            fillPodium('podium-1st', members[0], 1);
        }
        if (members.length >= 2) {
            fillPodium('podium-2nd', members[1], 2);
        } else {
            document.getElementById('podium-2nd').style.display = 'none';
        }
        if (members.length >= 3) {
            fillPodium('podium-3rd', members[2], 3);
        } else {
            document.getElementById('podium-3rd').style.display = 'none';
        }

        // Vẽ bảng điểm chi tiết
        const rankList = document.getElementById('ended-rank-list');
        rankList.innerHTML = members.map((player, idx) => {
            let rowBg = '#f8fafc';
            let rankBadge = `<span style="font-weight: 700; color: #64748b; width: 24px; text-align: center;">#${idx+1}</span>`;
            
            if (idx === 0) {
                rowBg = '#fef3c7';
                rankBadge = '🥇';
            } else if (idx === 1) {
                rowBg = '#f1f5f9';
                rankBadge = '🥈';
            } else if (idx === 2) {
                rowBg = '#ffedd5';
                rankBadge = '🥉';
            }

            return `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; background: ${rowBg}; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        ${rankBadge}
                        <strong style="color: #1f2937; font-size: 15px;">${player.name}</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <span style="font-size: 13px; color: #475569;">Số câu đúng: <strong style="color: #10b981;">${player.correct_count} câu</strong></span>
                        <span style="font-weight: 800; color: #4f46e5; font-size: 15px;">${player.score} đ</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function fillPodium(id, player, rank) {
        const container = document.getElementById(id);
        container.style.display = 'flex';

        const nameEl = container.querySelector('.podium-name');
        const scoreEl = container.querySelector('.podium-score');
        const avatarEl = container.querySelector('.podium-avatar');

        nameEl.textContent = player.name;
        scoreEl.textContent = `${player.score} đ`;

        if (player.avatar) {
            avatarEl.innerHTML = `<img src="${player.avatar}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
        } else {
            avatarEl.textContent = player.name.substring(0, 2).toUpperCase();
        }
    }
</script>

<style>
    /* CSS Animations */
    @keyframes bounce {
        0% { transform: translateY(0); }
        100% { transform: translateY(-8px); }
    }
    .pulse-dot {
        animation: pulse-animation 1.5s infinite;
    }
    @keyframes pulse-animation {
        0% { transform: scale(0.9); opacity: 0.6; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.6; }
    }
    /* Dynamic grid scrolling */
    #play-leaderboard-list::-webkit-scrollbar {
        width: 6px;
    }
    #play-leaderboard-list::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    #play-leaderboard-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    #play-leaderboard-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
