@extends('Client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('./frontend/asset/css/ai-chat.css') }}">
@endpush

@section('content')
    @php
        $currentUser = Auth::user();
        $isLoggedIn = (bool) $currentUser;
        $displayName = $isLoggedIn ? ($currentUser->ho_ten ?: $currentUser->email) : 'Khách';
        $avatarSrc = null;

        if ($isLoggedIn && !empty($currentUser->avatar_url)) {
            $avatarSrc = str_starts_with($currentUser->avatar_url, 'http')
                ? $currentUser->avatar_url
                : Storage::url($currentUser->avatar_url);
        }

        $userInitial = mb_strtoupper(mb_substr($displayName, 0, 1));
    @endphp

    <div class="container py-4">
        <div class="ai-chat-wrapper">
            <div class="ai-sidebar">
                <div class="ai-sidebar-header">
                    <h6>
                        <img src="{{ asset('./frontend/asset/images/t2.png') }}"
                            style="width:60px;height:60px;object-fit:contain;border-radius:6px;">

                        <div class="text">
                            <div class="title">Trợ lý AI</div>
                            <div class="sub">Hỗ Trợ Học Tập</div>
                        </div>
                    </h6>
                    <a href="#" class="btn-new-chat" id="btn-new-chat">
                        <i class="fas fa-plus"></i> Cuộc trò chuyện mới
                    </a>
                </div>

                <div class="subject-selector">
                    <div class="chat-search-box">
                        <i class="fas fa-search"></i>
                        <input type="search" id="chat-history-search" placeholder="Tìm kiếm phiên chat...">
                    </div>
                </div>

                <div class="ai-history-list" id="history-list">
                    @if ($isLoggedIn)
                        @forelse($history as $item)
                            <div class="history-item" data-id="{{ $item->id }}">
                                <div class="hi-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
                                    </svg>
                                </div>
                                <div class="history-content">
                                    <span class="fw-bold">{{ $item->cau_hoi }}</span>
                                    <small>{{ $item->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="chat-actions">
                                    <button class="btn-chat-menu" type="button" aria-label="Tùy chọn cuộc trò chuyện">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="chat-menu">
                                        <button class="btn-rename-chat" type="button">
                                            <i class="fas fa-pen"></i> Đổi tên
                                        </button>
                                        <button class="btn-delete-chat" type="button" onclick='deleteChat(event, @json($item->id))'>
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="history-empty">Chưa có lịch sử</div>
                        @endforelse
                    @else
                        <div class="history-empty">
                            Bạn đang dùng với tư cách khách.<br>
                            Tin nhắn vẫn gửi được nhưng sẽ không lưu lịch sử sau khi rời trang.
                        </div>
                    @endif
                </div>

                <div class="sidebar-user-footer">
                    <div class="user-active-card">
                        <div class="user-avatar-wrap">
                            @if ($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="Avatar"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="user-avatar-placeholder"
                                @if ($avatarSrc) style="display:none;" @endif>
                                {{ $userInitial }}
                            </div>
                            <span class="status-indicator"></span>
                        </div>
                        <div class="user-active-info">
                            <span class="user-name">{{ $displayName }}</span>
                            <span class="user-status-text">
                                <i class="fas fa-circle me-1"></i>
                                {{ $isLoggedIn ? 'Đang học trực tuyến' : 'Phiên khách tạm thời' }}
                            </span>
                        </div>
                    </div>

                    <div class="sidebar-copyright">
                        <p>© 2026 THƯ VIỆN PHÁP LUẬT</p>
                        <div class="footer-links">
                            <a href="#">Điều khoản</a>
                            <a href="#">Bảo mật</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ai-main-chat">
                <div class="chat-header">
                    <div class="chat-header-left">
                        <div class="ai-avatar">
                            <img src="{{ asset('./frontend/asset/images/t2.png') }}" alt="AI Avatar"
                                style="width:100%;height:100%;object-fit:contain;border-radius:inherit;">
                        </div>
                        <div class="ai-info">
                            <span class="ai-name">Trợ lý ảo IT Support</span>
                            <span class="ai-subtitle">Đang hoạt động</span>
                        </div>
                    </div>
                    <div class="streak-label">
                        @if ($isLoggedIn)
                            🔥 <span class="fw-bold">{{ $currentUser->current_streak ?? 0 }} ngày</span> học liên tiếp
                        @else
                            Khách: không lưu streak
                        @endif
                    </div>
                </div>

                <div class="chat-body" id="chat-box">
                    <div class="message ai">
                        <div class="msg-avatar"><img src="{{ asset('./frontend/asset/images/t2.png') }}" alt="AI"
                                style="width:100%;height:100%;object-fit:contain;border-radius:inherit;"></div>
                        <div>
                            <div class="msg-bubble">
                                Chào <strong>{{ $displayName }}</strong>! 👋<br>
                                Mình là trợ lý AI học tập IT, có thể giúp bạn <strong>giải thích mã nguồn</strong>,
                                <strong>giải đáp kiến thức</strong>, hoặc <strong>luyện tập thuật toán</strong>.<br><br>
                                Hãy chọn môn học bên trái hoặc đặt câu hỏi bất kỳ nhé!
                                @unless ($isLoggedIn)
                                    <br><br><em>Mẹo: đăng nhập để lưu lịch sử hỏi đáp.</em>
                                @endunless
                            </div>
                            <span class="msg-time">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>

                    <div class="suggestions-wrap">
                        <div class="suggestions-label">
                            <i class="fas fa-lightbulb"></i> Bạn có thể hỏi ngay:
                        </div>
                        <div class="suggestions-chips">
                            <span class="chip-suggest" data-q="Stack là gì? Cho ví dụ thực tế"><i
                                    class="fas fa-layer-group"></i> Stack là gì?</span>
                            <span class="chip-suggest" data-q="Queue khác Stack ở điểm nào?"><i class="fas fa-stream"></i>
                                Queue vs Stack</span>
                            <span class="chip-suggest" data-q="Giải thích thuật toán BFS và DFS"><i
                                    class="fas fa-sitemap"></i> BFS vs DFS</span>
                            <span class="chip-suggest" data-q="Big O notation là gì?"><i class="fas fa-chart-line"></i> Big
                                O Notation</span>
                            <span class="chip-suggest" data-q="Explain this code: "><i class="fas fa-code"></i> Giải thích
                                code</span>
                        </div>
                    </div>
                </div>

                <div class="chat-footer">
                    <form id="chat-form" autocomplete="off">
                        <div class="input-container">
                            <i class="fas fa-paperclip input-icon" title="Đính kèm file"></i>
                            <textarea id="user-input" placeholder="Nhập câu hỏi của bạn..." rows="1"></textarea>
                            <button type="submit" class="btn-send" title="Gửi">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                    <div class="input-footer-note">
                        <i class="fas fa-shield-alt"></i>
                        AI có thể mắc lỗi, hãy kiểm tra lại với tài liệu chính thức
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.currentChatUser = {
            avatar: @json($avatarSrc),
            initial: @json($userInitial),
            isLoggedIn: @json($isLoggedIn)
        };

        window.escapeHtml = function(text) {
            return String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        };

        window.formatAnswerText = function(text) {
            let safe = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            safe = safe.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) => {
                return `<pre><code class="lang-${lang}">${code.trim()}</code></pre>`;
            });
            safe = safe.replace(/`([^`]+)`/g, '<code>$1</code>');
        safe = safe.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        safe = safe.replace(/\n/g, '<br>');
        return safe;
    };

    window.scrollChatToBottom = function() {
        const chatBox = document.getElementById('chat-box');
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 50);
    };

    window.formatChatTime = function(value = new Date()) {
        const date = value instanceof Date ? value : new Date(value);

        if (Number.isNaN(date.getTime())) {
            return '';
        }

        return date.toLocaleTimeString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    window.appendMessageToChat = function(role, text, sentAt = new Date()) {
        const chatBox = document.getElementById('chat-box');
        const wrap = document.createElement('div');
        wrap.className = 'message ' + role;

        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.innerHTML = window.formatAnswerText(text);

        const time = document.createElement('span');
        time.className = 'msg-time';
        time.textContent = window.formatChatTime(sentAt);

        const avatarEl = document.createElement('div');
        avatarEl.className = 'msg-avatar';
        if (role === 'ai') {
            avatarEl.innerHTML =
                '<img src="{{ asset('./frontend/asset/images/t2.png') }}" alt="AI" style="width:100%;height:100%;object-fit:contain;border-radius:inherit;">';
        } else if (window.currentChatUser.avatar) {
            avatarEl.innerHTML =
                `<img src="${window.currentChatUser.avatar}" alt="Avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"><span class="msg-avatar-placeholder" style="display:none;">${window.currentChatUser.initial}</span>`;
        } else {
            avatarEl.innerHTML = `<span class="msg-avatar-placeholder">${window.currentChatUser.initial}</span>`;
        }

        const inner = document.createElement('div');
        inner.appendChild(bubble);
        inner.appendChild(time);

        if (role === 'ai') {
            wrap.appendChild(avatarEl);
            wrap.appendChild(inner);
        } else {
            wrap.appendChild(inner);
            wrap.appendChild(avatarEl);
        }

        chatBox.appendChild(wrap);
        window.scrollChatToBottom();
    };

    window.loadOldChat = function(id) {
        if (!window.currentChatUser.isLoggedIn) {
            return;
        }

        const chatBox = document.getElementById('chat-box');
        chatBox.innerHTML = '';

        fetch(`{{ url('hoi-dap-ai') }}/${encodeURIComponent(id)}`)
            .then(r => {
                if (!r.ok) {
                    throw new Error('Không tải được phiên chat');
                }
                return r.json();
            })
            .then(data => {
                if (data.messages && data.messages.length) {
                    data.messages.forEach(message => {
                        window.appendMessageToChat('user', message.cau_hoi, message.created_at);
                        window.appendMessageToChat('ai', message.cau_tra_loi, message.answered_at || message.created_at);
                    });
                }

                sessionStorage.setItem('ai_chat_session_id', id);

                document.querySelectorAll('.history-item').forEach(item => {
                    item.classList.remove('active');
                });
                const activeItem = document.querySelector(`.history-item[data-id="${id}"]`);
                if (activeItem) activeItem.classList.add('active');
            })
            .catch(() => {
                sessionStorage.removeItem('ai_chat_session_id');
                chatBox.innerHTML = '<div class="history-empty">Lỗi tải dữ liệu</div>';
            });
    };

    window.deleteChat = function(event, id) {
        event.stopPropagation();
        window.closeChatMenus?.();

        if (!window.currentChatUser.isLoggedIn) {
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?')) return;

        fetch(`{{ url('hoi-dap-ai') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => {
                if (!r.ok) {
                    return r.json().then(err => { throw err; }).catch(() => { throw new Error('Lỗi server'); });
                }
                return r.json();
            })
            .then(data => {
                if (data.success) {
                    const item = document.querySelector(`.history-item[data-id="${id}"]`);
                    if (item) item.remove();
                    const list = document.getElementById('history-list');

                    if (sessionStorage.getItem('ai_chat_session_id') === String(id)) {
                        sessionStorage.removeItem('ai_chat_session_id');
                        location.reload();
                        return;
                    }

                    const chatBox = document.getElementById('chat-box');
                    if (chatBox.children.length === 0 || chatBox.innerHTML.includes(
                        'Cuộc trò chuyện đã bị xóa')) {
                        location.reload();
                    }

                    if (list && !list.querySelector('.history-item')) {
                        list.innerHTML = '<div class="history-empty">Chưa có lịch sử</div>';
                    }
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể xóa cuộc trò chuyện.'));
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                alert('Có lỗi xảy ra khi xóa cuộc trò chuyện.');
            });
    };

    window.renameChat = function(event, id) {
        event.stopPropagation();
        window.closeChatMenus?.();

        if (!window.currentChatUser.isLoggedIn) {
            return;
        }

        const item = document.querySelector(`.history-item[data-id="${id}"]`);
        const titleEl = item?.querySelector('.history-content .fw-bold');
        const currentTitle = titleEl?.textContent?.trim() || '';
        const nextTitle = prompt('Nhập tên mới cho cuộc trò chuyện:', currentTitle);

        if (nextTitle === null) return;

        const title = nextTitle.trim();
        if (!title) {
            alert('Tên cuộc trò chuyện không được để trống.');
            return;
        }

        fetch(`{{ url('hoi-dap-ai') }}/${encodeURIComponent(id)}/rename`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ title })
            })
            .then(r => {
                if (!r.ok) {
                    return r.json().then(err => { throw err; }).catch(() => { throw new Error('Lỗi server'); });
                }
                return r.json();
            })
            .then(data => {
                if (data.success && titleEl) {
                    titleEl.textContent = data.title;
                } else {
                    alert('Lỗi: ' + (data.error || 'Không thể đổi tên.'));
                }
            })
            .catch(err => {
                console.error('Rename error:', err);
                alert('Không đổi tên được cuộc trò chuyện, vui lòng thử lại.');
            });
    };

    (function() {
        const chatBox = document.getElementById('chat-box');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('user-input');
        const historySearch = document.getElementById('chat-history-search');
        const chips = document.querySelectorAll('.chip-suggest');
        const btnNew = document.getElementById('btn-new-chat');
        const chatSessionStorageKey = 'ai_chat_session_id';
        const historyList = document.getElementById('history-list');

        window.closeChatMenus = function() {
            document.querySelectorAll('.chat-actions.open').forEach(menu => {
                menu.classList.remove('open');
            });
        };

        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                input.value = chip.dataset.q;
                input.focus();
                sendMessage();
            });
        });

        btnNew.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.removeItem(chatSessionStorageKey);
            location.reload();
        });

        historySearch?.addEventListener('input', () => {
            const keyword = historySearch.value.trim().toLowerCase();

            document.querySelectorAll('#history-list .history-item').forEach(item => {
                const title = item.querySelector('.history-content .fw-bold')?.textContent || '';
                item.style.display = title.toLowerCase().includes(keyword) ? '' : 'none';
            });
        });

        historyList?.addEventListener('click', (e) => {
            const menuButton = e.target.closest('.btn-chat-menu');
            if (menuButton) {
                e.stopPropagation();
                const actions = menuButton.closest('.chat-actions');
                const isOpen = actions?.classList.contains('open');
                window.closeChatMenus();
                if (actions && !isOpen) {
                    actions.classList.add('open');
                }
                return;
            }

            if (e.target.closest('.btn-rename-chat')) {
                const item = e.target.closest('.history-item');
                if (item) {
                    renameChat(e, item.dataset.id);
                }
                return;
            }

            if (e.target.closest('.chat-menu') || e.target.closest('.btn-delete-chat')) {
                return;
            }

            window.closeChatMenus();
            const item = e.target.closest('.history-item');
            if (!item) {
                return;
            }

            loadOldChat(item.dataset.id);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.chat-actions')) {
                window.closeChatMenus();
            }
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            sendMessage();
        });

        const savedSessionId = sessionStorage.getItem(chatSessionStorageKey);
        if (window.currentChatUser.isLoggedIn && savedSessionId) {
            loadOldChat(savedSessionId);
        }

        function sendMessage() {
            const text = input.value.trim();
            if (!text) return;

            const suggestWrap = chatBox.querySelector('.suggestions-wrap');
            if (suggestWrap) suggestWrap.remove();

            const askedAt = new Date();
            window.appendMessageToChat('user', text, askedAt);
            input.value = '';
            input.style.height = 'auto';

            const loadingEl = document.createElement('div');
            loadingEl.className = 'message ai loading';
            loadingEl.innerHTML = `
                        <div class="msg-avatar"><img src="{{ asset('./frontend/asset/images/t2.png') }}" alt="AI" style="width:100%;height:100%;object-fit:contain;border-radius:inherit;"></div>
                        <div class="msg-bubble"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>
                    `;
            chatBox.appendChild(loadingEl);
            window.scrollChatToBottom();

            fetch('{{ route('client.ai.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text,
                        mon_hoc_id: null,
                        cau_hoi_id: null,
                        chat_session_id: sessionStorage.getItem(chatSessionStorageKey)
                    })
                })
                .then(r => r.json())
                .then(data => {
                    loadingEl.remove();
                    if (data.error) {
                        window.appendMessageToChat('ai', '⚠️ ' + data.error, new Date());
                        return;
                    }

                    window.appendMessageToChat('ai', data.answer || 'Xin lỗi, mình chưa hiểu câu hỏi này.', new Date());

                    if (data.chat_session_id) {
                        sessionStorage.setItem(chatSessionStorageKey, data.chat_session_id);
                    }

                    if (!window.currentChatUser.isLoggedIn || !data.conversation_id) {
                        return;
                    }

                    const list = document.getElementById('history-list');
                    const empty = list.querySelector('.history-empty');
                    if (empty) empty.remove();

                    const sessionId = data.chat_session_id || data.conversation_id;
                    let historyItem = list.querySelector(`.history-item[data-id="${sessionId}"]`);

                    list.querySelectorAll('.history-item').forEach(i => i.classList.remove('active'));

                    if (historyItem) {
                        historyItem.classList.add('active');
                        const timeEl = historyItem.querySelector('small');
                        if (timeEl) timeEl.textContent = window.formatChatTime(askedAt);
                        list.prepend(historyItem);
                    } else {
                        historyItem = document.createElement('div');
                        historyItem.className = 'history-item active';
                        historyItem.setAttribute('data-id', sessionId);
                        historyItem.innerHTML = `
                                <div class="hi-icon">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                    </svg>
                                </div>
                                <div class="history-content">
                                    <span class="fw-bold">${window.escapeHtml(text.substring(0, 40))}</span>
                                    <small>${window.formatChatTime(askedAt)}</small>
                                </div>
                                <div class="chat-actions">
                                    <button class="btn-chat-menu" type="button" aria-label="Tùy chọn cuộc trò chuyện">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="chat-menu">
                                        <button class="btn-rename-chat" type="button">
                                            <i class="fas fa-pen"></i> Đổi tên
                                        </button>
                                        <button class="btn-delete-chat" type="button" onclick="deleteChat(event, '${window.escapeHtml(sessionId)}')">
                                            <i class="fas fa-trash-alt"></i> Xóa
                                        </button>
                                    </div>
                                </div>
                            `;
                        list.prepend(historyItem);
                    }
                    })
                    .catch(() => {
                        loadingEl.remove();
                        window.appendMessageToChat('ai', '⚠️ Có lỗi xảy ra, vui lòng thử lại sau.', new Date());
                    });
            }
        })();
    </script>
@endpush
