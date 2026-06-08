@extends('Admin.layouts.admin')
@section('title', 'Chỉnh sửa câu hỏi')

@section('header_action')
    <a href="{{ route('admin.cauhoi.index') }}" class="btn btn-secondary"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-weight: 500; font-size: 14px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Quay lại
    </a>
@endsection

@section('content')
    <style>
        .answer-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 15px;
            position: relative;
        }

        .ck-editor__editable {
            min-height: 120px;
        }

        .btn-delete-ans {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .current-image-preview {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: inline-block;
        }
    </style>

    <div class="form-section" style="padding: 24px 32px;">
        @if ($errors->any())
            <div class="alert alert-error"
                style="background: #fee2e2; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="table-card"
            style="background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; padding: 25px;">

            <h2
                style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 25px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">
                CẬP NHẬT CÂU HỎI #{{ $cauHoi->id }}
            </h2>

            <form action="{{ route('admin.cauhoi.save', $cauHoi->id) }}" method="POST" enctype="multipart/form-data"
                id="edit-question-form" novalidate>
                @csrf
                <div class="form-grid"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Môn học <span
                                style="color:red">*</span></label>
                        <select class="form-select" name="mon_hoc_id" id="mon_hoc_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            @foreach ($dsMonHoc as $mon)
                                <option value="{{ $mon->id }}"
                                    {{ $cauHoi->chuongHoc->mon_hoc_id == $mon->id ? 'selected' : '' }}>
                                    {{ $mon->ten_mon_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Chương học
                            <span style="color:red">*</span></label>
                        <select class="form-select" name="chuong_hoc_id" id="chuong_hoc_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            @foreach ($dsChuong as $chuong)
                                <option value="{{ $chuong->id }}" data-mon="{{ $chuong->mon_hoc_id }}"
                                    {{ $cauHoi->chuong_hoc_id == $chuong->id ? 'selected' : '' }}>
                                    {{ $chuong->ten_chuong }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            Thuộc Bài học (Tùy chọn)
                        </label>
                        <select class="form-select" name="bai_hoc_id" id="bai_hoc_id"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="">-- Dùng chung cho Chương --</option>
                            @foreach ($dsBaiHoc as $bai)
                                <option value="{{ $bai->id }}" data-chuong="{{ $bai->chuong_hoc_id }}"
                                    {{ isset($cauHoi) && $cauHoi->bai_hoc_id == $bai->id ? 'selected' : '' }}>
                                    {{ $bai->thu_tu }}: {{ $bai->ten_bai_hoc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Mức độ</label>
                        <select class="form-select" name="muc_do"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="1" {{ $cauHoi->muc_do == 1 ? 'selected' : '' }}>Nhận biết</option>
                            <option value="2" {{ $cauHoi->muc_do == 2 ? 'selected' : '' }}>Thông hiểu</option>
                            <option value="3" {{ $cauHoi->muc_do == 3 ? 'selected' : '' }}>Vận dụng</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Loại câu
                            hỏi</label>
                        <select class="form-select" name="loai_cau_hoi"
                            style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
                            <option value="1" {{ $cauHoi->loai_cau_hoi == 1 ? 'selected' : '' }}>Trắc nghiệm</option>
                            <option value="2" {{ $cauHoi->loai_cau_hoi == 2 ? 'selected' : '' }}>Đúng/Sai</option>
                            <option value="3" {{ $cauHoi->loai_cau_hoi == 3 ? 'selected' : '' }}>Điền khuyết</option>
                            <option value="4" {{ $cauHoi->loai_cau_hoi == 4 ? 'selected' : '' }}>Tự luận</option>
                        </select>
                    </div>
                </div>

                @php
                    $hasCode = strpos($cauHoi->noi_dung, '<pre') !== false || strpos($cauHoi->noi_dung, '<code') !== false;
                @endphp
                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">
                        Nội dung câu hỏi <span style="color:red">*</span>
                        @if($hasCode)
                            <span style="font-size: 13px; color: #d97706; font-weight: normal; margin-left: 10px; background: #fefce8; padding: 4px 8px; border-radius: 4px; border: 1px solid #fde68a;">⚠️ Chế độ mã nguồn (HTML) được bật để bảo toàn Code</span>
                        @endif
                    </label>
                    <textarea id="editor-cau-hoi" name="noi_dung" class="{{ $hasCode ? 'raw-html-editor' : '' }}" @if($hasCode) style="width: 100%; min-height: 250px; padding: 15px; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; color: #1e293b; line-height: 1.5; font-size: 14px;" @endif>{!! $cauHoi->noi_dung !!}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Hình ảnh đính
                        kèm</label>
                    <input type="file" name="hinh_anh" class="form-input" accept="image/*">
                    @if ($cauHoi->hinh_anh)
                        <div class="current-image-preview">
                            <p style="font-size: 12px; color: #64748b; margin-bottom: 5px;">Hình ảnh hiện tại:</p>
                            <img src="{{ asset('storage/' . $cauHoi->hinh_anh) }}" alt="Câu hỏi"
                                style="max-height: 150px; border-radius: 4px;">
                        </div>
                    @endif
                </div>

                <div class="answers-outer" style="margin-top: 40px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">DANH SÁCH ĐÁP ÁN</h3>
                        <button type="button" class="btn btn-secondary" onclick="addAnswer()"
                            style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; color: #4f46e5; cursor: pointer;">+
                            Thêm đáp án</button>
                    </div>
                    <div id="answers-container">
                        {{-- Đối với các đáp án cũ (Existing) --}}
                        @foreach ($cauHoi->dapAns as $index => $dapAn)
                            <div class="answer-item" id="container_ans_existing_{{ $index }}">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                        {{-- value phải là ID của mảng đáp án gửi lên --}}
                                        <input type="radio" name="dap_an_dung" value="{{ $index }}"
                                            {{ $dapAn->is_dung ? 'checked' : '' }}>
                                        Đáp án đúng {{ $index + 1 }}
                                    </label>
                                    <button type="button" class="btn-delete-ans"
                                        onclick="removeAnswer('ans_existing_{{ $index }}')">Xóa</button>
                                </div>
                                @php
                                    $ansHasCode = strpos($dapAn->noi_dung, '<pre') !== false || strpos($dapAn->noi_dung, '<code') !== false;
                                @endphp
                                @if($ansHasCode)
                                    <div style="font-size: 12px; color: #d97706; margin-bottom: 5px;">⚠️ Mã nguồn HTML</div>
                                @endif
                                <textarea name="dap_ans[{{ $index }}][noi_dung]" id="editor_ans_existing_{{ $index }}" class="{{ $ansHasCode ? 'raw-html-editor' : '' }}" @if($ansHasCode) style="width: 100%; min-height: 100px; padding: 12px; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; color: #1e293b; line-height: 1.5;" @endif>{!! $dapAn->noi_dung !!}</textarea>
                                <input type="hidden" name="dap_ans[{{ $index }}][id]"
                                    value="{{ $dapAn->id }}">
                                <div style="margin-top: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px dashed #cbd5e1;">
                                    <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #475569; font-weight: 500; cursor: pointer;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                            <circle cx="8.5" cy="8.5" r="1.5"/>
                                            <polyline points="21 15 16 10 5 21"/>
                                        </svg>
                                        Đính kèm hình ảnh mới (tùy chọn)
                                        <input type="file" name="dap_ans[{{ $index }}][hinh_anh]" accept="image/*" style="margin-left: 10px; font-size: 12px;">
                                    </label>
                                    @if ($dapAn->hinh_anh)
                                        <div style="margin-top: 8px;">
                                            <p style="font-size: 11px; color: #64748b; margin-bottom: 4px;">Hình ảnh hiện tại:</p>
                                            <img src="{{ asset('storage/' . $dapAn->hinh_anh) }}" style="max-height: 80px; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group" style="margin-top: 30px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #374151;">Giải thích đáp
                        án</label>
                    <textarea id="editor-giai-thich" name="giai_thich">{!! $cauHoi->giai_thich !!}</textarea>
                </div>

                <div class="form-actions"
                    style="margin-top: 30px; display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 20px; gap: 15px;">
                    <a href="{{ route('admin.cauhoi.index', ['chuong_hoc_id' => $cauHoi->chuong_hoc_id]) }}" class="btn btn-secondary"
                        style="padding: 12px 24px; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; background: white; border: 1px solid #cbd5e1; color: #475569; font-weight: 500;">Hủy thay đổi</a>
                    <button type="submit" class="btn btn-primary"
                        style="padding: 12px 48px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cập
                        nhật câu hỏi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editors = {};
        // Khởi tạo count dựa trên số lượng đáp án hiện có
        let answerCount = {{ $cauHoi->dapAns->count() }};

        const editorConfig = {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'codeBlock', '|',
                'link', 'insertTable', '|',
                'bulletedList', 'numberedList', '|',
                'undo', 'redo'
            ],
            language: 'vi',
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        };

        const answerEditorConfig = {
            toolbar: ['bold', 'italic', '|', 'undo', 'redo'],
            language: 'vi',
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            }
        };

        function initExistingEditors() {
            // Chính câu hỏi
            const qEditor = document.querySelector('#editor-cau-hoi');
            if (!qEditor.classList.contains('raw-html-editor')) {
                ClassicEditor.create(qEditor, editorConfig).then(editor => {
                    editors['main-question'] = editor;
                });
            }
            // Giải thích
            ClassicEditor.create(document.querySelector('#editor-giai-thich'), editorConfig).then(editor => {
                editors['main-explain'] = editor;
            });
            // Các đáp án hiện có
            @foreach ($cauHoi->dapAns as $index => $dapAn)
                const aEditor_{{ $index }} = document.querySelector('#editor_ans_existing_{{ $index }}');
                if (!aEditor_{{ $index }}.classList.contains('raw-html-editor')) {
                    ClassicEditor.create(aEditor_{{ $index }}, answerEditorConfig).then(editor => {
                        editors['ans_existing_{{ $index }}'] = editor;
                    });
                }
            @endforeach
        }

        function addAnswer() {
            // answerCount đã được khởi tạo bằng $cauHoi->dapAns->count() ở đầu script
            const index = answerCount;
            answerCount++;

            const id = `ans_new_${index}`;
            const html = `
    <div class="answer-item" id="container_${id}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="radio" name="dap_an_dung" value="${index}"> Đáp án mới (${answerCount})
            </label>
            <button type="button" class="btn-delete-ans" onclick="removeAnswer('${id}')">Xóa</button>
        </div>
        <textarea name="dap_ans[${index}][noi_dung]" id="editor_${id}"></textarea>
        <div style="margin-top: 12px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px dashed #cbd5e1;">
            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: #475569; font-weight: 500; cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                Đính kèm hình ảnh (tùy chọn)
                <input type="file" name="dap_ans[${index}][hinh_anh]" accept="image/*" style="margin-left: 10px; font-size: 12px;">
            </label>
        </div>
    </div>`;

            document.getElementById('answers-container').insertAdjacentHTML('beforeend', html);
            ClassicEditor.create(document.querySelector(`#editor_${id}`), answerEditorConfig).then(editor => {
                editors[id] = editor;
            });
        }

        function removeAnswer(id) {
            if (confirm('Bạn có chắc muốn xóa đáp án này?')) {
                document.getElementById(`container_${id}`).remove();
                delete editors[id];
            }
        }

        // Đồng bộ dữ liệu CKEditor trước khi submit
        document.getElementById('edit-question-form').addEventListener('submit', function(e) {
            // Update nội dung chính
            if (editors['main-question']) document.getElementById('editor-cau-hoi').value = editors['main-question']
                .getData();
            if (editors['main-explain']) document.getElementById('editor-giai-thich').value = editors[
                'main-explain'].getData();

            // Update tất cả các editor đáp án
            Object.keys(editors).forEach(key => {
                if (key.startsWith('ans_')) {
                    const textarea = document.getElementById('editor_' + key);
                    if (textarea) textarea.value = editors[key].getData();
                }
            });
        });

        // Logic lọc chương học theo môn học
        const monSelect = document.getElementById('mon_hoc_id');
        const chuongSelect = document.getElementById('chuong_hoc_id');
        const baiSelect = document.getElementById('bai_hoc_id');

        function filterChuongHoc() {
            const monId = monSelect.value;
            let firstVisible = null;
            let currentVal = chuongSelect.value;
            let valueExists = false;

            chuongSelect.querySelectorAll('option').forEach(opt => {
                if (opt.value === "") return; // Bỏ qua option mặc định nếu có
                const isMatch = opt.getAttribute('data-mon') == monId;
                opt.style.display = isMatch ? "block" : "none";
                
                if (isMatch) {
                    if (!firstVisible) firstVisible = opt.value;
                    if (opt.value == currentVal) valueExists = true;
                }
            });

            if (!valueExists && firstVisible) {
                chuongSelect.value = firstVisible;
            }
            filterBaiHoc(); // Cập nhật luôn bài học
        }

        function filterBaiHoc() {
            const chuongId = chuongSelect.value;
            let currentVal = baiSelect.value;
            let valueExists = false;

            baiSelect.querySelectorAll('option').forEach(opt => {
                if (opt.value === "") return; // Option "-- Dùng chung cho Chương --"
                const isMatch = opt.getAttribute('data-chuong') == chuongId;
                opt.style.display = isMatch ? "block" : "none";

                if (isMatch && opt.value == currentVal) {
                    valueExists = true;
                }
            });

            // Nếu giá trị hiện tại không thuộc chương này thì reset về mặc định
            if (currentVal !== "" && !valueExists) {
                baiSelect.value = "";
            }
        }

        monSelect.addEventListener('change', filterChuongHoc);
        chuongSelect.addEventListener('change', filterBaiHoc);

        window.onload = function() {
            initExistingEditors();
            filterChuongHoc();
        };
    </script>
    @include('Admin.cauhoi.dien_khuyet_script')
    @include('Admin.cauhoi.tu_luan_script')
@endsection
