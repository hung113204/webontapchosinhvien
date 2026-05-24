<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loaiCauHoiSelect = document.querySelector('select[name="loai_cau_hoi"]');
        if (!loaiCauHoiSelect) return;

        const answersOuter = document.querySelector('.answers-outer');
        
        // Tạo vùng "Đáp án Điền khuyết"
        const dienKhuyetOuter = document.createElement('div');
        dienKhuyetOuter.className = 'dien-khuyet-outer';
        dienKhuyetOuter.style.marginTop = '40px';
        dienKhuyetOuter.style.display = 'none';
        
        dienKhuyetOuter.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">ĐÁP ÁN ĐIỀN KHUYẾT</h3>
                <button type="button" class="btn btn-secondary" onclick="addDienKhuyetAnswer()" 
                    style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; color: #4f46e5; cursor: pointer;">
                    + Thêm ô trống
                </button>
            </div>
            <div style="margin-bottom: 15px; padding: 12px; background: #eff6ff; border-radius: 8px; border: 1px solid #bfdbfe;">
                <small style="color: #1e40af;">
                    <strong>Hướng dẫn:</strong> Trong nội dung câu hỏi, sử dụng dấu <strong>[...]</strong> để đại diện cho ô trống. 
                    Mỗi ô trống sẽ tương ứng với một đáp án bên dưới theo đúng thứ tự.
                </small>
            </div>
            <div id="dien-khuyet-container"></div>
        `;
        
        // Chèn vào sau answersOuter
        if (answersOuter) {
            answersOuter.parentNode.insertBefore(dienKhuyetOuter, answersOuter.nextSibling);
            
            let dkCount = 0;

            window.addDienKhuyetAnswer = function(content = '', id = '') {
                dkCount++;
                const index = dkCount - 1;
                const containerId = `dk_container_${dkCount}`;
                
                const html = `
                    <div class="answer-item" id="${containerId}" style="margin-bottom: 10px; padding: 15px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-weight: 600; color: #64748b; min-width: 80px;">Ô trống ${dkCount}:</span>
                            <input type="text" name="dap_ans[${index}][noi_dung]" value="${content}" 
                                class="form-input" placeholder="Nhập đáp án đúng cho ô này..." 
                                style="flex: 1; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px;">
                            <input type="hidden" name="dap_ans[${index}][is_dung]" value="1">
                            <input type="hidden" name="dap_an_dung[]" value="${index}">
                            ${id ? `<input type="hidden" name="dap_ans[${index}][id]" value="${id}">` : ''}
                            <button type="button" onclick="removeDienKhuyetAnswer('${containerId}')" 
                                style="background: #fee2e2; color: #ef4444; border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;">
                                Xóa
                            </button>
                        </div>
                    </div>
                `;
                document.getElementById('dien-khuyet-container').insertAdjacentHTML('beforeend', html);
            };

            window.removeDienKhuyetAnswer = function(id) {
                document.getElementById(id).remove();
                // Re-index might be needed if the order matters strictly for the backend, 
                // but processDapAns uses the index of the array which is preserved by the browser/server.
            };

            // Load existing answers if editing
            @if(isset($cauHoi) && $cauHoi->loai_cau_hoi == 3)
                @foreach($cauHoi->dapAns as $dapAn)
                    addDienKhuyetAnswer(`{!! addslashes($dapAn->noi_dung) !!}`, `{{ $dapAn->id }}`);
                @endforeach
            @else
                // Add one blank by default for new question
                setTimeout(() => {
                    if (loaiCauHoiSelect.value == '3' && document.getElementById('dien-khuyet-container').children.length === 0) {
                        addDienKhuyetAnswer();
                    }
                }, 100);
            @endif

            function toggleLoaiCauHoi() {
                const val = loaiCauHoiSelect.value;
                const tuLuanOuter = document.querySelector('.tu-luan-outer');
                
                if (val == '3') { // Điền khuyết
                    answersOuter.style.display = 'none';
                    dienKhuyetOuter.style.display = 'block';
                    if (tuLuanOuter) tuLuanOuter.style.display = 'none';
                    
                    // Disable other inputs
                    answersOuter.querySelectorAll('input, textarea, select').forEach(i => i.disabled = true);
                    if (tuLuanOuter) tuLuanOuter.querySelectorAll('input, textarea').forEach(i => i.disabled = true);
                    
                    // Enable dk inputs
                    dienKhuyetOuter.querySelectorAll('input').forEach(i => i.disabled = false);
                    
                    // Nếu chưa có ô trống nào thì thêm 1 ô
                    if (document.getElementById('dien-khuyet-container').children.length === 0) {
                        addDienKhuyetAnswer();
                    }
                } else if (val == '4') { // Tự luận - Sẽ được xử lý bởi tu_luan_script.blade.php
                    dienKhuyetOuter.style.display = 'none';
                    dienKhuyetOuter.querySelectorAll('input').forEach(i => i.disabled = true);
                } else { // Trắc nghiệm, Đúng/Sai
                    dienKhuyetOuter.style.display = 'none';
                    dienKhuyetOuter.querySelectorAll('input').forEach(i => i.disabled = true);
                }
            }
            
            loaiCauHoiSelect.addEventListener('change', toggleLoaiCauHoi);
            
            // Khởi tạo trạng thái ban đầu
            setTimeout(toggleLoaiCauHoi, 600); 
        }
    });
</script>
