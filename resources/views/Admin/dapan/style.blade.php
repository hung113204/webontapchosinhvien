<style>
    .answers-page {
        padding: 24px 32px 32px;
    }

    .answer-alert {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 16px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid;
        background: #fff;
    }

    .answer-alert i {
        margin-top: 3px;
        font-size: 18px;
    }

    .answer-alert strong,
    .answer-alert span {
        display: block;
    }

    .answer-alert ul {
        margin: 4px 0 0;
        padding-left: 18px;
    }

    .answer-alert-success {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .answer-alert-danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .answers-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
        padding: 28px;
        border-radius: 8px;
        color: #fff;
        background: linear-gradient(135deg, #2563eb 0%, #0f766e 100%);
        box-shadow: var(--shadow-md);
    }

    .answers-hero-main {
        min-width: 0;
        flex: 1;
    }

    .answers-eyebrow {
        display: inline-flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .86;
    }

    .answers-hero h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0;
    }

    .answers-hero-copy {
        max-width: 720px;
        margin: 10px 0 0;
        color: rgba(255, 255, 255, .88);
    }

    .answers-question-card {
        margin-top: 16px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, .24);
        border-radius: 8px;
        background: rgba(255, 255, 255, .12);
    }

    .answers-question-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    .answers-question-meta span {
        padding: 4px 9px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .16);
        font-size: 12px;
        font-weight: 600;
    }

    .answers-question-content {
        max-height: 160px;
        overflow: auto;
        line-height: 1.65;
        color: rgba(255, 255, 255, .96);
    }

    .answers-question-content p {
        margin: 0 0 6px;
    }

    .answers-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-end;
    }

    .answers-light-btn,
    .answers-add-inline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 40px;
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 8px;
        color: #fff;
        background: rgba(255, 255, 255, .13);
        text-decoration: none;
        font-weight: 700;
        transition: var(--transition-fast);
        cursor: pointer;
    }

    .answers-light-btn:hover {
        background: rgba(255, 255, 255, .22);
        color: #fff;
    }

    .answers-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-top: 16px;
    }

    .answers-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        padding: 18px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        background: #fff;
        box-shadow: var(--shadow);
    }

    .answers-stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        flex: 0 0 auto;
    }

    .answers-stat-total {
        color: #2563eb;
        background: #eff6ff;
    }

    .answers-stat-correct {
        color: #059669;
        background: #ecfdf5;
    }

    .answers-stat-hidden {
        color: #d97706;
        background: #fffbeb;
    }

    .answers-stat-image {
        color: #0891b2;
        background: #ecfeff;
    }

    .answers-stat strong {
        display: block;
        font-size: 22px;
        line-height: 1.1;
        color: var(--gray-900);
    }

    .answers-stat span:last-child {
        display: block;
        margin-top: 4px;
        color: var(--gray-500);
        font-size: 13px;
    }

    .answers-filter-card,
    .answers-table-card {
        margin-top: 16px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        background: #fff;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .answers-filter-form {
        display: grid;
        grid-template-columns: minmax(280px, 1fr) 180px 180px auto;
        gap: 16px;
        align-items: end;
        padding: 18px;
    }

    .answers-filter-item label,
    .answers-form-group label,
    .answers-modal-question label {
        display: block;
        margin-bottom: 8px;
        color: var(--gray-700);
        font-size: 13px;
        font-weight: 700;
    }

    .answers-search-box {
        position: relative;
    }

    .answers-search-box i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    .answers-search-box input,
    .answers-form-group input,
    .answers-form-group textarea {
        width: 100%;
        min-height: 42px;
        padding: 10px 12px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        color: var(--gray-800);
        font-family: inherit;
        font-size: 14px;
        transition: var(--transition-fast);
    }

    .answers-search-box input {
        padding-left: 40px;
    }

    .answers-search-box input:focus,
    .answers-form-group input:focus,
    .answers-form-group textarea:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
    }

    .answers-filter-actions {
        display: flex;
        gap: 8px;
    }

    .answers-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid var(--gray-200);
    }

    .answers-table-header h3 {
        margin: 0;
        color: var(--gray-900);
        font-size: 18px;
    }

    .answers-table-header p {
        margin: 4px 0 0;
        color: var(--gray-500);
        font-size: 13px;
    }

    .answers-add-inline {
        border-color: #2563eb;
        color: #fff;
        background: #2563eb;
    }

    .answers-add-inline:hover {
        background: #1d4ed8;
    }

    .answers-table-wrap {
        overflow-x: auto;
    }

    .answers-table {
        width: 100%;
        min-width: 1060px;
        border-collapse: collapse;
    }

    .answers-table th {
        padding: 14px 18px;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        color: var(--gray-600);
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .answers-table td {
        padding: 16px 18px;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
        color: var(--gray-700);
        font-size: 14px;
    }

    .answers-table tbody tr:hover {
        background: #f8fafc;
    }

    .answers-id {
        display: inline-flex;
        padding: 5px 9px;
        border-radius: 6px;
        background: var(--gray-100);
        color: var(--gray-700);
        font-family: "JetBrains Mono", monospace;
        font-size: 12px;
        font-weight: 700;
    }

    .answers-answer-cell {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        min-width: 0;
    }

    .answers-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #eef2ff;
        color: #3730a3;
        flex: 0 0 auto;
        font-weight: 800;
    }

    .answers-answer-body {
        min-width: 0;
    }

    .answers-answer-text {
        max-width: 440px;
        line-height: 1.55;
        color: var(--gray-900);
        word-break: break-word;
    }

    .answers-answer-text p {
        margin: 0 0 6px;
    }

    .answers-image-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        color: #0891b2;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
    }

    .answers-question-cell {
        display: grid;
        gap: 4px;
        max-width: 420px;
    }

    .answers-question-cell strong {
        color: var(--gray-900);
        font-size: 13px;
    }

    .answers-question-cell span {
        color: var(--gray-500);
        line-height: 1.45;
    }

    .answers-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .answers-pill-correct {
        color: #047857;
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .answers-pill-muted {
        color: var(--gray-600);
        background: var(--gray-100);
        border-color: var(--gray-200);
        cursor: pointer;
    }

    .answers-pill-muted:hover {
        color: #2563eb;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .answers-switch {
        display: inline-flex;
        position: relative;
        width: 46px;
        height: 26px;
        vertical-align: middle;
    }

    .answers-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .answers-switch span {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: var(--gray-300);
        cursor: pointer;
        transition: var(--transition-fast);
    }

    .answers-switch span::before {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        left: 3px;
        top: 3px;
        border-radius: 50%;
        background: #fff;
        box-shadow: var(--shadow-sm);
        transition: var(--transition-fast);
    }

    .answers-switch input:checked + span {
        background: #059669;
    }

    .answers-switch input:checked + span::before {
        transform: translateX(20px);
    }

    .answers-status-text,
    .answers-date {
        display: block;
        margin-top: 5px;
        color: var(--gray-500);
        font-size: 12px;
    }

    .answers-table .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-start;
    }

    .answers-table .btn-action {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
    }

    .answers-table .btn-action i {
        font-size: 14px;
        line-height: 1;
    }

    .answers-empty {
        display: grid;
        justify-items: center;
        gap: 8px;
        padding: 34px 16px;
        color: var(--gray-500);
        text-align: center;
    }

    .answers-empty i {
        font-size: 28px;
        color: var(--gray-400);
    }

    .answers-empty strong {
        color: var(--gray-800);
    }

    .answers-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 22px;
    }

    .answers-table-footer > span {
        color: var(--gray-500);
        font-size: 13px;
    }

    .answers-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        align-items: flex-start;
        justify-content: center;
        padding: 34px 18px;
        overflow-y: auto;
        background: rgba(15, 23, 42, .58);
    }

    .answers-modal {
        width: min(760px, 100%);
        height: min(720px, calc(100vh - 68px));
        max-height: calc(100vh - 68px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        border-radius: 8px;
        background: #fff;
        box-shadow: var(--shadow-xl);
    }

    .answers-modal form {
        display: flex;
        flex: 1 1 auto;
        min-height: 0;
        flex-direction: column;
    }

    .answers-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid var(--gray-200);
        background: #f8fafc;
    }

    .answers-modal-header span {
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .answers-modal-header h3 {
        margin: 2px 0 0;
        color: var(--gray-900);
        font-size: 20px;
    }

    .answers-modal-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        background: #fff;
        color: var(--gray-600);
        cursor: pointer;
    }

    .answers-modal-body {
        flex: 1 1 auto;
        min-height: 0;
        padding: 22px 24px;
        overflow-y: auto;
    }

    .answers-modal-question {
        margin-bottom: 18px;
        padding: 14px;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        background: #eff6ff;
    }

    .answers-modal-question > div {
        max-height: 140px;
        overflow: auto;
        color: var(--gray-800);
        line-height: 1.55;
    }

    .answers-form-group {
        margin-bottom: 16px;
    }

    .answers-form-group label span {
        color: #dc2626;
    }

    .answers-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .answers-preview {
        margin-top: 10px;
    }

    .answers-preview img {
        max-width: 180px;
        max-height: 130px;
        border-radius: 8px;
        border: 1px solid var(--gray-200);
        object-fit: cover;
    }

    .answers-option-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .answers-check-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        background: var(--gray-50);
        cursor: pointer;
    }

    .answers-check-option input {
        width: 18px;
        height: 18px;
        margin-top: 2px;
        accent-color: #2563eb;
    }

    .answers-check-option strong {
        display: block;
        color: var(--gray-800);
        font-size: 14px;
    }

    .answers-check-option small {
        display: block;
        margin-top: 2px;
        color: var(--gray-500);
        line-height: 1.35;
    }

    .answers-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px;
        border-top: 1px solid var(--gray-200);
        background: #f8fafc;
    }

    .ck-editor__editable_inline {
        min-height: 140px;
    }

    @media (max-width: 1180px) {
        .answers-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .answers-filter-form {
            grid-template-columns: 1fr 1fr;
        }

        .answers-filter-search,
        .answers-filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 768px) {
        .answers-page {
            padding: 16px;
        }

        .answers-hero,
        .answers-table-header,
        .answers-table-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .answers-hero h2 {
            font-size: 23px;
        }

        .answers-hero-actions {
            justify-content: flex-start;
        }

        .answers-stats,
        .answers-filter-form,
        .answers-form-grid,
        .answers-option-row {
            grid-template-columns: 1fr;
        }

        .answers-filter-actions {
            flex-direction: column;
        }

        .answers-modal-overlay {
            padding: 12px;
        }

        .answers-modal {
            height: calc(100vh - 24px);
            max-height: calc(100vh - 24px);
        }
    }
</style>
