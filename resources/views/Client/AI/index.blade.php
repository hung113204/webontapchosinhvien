@extends('Client.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('./frontend/asset/css/ai-chat.css') }}">
    <style>
        #custom-select-trigger:hover {
            border-color: #a5b4fc !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08) !important;
        }
        #custom-select-trigger:active {
            transform: scale(0.98);
        }
        .custom-option {
            margin: 4px;
            border-radius: 8px;
        }
        .custom-option:hover {
            background-color: #f1f5f9 !important;
            color: #4f46e5 !important;
        }
        .custom-option.active {
            background-color: #f5f3ff !important;
            color: #4f46e5 !important;
        }
        #custom-select-options::-webkit-scrollbar {
            width: 6px;
        }
        #custom-select-options::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        .chat-menu.drop-up {
            top: auto !important;
            bottom: calc(100% + 6px) !important;
        }
    </style>
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
                    <div class="chat-search-box" style="margin-bottom: 12px;">
                        <i class="fas fa-search"></i>
                        <input type="search" id="chat-history-search" placeholder="Tìm kiếm phiên chat...">
                    </div>
                    
                    <div class="ai-config-section" style="padding-top: 12px; border-top: 1px solid #eef2f6;">
                        <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">Chế độ Trợ lý AI</label>
                        <div class="ai-mode-selector" style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-bottom: 12px;">
                            <button type="button" class="ai-mode-btn active" data-mode="academic" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 12px 6px; border: 1.5px solid #a5b4fc; background: #fff; border-radius: 12px; font-size: 11px; font-weight: 700; color: #4f46e5; transition: all 0.2s; cursor: pointer;">
                                <i class="fas fa-graduation-cap" style="font-size: 15px;"></i>
                                <span style="margin-top: 2px;">Giải đáp Học tập</span>
                            </button>
                            <button type="button" class="ai-mode-btn" data-mode="counseling" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 12px 6px; border: 1.5px solid #e2eaf6; background: #f8fafc; border-radius: 12px; font-size: 11px; font-weight: 700; color: #64748b; transition: all 0.2s; cursor: pointer;">
                                <i class="fas fa-comments" style="font-size: 15px;"></i>
                                <span style="margin-top: 2px;">Tư vấn & Hướng nghiệp</span>
                            </button>
                        </div>

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
                            <!-- <span class="status-indicator"></span> -->
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
                        <!-- <div class="ai-avatar">
                            <img src="{{ asset('./frontend/asset/images/t2.png') }}" alt="AI Avatar"
                                style="width:100%;height:100%;object-fit:contain;border-radius:inherit;">
                        </div>
                        <div class="ai-info">
                            <span class="ai-name">Trợ lý ảo IT Support</span>
                            <span class="ai-subtitle">Đang hoạt động</span>
                        </div> -->
                        <div id="ai-subject-select-wrapper" style="transition: all 0.2s; display: flex; align-items: center; gap: 10px; position: relative;">
                            <label style="font-size: 14px; font-weight: 700; color: #475569; white-space: nowrap; margin-bottom: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-book" style="color: #4f46e5; font-size: 16px;"></i> Môn học:
                            </label>
                            
                            <select id="ai-subject-select" style="display: none;">
                                <option value="">-- Tất cả môn học --</option>
                                @foreach($monHocs as $mh)
                                    <option value="{{ $mh->id }}">{{ $mh->ten_mon_hoc }}</option>
                                @endforeach
                            </select>

                            <div class="custom-select-container" style="position: relative;">
                                <div id="custom-select-trigger" 
                                    style="min-width: 240px; max-width: 320px; border: 1.5px solid #e2eaf6; border-radius: 20px; padding: 8px 34px 8px 14px; font-size: 14px; color: #334155; font-weight: 600; background: #fff; outline: none; transition: all 0.2s; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; user-select: none;"
                                >
                                    <span class="selected-text">-- Tất cả môn học --</span>
                                    <i class="fas fa-chevron-down" style="color: #64748b; font-size: 11px; transition: transform 0.2s;"></i>
                                </div>

                                <div id="custom-select-options" 
                                    style="position: absolute; top: calc(100% + 6px); left: 0; right: 0; background: #fff; border: 1px solid #e2eaf6; border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden; opacity: 0; transform: translateY(-10px); pointer-events: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); z-index: 999; max-height: 260px; overflow-y: auto;"
                                >
                                    <div class="custom-option active" data-value="" style="padding: 10px 16px; font-size: 13.5px; font-weight: 600; color: #4f46e5; background: #f5f3ff; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: space-between;">
                                        <span>-- Tất cả môn học --</span>
                                        <i class="fas fa-check" style="font-size: 11px;"></i>
                                    </div>
                                    @foreach($monHocs as $mh)
                                        <div class="custom-option" data-value="{{ $mh->id }}" style="padding: 10px 16px; font-size: 13.5px; font-weight: 600; color: #334155; cursor: pointer; transition: all 0.15s; display: flex; align-items: center; justify-content: space-between;">
                                            <span>{{ $mh->ten_mon_hoc }}</span>
                                            <i class="fas fa-check" style="font-size: 11px; display: none; color: #4f46e5;"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="streak-label" style="display: none;"></div>
                    <button class="btn-settings" id="btn-open-settings" aria-label="Cài đặt">
                        <i class="fas fa-cog"></i>
                    </button>
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

            <!-- Modal Cài đặt -->
            <div class="ai-modal-overlay" id="ai-settings-modal">
                <div class="ai-settings-card">
                    <div class="ai-settings-header">
                        <h3>Cài đặt</h3>
                        <button class="btn-close-modal" id="btn-close-settings">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="ai-settings-tabs-wrapper">
                        <div class="ai-settings-tabs">
                            <button class="tab-btn active"><i class="fas fa-sliders-h"></i> Chung</button>
                            <button class="tab-btn"><i class="far fa-user"></i> Tài khoản</button>
                        </div>
                    </div>

                    <div class="ai-settings-body">
                        <div class="settings-section">
                            <div class="settings-label">GIAO DIỆN</div>
                            <div class="theme-options">
                                <button class="theme-option active" id="theme-light-btn">
                                    <i class="far fa-sun"></i>
                                    <span>Sáng</span>
                                </button>
                                <button class="theme-option" id="theme-dark-btn">
                                    <i class="far fa-moon"></i>
                                    <span>Tối</span>
                                </button>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="settings-label">CỠ CHỮ</div>
                            <div class="fontsize-options-wrapper">
                                <div class="fontsize-options">
                                    <button class="font-btn" id="font-small-btn">Nhỏ</button>
                                    <button class="font-btn active" id="font-medium-btn">Vừa</button>
                                    <button class="font-btn" id="font-large-btn">Lớn</button>
                                </div>
                            </div>
                        </div>

                        <div class="settings-preview">
                            {{ $displayName }} sẽ hiển thị văn bản như thế này.
                        </div>
                    </div>

                    <div class="ai-settings-footer">
                        <button class="btn-primary-close" id="btn-close-settings-footer">Đóng</button>
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
            const codeBlocks = [];
            let placeholderIndex = 0;
            const uuid = Math.random().toString(36).substring(2, 15);
            
            // 1. Extract code blocks and replace with placeholders
            let processed = text.replace(/```(\w*)\n?([\s\S]*?)```/g, (_, lang, code) => {
                const placeholder = `__CODE_BLOCK_PLACEHOLDER_${uuid}_${placeholderIndex}__`;
                codeBlocks.push({
                    placeholder: placeholder,
                    lang: lang,
                    code: code.trim()
                });
                placeholderIndex++;
                return placeholder;
            });
            
            // 2. Escape HTML special characters for the rest of the text
            processed = processed.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            
            // 3. Process inline code, bold and newlines
            processed = processed.replace(/`([^`]+)`/g, '<code>$1</code>');
            processed = processed.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
            processed = processed.replace(/\n/g, '<br>');
            
            // 4. Restore the code blocks with proper escaping for display and base64 for copy-paste
            codeBlocks.forEach(item => {
                const displayLang = item.lang ? item.lang.toUpperCase() : 'CODE';
                const escapedDisplayCode = item.code.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                
                let base64Code = '';
                try {
                    base64Code = btoa(unescape(encodeURIComponent(item.code)));
                } catch (e) {
                    console.error('Failed to base64 encode code block:', e);
                }
                
                const html = `<div class="code-block-wrapper"><div class="code-block-header"><span class="code-block-lang">${displayLang}</span><button class="btn-copy-code" data-code="${base64Code}" onclick="window.copyCodeToClipboard(this)"><i class="far fa-copy"></i> Sao chép</button></div><pre><code class="lang-${item.lang}">${escapedDisplayCode}</code></pre></div>`;
                
                processed = processed.replace(item.placeholder, html);
            });
            
            return processed;
        };

        window.copyCodeToClipboard = function(button) {
            const base64Code = button.getAttribute('data-code');
            if (!base64Code) return;
            
            let textToCopy = '';
            try {
                textToCopy = decodeURIComponent(escape(atob(base64Code)));
            } catch (e) {
                console.error('Failed to decode base64 code:', e);
                return;
            }
            
            const doFeedback = () => {
                button.innerHTML = '<i class="fas fa-check"></i> Đã chép';
                button.classList.add('copied');
                setTimeout(() => {
                    button.innerHTML = '<i class="far fa-copy"></i> Sao chép';
                    button.classList.remove('copied');
                }, 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(doFeedback).catch(err => {
                    console.error('navigator.clipboard error:', err);
                    fallbackCopyText(textToCopy, doFeedback);
                });
            } else {
                fallbackCopyText(textToCopy, doFeedback);
            }
        };

        function fallbackCopyText(text, callback) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    callback();
                } else {
                    console.error('Fallback copy command failed');
                }
            } catch (err) {
                console.error('Fallback copy failed', err);
            }
            document.body.removeChild(textArea);
        }

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

    window.loadOldChat = function(id, isAutoLoad = false) {
        if (!window.currentChatUser.isLoggedIn) {
            return;
        }

        const chatBox = document.getElementById('chat-box');
        const originalContent = chatBox.innerHTML;
        chatBox.innerHTML = '<div style="display:flex; justify-content:center; align-items:center; height:100%; color:var(--blue); font-size:1.5rem;"><i class="fas fa-spinner fa-spin"></i></div>';

        fetch(`{{ url('hoi-dap-ai') }}/${encodeURIComponent(id)}`)
            .then(r => {
                if (!r.ok) {
                    throw new Error('Không tải được phiên chat');
                }
                return r.json();
            })
            .then(data => {
                chatBox.innerHTML = '';
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
                chatBox.innerHTML = originalContent;
                if (!isAutoLoad) {
                    alert('Không thể tải lịch sử cuộc trò chuyện này (có thể đã bị xóa hoặc không thuộc về tài khoản này).');
                }
                document.querySelectorAll('.history-item').forEach(item => {
                    item.classList.remove('active');
                });
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
        // Custom Select Logic
        const trigger = document.getElementById('custom-select-trigger');
        const optionsList = document.getElementById('custom-select-options');
        const nativeSelect = document.getElementById('ai-subject-select');
        
        if (trigger && optionsList && nativeSelect) {
            const selectedText = trigger.querySelector('.selected-text');
            const chevron = trigger.querySelector('.fa-chevron-down');
            
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = optionsList.classList.contains('open');
                if (isOpen) {
                    closeDropdown();
                } else {
                    optionsList.classList.add('open');
                    optionsList.style.opacity = '1';
                    optionsList.style.transform = 'translateY(0)';
                    optionsList.style.pointerEvents = 'auto';
                    trigger.style.borderColor = '#4f46e5';
                    trigger.style.boxShadow = '0 0 0 3px rgba(79, 70, 229, 0.12)';
                    chevron.style.transform = 'rotate(180deg)';
                }
            });
            
            optionsList.querySelectorAll('.custom-option').forEach(opt => {
                opt.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = opt.getAttribute('data-value');
                    const label = opt.querySelector('span').textContent;
                    
                    nativeSelect.value = value;
                    nativeSelect.dispatchEvent(new Event('change'));
                    
                    optionsList.querySelectorAll('.custom-option').forEach(item => {
                        item.classList.remove('active');
                        item.style.background = 'transparent';
                        item.style.color = '#334155';
                        item.querySelector('.fa-check').style.display = 'none';
                    });
                    
                    opt.classList.add('active');
                    opt.style.background = '#f5f3ff';
                    opt.style.color = '#4f46e5';
                    opt.querySelector('.fa-check').style.display = 'block';
                    selectedText.textContent = label;
                    closeDropdown();
                });
            });
            
            document.addEventListener('click', closeDropdown);
            
            function closeDropdown() {
                optionsList.classList.remove('open');
                optionsList.style.opacity = '0';
                optionsList.style.transform = 'translateY(-10px)';
                optionsList.style.pointerEvents = 'none';
                trigger.style.borderColor = '#e2eaf6';
                trigger.style.boxShadow = '0 2px 4px rgba(0,0,0,0.02)';
                chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Settings Modal Logic
        const btnOpenSettings = document.getElementById('btn-open-settings');
        const aiSettingsModal = document.getElementById('ai-settings-modal');
        const btnCloseSettings = document.getElementById('btn-close-settings');
        const btnCloseSettingsFooter = document.getElementById('btn-close-settings-footer');
        const aiChatWrapper = document.querySelector('.ai-chat-wrapper');

        if (btnOpenSettings) {
            btnOpenSettings.addEventListener('click', () => {
                aiSettingsModal.classList.add('show');
            });
        }

        function closeSettings() {
            aiSettingsModal.classList.remove('show');
        }

        if (btnCloseSettings) btnCloseSettings.addEventListener('click', closeSettings);
        if (btnCloseSettingsFooter) btnCloseSettingsFooter.addEventListener('click', closeSettings);

        // Theme Switcher Logic
        const themeLightBtn = document.getElementById('theme-light-btn');
        const themeDarkBtn = document.getElementById('theme-dark-btn');

        function setTheme(theme) {
            if (theme === 'dark') {
                aiChatWrapper.classList.add('dark-theme');
                themeDarkBtn.classList.add('active');
                themeLightBtn.classList.remove('active');
                localStorage.setItem('ai-chat-theme', 'dark');
            } else {
                aiChatWrapper.classList.remove('dark-theme');
                themeLightBtn.classList.add('active');
                themeDarkBtn.classList.remove('active');
                localStorage.setItem('ai-chat-theme', 'light');
            }
        }

        if (themeLightBtn) themeLightBtn.addEventListener('click', () => setTheme('light'));
        if (themeDarkBtn) themeDarkBtn.addEventListener('click', () => setTheme('dark'));

        // Font Size Logic
        const fontSmallBtn = document.getElementById('font-small-btn');
        const fontMediumBtn = document.getElementById('font-medium-btn');
        const fontLargeBtn = document.getElementById('font-large-btn');

        function setFontSize(size) {
            aiChatWrapper.classList.remove('font-small', 'font-large');
            fontSmallBtn.classList.remove('active');
            fontMediumBtn.classList.remove('active');
            fontLargeBtn.classList.remove('active');

            if (size === 'small') {
                aiChatWrapper.classList.add('font-small');
                fontSmallBtn.classList.add('active');
            } else if (size === 'large') {
                aiChatWrapper.classList.add('font-large');
                fontLargeBtn.classList.add('active');
            } else {
                fontMediumBtn.classList.add('active');
            }
            localStorage.setItem('ai-chat-fontsize', size);
        }

        if (fontSmallBtn) fontSmallBtn.addEventListener('click', () => setFontSize('small'));
        if (fontMediumBtn) fontMediumBtn.addEventListener('click', () => setFontSize('medium'));
        if (fontLargeBtn) fontLargeBtn.addEventListener('click', () => setFontSize('large'));

        // Load saved settings
        const savedTheme = localStorage.getItem('ai-chat-theme');
        if (savedTheme) setTheme(savedTheme);

        const savedFontSize = localStorage.getItem('ai-chat-fontsize');
        if (savedFontSize) setFontSize(savedFontSize);

        const chatBox = document.getElementById('chat-box');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('user-input');
        const historySearch = document.getElementById('chat-history-search');
        const btnNew = document.getElementById('btn-new-chat');
        const chatSessionStorageKey = 'ai_chat_session_id';
        const historyList = document.getElementById('history-list');

        window.closeChatMenus = function() {
            document.querySelectorAll('.chat-actions.open').forEach(actions => {
                actions.classList.remove('open');
                const menu = actions.querySelector('.chat-menu');
                if (menu) {
                    menu.classList.remove('drop-up');
                }
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

        // Thiết lập danh sách gợi ý động theo chế độ AI
        const suggestionsWrap = document.querySelector('.suggestions-chips');
        const suggestions = {
            academic: [
                { q: "Stack là gì? Cho ví dụ thực tế", label: "Stack là gì?" },
                { q: "Queue khác Stack ở điểm nào?", label: "Queue vs Stack" },
                { q: "Giải thích thuật toán BFS và DFS", label: "BFS vs DFS" },
                { q: "Big O notation là gì?", label: "Big O Notation" }
            ],
            counseling: [
                { q: "Lộ trình tự học lập trình Web toàn diện?", label: "Lộ trình Web" },
                { q: "Nên học Java hay C++ cho Backend?", label: "Java vs C++ Backend" },
                { q: "Cách viết CV và Portfolio xin việc ngành IT?", label: "Viết CV & Portfolio" },
                { q: "Ngành Trí tuệ nhân tạo (AI) cần học tốt môn gì?", label: "Định hướng ngành AI" }
            ]
        };

        function renderSuggestions(mode) {
            if (!suggestionsWrap) return;
            suggestionsWrap.innerHTML = '';
            suggestions[mode].forEach(item => {
                const chip = document.createElement('span');
                chip.className = 'chip-suggest';
                chip.setAttribute('data-q', item.q);
                chip.innerHTML = `<i class="fas fa-lightbulb" style="margin-right: 4px;"></i> ${item.label}`;
                chip.addEventListener('click', () => {
                    input.value = item.q;
                    input.focus();
                    sendMessage();
                });
                suggestionsWrap.appendChild(chip);
            });
        }

        // Xử lý chuyển đổi chế độ AI
        const modeButtons = document.querySelectorAll('.ai-mode-btn');
        const subjectWrapper = document.getElementById('ai-subject-select-wrapper');

        modeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modeButtons.forEach(b => {
                    b.classList.remove('active');
                    b.style.border = '1.5px solid #e2eaf6';
                    b.style.background = '#f8fafc';
                    b.style.color = '#64748b';
                });

                btn.classList.add('active');
                btn.style.border = '1.5px solid #a5b4fc';
                btn.style.background = '#fff';
                btn.style.color = '#4f46e5';

                const mode = btn.dataset.mode;
                if (mode === 'counseling') {
                    if (subjectWrapper) {
                        subjectWrapper.style.opacity = '0.5';
                        subjectWrapper.style.pointerEvents = 'none';
                    }
                } else {
                    if (subjectWrapper) {
                        subjectWrapper.style.opacity = '1';
                        subjectWrapper.style.pointerEvents = 'auto';
                    }
                }
                renderSuggestions(mode);
            });
        });

        // Thiết lập gợi ý ban đầu
        renderSuggestions('academic');

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
                    
                    const menu = actions.querySelector('.chat-menu');
                    const sidebar = document.querySelector('.ai-sidebar');
                    if (menu && sidebar) {
                        const menuRect = menu.getBoundingClientRect();
                        const sidebarRect = sidebar.getBoundingClientRect();
                        
                        if (menuRect.bottom > sidebarRect.bottom - 10) {
                            menu.classList.add('drop-up');
                        }
                    }
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

            loadOldChat(item.dataset.id, false);
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
            loadOldChat(savedSessionId, true);
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

            const activeModeBtn = document.querySelector('.ai-mode-btn.active');
            const selectedMode = activeModeBtn ? activeModeBtn.dataset.mode : 'academic';
            const subjectSelect = document.getElementById('ai-subject-select');
            const selectedSubjectId = (selectedMode === 'academic' && subjectSelect) ? subjectSelect.value : null;

            fetch('{{ route('client.ai.ask') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text,
                        mon_hoc_id: selectedSubjectId || null,
                        cau_hoi_id: null,
                        ai_mode: selectedMode,
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
