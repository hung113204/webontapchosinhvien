<div id="student-playing" class="qv-room-layout">

    {{-- Left: Question + Controls --}}
    <div class="qv-playing-section">

        {{-- My Score Pill (Hidden but kept for JS functionality) --}}
        <div style="display: none;">
            <div style="flex:1;">
                <div class="label">Điểm của bạn</div>
                <div class="value" id="my-score-display">0</div>
                <div class="correct" id="my-correct-display">0 câu đúng</div>
            </div>
            <div style="font-size:36px;">⚡</div>
        </div>

        {{-- Playing Header info --}}
        <div class="qv-playing-header">
            <div>
                <div id="playing-q-number" class="qv-playing-q-number">Câu hỏi -- / --</div>
                <div class="qv-playing-room-code">Phòng thi đấu #{{ $room->ma_phong }}</div>
            </div>
            <div class="qv-timer-badge">
                <span id="playing-timer" class="number">--</span>
                <span class="unit">giây</span>
            </div>
        </div>

        {{-- Workspace containing Question and Choices --}}
        <div id="playing-workspace" class="qv-question-card">
            <h3 id="playing-q-text" class="qv-question-text">Đang tải nội dung câu hỏi...</h3>
            
            <div id="playing-q-image-container" class="qv-question-image-box">
                <img id="playing-q-image" src="" alt="Câu hỏi trực quan">
            </div>

            <div id="playing-options-grid" class="qv-answers-grid">
                <!-- Option buttons dynamically rendered here -->
            </div>

            <div class="qv-confirm-wrapper" style="margin-top: 28px; display: flex; justify-content: center; width: 100%;">
                <button id="qv-confirm-btn" class="qv-confirm-btn" onclick="submitSelectedAnswer()" disabled>
                    Xác nhận
                </button>
            </div>
        </div>

        {{-- Submitted State view --}}
        <div id="playing-submitted-lobby" class="qv-submitted-card" style="display: none;">
            <div class="qv-spinner"></div>
            <h3>Ghi nhận câu trả lời thành công!</h3>
            <p>Hãy giữ bình tĩnh, hệ thống đang đợi tất cả đối thủ khác hoàn thành câu hỏi...</p>
        </div>

        {{-- Instant result card (shown inline below workspace) --}}
        <div id="playing-grade-result" class="qv-grade-card" style="display: none;">
            <div id="grade-icon-wrapper" class="grade-icon-wrapper">
                <i class="fa-solid fa-bomb text-red-500 text-8xl bomb-icon"></i>
                <span class="check">✔</span>
            </div>
            <h2 class="qv-grade-title" id="grade-title">CHÍNH XÁC!</h2>
            <div class="qv-grade-pts" id="grade-points">Bạn đã gửi đáp án.</div>
            <div class="qv-grade-explain-box" id="grade-explain-box" style="display:none;">
                <div class="ex-label">Giải thích</div>
                <div class="ex-text" id="grade-explain-text"></div>
            </div>
            <div id="grade-total-score" style="display:none;"></div>
            <div>
                <button class="qv-grade-continue-btn" id="grade-continue-btn" onclick="gradeCardContinue()" disabled>
                    Tiếp tục
                </button>
            </div>
            <div class="qv-grade-tip" id="grade-tip">Tip: Bấm tiếp tục càng sớm để chốt nhịp chơi và tối ưu điểm số, nhưng nhớ ưu tiên trả lời thật chính xác nha :D</div>
        </div>

        {{-- Host Controls Row (hidden - merged into grade card's Tiếp tục button) --}}
        @if($room->chu_phong_id === $member->user_id)
            <div id="host-playing-controls" style="display: none;">
                <button onclick="nextQuestionByHost()" id="host-playing-next-btn"></button>
            </div>
        @endif

    </div>{{-- end left --}}

    {{-- Live Leaderboard as Table --}}
    <div class="qv-leaderboard-panel">
        <div class="qv-lb-panel-header">
            <h4>Bảng xếp hạng</h4>
        </div>
        <div style="overflow-x:auto; max-height:300px; overflow-y:auto;">
            <table class="qv-lb-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Tên</th>
                        <th class="col-right">Đúng</th>
                        <th class="col-time">Time</th>
                        <th class="col-score">Điểm</th>
                    </tr>
                </thead>
                <tbody id="room-leaderboard-list">
                    <tr><td colspan="5" style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">Đang tải...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="qv-lb-footer">Phòng: <strong>{{ $room->ma_phong }}</strong></div>
    </div>

</div>
