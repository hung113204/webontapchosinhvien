<script>
    // ============ Fullscreen Toggle ============
    const fullscreenBtn = document.querySelector('.qv-fullscreen-btn');
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                // Fallback: CSS-based fullscreen
                document.body.classList.toggle('qv-fullscreen-mode');
            });
        } else {
            document.exitFullscreen();
        }
    }
    document.addEventListener('fullscreenchange', function() {
        const isFs = !!document.fullscreenElement;
        if (fullscreenBtn) {
            fullscreenBtn.innerHTML = isFs
                ? `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg> Thoát toàn màn hình`
                : `<svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg> Toàn màn hình`;
        }
    });

    const ma_phong = "{{ $room->ma_phong }}";
    const current_user_id = {{ $member->user_id }};
    const STATUS_URL = "{{ route('client.phongquiz.status', $room->ma_phong) }}";
    const SUBMIT_URL = "{{ route('client.phongquiz.submit', $room->ma_phong) }}";
    const START_ROOM_URL = "{{ route('client.phongquiz.start', $room->ma_phong) }}";
    const NEXT_QUESTION_URL = "{{ route('client.phongquiz.next', $room->ma_phong) }}";
    const is_host = {{ $room->chu_phong_id === $member->user_id ? 'true' : 'false' }};
    
    let current_state = null;
    let room_data = null;
    let active_question_id = null;
    let is_submitted = false;
    let selected_choice_id = null;
    let last_submit_result = null;
    let auto_advance_timeout = null; // Timeout cho auto-advance khi hết giờ
    let ended_countdown_interval = null; // Interval cho auto-redirect khi kết thúc game
    let last_bomb_effect_question_id = null;
    let last_check_effect_question_id = null;
    let local_seconds_left = null;
    let local_timer_interval = null;
 
    // Beautiful shapes and colors for options matching modern kahoot style
    const option_styles = [
        { color: '#e53935', icon: '▲' }, // Ruby Red
        { color: '#1e88e5', icon: '◆' }, // Sapphire Blue
        { color: '#ffb300', icon: '●' }, // Amber Yellow
        { color: '#43a047', icon: '■' }  // Emerald Green
    ];
 
    let is_polling = false;
    let poll_timeout_id = null;
 
    function fetchStatus() {
        if (is_polling) return;
        is_polling = true;
 
        fetch(STATUS_URL)
            .then(res => res.json())
            .then(data => {
                is_polling = false;
                if (data.success) {
                    room_data = data;
                    const state = data.room.trang_thai;
                    const stateChanged = (current_state !== state);
 
                    switchState(state);
 
                    if (state === 1) {
                        updateLobby(data);
                    } else if (state === 2) {
                        updatePlaying(data);
                    } else if (state === 3) {
                        updateEnded(data, stateChanged);
                    }
                }
                
                if (poll_timeout_id) clearTimeout(poll_timeout_id);
                poll_timeout_id = setTimeout(fetchStatus, 1500);
            })
            .catch(err => {
                is_polling = false;
                console.error("Lỗi đồng bộ Realtime học viên: ", err);
                
                if (poll_timeout_id) clearTimeout(poll_timeout_id);
                poll_timeout_id = setTimeout(fetchStatus, 3000);
            });
    }
 
    function forceFetchStatus() {
        is_polling = false;
        if (poll_timeout_id) clearTimeout(poll_timeout_id);
        fetchStatus();
    }
 
    fetchStatus();

    function switchState(state) {
        if (current_state === state) return;
        current_state = state;

        // Reset variables when transitioning back to lobby
        if (state === 1) {
            is_submitted = false;
            selected_choice_id = null;
            active_question_id = null;
            last_submit_result = null;
            is_requesting_next = false;
            
            if (auto_advance_timeout) {
                clearTimeout(auto_advance_timeout);
                auto_advance_timeout = null;
            }
            if (ended_countdown_interval) {
                clearInterval(ended_countdown_interval);
                ended_countdown_interval = null;
            }
            if (local_timer_interval) {
                clearInterval(local_timer_interval);
                local_timer_interval = null;
            }
            local_seconds_left = null;

            // Reset UI state for play buttons / host controls if host
            const hostBtn = document.getElementById('host-start-btn');
            if (hostBtn) {
                hostBtn.disabled = false;
                hostBtn.innerHTML = `
                    <i class="fas fa-play"></i>
                    Chơi ngay
                `;
            }
        }

        document.getElementById('student-lobby').style.display = state === 1 ? 'block' : 'none';
        document.getElementById('student-playing').style.display = state === 2 ? 'flex' : 'none';
        document.getElementById('student-ended').style.display = state === 3 ? 'block' : 'none';
    }

    function updateLobby(data) {
        if (!data || !data.members) return;

        // Update header info
        document.getElementById('lobby-subject').textContent = (data.room?.mon_hoc?.ten_mon_hoc || 'Môn học');
        
        // Update level info
        const levelBadge = document.getElementById('lobby-level');
        if (levelBadge) {
            let mucDoText = 'Tất cả mức độ';
            if (data.room?.muc_do_cau_hoi == 1) mucDoText = 'Mức độ: Nhận biết';
            else if (data.room?.muc_do_cau_hoi == 2) mucDoText = 'Mức độ: Thông hiểu';
            else if (data.room?.muc_do_cau_hoi == 3) mucDoText = 'Mức độ: Vận dụng';
            levelBadge.textContent = mucDoText;
        }
        
        // Update member count badges
        const memberCountBadge = document.getElementById('lobby-member-count');
        if (memberCountBadge) {
            memberCountBadge.textContent = `${data.members.length}/50`;
        }
        
        // Update player count text
        const playerCount = document.getElementById('lobby-player-count');
        if (playerCount) {
            playerCount.textContent = `${data.members.length} người`;
        }

        // Update players list
        const playersList = document.getElementById('lobby-players-list');
        if (!playersList) return;

        playersList.innerHTML = data.members.map((member, index) => {
            const isMe = member.user_id === current_user_id;
            const rankDisplay = index + 1;
            const avatarClass = index === 0 ? '' : 'avatar-normal';
            const avatarIcon = index === 0 ? '👑' : rankDisplay;
            return `
                <div class="qv-lobby-player-item">
                    <div style="display: flex; align-items: center;">
                        <div class="qv-lobby-player-avatar ${avatarClass}">${avatarIcon}</div>
                        <div class="qv-lobby-player-info">
                            <p class="qv-lobby-player-name">${member.name}${isMe ? ' <span style="font-size:13px;color:#64748b;font-weight:600;">(bạn)</span>' : ''}</p>
                            <div class="qv-lobby-player-status">
                                <span class="dot"></span> Online
                            </div>
                        </div>
                    </div>
                    <div class="qv-lobby-player-rank">#${rankDisplay}</div>
                </div>
            `;
        }).join('');

        // Update status
        const statusMsg = document.getElementById('lobby-status-message');
        if (statusMsg) {
            @if($room->chu_phong_id === $member->user_id)
                const globalStatus = document.getElementById('lobby-global-status');
                if (data.all_ready) {
                    if (globalStatus) {
                        globalStatus.style.display = 'block';
                        globalStatus.innerHTML = '<div style="display:flex; justify-content:center; align-items:center;"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#d97706" stroke-width="2.5" style="margin-right: 10px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> <span style="color:#d97706; font-weight:900; font-size: 19px; text-transform: uppercase; letter-spacing: 0.5px;">Tất cả thành viên đã sẵn sàng - Host hãy bấm chơi ngay</span></div>';
                    }
                    statusMsg.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#10b981" stroke-width="2.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> <span style="color:#10b981; font-weight:700;">Mọi người đã sẵn sàng!</span>';
                } else {
                    if (globalStatus) globalStatus.style.display = 'none';
                    statusMsg.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> Bạn sẵn sàng. Đang chờ người chơi...';
                }
            @else
                if (data.members.find(m => m.user_id === current_user_id)?.is_ready) {
                    statusMsg.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#10b981" stroke-width="2"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> <span style="color:#10b981;">Đã sẵn sàng. Đang đợi Host bắt đầu...</span>';
                } else {
                    statusMsg.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" style="animation:spin 2s linear infinite;"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2"/><path d="M12 2v6"/></svg> Đang chờ Host bắt đầu...';
                }
            @endif
        }
    }

    function updatePlaying(data) {
        const q = data.current_question;
        if (!q) return;

        const secondsLeft = data.seconds_remaining;
        
        // Synchronize local timer with server if not initialized or drift is > 1s
        if (local_seconds_left === null || Math.abs(local_seconds_left - secondsLeft) > 1) {
            local_seconds_left = secondsLeft;
            document.getElementById('playing-timer').textContent = local_seconds_left;
        }

        // Start smooth local countdown interval
        if (!local_timer_interval && local_seconds_left > 0) {
            local_timer_interval = setInterval(() => {
                if (local_seconds_left > 0) {
                    local_seconds_left--;
                    document.getElementById('playing-timer').textContent = local_seconds_left;
                    if (local_seconds_left === 0) {
                        clearInterval(local_timer_interval);
                        local_timer_interval = null;
                    }
                }
            }, 1000);
        }

        // Update my score pill
        const player = data.members.find(m => m.user_id === current_user_id);
        if (player) {
            document.getElementById('my-score-display').textContent = player.score.toLocaleString('vi-VN');
            document.getElementById('my-correct-display').textContent = `${player.correct_count} câu đúng`;
        }

        // Update live leaderboard
        updateRoomLeaderboard(data.members);

        // Load new question state
        if (active_question_id !== q.id) {
            // Clear auto-advance timeout từ câu hỏi trước
            if (auto_advance_timeout) {
                clearTimeout(auto_advance_timeout);
                auto_advance_timeout = null;
            }
            if (local_timer_interval) {
                clearInterval(local_timer_interval);
                local_timer_interval = null;
            }
            local_seconds_left = null;

            active_question_id = q.id;
            is_submitted = false;
            last_submit_result = null;
            selected_choice_id = null;

            const confirmBtn = document.getElementById('qv-confirm-btn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Xác nhận';
                confirmBtn.style.display = 'inline-block';
            }

            const grid = document.getElementById('playing-options-grid');
            if (grid) {
                grid.classList.remove('has-selection');
                grid.classList.remove('revealed');
            }

            document.getElementById('playing-grade-result').style.display = 'none';
            triggerBombIcon(false);
            triggerCheckIcon(false);
            document.getElementById('playing-submitted-lobby').style.display = 'none';
            document.getElementById('playing-workspace').style.display = 'block';

            document.getElementById('playing-q-number').textContent = `Câu hỏi ${q.number} / ${data.room.tong_so_cau}`;
            document.getElementById('playing-q-text').innerHTML = q.noi_dung;

            // Handle optional visual image attachment
            const imgCont = document.getElementById('playing-q-image-container');
            const imgTag = document.getElementById('playing-q-image');
            if (q.hinh_anh) {
                imgTag.src = q.hinh_anh;
                imgCont.style.display = 'block';
            } else {
                imgCont.style.display = 'none';
            }

            // Draw answer buttons
            if (grid) {
                grid.innerHTML = q.choices.map((choice, i) => {
                    const s = option_styles[i % 4];
                    return `
                        <button class="qv-answer-btn" data-choice-id="${choice.id}" onclick="selectAnswer(this, ${choice.id})">
                            <span class="qv-shape-icon">${choice.ky_hieu}</span>
                            <span>${choice.noi_dung}</span>
                        </button>
                    `;
                }).join('');
            }
            
            // Re-render MathJax
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise().catch(err => console.log('MathJax error:', err));
            }
        }

        // Check if current user has already answered on another tab or device
        const isUserAnswered = data.answered_users.includes(current_user_id);
        if (isUserAnswered && !is_submitted) {
            is_submitted = true;
            // Disable buttons if answered from another tab
            document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            });
            const confirmBtn = document.getElementById('qv-confirm-btn');
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.textContent = 'Đã trả lời';
                confirmBtn.style.display = 'none';
            }
        }

        // Update host skip/next controls dynamically
        const nextBtn = document.getElementById('host-playing-next-btn');
        if (nextBtn) {
            nextBtn.disabled = false;
            const isLast = q.number === data.room.tong_so_cau;
            if (isLast) {
                nextBtn.innerHTML = 'Kết thúc & Xem kết quả 🏆';
                nextBtn.style.background = 'linear-gradient(135deg, #8b5cf6, #7c3aed)';
            } else {
                if (secondsLeft === 0 || data.all_answered) {
                    nextBtn.innerHTML = 'Câu tiếp theo ➜';
                    nextBtn.style.background = 'linear-gradient(135deg, #2563eb, #1d4ed8)';
                } else {
                    nextBtn.innerHTML = 'Bỏ qua & Sang câu tiếp theo ➜';
                    nextBtn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
                }
            }
        }

        // When timer hits 0 OR everyone has answered: show full grade result below workspace
        if (secondsLeft === 0 || data.all_answered) {
            if (local_timer_interval) {
                clearInterval(local_timer_interval);
                local_timer_interval = null;
            }
            local_seconds_left = 0;
            document.getElementById('playing-timer').textContent = 0;

            document.getElementById('playing-submitted-lobby').style.display = 'none';
            showGradeResult(data);

            // Auto-advance: Sau 2 giây, tự động sang câu tiếp theo nếu bạn là host
            @if($room->chu_phong_id === $member->user_id)
                if (!auto_advance_timeout) {
                    auto_advance_timeout = setTimeout(() => {
                        auto_advance_timeout = null;
                        const isLast = q.number === data.room.tong_so_cau;
                        if (!isLast) {
                            nextQuestionByHost();
                        }
                    }, 2000); // 2 giây delay để xem kết quả
                }
            @endif
        }
    }

    function updateRoomLeaderboard(members) {
        const tbody = document.getElementById('room-leaderboard-list');
        if (!tbody || !members || members.length === 0) return;

        tbody.innerHTML = members.map((m, i) => {
            const rank = i + 1;
            const isMe = m.user_id === current_user_id;
            const rankDisplay = rank <= 3 ? ['🥇','🥈','🥉'][rank-1] : rank;
            // Approximate time score (higher score = faster answer time approximation)
            const timeDisplay = m.score > 0 ? Math.round(m.score / (m.correct_count || 1)) : 0;
            return `
                <tr class="${isMe ? 'is-me' : ''}">
                    <td class="qv-lb-td-rank">${rankDisplay}</td>
                    <td class="qv-lb-td-name ${isMe ? 'is-me' : ''}">${m.name}${isMe ? ' <span style="color:#2563eb;font-size:11px;">(bạn)</span>' : ''}</td>
                    <td class="qv-lb-td-right">${m.correct_count}</td>
                    <td class="qv-lb-td-time">${timeDisplay}</td>
                    <td class="qv-lb-td-score">${m.score.toLocaleString('vi-VN')}</td>
                </tr>
            `;
        }).join('');
    }

    function selectAnswer(btnElement, choiceId) {
        if (is_submitted) return;
        selected_choice_id = choiceId;

        // Remove selected class from all buttons
        document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
            btn.classList.remove('selected');
        });

        // Add selected class to current button
        btnElement.classList.add('selected');

        // Add has-selection class to grid
        const grid = document.getElementById('playing-options-grid');
        if (grid) {
            grid.classList.add('has-selection');
        }

        // Enable Confirm button
        const confirmBtn = document.getElementById('qv-confirm-btn');
        if (confirmBtn) {
            confirmBtn.disabled = false;
        }
    }

    function submitSelectedAnswer() {
        if (selected_choice_id !== null) {
            submitAnswer(selected_choice_id);
        }
    }

    function submitAnswer(dap_an_id) {
        if (is_submitted) return;
        is_submitted = true;

        // Disable all answer buttons (keep workspace visible)
        document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.6';
            btn.style.cursor = 'not-allowed';
            btn.style.transform = 'none';
        });

        // Disable Confirm button
        const confirmBtn = document.getElementById('qv-confirm-btn');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Đã xác nhận';
            confirmBtn.style.display = 'none';
        }

        fetch(SUBMIT_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phong_quiz_id: room_data.room.id,
                cau_hoi_id: active_question_id,
                dap_an_id: dap_an_id
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                is_submitted = false;
                // Re-enable buttons on failure
                document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '';
                    btn.style.cursor = '';
                    btn.style.transform = '';
                });
                const confirmBtn = document.getElementById('qv-confirm-btn');
                if (confirmBtn) {
                    confirmBtn.disabled = false;
                    confirmBtn.textContent = 'Xác nhận';
                    confirmBtn.style.display = 'inline-block';
                }
            } else {
                last_submit_result = data;
                // Show inline grade result immediately after submitting
                showInlineGradeResult(data);
                
                // Force status sync immediately to show updated user scores/stats
                forceFetchStatus();
            }
        })
        .catch(err => {
            console.error("Nộp bài thất bại: ", err);
            is_submitted = false;
            document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '';
                btn.style.cursor = '';
                btn.style.transform = '';
            });
            const confirmBtn = document.getElementById('qv-confirm-btn');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Xác nhận';
                confirmBtn.style.display = 'inline-block';
            }
        });
    }

    function triggerBombIcon(show) {
        const iconWrapper = document.getElementById('grade-icon-wrapper');
        if (!iconWrapper) return;
        
        if (window.bombTimeoutId) {
            clearTimeout(window.bombTimeoutId);
            window.bombTimeoutId = null;
        }
        
        if (show) {
            if (last_bomb_effect_question_id === active_question_id) {
                return;
            }
            last_bomb_effect_question_id = active_question_id;

            // First hide it immediately (in case it was showing)
            iconWrapper.classList.remove('show', 'is-bomb', 'is-check', 'is-exploding', 'is-checking');
            void iconWrapper.offsetWidth; // trigger reflow
            
            // Add class 'show' to trigger entry transition
            iconWrapper.classList.add('show', 'is-bomb', 'is-exploding');
            
            // After 1 second, remove class 'show' to trigger exit transition
            window.bombTimeoutId = setTimeout(() => {
                iconWrapper.classList.remove('show', 'is-bomb', 'is-exploding');
            }, 1100);
        } else {
            iconWrapper.classList.remove('show', 'is-bomb', 'is-exploding');
        }
    }

    function triggerCheckIcon(show) {
        const iconWrapper = document.getElementById('grade-icon-wrapper');
        if (!iconWrapper) return;

        if (window.checkTimeoutId) {
            clearTimeout(window.checkTimeoutId);
            window.checkTimeoutId = null;
        }

        if (show) {
            if (last_check_effect_question_id === active_question_id) {
                return;
            }
            last_check_effect_question_id = active_question_id;

            iconWrapper.classList.remove('show', 'is-bomb', 'is-check', 'is-exploding', 'is-checking');
            void iconWrapper.offsetWidth;
            iconWrapper.classList.add('show', 'is-check', 'is-checking');

            window.checkTimeoutId = setTimeout(() => {
                iconWrapper.classList.remove('show', 'is-check', 'is-checking');
            }, 1100);
        } else {
            iconWrapper.classList.remove('show', 'is-check', 'is-checking');
        }
    }

    function highlightAnswerButtons(correctChoiceId, userAnswerId) {
        const grid = document.getElementById('playing-options-grid');
        if (!grid) return;

        grid.classList.add('revealed');

        // Reset previous reveal styles if any
        document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
            btn.classList.remove('is-correct-reveal', 'is-wrong-reveal');
        });

        // Loop through children buttons and add correct/wrong classes
        const buttons = grid.querySelectorAll('.qv-answer-btn');
        buttons.forEach(btn => {
            const choiceIdAttr = btn.getAttribute('data-choice-id');
            const choiceId = choiceIdAttr ? parseInt(choiceIdAttr) : null;

            if (choiceId) {
                if (choiceId === correctChoiceId) {
                    btn.classList.add('is-correct-reveal');
                } else if (choiceId === userAnswerId) {
                    btn.classList.add('is-wrong-reveal');
                }
            }
        });
    }

    function showInlineGradeResult(submitData) {
        const gradeCard = document.getElementById('playing-grade-result');
        const title = document.getElementById('grade-title');
        const points = document.getElementById('grade-points');
        const explainBox = document.getElementById('grade-explain-box');
        const explainText = document.getElementById('grade-explain-text');
        const continueBtn = document.getElementById('grade-continue-btn');

        gradeCard.className = 'qv-grade-card';

        if (submitData && submitData.is_correct) {
            gradeCard.classList.add('correct');
            title.textContent = 'Chúc mừng! Bạn trả lời đúng.';
            points.textContent = `Bạn đã gửi đáp án. Bấm tiếp tục để sang câu mới ngay khi mọi người trong phòng đều sẵn sàng.`;
            triggerBombIcon(false);
            triggerCheckIcon(true);
        } else {
            gradeCard.classList.add('wrong');
            const correctStr = (submitData && submitData.correct_choice)
                ? ` (Đáp án đúng: ${submitData.correct_choice.ky_hieu} - ${submitData.correct_choice.noi_dung})`
                : '';
            title.textContent = 'Tiếc quá! Bạn trả lời sai.';
            points.textContent = `Bạn đã gửi đáp án. Bấm tiếp tục để sang câu mới ngay khi mọi người trong phòng đều sẵn sàng.${correctStr}`;
            triggerCheckIcon(false);
            triggerBombIcon(true);
        }

        if (submitData && submitData.giai_thich) {
            explainText.innerHTML = submitData.giai_thich;
            explainBox.style.display = 'block';
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise([explainText]).catch(err => console.log('MathJax error:', err));
            }
        } else {
            explainBox.style.display = 'none';
        }

        const correctChoiceId = (submitData && submitData.correct_choice) ? submitData.correct_choice.id : null;
        const userAnswerId = selected_choice_id;
        highlightAnswerButtons(correctChoiceId, userAnswerId);

        continueBtn.disabled = false;
        gradeCard.style.display = 'block';
    }

    function gradeCardContinue() {
        // Host: advance question. Non-host: just show waiting state
        const btn = document.getElementById('grade-continue-btn');
        if (btn) btn.disabled = true;
        @if($room->chu_phong_id === $member->user_id)
            nextQuestionByHost();
        @endif
    }

    let active_grade_id = null;
    function showGradeResult(data) {
        const q = data.current_question;
        if (!q) return;

        if (active_grade_id === q.id) return;
        active_grade_id = q.id;

        const correctChoice = q.choices.find(c => c.is_dung);
        const correctChar = correctChoice ? correctChoice.ky_hieu : '';
        const correctText = correctChoice ? correctChoice.noi_dung : '';
        const correctChoiceId = correctChoice ? correctChoice.id : null;

        const gradeCard = document.getElementById('playing-grade-result');
        const title = document.getElementById('grade-title');
        const points = document.getElementById('grade-points');
        const explainBox = document.getElementById('grade-explain-box');
        const explainText = document.getElementById('grade-explain-text');
        const continueBtn = document.getElementById('grade-continue-btn');

        // Disable all answer buttons
        document.querySelectorAll('#playing-options-grid .qv-answer-btn').forEach(btn => {
            btn.disabled = true;
            btn.style.cursor = 'not-allowed';
            btn.style.opacity = '0.55';
            btn.style.transform = 'none';
        });

        const confirmBtn = document.getElementById('qv-confirm-btn');
        if (confirmBtn) {
            confirmBtn.style.display = 'none';
        }

        gradeCard.className = 'qv-grade-card';
        // Nếu user vừa submit xong (có last_submit_result) hoặc vừa refresh/đồng bộ mà đã nộp bài (có my_answer)
        if (last_submit_result || data.my_answer) {
            const isCorrect = last_submit_result ? last_submit_result.is_correct : data.my_answer_correct;
            if (isCorrect) {
                gradeCard.classList.add('correct');
                title.textContent = 'Chúc mừng! Bạn trả lời đúng.';
                points.textContent = `Bạn đã gửi đáp án. Bấm tiếp tục để sang câu mới ngay khi mọi người trong phòng đều sẵn sàng.`;
                triggerBombIcon(false);
                triggerCheckIcon(true);
            } else {
                gradeCard.classList.add('wrong');
                title.textContent = 'Tiếc quá! Bạn trả lời sai.';
                points.textContent = `Bạn đã gửi đáp án. Bấm tiếp tục để sang câu mới ngay khi mọi người trong phòng đều sẵn sàng. (Đáp án đúng: ${correctChar} - ${correctText})`;
                triggerCheckIcon(false);
                triggerBombIcon(true);
            }
        } else {
            gradeCard.classList.add('timeout');
            title.textContent = 'Hết giờ suy nghĩ! ⏱️';
            points.textContent = `Bạn đã không đưa ra đáp án. Đáp án đúng là: ${correctChar} - ${correctText}`;
            triggerCheckIcon(false);
            triggerBombIcon(true);
        }

        // Show explanation if available
        if (q.giai_thich) {
            explainText.innerHTML = q.giai_thich;
            explainBox.style.display = 'block';
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.typesetPromise([explainText]).catch(err => console.log('MathJax error:', err));
            }
        } else {
            explainBox.style.display = 'none';
        }

        const userAnswerId = selected_choice_id || data.my_answer;
        highlightAnswerButtons(correctChoiceId, userAnswerId);

        continueBtn.disabled = false;
        gradeCard.style.display = 'block';
    }

    function updateEnded(data, stateChanged) {
        if (!stateChanged) return;
        
        // Clear auto-advance timeout khi phòng kết thúc
        if (auto_advance_timeout) {
            clearTimeout(auto_advance_timeout);
            auto_advance_timeout = null;
        }

        const endedLbList = document.getElementById('ended-leaderboard-list');
        if (endedLbList && data.members) {
            endedLbList.innerHTML = data.members.map((m, i) => {
                const rank = i + 1;
                const isMe = m.user_id === current_user_id;
                let rankClass = 'normal';
                if (rank === 1) rankClass = 'gold';
                else if (rank === 2) rankClass = 'silver';
                else if (rank === 3) rankClass = 'bronze';
                
                return `
                    <div class="qv-ended-lb-row ${rank === 1 ? 'first-place' : ''} ${isMe ? 'is-me' : ''}">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div class="qv-ended-rank-badge ${rankClass}">${rank}</div>
                            <div style="text-align: left;">
                                <div class="qv-ended-player-name">${m.name}${isMe ? ' <span style="font-size:13px;color:#64748b;font-weight:600;">(bạn)</span>' : ''}</div>
                                <div class="qv-ended-player-sub">Đúng ${m.correct_count} • Time ${Math.round(m.score / (m.correct_count || 1))} • Câu ${data.room.tong_so_cau}</div>
                            </div>
                        </div>
                        <div class="qv-ended-player-score">${m.score.toLocaleString('vi-VN')}</div>
                    </div>
                `;
            }).join('');
        }

        switchState(3);

        // Tự động quay về phòng chờ sau 30 giây
        let countdownSeconds = 30;
        const countdownEl = document.getElementById('ended-countdown-seconds');
        if (countdownEl) {
            countdownEl.textContent = countdownSeconds;
        }

        if (ended_countdown_interval) {
            clearInterval(ended_countdown_interval);
        }

        ended_countdown_interval = setInterval(() => {
            countdownSeconds--;
            if (countdownEl) {
                countdownEl.textContent = countdownSeconds;
            }
            if (countdownSeconds <= 0) {
                clearInterval(ended_countdown_interval);
                goBackToLobby(null);
            }
        }, 1000);
    }

    function goBackToLobby(event) {
        if (event) event.preventDefault();
        if (ended_countdown_interval) {
            clearInterval(ended_countdown_interval);
            ended_countdown_interval = null;
        }

        if (is_host) {
            resetRoomByHost();
        } else {
            // Player: Switch local state to waiting lobby
            switchState(1);
        }
    }

    function resetRoomByHost() {
        const resetUrl = "{{ route('client.phongquiz.reset', $room->ma_phong) }}";
        fetch(resetUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Host switches state back to lobby immediately, others will follow via polling
                switchState(1);
            } else {
                alert("Không thể thiết lập lại phòng: " + data.message);
            }
        })
        .catch(err => console.error("Lỗi reset phòng: ", err));
    }

    function togglePlayerReady() {
        const btn = document.getElementById('player-ready-btn');
        if (btn) {
            btn.disabled = true;
            fetch("{{ route('client.phongquiz.ready', $room->ma_phong) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            }).then(res => res.json()).then(data => {
                if(data.success) {
                    btn.innerHTML = `
                        <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Đã sẵn sàng
                    `;
                    btn.style.background = '#10b981'; // green
                    fetchStatus(); // Immediately update lobby status
                }
            }).catch(err => {
                console.error(err);
                btn.disabled = false; // Re-enable if error
            });
        }
    }

    // ============ Student Host Game Control triggers ============
    function startRoomByHost() {
        const btn = document.getElementById('host-start-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = '⏳ Đang khởi chạy...';
        }

        fetch(START_ROOM_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                forceFetchStatus();
            } else {
                alert(data.message);
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = '🚀 Bắt đầu thi đấu ngay';
                }
            }
        })
        .catch(err => {
            console.error("Lỗi khởi chạy phòng: ", err);
            if (btn) {
                btn.disabled = false;
                btn.textContent = '🚀 Bắt đầu thi đấu ngay';
            }
        });
    }

    let is_requesting_next = false;
    function nextQuestionByHost() {
        if (is_requesting_next) return;
        is_requesting_next = true;

        if (auto_advance_timeout) {
            clearTimeout(auto_advance_timeout);
            auto_advance_timeout = null;
        }
        const btn = document.getElementById('host-playing-next-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = '⏳ Đang chuyển tiếp...';
        }

        fetch(NEXT_QUESTION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            is_requesting_next = false;
            if (data.success) {
                forceFetchStatus();
            } else {
                alert(data.message);
                if (btn) btn.disabled = false;
            }
        })
        .catch(err => {
            is_requesting_next = false;
            console.error("Lỗi chuyển câu hỏi: ", err);
            if (btn) btn.disabled = false;
        });
    }

    function leaveRoom(event) {
        if (event) event.preventDefault();
        
        // Clear auto-advance timeout khi rời phòng
        if (auto_advance_timeout) {
            clearTimeout(auto_advance_timeout);
            auto_advance_timeout = null;
        }
        if (ended_countdown_interval) {
            clearInterval(ended_countdown_interval);
            ended_countdown_interval = null;
        }
        
        const data = new FormData();
        data.append('_token', "{{ csrf_token() }}");
        data.append('explicit', '1');
        
        navigator.sendBeacon("{{ route('client.phongquiz.leave', $room->ma_phong) }}", data);
        
        window.location.href = "{{ route('client.phongquiz.join') }}";
    }

    window.addEventListener('pagehide', function () {
        const data = new FormData();
        data.append('_token', "{{ csrf_token() }}");
        navigator.sendBeacon("{{ route('client.phongquiz.leave', $room->ma_phong) }}", data);
    });

    // User Profile JS
    function toggleUserDropdown(e) {
        e.stopPropagation();
        const dropdown = document.getElementById('qv-user-dropdown');
        const arrow = document.getElementById('qv-user-arrow-icon');
        if(dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            dropdown.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
        }
    }
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('qv-user-dropdown');
        const wrapper = document.querySelector('.qv-user-wrapper');
        if (dropdown && dropdown.style.display === 'block' && wrapper && !wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
            document.getElementById('qv-user-arrow-icon').style.transform = 'rotate(0deg)';
        }
    });
    function openRenameModal(e) {
        e.stopPropagation();
        document.getElementById('qv-user-dropdown').style.display = 'none';
        document.getElementById('qv-user-arrow-icon').style.transform = 'rotate(0deg)';
        document.getElementById('qv-rename-modal').style.display = 'flex';
    }
    function closeRenameModal() {
        document.getElementById('qv-rename-modal').style.display = 'none';
    }
    function submitRename() {
        const newName = document.getElementById('qv-nickname-input').value.trim();
        if(!newName) return;
        
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
        
        fetch("{{ route('client.phongquiz.nickname') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({ ho_ten: newName })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) location.reload();
        })
        .catch(err => console.error(err));
    }
</script>
