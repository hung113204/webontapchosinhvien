        {{-- Custom Rename Modal --}}
        <div id="rename-modal" class="qv-modal-overlay" style="display: none;" onclick="closeRenameModalOutside(event)">
            <div class="qv-modal-content">
                <h3>✏️ Đổi nickname hiển thị</h3>
                <p>Nhập nickname mới của bạn để hiển thị trong phòng thi đấu:</p>
                <input type="text" id="new-nickname-input" class="qv-modal-input" value="{{ session('quiz_nickname', optional(auth()->user())->ho_ten ?? 'Khách') }}" placeholder="Nhập nickname..." maxLength="30">
                <div class="qv-modal-actions">
                    <button onclick="closeRenameModal()" class="qv-modal-btn btn-cancel">Hủy bỏ</button>
                    <button onclick="submitRename()" class="qv-modal-btn btn-confirm">Xác nhận</button>
                </div>
            </div>
        </div>
