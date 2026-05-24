<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loaiCauHoiSelect = document.querySelector('select[name="loai_cau_hoi"]');
        if (!loaiCauHoiSelect) return;

        const answersOuter = document.querySelector('.answers-outer');
        
        // Thêm một vùng "Đáp án tham khảo (Tự luận)" vào DOM
        const tuLuanOuter = document.createElement('div');
        tuLuanOuter.className = 'tu-luan-outer';
        tuLuanOuter.style.marginTop = '40px';
        tuLuanOuter.style.display = 'none';
        
        // Kiểm tra xem đã có đáp án tự luận chưa (trong trường hợp edit)
        let tuLuanContent = '';
        let tuLuanId = '';
        @if(isset($cauHoi) && $cauHoi->loai_cau_hoi == 4 && $cauHoi->dapAns->count() > 0)
            tuLuanContent = `{!! addslashes($cauHoi->dapAns->first()->noi_dung) !!}`;
            tuLuanId = '{{ $cauHoi->dapAns->first()->id }}';
        @endif

        // Cấu trúc HTML của phần tự luận
        tuLuanOuter.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">ĐÁP ÁN THAM KHẢO (TỰ LUẬN)</h3>
            </div>
            <div class="form-group" style="margin-bottom: 25px;">
                <textarea id="editor-tu-luan" name="dap_ans[0][noi_dung]">${tuLuanContent}</textarea>
                <input type="hidden" name="dap_an_dung" value="0">
                <input type="hidden" name="dap_ans[0][id]" value="${tuLuanId}">
            </div>
        `;
        
        // Chèn vào sau answersOuter
        if (answersOuter) {
            answersOuter.parentNode.insertBefore(tuLuanOuter, answersOuter.nextSibling);
            
            // Khởi tạo CKEditor cho Tự luận
            let tuLuanEditor;
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor.create(document.querySelector('#editor-tu-luan'), {
                    toolbar: [
                        'heading', '|', 'bold', 'italic', '|',
                        'link', 'insertTable', '|',
                        'bulletedList', 'numberedList', '|', 'undo', 'redo'
                    ],
                    language: 'vi',
                    htmlSupport: { allow: [{ name: /.*/, attributes: true, classes: true, styles: true }] }
                }).then(editor => {
                    tuLuanEditor = editor;
                    // Lắng nghe form submit để đồng bộ dữ liệu CKEditor
                    const form = document.querySelector('form');
                    if (form) {
                        form.addEventListener('submit', () => {
                            if (loaiCauHoiSelect.value == '4' && tuLuanEditor) {
                                document.querySelector('#editor-tu-luan').value = tuLuanEditor.getData();
                            }
                        });
                    }
                }).catch(err => console.error(err));
            }

            function toggleLoaiCauHoi() {
                if (loaiCauHoiSelect.value == '4') { // Tự luận
                    answersOuter.style.display = 'none';
                    tuLuanOuter.style.display = 'block';
                    
                    // Vô hiệu hóa các input trong answersOuter để không gửi lên server
                    const inputs = answersOuter.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => input.disabled = true);
                    
                    // Kích hoạt các input trong tuLuanOuter
                    const tlInputs = tuLuanOuter.querySelectorAll('input, textarea');
                    tlInputs.forEach(input => input.disabled = false);

                } else {
                    answersOuter.style.display = 'block';
                    tuLuanOuter.style.display = 'none';
                    
                    // Kích hoạt lại các input trong answersOuter
                    const inputs = answersOuter.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => input.disabled = false);
                    
                    // Vô hiệu hóa các input trong tuLuanOuter
                    const tlInputs = tuLuanOuter.querySelectorAll('input, textarea');
                    tlInputs.forEach(input => input.disabled = true);
                }
            }
            
            loaiCauHoiSelect.addEventListener('change', toggleLoaiCauHoi);
            
            // Khởi tạo trạng thái ban đầu
            setTimeout(toggleLoaiCauHoi, 500); 
        }
    });
</script>
