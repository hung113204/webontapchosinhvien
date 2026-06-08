@extends('Admin.layouts.admin')
@section('title', 'Chỉnh sửa bài học')

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

    {{-- ===== VALIDATION ERRORS ===== --}}
    @if ($errors->any())
        <div class="bf-alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== FORM ===== --}}
    <form action="{{ route('admin.baihoc.storeOrUpdate') }}" 
          method="POST"
          enctype="multipart/form-data"
          id="baiHocForm">
        @csrf
        <input type="hidden" name="id" value="{{ $baiHoc->id }}" />

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
                        <input type="text"
                               name="ten_bai_hoc"
                               class="bf-input {{ $errors->has('ten_bai_hoc') ? 'is-invalid' : '' }}"
                               value="{{ old('ten_bai_hoc', $baiHoc->ten_bai_hoc ?? '') }}"
                               required
                               placeholder="Ví dụ: Giới thiệu ngôn ngữ C++...">
                        @error('ten_bai_hoc')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bf-form-group">
                        <label class="bf-label">Nội dung chi tiết</label>
                        <textarea name="noi_dung_ly_thuyet"
                                  id="editor-bai-hoc"
                                  class="bf-textarea">{{ old('noi_dung_ly_thuyet', $baiHoc->noi_dung_ly_thuyet ?? '') }}</textarea>
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
                            <input type="text"
                                   name="ten_file_code"
                                   class="bf-input {{ $errors->has('ten_file_code') ? 'is-invalid' : '' }}"
                                   value="{{ old('ten_file_code', $baiHoc->ten_file_code ?? '') }}"
                                   placeholder="VD: hello.cpp">
                            @error('ten_file_code')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="bf-form-group">
                            <label class="bf-label">Ngôn ngữ lập trình</label>
                            <select name="ngon_ngu_code" class="bf-select {{ $errors->has('ngon_ngu_code') ? 'is-invalid' : '' }}">
                                <option value="">-- Không có code --</option>
                                @php $selectedLang = old('ngon_ngu_code', $baiHoc->ngon_ngu_code ?? ''); @endphp
                                <option value="cpp"        {{ $selectedLang == 'cpp' ? 'selected' : '' }}>C / C++</option>
                                <option value="php"        {{ $selectedLang == 'php' ? 'selected' : '' }}>PHP</option>
                                <option value="javascript" {{ $selectedLang == 'javascript' ? 'selected' : '' }}>JavaScript</option>
                                <option value="html"       {{ $selectedLang == 'html' ? 'selected' : '' }}>HTML/CSS</option>
                                <option value="java"       {{ $selectedLang == 'java' ? 'selected' : '' }}>Java</option>
                                <option value="python"     {{ $selectedLang == 'python' ? 'selected' : '' }}>Python</option>
                                <option value="sql"        {{ $selectedLang == 'sql' ? 'selected' : '' }}>SQL</option>
                            </select>
                            @error('ngon_ngu_code')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="bf-form-group">
                        <label class="bf-label">Đoạn code mẫu</label>
                        <textarea name="ma_nguon_mau"
                                  rows="8"
                                  class="bf-textarea bf-textarea--code"
                                  placeholder="// Nhập code vào đây...">{{ old('ma_nguon_mau', $baiHoc->ma_nguon_mau ?? '') }}</textarea>
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
                                $oldExplains = old('giai_thich_code', $baiHoc->giai_thich_code ?? []);
                                if (is_string($oldExplains)) {
                                    $oldExplains = json_decode($oldExplains, true) ?? [];
                                }
                                if (!is_array($oldExplains)) {
                                    $oldExplains = [];
                                }
                            @endphp

                            @foreach($oldExplains as $index => $item)
                                <div class="bf-explain-row">
                                    <input type="number" 
                                           name="giai_thich_code[{{ $index }}][dong]" 
                                           value="{{ $item['dong'] ?? '' }}" 
                                           placeholder="Dòng" 
                                           class="bf-input bf-input--line-num">
                                    <input type="text" 
                                           name="giai_thich_code[{{ $index }}][giai_thich]" 
                                           value="{{ $item['giai_thich'] ?? '' }}" 
                                           placeholder="Nội dung giải thích..." 
                                           class="bf-input">
                                    <button type="button" class="bf-btn-remove" title="Xóa dòng này">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach

                            @if(count($oldExplains) == 0)
                                <div class="bf-explain-row">
                                    <input type="number" name="giai_thich_code[0][dong]" placeholder="Dòng" class="bf-input bf-input--line-num">
                                    <input type="text" name="giai_thich_code[0][giai_thich]" placeholder="Nội dung giải thích..." class="bf-input">
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
                        <select name="chuong_hoc_id" class="bf-select {{ $errors->has('chuong_hoc_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Chọn Chương học --</option>
                            @foreach($monHocs as $mon)
                                <optgroup label="Môn: {{ $mon->ten_mon_hoc }}">
                                    @foreach($mon->chuongHocs as $chuong)
                                        <option value="{{ $chuong->id }}"
                                            {{ old('chuong_hoc_id', $baiHoc->chuong_hoc_id ?? '') == $chuong->id ? 'selected' : '' }}>
                                            {{ $chuong->ten_chuong }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('chuong_hoc_id')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    @php
                        $isVideoFile = false;
                        if (old('video_source') === 'file') {
                            $isVideoFile = true;
                        } elseif (old('video_source') === 'url') {
                            $isVideoFile = false;
                        } elseif (isset($baiHoc) && !empty($baiHoc->video_url)) {
                            $isVideoFile = !str_contains($baiHoc->video_url, 'youtube.com') && !str_contains($baiHoc->video_url, 'youtu.be') && !str_contains($baiHoc->video_url, 'vimeo.com');
                        }
                    @endphp

                    <div class="bf-form-group">
                        <label class="bf-label">Video bài học (Tùy chọn)</label>
                        
                        <div style="margin-bottom: 10px; display: flex; gap: 15px;">
                            <label style="font-weight: 500; font-size: 0.85rem; color: #475569; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="radio" name="video_source" value="url" id="video_source_url" {{ !$isVideoFile ? 'checked' : '' }} onclick="toggleVideoInput('url')"> Link Video (YouTube / Vimeo)
                            </label>
                            <label style="font-weight: 500; font-size: 0.85rem; color: #475569; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                <input type="radio" name="video_source" value="file" id="video_source_file" {{ $isVideoFile ? 'checked' : '' }} onclick="toggleVideoInput('file')"> Tải lên file video
                            </label>
                        </div>

                        <div id="video_url_container" style="{{ !$isVideoFile ? 'display: block;' : 'display: none;' }}">
                            <input type="text" name="video_url" id="video_url_input"
                                   class="bf-input {{ $errors->has('video_url') ? 'is-invalid' : '' }}"
                                   value="{{ !$isVideoFile ? old('video_url', $baiHoc->video_url ?? '') : '' }}"
                                   placeholder="URL Youtube, Vimeo hoặc link video trực tiếp...">
                            @error('video_url')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="video_file_container" style="{{ $isVideoFile ? 'display: block;' : 'display: none;' }} margin-top: 5px;">
                            <input type="file" name="video_file" id="video_file_input" accept="video/*"
                                   class="bf-input {{ $errors->has('video_file') ? 'is-invalid' : '' }}"
                                   style="padding: 7px;">
                            @if(isset($baiHoc) && !empty($baiHoc->video_url) && $isVideoFile)
                                <div class="bf-file-existing" style="margin-top: 8px; font-size: 0.85rem; color: #10b981; display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-play-circle" style="color: #3b82f6;"></i> Video hiện tại: 
                                    <a href="{{ asset('storage/' . $baiHoc->video_url) }}" target="_blank" style="color: var(--blue); text-decoration: underline;">{{ basename($baiHoc->video_url) }}</a>
                                </div>
                            @endif
                            <small style="font-size: 0.8rem; color: #64748b; display: block; margin-top: 4px;">Hỗ trợ định dạng: mp4, webm, ogg, avi, mov. Dung lượng tối đa: 100MB.</small>
                            @error('video_file')
                                <div class="bf-error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="bf-form-group">
                        <label class="bf-label">Tài liệu đính kèm (Có thể chọn nhiều file)</label>
                        <input type="file"
                               name="tai_lieu_dinh_kem[]" multiple
                               class="bf-input {{ $errors->has('tai_lieu_dinh_kem') || $errors->has('tai_lieu_dinh_kem.*') ? 'is-invalid' : '' }}"
                               style="padding: 7px;">
                        @if(isset($baiHoc) && !empty($baiHoc->tai_lieu_dinh_kem))
                            @php
                                $oldFiles = is_array($baiHoc->tai_lieu_dinh_kem) 
                                    ? $baiHoc->tai_lieu_dinh_kem 
                                    : (is_string($baiHoc->tai_lieu_dinh_kem) && str_starts_with(trim($baiHoc->tai_lieu_dinh_kem), '[') 
                                        ? json_decode($baiHoc->tai_lieu_dinh_kem, true) 
                                        : [$baiHoc->tai_lieu_dinh_kem]);
                            @endphp
                            @if (is_array($oldFiles) && count($oldFiles) > 0)
                                <div class="bf-file-existing" style="margin-top: 8px;">
                                    <div style="font-weight: 500; margin-bottom: 5px;"><i class="fas fa-check-circle" style="color: #10b981;"></i> Đã có {{ count($oldFiles) }} file đính kèm:</div>
                                    <ul style="padding-left: 20px; margin: 0; font-size: 0.85rem;">
                                        @foreach($oldFiles as $f)
                                            <li><a href="{{ asset('storage/' . $f) }}" target="_blank" style="color: var(--blue);">{{ basename($f) }}</a></li>
                                        @endforeach
                                    </ul>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">* Upload file mới sẽ thay thế toàn bộ file cũ hiện tại.</div>
                                </div>
                            @endif
                        @endif
                        @error('tai_lieu_dinh_kem')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                        @error('tai_lieu_dinh_kem.*')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bf-form-group">
                        <label class="bf-label">Thứ tự hiển thị</label>
                        <input type="number"
                               name="thu_tu"
                               class="bf-input {{ $errors->has('thu_tu') ? 'is-invalid' : '' }}"
                               value="{{ old('thu_tu', $baiHoc->thu_tu ?? 0) }}"
                               min="0">
                        @error('thu_tu')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="bf-form-group">
                        <label class="bf-label">Trạng thái xuất bản</label>
                        <div class="bf-radio-group">
                            <label class="bf-radio-label bf-radio-label--active">
                                <input type="radio" 
                                       name="trang_thai" 
                                       value="1"
                                       {{ old('trang_thai', $baiHoc->trang_thai ?? 1) == 1 ? 'checked' : '' }}>
                                Đang hoạt động
                            </label>
                            <label class="bf-radio-label bf-radio-label--hidden">
                                <input type="radio" 
                                       name="trang_thai" 
                                       value="0"
                                       {{ old('trang_thai', $baiHoc->trang_thai ?? 1) == 0 ? 'checked' : '' }}>
                                Tạm ẩn
                            </label>
                        </div>
                        @error('trang_thai')
                            <div class="bf-error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="bf-btn-submit">
                        <i class="fas fa-save"></i> CẬP NHẬT BÀI HỌC
                    </button>

                </div>
            </div>

        </div>
    </form>

    {{-- PHẦN QUẢN LÝ CÂU HỎI CỦA BÀI HỌC --}}
    <hr style="border-top: 1px solid #e2e8f0; margin: 30px 0;">
    
    <div class="bf-card" style="margin-top: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4 class="bf-card-title" style="margin: 0;">
                <i class="fas fa-question-circle" style="color: var(--warning);"></i>
                Danh sách câu hỏi luyện tập của bài học
            </h4>
            <button type="button" class="btn btn-primary" onclick="openAddQuestionModal()" style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Thêm câu hỏi từ ngân hàng
            </button>
        </div>

        <div class="table-responsive">
            <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px;">ID</th>
                        <th style="padding: 12px;">Nội dung câu hỏi</th>
                        <th style="padding: 12px;">Mức độ</th>
                        <th style="padding: 12px; width: 100px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($baiHoc->cauHois as $cauHoi)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px;">#{{ $cauHoi->id }}</td>
                            <td style="padding: 12px;">
                                {!! Str::limit(strip_tags($cauHoi->noi_dung), 100) !!}
                            </td>
                            <td style="padding: 12px;">
                                @php
                                    $badges = [1 => 'badge-easy', 2 => 'badge-medium', 3 => 'badge-hard'];
                                    $labels = [1 => 'Nhận biết', 2 => 'Thông hiểu', 3 => 'Vận dụng'];
                                @endphp
                                <span class="badge {{ $badges[$cauHoi->muc_do] ?? 'badge-draft' }}">
                                    {{ $labels[$cauHoi->muc_do] ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <button type="button" class="btn-action btn-delete" style="color: #ef4444; background: none; border: none; cursor: pointer;" title="Gỡ khỏi bài học" onclick="removeQuestion({{ $cauHoi->id }})">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 30px; color: #64748b;">
                                Bài học này chưa có câu hỏi nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- MODAL THÊM CÂU HỎI TỪ NGÂN HÀNG --}}
<div id="modalAddQuestions" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-card" style="background: white; border-radius: 12px; width: 90%; max-width: 800px; max-height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="padding: 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 18px; color: #1e293b;">Chọn câu hỏi từ ngân hàng (Chương: {{ $baiHoc->chuongHoc->ten_chuong ?? '' }})</h3>
            <button type="button" onclick="closeAddQuestionModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px; overflow-y: auto; flex: 1;">
            <div id="loadingQuestions" style="text-align: center; padding: 20px; display: none;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #4f46e5;"></i> Đang tải...
            </div>
            <div id="availableQuestionsList"></div>
        </div>
        <div class="modal-footer" style="padding: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" onclick="closeAddQuestionModal()" style="padding: 10px 20px; border-radius: 6px; border: 1px solid #e2e8f0; background: white; cursor: pointer;">Hủy</button>
            <button type="button" class="btn btn-primary" onclick="assignSelectedQuestions()" style="padding: 10px 20px; border-radius: 6px; border: none; background: #4f46e5; color: white; cursor: pointer;">Thêm vào bài học</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    function processLatexBaiHoc(content) {
        if (!content) return content;
        let processed = content;
        processed = processed.replace(/\\begin\{cases\}([\s\S]*?)\\end\{cases\}/g, '\\[\\begin{cases}$1\\end{cases}\\]');
        processed = processed.replace(/\\\\/g, '\\\\');
        return processed;
    }

    function toggleVideoInput(source) {
        const urlContainer = document.getElementById('video_url_container');
        const fileContainer = document.getElementById('video_file_container');
        const urlInput = document.getElementById('video_url_input');
        const fileInput = document.getElementById('video_file_input');
        
        if (source === 'url') {
            if (urlContainer) urlContainer.style.display = 'block';
            if (fileContainer) fileContainer.style.display = 'none';
            if (fileInput) fileInput.value = '';
        } else {
            if (urlContainer) urlContainer.style.display = 'none';
            if (fileContainer) fileContainer.style.display = 'block';
            if (urlInput) urlInput.value = '';
        }
    }

    window.addEventListener('load', function () {
        // Khởi tạo trạng thái video ban đầu
        const isVideoFile = @json($isVideoFile ?? false);
        if (isVideoFile) {
            const radioFile = document.getElementById('video_source_file');
            if (radioFile) radioFile.checked = true;
            toggleVideoInput('file');
        } else {
            const radioUrl = document.getElementById('video_source_url');
            if (radioUrl) radioUrl.checked = true;
            toggleVideoInput('url');
        }

        tinymce.init({
            selector: '#editor-bai-hoc',
            plugins: 'codesample code lists link table image',
            toolbar: 'blocks | bold italic | alignleft aligncenter alignright alignjustify | codesample code | bullist numlist | link table image | undo redo',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:15px } table { border-collapse: collapse; width: 100%; margin: 10px 0; border: 1px solid #e2e8f0; } table th, table td { border: 1px solid #e2e8f0; padding: 10px; } table th { background-color: #f8fafc; font-weight: bold; } table td img { max-width: 100%; height: auto; display: block; margin: 0 auto; }',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                    setTimeout(() => window.MathJax?.typesetPromise?.(), 100);
                });
            }
        });

        document.getElementById('baiHocForm').addEventListener('submit', () => {
            let content = tinymce.get('editor-bai-hoc').getContent();
            document.querySelector('#editor-bai-hoc').value = processLatexBaiHoc(content);
        });

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

    // --- CÁC HÀM XỬ LÝ GÁN CÂU HỎI ---
    const baiHocId = {{ $baiHoc->id }};
    const chuongHocId = {{ $baiHoc->chuong_hoc_id ?? 'null' }};

    function openAddQuestionModal() {
        document.getElementById('modalAddQuestions').style.display = 'flex';
        document.getElementById('loadingQuestions').style.display = 'block';
        document.getElementById('availableQuestionsList').innerHTML = '';

        fetch(`{{ route('admin.baihoc.api.available-questions') }}?bai_hoc_id=${baiHocId}&chuong_hoc_id=${chuongHocId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('loadingQuestions').style.display = 'none';
                if(data.success) {
                    let html = '';
                    if(data.data.length === 0) {
                        html = '<div style="text-align: center; color: #64748b;">Không có câu hỏi nào trong chương này hoặc tất cả đã được gán.</div>';
                    } else {
                        html = `<table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 10px; width: 50px; text-align: center;"><input type="checkbox" id="selectAllModal" onchange="toggleSelectAllModal(this)"></th>
                                    <th style="padding: 10px; text-align: left;">Nội dung</th>
                                    <th style="padding: 10px; width: 100px; text-align: left;">Mức độ</th>
                                </tr>
                            </thead>
                            <tbody>`;
                        data.data.forEach(q => {
                            let stripText = document.createElement('div');
                            stripText.innerHTML = q.noi_dung;
                            let text = stripText.innerText || stripText.textContent;
                            text = text.length > 80 ? text.substring(0, 80) + '...' : text;
                            
                            html += `
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px; text-align: center;">
                                        <input type="checkbox" class="modal-q-checkbox" value="${q.id}">
                                    </td>
                                    <td style="padding: 10px;">#${q.id} - ${text}</td>
                                    <td style="padding: 10px;">${q.muc_do == 1 ? 'Nhận biết' : (q.muc_do == 2 ? 'Thông hiểu' : 'Vận dụng')}</td>
                                </tr>`;
                        });
                        html += `</tbody></table>`;
                    }
                    document.getElementById('availableQuestionsList').innerHTML = html;
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => {
                document.getElementById('loadingQuestions').style.display = 'none';
                console.error(err);
            });
    }

    function closeAddQuestionModal() {
        document.getElementById('modalAddQuestions').style.display = 'none';
    }

    function toggleSelectAllModal(source) {
        let checkboxes = document.querySelectorAll('.modal-q-checkbox');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function assignSelectedQuestions() {
        let checkboxes = document.querySelectorAll('.modal-q-checkbox:checked');
        let ids = Array.from(checkboxes).map(cb => cb.value);
        if(ids.length === 0) {
            alert('Vui lòng chọn ít nhất 1 câu hỏi.');
            return;
        }

        fetch(`{{ route('admin.baihoc.api.assign-questions') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                bai_hoc_id: baiHocId,
                cau_hoi_ids: ids
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }

    function removeQuestion(cauHoiId) {
        if(!confirm('Bạn có chắc muốn gỡ câu hỏi này khỏi bài học?')) return;

        fetch(`{{ route('admin.baihoc.api.remove-question') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cau_hoi_id: cauHoiId
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    }
</script>
@endpush