<style>
    /* ============================================
       BASE STYLES - TABLE & CONTENT
    ============================================ */
    .content-section {
        padding: 24px 32px;
    }

    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #f3f4f6;
    }

    .table-header-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 28px 32px;
        color: white;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    .header-text {
        flex: 1;
    }

    .header-title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .question-display {
        font-size: 15px;
        opacity: 0.95;
        line-height: 1.6;
        margin-top: 8px;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        backdrop-filter: blur(10px);
    }

    .btn-add-white {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-add-white:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* ============================================
       TABLE STYLES
    ============================================ */
    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: #f8fafc;
        padding: 16px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 14px;
        color: #4b5563;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
        border-left: 3px solid #667eea;
    }

    .id-text {
        font-weight: 600;
        color: #6366f1;
        font-family: 'Courier New', monospace;
    }

    /* ============================================
       ANSWER LABELS
    ============================================ */
    .answer-label-container {
        display: flex;
        align-items: center;
    }

    .answer-label {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .answer-correct {
        background: #ecfdf5;
        color: #065f46;
        border: 2px solid #34d399;
        box-shadow: 0 2px 4px rgba(52, 211, 153, 0.2);
    }

    .answer-wrong {
        background: #fff1f2;
        color: #9f1239;
        border: 1px solid #fda4af;
        opacity: 0.8;
    }

    tr:hover .answer-correct {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(52, 211, 153, 0.3);
    }

    .question-text {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
    }

    /* ============================================
       TOGGLE SWITCH
    ============================================ */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: 0.3s;
    }

    input:checked+.slider {
        background-color: #10b981;
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }

    .slider.round {
        border-radius: 26px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .status-label {
        margin-top: 4px;
        font-size: 13px;
        color: #6b7280;
    }

    .date-text {
        font-size: 13px;
        color: #6b7280;
    }

    /* ============================================
       ACTION BUTTONS
    ============================================ */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action svg {
        width: 18px;
        height: 18px;
    }

    .btn-edit {
        color: #3b82f6;
    }

    .btn-edit:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        transform: scale(1.05);
    }

    .btn-delete {
        color: #ef4444;
    }

    .btn-delete:hover {
        background: #fef2f2;
        border-color: #ef4444;
        transform: scale(1.05);
    }

    /* ============================================
       TABLE FOOTER & PAGINATION
    ============================================ */
    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 32px;
        border-top: 1px solid #f3f4f6;
    }

    .showing-info {
        font-size: 14px;
        color: #6b7280;
    }

    /* ============================================
       MODAL STYLES - THIẾT KẾ MỚI THEO ẢNH
    ============================================ */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;

        
        align-items: flex-start;
        /* QUAN TRỌNG */
        justify-content: center;

        overflow-y: auto;
        /* QUAN TRỌNG */
        padding: 40px 0;
        /* tạo khoảng cách trên dưới */
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-card {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        max-height: 90vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.3s ease;
        position: relative;
        transition: box-shadow 0.3s ease;
    }

    .modal-card:hover {
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    /* ============================================
       MODAL HEADER - GRADIENT + DRAGGABLE
    ============================================ */
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        cursor: grab;
    }

    .modal-header:active {
        cursor: grabbing;
    }

    .modal-title {
        font-size: 22px;
        font-weight: 700;
        color: white;
        margin: 0;
        letter-spacing: -0.3px;
        pointer-events: none;
    }

    .close-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    /* ============================================
       MODAL BODY
    ============================================ */
    .modal-body {
        padding: 32px;
        overflow-y: auto;
        flex: 1;
    }

    /* ============================================
       FORM STYLES
    ============================================ */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-label.required::after {
        content: " *";
        color: #ef4444;
        font-weight: 700;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #374151;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* ============================================
       FORM ROW - 2 CỘT
    ============================================ */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    /* ============================================
       CHECKBOX WRAPPER
    ============================================ */
    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 12px;
        border-radius: 8px;
        background: #f9fafb;
        transition: all 0.2s ease;
    }

    .checkbox-wrapper:hover {
        background: #f3f4f6;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #d1d5db;
        cursor: pointer;
        accent-color: #6366f1;
    }

    .checkbox-label {
        font-size: 14px;
        color: #374151;
        font-weight: 600;
    }

    /* ============================================
       IMAGE PREVIEW
    ============================================ */
    .image-preview {
        margin-top: 12px;
    }

    .image-preview img {
        max-width: 150px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* ============================================
       QUESTION DISPLAY IN MODAL
    ============================================ */
    .question-display-box {
        margin-bottom: 24px;
        padding: 16px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e5edff 100%);
        border-radius: 12px;
        border: 2px solid #c7d2fe;
    }

    .question-content-display {
        margin-top: 8px;
        padding: 12px;
        background: white;
        border-radius: 8px;
        font-size: 14px;
        line-height: 1.6;
        color: #374151;
        min-height: 50px;
    }

    .question-content-display p {
        margin: 0;
    }

    /* ============================================
       MODAL FOOTER
    ============================================ */
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 20px 32px;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    /* ============================================
       BUTTONS
    ============================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        font-family: inherit;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 768px) {
        .content-section {
            padding: 16px;
        }

        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .modal-card {
            width: 95%;
            margin: 10px;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .table-footer {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
    }

    /* ============================================
       TEXT UTILITIES
    ============================================ */
    .text-center {
        text-align: center;
    }

    .text-success {
        color: #10b981;
    }

    .text-muted {
        color: #9ca3af;
    }

    /* Tối ưu phần thân Modal để hỗ trợ cuộn */
    .modal-body {
        padding: 24px 32px;
        max-height: calc(90vh - 150px);
        /* Giới hạn chiều cao trừ đi header và footer */
        overflow-y: auto;
        /* Hiện thanh cuộn khi nội dung dài */
        scroll-behavior: smooth;
        /* Hiệu ứng cuộn mượt mà */
    }

    /* Tùy chỉnh thanh cuộn (Scrollbar) cho đẹp hơn */
    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
        /* Màu theo tông tím của header */
    }

    /* style.blade.php */
</style>

{{-- CKEDITOR SCRIPT --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
    let dapanEditor;

    // Khởi tạo CKEditor khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#editor-dapan'), {
                toolbar: ['bold', 'italic', 'link', 'undo', 'redo'],
                language: 'vi'
            })
            .then(editor => {
                window.dapanEditor = editor;
            })
            .catch(error => {
                console.error('Error initializing CKEditor:', error);
            });
    });
</script>

{{-- MATHJAX SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
    MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ],
            displayMath: [
                ['$$', '$$'],
                ['\\[', '\\]']
            ]
        },
        svg: {
            fontCache: 'global'
        }
    };
</script>
