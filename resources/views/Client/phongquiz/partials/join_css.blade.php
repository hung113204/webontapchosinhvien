<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    body {
        background-color: #f8fafc;
    }

    /* ---- QUIZ FULLSCREEN MODE ---- */
    :fullscreen .qv-top-bar,
    :fullscreen .site-header,
    :fullscreen footer {
        display: none !important;
    }
    :fullscreen .qv-container {
        max-width: 100vw !important;
        width: 100vw !important;
        height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex;
        flex-direction: column;
        box-sizing: border-box;
    }
    :fullscreen .qv-main-panel {
        flex: 1;
        border-radius: 0 !important;
        min-height: 100vh !important;
        display: flex;
        flex-direction: column;
    }
    :fullscreen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :fullscreen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    .qv-fs-exit-btn {
        display: none;
        position: fixed;
        top: 16px;
        left: 20px;
        z-index: 10000;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(8px);
        border: 1.5px solid rgba(255,255,255,0.2);
        color: white;
        font-size: 20px;
        font-weight: 700;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .qv-fs-exit-btn:hover { background: rgba(239, 68, 68, 0.8); }
    :fullscreen .qv-fs-exit-btn { display: flex; }
    :-moz-full-screen .qv-top-bar, :-moz-full-screen .site-header, :-moz-full-screen footer { display: none !important; }
    :-moz-full-screen .qv-container { max-width:100vw!important; width:100vw!important; height:100vh!important; padding:0!important; margin:0!important; display:flex; flex-direction:column; }
    :-moz-full-screen .qv-main-panel { flex:1; border-radius:0!important; min-height:100vh!important; display: flex; flex-direction: column; }
    :-moz-full-screen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :-moz-full-screen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    :-moz-full-screen .qv-fs-exit-btn { display: flex; }
    :-webkit-full-screen .qv-top-bar, :-webkit-full-screen .site-header, :-webkit-full-screen footer { display: none !important; }
    :-webkit-full-screen .qv-container { max-width:100vw!important; width:100vw!important; height:100vh!important; padding:0!important; margin:0!important; display:flex; flex-direction:column; }
    :-webkit-full-screen .qv-main-panel { flex:1; border-radius:0!important; min-height:100vh!important; display: flex; flex-direction: column; }
    :-webkit-full-screen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :-webkit-full-screen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    :-webkit-full-screen .qv-fs-exit-btn { display: flex; }

    /* ============ IMMERSIVE QUIZ VUI LAYOUT ============ */
    .qv-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
        box-sizing: border-box;
    }

    /* ---- TOP BAR ---- */
    .qv-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* margin-bottom: 16px; */
        padding: 10px 5px;
    }
    .qv-brand {
        font-size: 26px;
        font-weight: 800;
        color: #1e293b;
        letter-spacing: -0.5px;
    }
    .qv-fullscreen-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: white;
        border: 1.5px solid #3b82f6;
        color: #2563eb;
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .qv-fullscreen-btn:hover {
        background: #f0f7ff;
        transform: scale(1.02);
    }

    /* ---- MAIN PANEL ---- */
    .qv-main-panel {
        background: linear-gradient(180deg, #dbebff 0%, #f0f7ff 100%);
        border-radius: 24px;
        /* padding: 24px 40px 40px 40px; */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        position: relative;
        min-height: 600px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        max-height: 85vh;
        overflow-y: auto;
    }

    /* Scrollbar styling cho qv-main-panel (Giống ảnh 1) */
    .qv-main-panel::-webkit-scrollbar {
        width: 16px;
    }
    .qv-main-panel::-webkit-scrollbar-track {
        background: transparent;
    }
    .qv-main-panel::-webkit-scrollbar-thumb {
        background-color: #9ca3af;
        border-radius: 10px;
        border: 4px solid transparent;
        background-clip: padding-box;
    }
    .qv-main-panel::-webkit-scrollbar-thumb:hover {
        background-color: #6b7280;
    }
    
    /* Nút cuộn (mũi tên lên/xuống) */
    .qv-main-panel::-webkit-scrollbar-button:vertical:decrement {
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%239ca3af"><polygon points="8,5 13,11 3,11"/></svg>') no-repeat center center;
        height: 24px;
        cursor: pointer;
    }
    .qv-main-panel::-webkit-scrollbar-button:vertical:increment {
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%239ca3af"><polygon points="8,11 3,5 13,5"/></svg>') no-repeat center center;
        height: 24px;
        cursor: pointer;
    }
    .qv-main-panel::-webkit-scrollbar-button:vertical:decrement:hover,
    .qv-main-panel::-webkit-scrollbar-button:vertical:increment:hover {
        background-color: rgba(0,0,0,0.05);
    }

    /* User dropdown profile in top right corner */
    .qv-user-wrapper {
        position: sticky;
        top: 24px;
        z-index: 100;
        margin-left: auto;
        width: fit-content;
        margin-bottom: 20px;
    }
    .qv-user-pill {
        background: white;
        border: 1.5px solid #e2e8f0;
        border-radius: 50px;
        padding: 5px 14px 5px 5px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
        pointer-events: auto;
    }
    .qv-user-pill:hover {
        background: #f8fafc;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .qv-user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
        overflow: hidden;
    }
    .qv-user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .qv-user-info {
        text-align: left;
    }
    .qv-user-name {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    .qv-user-role {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
    }
    .qv-user-arrow {
        color: #64748b;
        margin-left: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    /* Dropdown menu card matching screenshot */
    .qv-user-dropdown {
        position: absolute;
        top: 54px;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        width: 280px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        padding: 20px;
        display: none;
        animation: qvFadeIn 0.2s ease;
        z-index: 101;
        pointer-events: auto;
    }
    .qv-dropdown-header {
        padding-bottom: 4px;
    }
    .qv-dropdown-name {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }
    .qv-dropdown-email {
        font-size: 13px;
        color: #64748b;
        margin-top: 3px;
    }
    .qv-dropdown-divider {
        height: 1px;
        background: #f1f5f9;
        margin: 15px -20px;
    }
    .qv-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        color: #334155;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .qv-dropdown-item:hover {
        background: #f1f5f9;
        color: #2563eb;
    }
    .qv-item-icon {
        color: #3b82f6;
    }

    /* ---- CUSTOM RENAME MODAL ---- */
    .qv-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        animation: qvFadeIn 0.2s ease;
    }
    .qv-modal-content {
        background: white;
        border-radius: 24px;
        padding: 35px 30px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        text-align: center;
        animation: qvScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid #e2e8f0;
    }
    .qv-modal-content h3 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 10px;
    }
    .qv-modal-content p {
        font-size: 14px;
        color: #64748b;
        margin: 0 0 20px;
    }
    .qv-modal-input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        transition: all 0.2s;
        box-sizing: border-box;
        text-align: center;
        margin-bottom: 24px;
    }
    .qv-modal-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
    .qv-modal-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    .qv-modal-btn {
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .qv-modal-btn.btn-cancel {
        background: #f1f5f9;
        color: #475569;
    }
    .qv-modal-btn.btn-cancel:hover {
        background: #e2e8f0;
    }
    .qv-modal-btn.btn-confirm {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .qv-modal-btn.btn-confirm:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
    }

    @keyframes qvFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes qvScaleUp {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* ---- CENTRAL CONTENT ---- */
    .qv-central-card {
        background: white;
        border-radius: 28px;
        padding: 60px 50px;
        max-width: 1200px;
        margin: 40px auto 0;
        text-align: center;
        box-shadow: 0 10px 40px rgba(59, 130, 246, 0.05);
        border: 1px solid #e2e8f0;
    }

    /* Group people icon */
    .qv-group-icon-wrap {
        width: 120px;
        height: 100px;
        margin: 0 auto 24px;
    }
    .qv-group-icon-wrap svg {
        width: 100%;
        height: 100%;
    }

    .qv-title {
        font-size: 46px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -1px;
    }
    .qv-subtitle {
        font-size: 18px;
        color: #64748b;
        margin: 0 0 40px;
        font-weight: 500;
    }

    .qv-main-content-wrapper {
        max-width: 760px;
        margin: 0 auto;
        width: 100%;
    }
    
    /* ---- BUTTONS ROW ---- */
    .qv-btn-row {
        display: flex;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }
    .qv-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 36px;
        border-radius: 50px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: inherit;
        text-decoration: none;
    }
    .qv-btn-blue {
        background: #1e88e5;
        color: white;
        box-shadow: 0 4px 15px rgba(30, 136, 229, 0.3);
    }
    .qv-btn-blue:hover {
        background: #1565c0;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 136, 229, 0.4);
    }
    .qv-btn-white {
        background: white;
        color: #1e293b;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .qv-btn-white:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    .qv-btn-gray {
        background: #eceff1;
        color: #546e7a;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .qv-btn-gray:hover {
        background: #cfd8dc;
        transform: translateY(-2px);
    }

    /* ---- LEADERBOARD ---- */
    .qv-leaderboard-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 20px;
        text-align: left;
    }
    
    .qv-tabs {
        display: flex;
        gap: 4px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 10px;
        width: fit-content;
        margin-bottom: 16px;
    }
    .qv-tab {
        border: none;
        background: transparent;
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .qv-tab.active {
        background: #1e88e5;
        color: white;
        box-shadow: 0 2px 6px rgba(30, 136, 229, 0.2);
    }

    .qv-leaderboard-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 8px;
        text-align: left;
    }
    .qv-lb-row {
        display: flex;
        align-items: center;
        background: white;
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 8px;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s;
    }
    .qv-lb-row:last-child {
        margin-bottom: 0;
    }
    .qv-lb-row:hover {
        transform: translateX(4px);
    }
    .qv-lb-rank {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 800;
        margin-right: 16px;
    }
    .qv-lb-rank.gold { background: #fff8e1; color: #ffb300; border: 1px solid #ffe082; }
    .qv-lb-rank.silver { background: #eceff1; color: #78909c; border: 1px solid #cfd8dc; }
    .qv-lb-rank.bronze { background: #efebe9; color: #8d6e63; border: 1px solid #d7ccc8; }
    .qv-lb-rank.normal { background: #f8fafc; color: #94a3b8; }
    
    .qv-lb-name {
        flex: 1;
        font-weight: 700;
        color: #1e293b;
        font-size: 17px;
    }
    .qv-lb-score {
        font-weight: 800;
        color: #1e88e5;
        font-size: 18px;
    }

    /* ---- FLOATING CHAT BUBBLE ---- */
    .qv-chat-bubble {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        background: #00e5ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(0, 229, 255, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 1000;
    }
    .qv-chat-bubble:hover {
        transform: scale(1.08);
        box-shadow: 0 10px 28px rgba(0, 229, 255, 0.5);
    }
    .qv-chat-badge {
        position: absolute;
        top: 0;
        right: 0;
        background: #ff1744;
        color: white;
        font-size: 11px;
        font-weight: 800;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
    }

    /* ============ INLINE FORMS LAYOUT ============ */
    .pq-pin-input {
        width: 100%;
        padding: 16px;
        background: #f8fafc;
        border: 2.5px solid #e2e8f0;
        border-radius: 14px;
        font-size: 26px;
        font-weight: 800;
        text-align: center;
        letter-spacing: 6px;
        outline: none;
        transition: all 0.2s;
        font-family: monospace;
    }
    .pq-pin-input:focus {
        border-color: #1e88e5;
        background: white;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
    }
    .pq-submit-btn {
        width: 100%;
        padding: 16px;
        border-radius: 14px;
        border: none;
        background: linear-gradient(135deg, #1e88e5, #1565c0);
        color: white;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 16px;
        box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .pq-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(30, 136, 229, 0.35);
    }

    .pq-error-box {
        background: #fff5f5;
        border: 1px solid #ffe3e3;
        color: #e53935;
        padding: 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* BACK BUTTON (Create/Join Form) */
    .qv-back-btn {
        position: absolute;
        left: 0;
        background: none;
        border: none;
        color: #475569;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0;
        transition: all 0.2s ease;
    }
    .qv-back-btn:hover {
        color: #2563eb;
        transform: translateX(-3px);
    }

    @keyframes qvFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes qvSlideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (max-width: 768px) {
        .qv-main-panel { padding: 24px; }
        .qv-title { font-size: 28px; }
        .qv-user-pill { position: static; margin: 0 auto 20px; width: fit-content; }
        .qv-central-card { margin-top: 10px; padding: 30px 20px; }
    }
</style>
