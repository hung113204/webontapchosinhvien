<script>
    // ============ Fullscreen Toggle ============
    const fullscreenBtn = document.querySelector('.qv-fullscreen-btn');
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.warn('Fullscreen not supported:', err);
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

    // ============ Tab Views Controller ============
    function showLobbyView() {
        document.getElementById('qv-view-lobby').style.display = 'block';
        document.getElementById('qv-view-create').style.display = 'none';
        document.getElementById('qv-view-join').style.display = 'none';
    }

    function openCreateModal() {
        document.getElementById('qv-view-lobby').style.display = 'none';
        document.getElementById('qv-view-create').style.display = 'block';
        document.getElementById('qv-view-join').style.display = 'none';
    }

    // This handles both clicking the "Tham gia phòng" button and quick joining from the active list
    function openJoinModal() {
        document.getElementById('qv-view-lobby').style.display = 'none';
        document.getElementById('qv-view-create').style.display = 'none';
        document.getElementById('qv-view-join').style.display = 'block';
        setTimeout(() => {
            const input = document.getElementById('ma_phong_input');
            if (input) input.focus();
        }, 150);
    }

    function quickJoinRoom(pin) {
        document.getElementById('ma_phong_input').value = pin;
        openJoinModal();
    }

    // Trigger Join view on validation errors automatically
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', () => {
            openJoinModal();
        });
    @endif

    // ============ Leaderboard Controller ============
    function switchLeaderboard(period, btn) {
        document.querySelectorAll('.qv-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        loadLeaderboard(period);
    }

    function loadLeaderboard(period) {
        const container = document.getElementById('qv-leaderboard-list');
        container.innerHTML = `<div style="padding: 30px; text-align: center; color: #94a3b8;">Đang tải bảng xếp hạng...</div>`;

        fetch(`{{ route('client.phongquiz.leaderboard') }}?period=${period}`)
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data && res.data.length > 0) {
                    container.innerHTML = res.data.map((item, i) => {
                        const rank = i + 1;
                        let rankClass = 'normal';
                        if (rank === 1) rankClass = 'gold';
                        else if (rank === 2) rankClass = 'silver';
                        else if (rank === 3) rankClass = 'bronze';

                        return `
                            <div class="qv-lb-row">
                                <div class="qv-lb-rank ${rankClass}">${rank}</div>
                                <div class="qv-lb-name">${item.name}</div>
                                <div class="qv-lb-score">${item.score.toLocaleString('vi-VN')}</div>
                            </div>
                        `;
                    }).join('');
                } else {
                    container.innerHTML = `<div style="padding: 30px; text-align: center; color: #94a3b8;">Chưa có dữ liệu bảng xếp hạng</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = `<div style="padding: 30px; text-align: center; color: #ef4444;">Lỗi tải dữ liệu bảng xếp hạng</div>`;
            });
    }

    // Initialize leaderboard on load and setup auto room name filling
    document.addEventListener('DOMContentLoaded', () => {
        loadLeaderboard('week');

        // Automatic room name generator logic
        const selectMonHoc = document.getElementById('select_mon_hoc');
        const inputTenPhong = document.getElementById('input_ten_phong');
        let userEditedName = false;

        if (selectMonHoc && inputTenPhong) {
            selectMonHoc.addEventListener('change', function() {
                if (!userEditedName) {
                    const selectedText = selectMonHoc.options[selectMonHoc.selectedIndex].text;
                    if (selectMonHoc.value) {
                        inputTenPhong.value = `Đấu trí ${selectedText} - {{ session('quiz_nickname', optional(auth()->user())->ho_ten ?? 'Khách') }}`;
                    } else {
                        inputTenPhong.value = '';
                    }
                }
            });

            inputTenPhong.addEventListener('input', function() {
                if (inputTenPhong.value.trim() === '') {
                    userEditedName = false;
                } else {
                    userEditedName = true;
                }
            });
        }
    });

    // ============ Ajax: Create Room submit ============
    document.getElementById('create-room-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('create-submit-btn');
        const errorBox = document.getElementById('create-error-box');
        errorBox.style.display = 'none';
        btn.disabled = true;
        btn.innerHTML = '<span>Đang tạo phòng thi đấu...</span>';

        const formData = new FormData(this);
        const data = {};
        formData.forEach((val, key) => { if (val) data[key] = val; });

        fetch("{{ route('client.phongquiz.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                window.location.href = res.redirect;
            } else {
                errorBox.innerHTML = '<span>⚠️</span><div>' + res.message + '</div>';
                errorBox.style.display = 'flex';
                btn.disabled = false;
                btn.innerHTML = '<span>Khởi tạo phòng ngay</span><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
            }
        })
        .catch(err => {
            errorBox.innerHTML = '<span>⚠️</span><div>Môn học này hiện chưa đủ số lượng câu hỏi hợp lệ để khởi tạo phòng đấu.</div>';
            errorBox.style.display = 'flex';
            btn.disabled = false;
            btn.innerHTML = '<span>Khởi tạo phòng ngay</span><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';
    });
    });

    // ============ User dropdown toggle and nickname modal ============
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('qv-user-dropdown');
        const arrow = document.getElementById('qv-user-arrow-icon');
        const isOpen = dropdown.style.display === 'block';
        
        dropdown.style.display = isOpen ? 'none' : 'block';
        if (arrow) {
            arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    function openRenameModal(event) {
        if (event) event.stopPropagation();
        
        // Hide dropdown
        const dropdown = document.getElementById('qv-user-dropdown');
        const arrow = document.getElementById('qv-user-arrow-icon');
        if (dropdown) dropdown.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
        
        document.getElementById('rename-modal').style.display = 'flex';
        document.getElementById('new-nickname-input').focus();
    }

    function closeRenameModal() {
        document.getElementById('rename-modal').style.display = 'none';
    }

    function closeRenameModalOutside(event) {
        if (event.target.id === 'rename-modal') {
            closeRenameModal();
        }
    }

    function submitRename() {
        const input = document.getElementById('new-nickname-input');
        const newName = input.value.trim();
        if (newName === '') {
            alert('Vui lòng nhập nickname hợp lệ!');
            return;
        }

        const confirmBtn = document.querySelector('#rename-modal .btn-confirm');
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Đang lưu...';

        fetch("{{ route('client.phongquiz.nickname') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ho_ten: newName
            })
        })
        .then(r => {
            location.reload();
        })
        .catch(err => {
            console.error('Lỗi khi cập nhật nickname:', err);
            location.reload();
        });
    }

    // Close user dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const wrapper = document.querySelector('.qv-user-wrapper');
        const dropdown = document.getElementById('qv-user-dropdown');
        const arrow = document.getElementById('qv-user-arrow-icon');
        
        if (wrapper && !wrapper.contains(event.target)) {
            if (dropdown) dropdown.style.display = 'none';
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    });
</script>
