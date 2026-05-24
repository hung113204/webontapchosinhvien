        {{-- Profile Pill Wrapper --}}
        <div class="qv-user-wrapper">
            <div class="qv-user-pill" id="qv-user-pill" onclick="toggleUserDropdown(event)">
                <div class="qv-user-avatar">
                    @if(auth()->check() && auth()->user()->avatar_url)
                        <img src="{{ str_starts_with(auth()->user()->avatar_url, 'http') ? auth()->user()->avatar_url : \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_url) }}" alt="avatar">
                    @else
                        {{ strtoupper(substr(session('quiz_nickname', optional(auth()->user())->ho_ten ?? 'Khách'), 0, 1)) }}
                    @endif
                </div>
                <div class="qv-user-info">
                    <div class="qv-user-name">{{ session('quiz_nickname', optional(auth()->user())->ho_ten ?? 'Khách') }}</div>
                    <div class="qv-user-role">Thành viên</div>
                </div>
                <div class="qv-user-arrow" id="qv-user-arrow-icon">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>
            </div>
            
            {{-- Dropdown Card --}}
            <div class="qv-user-dropdown" id="qv-user-dropdown">
                <div class="qv-dropdown-header">
                    <div class="qv-dropdown-name">{{ session('quiz_nickname', optional(auth()->user())->ho_ten ?? 'Khách') }}</div>
                    <div class="qv-dropdown-email">{{ optional(auth()->user())->email ?? 'Khách tham quan' }}</div>
                </div>
                <div class="qv-dropdown-divider"></div>
                <div class="qv-dropdown-item" onclick="openRenameModal(event)">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="qv-item-icon">
                        <path d="M12 20h9"></path>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>
                    <span>Đổi nickname hiển thị</span>
                </div>
            </div>
        </div>
