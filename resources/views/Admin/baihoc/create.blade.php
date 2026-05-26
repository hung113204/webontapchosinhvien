@extends('Admin.layouts.admin')
@section('title', 'Thêm bài học mới')
@section('header_action')
    <a href="{{ route('admin.baihoc.index') }}" class="btn btn-secondary"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background: white; border: 1px solid #e2e8f0; border-radius: 8px; color: #475569; text-decoration: none; font-weight: 500; font-size: 14px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px">
            <polyline points="15 18 9 12 15 6" />
        </svg>
        Quay lại
    </a>
@endsection
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/baihoc-form.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        {{-- ===== HEADER ===== --}}

        {{-- ===== VALIDATION ERRORS ===== --}}
        {{--  @if ($errors->any())
        <div class="bf-alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif --}}

        {{-- ===== FORM ===== --}}
        <form action="{{ route('admin.baihoc.storeOrUpdate') }}" method="POST" enctype="multipart/form-data"
            id="baiHocForm" novalidate>
            @csrf

            <div class="bf-form-grid">

                {{-- ================== CỘT TRÁI ================== --}}
                <div class="bf-col-left">

                    {{-- NỘI DUNG LÝ THUYẾT --}}
                    <div class="bf-card">
                        <h4 class="bf-card-title">
                            <i class="fas fa-book-open" style="color: var(--primary-dark);"></i>
                            Nội dung lý thuyết
                        </h4>

                        <div class="bf-form-group">
                            <label class="bf-label">
                                Tên bài học <span class="required">*</span>
                            </label>
                            <input type="text" name="ten_bai_hoc"
                                class="bf-input {{ $errors->has('ten_bai_hoc') ? 'is-invalid' : '' }}"
                                value="{{ old('ten_bai_hoc') }}" required placeholder="Ví dụ: Giới thiệu ngôn ngữ C++...">
                            {{-- @error('ten_bai_hoc')
                            <div class="bf-error-message" style="color: red">{{ $message }}</div>
                        @enderror --}}
                            @error('ten_bai_hoc')
                                <div class="bf-error-message" style="color: red">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Nội dung chi tiết</label>
                            <textarea name="noi_dung_ly_thuyet" id="editor-bai-hoc" class="bf-textarea">{{ old('noi_dung_ly_thuyet') }}</textarea>
                        </div>
                    </div>

                    {{-- CODE MẪU --}}
                    <div class="bf-card bf-card--code">
                        <h4 class="bf-card-title">
                            <i class="fas fa-code" style="color: var(--success);"></i>
                            Khu vực Code mẫu
                        </h4>

                        <div class="bf-grid-2">
                            <div class="bf-form-group">
                                <label class="bf-label">Tên file code</label>
                                <input type="text" name="ten_file_code"
                                    class="bf-input {{ $errors->has('ten_file_code') ? 'is-invalid' : '' }}"
                                    value="{{ old('ten_file_code') }}" placeholder="VD: hello.cpp">
                                @error('ten_file_code')
                                    <div class="bf-error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="bf-form-group">
                                <label class="bf-label">Ngôn ngữ lập trình</label>
                                <select name="ngon_ngu_code"
                                    class="bf-select {{ $errors->has('ngon_ngu_code') ? 'is-invalid' : '' }}">
                                    <option value="">-- Không có code --</option>
                                    <option value="cpp" {{ old('ngon_ngu_code') == 'cpp' ? 'selected' : '' }}>C / C++
                                    </option>
                                    <option value="php" {{ old('ngon_ngu_code') == 'php' ? 'selected' : '' }}>PHP
                                    </option>
                                    <option value="javascript"
                                        {{ old('ngon_ngu_code') == 'javascript' ? 'selected' : '' }}>JavaScript</option>
                                    <option value="html" {{ old('ngon_ngu_code') == 'html' ? 'selected' : '' }}>HTML/CSS
                                    </option>
                                    <option value="java" {{ old('ngon_ngu_code') == 'java' ? 'selected' : '' }}>Java
                                    </option>
                                    <option value="python" {{ old('ngon_ngu_code') == 'python' ? 'selected' : '' }}>Python
                                    </option>
                                    <option value="sql" {{ old('ngon_ngu_code') == 'sql' ? 'selected' : '' }}>SQL
                                    </option>
                                </select>
                                @error('ngon_ngu_code')
                                    <div class="bf-error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Đoạn code mẫu</label>
                            <textarea name="ma_nguon_mau" rows="8" class="bf-textarea bf-textarea--code"
                                placeholder="// Nhập code vào đây...">{{ old('ma_nguon_mau') }}</textarea>
                        </div>

                        {{-- Giải thích code --}}
                        <div class="bf-form-group">
                            <div class="bf-explain-header">
                                <label class="bf-label" style="margin: 0;">Giải thích từng dòng code</label>
                                <button type="button" id="btnAddExplain" class="bf-btn-add">
                                    <i class="fas fa-plus"></i> Thêm dòng
                                </button>
                            </div>

                            <div id="explainContainer" class="bf-explain-container">
                                @php
                                    $oldExplains = old('giai_thich_code', []);
                                    if (is_string($oldExplains)) {
                                        $oldExplains = json_decode($oldExplains, true) ?? [];
                                    }
                                    if (!is_array($oldExplains)) {
                                        $oldExplains = [];
                                    }
                                @endphp

                                @foreach ($oldExplains as $index => $item)
                                    <div class="bf-explain-row">
                                        <input type="number" name="giai_thich_code[{{ $index }}][dong]"
                                            value="{{ $item['dong'] ?? '' }}" placeholder="Dòng"
                                            class="bf-input bf-input--line-num">
                                        <input type="text" name="giai_thich_code[{{ $index }}][giai_thich]"
                                            value="{{ $item['giai_thich'] ?? '' }}" placeholder="Nội dung giải thích..."
                                            class="bf-input">
                                        <button type="button" class="bf-btn-remove" title="Xóa dòng này">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach

                                @if (count($oldExplains) == 0)
                                    <div class="bf-explain-row">
                                        <input type="number" name="giai_thich_code[0][dong]" placeholder="Dòng"
                                            class="bf-input bf-input--line-num">
                                        <input type="text" name="giai_thich_code[0][giai_thich]"
                                            placeholder="Nội dung giải thích..." class="bf-input">
                                        <button type="button" class="bf-btn-remove" title="Xóa dòng này">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ================== CỘT PHẢI ================== --}}
                <div class="bf-col-right">
                    <div class="bf-card">
                        <h4 class="bf-card-title">
                            <i class="fas fa-cog" style="color: var(--gray-500);"></i>
                            Cấu hình bài học
                        </h4>

                        <div class="bf-form-group">
                            <label class="bf-label">
                                Thuộc Chương / Môn <span class="required">*</span>
                            </label>
                            <select name="chuong_hoc_id"
                                class="bf-select {{ $errors->has('chuong_hoc_id') ? 'is-invalid' : '' }}" required>
                                <option value="">-- Chọn Chương học --</option>
                                @foreach ($monHocs as $mon)
                                    <optgroup label="Môn: {{ $mon->ten_mon_hoc }}">
                                        @foreach ($mon->chuongHocs as $chuong)
                                            <option value="{{ $chuong->id }}"
                                                {{ old('chuong_hoc_id') == $chuong->id ? 'selected' : '' }}>
                                                Chương {{ $chuong->thu_tu }}: {{ $chuong->ten_chuong }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @error('chuong_hoc_id')
                                <div class="bf-error-message" style="color: red">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Link Video (Tùy chọn)</label>
                            <input type="text" name="video_url"
                                class="bf-input {{ $errors->has('video_url') ? 'is-invalid' : '' }}"
                                value="{{ old('video_url') }}" placeholder="URL Youtube / Vimeo...">
                            @error('video_url')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Tài liệu đính kèm (Có thể chọn nhiều file)</label>
                            <input type="file" name="tai_lieu_dinh_kem[]" multiple
                                class="bf-input {{ $errors->has('tai_lieu_dinh_kem') || $errors->has('tai_lieu_dinh_kem.*') ? 'is-invalid' : '' }}"
                                style="padding: 7px;">
                            @error('tai_lieu_dinh_kem')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                            @error('tai_lieu_dinh_kem.*')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu"
                                class="bf-input {{ $errors->has('thu_tu') ? 'is-invalid' : '' }}"
                                value="{{ old('thu_tu', 0) }}" min="0">
                            @error('thu_tu')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="bf-form-group">
                            <label class="bf-label">Trạng thái xuất bản</label>
                            <div class="bf-radio-group">
                                <label class="bf-radio-label bf-radio-label--active">
                                    <input type="radio" name="trang_thai" value="1" checked> Đang hoạt động
                                </label>
                                <label class="bf-radio-label bf-radio-label--hidden">
                                    <input type="radio" name="trang_thai" value="0"> Tạm ẩn
                                </label>
                            </div>
                            @error('trang_thai')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="bf-btn-submit">
                            <i class="fas fa-save"></i> THÊM BÀI HỌC MỚI
                        </button>

                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        // Load CKEditor nếu chưa load
        (function() {
            if (typeof ClassicEditor === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js';
                script.async = false;
                document.head.appendChild(script);
            }
        })();
    </script>

    <script>
        function processLatexBaiHoc(content) {
            if (!content) return content;
            let processed = content;
            processed = processed.replace(/\\begin\{cases\}([\s\S]*?)\\end\{cases\}/g,
            '\\[\\begin{cases}$1\\end{cases}\\]');
            processed = processed.replace(/\\\\/g, '\\\\');
            return processed;
        }

        window.addEventListener('load', function() {

            const editorConfig = {
                toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', 'insertTable', '|', 'bulletedList',
                    'numberedList', '|', 'undo', 'redo'
                ],
                language: 'vi',
                htmlSupport: {
                    allow: [{
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }]
                }
            };

            ClassicEditor
                .create(document.querySelector('#editor-bai-hoc'), editorConfig)
                .then(editor => {

                    editor.editing.view.document.on('clipboardInput', (evt, data) => {
                        const html = data.dataTransfer.getData('text/html') || data.dataTransfer
                            .getData('text/plain');
                        if (html) {
                            data.dataTransfer.setData('text/html', processLatexBaiHoc(html));
                        }
                    });

                    editor.model.document.on('change:data', () => {
                        setTimeout(() => window.MathJax?.typesetPromise?.(), 100);
                    });

                    document.getElementById('baiHocForm').addEventListener('submit', () => {
                        let content = editor.getData();
                        document.querySelector('#editor-bai-hoc').value = processLatexBaiHoc(content);
                    });
                })
                .catch(err => console.error('CKEditor Error:', err));

            // Thêm / Xóa giải thích code
            const btnAdd = document.getElementById('btnAddExplain');
            const container = document.getElementById('explainContainer');

            if (btnAdd && container) {
                let index = container.querySelectorAll('.bf-explain-row').length;

                btnAdd.addEventListener('click', () => {
                    const row = document.createElement('div');
                    row.className = 'bf-explain-row';
                    row.innerHTML = `
                    <input type="number" name="giai_thich_code[${index}][dong]" placeholder="Dòng" class="bf-input bf-input--line-num">
                    <input type="text" name="giai_thich_code[${index}][giai_thich]" placeholder="Nội dung giải thích..." class="bf-input">
                    <button type="button" class="bf-btn-remove" title="Xóa dòng này"><i class="fas fa-trash"></i></button>
                `;
                    container.appendChild(row);
                    index++;
                });

                container.addEventListener('click', e => {
                    if (e.target.closest('.bf-btn-remove')) {
                        e.target.closest('.bf-explain-row').remove();
                    }
                });
            }
        });
    </script>
@endpush
