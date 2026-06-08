<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
    body {
        background-color: #f8fafc;
    }

    /* ---- QUIZ FULLSCREEN MODE ---- */
    /* Only show qv-main-panel in fullscreen */
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
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }
    :fullscreen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :fullscreen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    /* X exit button shown in fullscreen */
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
    .qv-fs-exit-btn:hover {
        background: rgba(239, 68, 68, 0.8);
    }
    :fullscreen .qv-fs-exit-btn {
        display: flex;
    }
    /* Firefox prefix */
    :-moz-full-screen .qv-top-bar,
    :-moz-full-screen .site-header,
    :-moz-full-screen footer { display: none !important; }
    :-moz-full-screen .qv-container { max-width:100vw!important; width:100vw!important; height:100vh!important; padding:0!important; margin:0!important; display:flex; flex-direction:column; }
    :-moz-full-screen .qv-main-panel { flex:1; border-radius:0!important; min-height:100vh!important; overflow-y:auto; display: flex; flex-direction: column; }
    :-moz-full-screen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :-moz-full-screen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    :-moz-full-screen .qv-fs-exit-btn { display: flex; }
    /* Webkit prefix */
    :-webkit-full-screen .qv-top-bar,
    :-webkit-full-screen .site-header,
    :-webkit-full-screen footer { display: none !important; }
    :-webkit-full-screen .qv-container { max-width:100vw!important; width:100vw!important; height:100vh!important; padding:0!important; margin:0!important; display:flex; flex-direction:column; }
    :-webkit-full-screen .qv-main-panel { flex:1; border-radius:0!important; min-height:100vh!important; overflow-y:auto; display: flex; flex-direction: column; }
    :-webkit-full-screen .qv-main-panel > *:not(.qv-user-wrapper):not(.qv-modal-overlay):not(script):not(style) { margin-top: auto !important; margin-bottom: auto !important; }
    :-webkit-full-screen .qv-user-wrapper { position: absolute; top: 24px; right: 40px; margin: 0; }
    :-webkit-full-screen .qv-fs-exit-btn { display: flex; }

    /* ============ ROOM CONTAINER ============ */
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
        margin-bottom: 16px;
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

    /* ---- MAIN GRADIENT PANEL ---- */
    .qv-main-panel {
        background: linear-gradient(180deg, #dbebff 0%, #f0f7ff 100%);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        position: relative;
        min-height: 600px;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    /* ---- STATE 1: LOBBY WAITING ---- */
    .qv-lobby-card {
        background: white;
        border-radius: 24px;
        padding: 50px 40px;
        max-width: 700px;
        margin: 40px auto 0;
        text-align: center;
        box-shadow: 0 15px 45px rgba(59, 130, 246, 0.08);
        border: 1px solid #e2e8f0;
    }
   
    .qv-lobby-title {
        font-size: 32px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -0.5px;
    }
    .qv-lobby-badge {
        display: inline-block;
        background: #f1f5f9;
        padding: 6px 18px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }
    .qv-lobby-player {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        max-width: 320px;
        margin: 0 auto 30px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
    }
    .qv-lobby-player .label {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 800;
        letter-spacing: 1.5px;
        margin-bottom: 6px;
    }
    .qv-lobby-player .name {
        font-size: 20px;
        font-weight: 800;
        color: #1e293b;
    }
    .qv-lobby-waiting-text {
        font-size: 15px;
        color: #64748b;
        font-weight: 600;
        animation: textBlink 1.5s infinite ease-in-out;
    }

    /* ---- STATE 2: PLAYING WORKSPACE ---- */
    .qv-playing-section {
        max-width: 1100px;
        margin: 0 auto;
    }
    .qv-playing-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 16px 28px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
    }
    .qv-playing-q-number {
        font-size: 15px;
        font-weight: 800;
        color: #2563eb;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .qv-playing-room-code {
        font-size: 13px;
        color: #94a3b8;
        font-weight: 600;
        margin-top: 2px;
    }
    
    /* Countdown Timer styling */
    .qv-timer-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff5f5;
        border: 1.5px solid #ffe3e3;
        padding: 10px 22px;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.05);
    }
    .qv-timer-badge .number {
        font-size: 24px;
        font-weight: 900;
        color: #ef4444;
        font-family: monospace;
    }
    .qv-timer-badge .unit {
        font-size: 11px;
        font-weight: 800;
        color: #ef4444;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .qv-question-card {
        /* background: white; */
        border-radius: 28px;
        /* border: 1px solid #e2e8f0; */
        /* box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03); */
        padding: 40px;
        margin-bottom: 24px;
        text-align: center;
        min-height: 450px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
    }
    .qv-question-text {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.4;
        margin: 0 0 25px;
    }
    .qv-question-image-box {
        display: none;
        max-width: 500px;
        margin: 0 auto 25px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1.5px dashed #cbd5e1;
    }
    .qv-question-image-box img {
        width: 100%;
        max-height: 250px;
        border-radius: 10px;
        object-fit: contain;
    }
 
    /* Answers Grid - kahoot style colors but sleek modern rounded look */
    .qv-answers-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-auto-rows: 1fr;
        gap: 16px;
    }
    .qv-answer-btn {
        border: 2px solid #e2e8f0;
        border-radius: 18px;
        padding: 12px 24px;
        color: #1e293b;
        background: #ffffff;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: left;
        font-family: inherit;
        min-height: 80px;
        height: 100%;
        box-sizing: border-box;
    }
    .qv-answer-btn:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
    }
    .qv-answer-btn:active {
        transform: translateY(0);
    }
    .qv-shape-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 800;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    /* Selection Highlight System */
    .qv-answers-grid.has-selection .qv-answer-btn:not(.selected) {
        opacity: 0.5;
        transform: scale(0.96);
    }
    .qv-answers-grid.has-selection .qv-answer-btn.selected {
        border-color: #3b82f6;
        background: #f0f7ff;
        transform: scale(1.02);
        box-shadow: 0 0 0 2px #3b82f6, 0 8px 24px rgba(59, 130, 246, 0.15);
    }
    .qv-answers-grid.has-selection .qv-answer-btn.selected .qv-shape-icon {
        background: #3b82f6;
        color: #ffffff;
    }

    /* Correct & Wrong Answer Reveal Styles */
    .qv-answer-btn.is-correct-reveal {
        border-color: #10b981 !important;
        background: #ecfdf5 !important;
        color: #065f46 !important;
        transform: scale(1.03) !important;
        box-shadow: 0 0 0 2px #10b981, 0 8px 24px rgba(16, 185, 129, 0.15) !important;
        opacity: 1 !important;
    }
    .qv-answer-btn.is-correct-reveal .qv-shape-icon {
        background: #10b981 !important;
        color: #ffffff !important;
    }

    .qv-answer-btn.is-wrong-reveal {
        border-color: #ef4444 !important;
        background: #fef2f2 !important;
        color: #991b1b !important;
        transform: scale(0.97) !important;
        box-shadow: 0 0 0 2px #ef4444, 0 8px 24px rgba(239, 68, 68, 0.1) !important;
        opacity: 0.8 !important;
    }
    .qv-answer-btn.is-wrong-reveal .qv-shape-icon {
        background: #ef4444 !important;
        color: #ffffff !important;
    }

    .qv-answers-grid.revealed .qv-answer-btn:not(.is-correct-reveal):not(.is-wrong-reveal) {
        opacity: 0.35 !important;
        transform: scale(0.95) !important;
        border-color: #e2e8f0 !important;
        background: #f8fafc !important;
        box-shadow: none !important;
    }

    /* Confirm Answer Button Styling */
    .qv-confirm-btn {
        width: 100%;
        max-width: 100%;
        padding: 18px 32px;
        border-radius: 18px;
        border: none;
        background: #60a5fa;
        color: white;
        font-size: 20px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(96, 165, 250, 0.25);
        transition: all 0.25s ease;
        letter-spacing: 0.5px;
        font-family: inherit;
    }
    .qv-confirm-btn:disabled {
        background: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
        opacity: 0.7;
    }
    .qv-confirm-btn:hover:not(:disabled) {
        background: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
    }

    /* Submitted State view */
    .qv-submitted-card {
        background: white;
        border-radius: 24px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        border: 1px solid #e2e8f0;
    }
    .qv-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f1f5f9;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 24px;
    }
    .qv-submitted-card h3 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 8px;
    }
    .qv-submitted-card p {
        font-size: 15px;
        color: #64748b;
        font-weight: 500;
        margin: 0;
    }

    /* Instant Grade Result View */
    .qv-grade-card {
        position: relative;
        border-radius: 24px;
        padding: 28px 24px 20px; /* Reduced padding */
        text-align: center;
        border: 2px solid transparent;
        animation: qvSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        margin-top: 50px; /* margin to make room for absolute floating bomb icon */
        display: block;
    }
    .grade-icon-wrapper {
        position: absolute;
        top: -85px;
        left: 50%;
        transform: translateX(-50%) scale(0) rotate(-15deg);
        opacity: 0;
        z-index: 10;
        pointer-events: none;
        transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .grade-icon-wrapper.show {
        transform: translateX(-50%) scale(1.1) rotate(0deg);
        opacity: 1;
    }
    .grade-icon-wrapper .bomb-icon,
    .grade-icon-wrapper .check {
        display: none;
    }
    .grade-icon-wrapper.is-bomb .bomb-icon,
    .grade-icon-wrapper.is-check .check {
        display: inline-flex;
    }
    .grade-icon-wrapper.is-exploding::after {
        content: "";
        position: absolute;
        inset: 50%;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        border: 4px solid rgba(239, 68, 68, 0.45);
        transform: translate(-50%, -50%) scale(0.2);
        animation: bombShockwave 0.75s ease-out forwards;
        pointer-events: none;
    }
    .grade-icon-wrapper.is-checking::after {
        content: "";
        position: absolute;
        inset: 50%;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid rgba(16, 185, 129, 0.45);
        transform: translate(-50%, -50%) scale(0.2);
        animation: checkShockwave 0.75s ease-out forwards;
        pointer-events: none;
    }
    .bomb-icon {
        font-size: 130px !important;
        color: #dc2626 !important;
        filter: drop-shadow(0 12px 24px rgba(220, 38, 38, 0.5)) !important;
        align-items: center;
        justify-content: center;
    }
    .check {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #22c55e, #10b981);
        color: #ffffff;
        font-size: 78px;
        font-weight: 900;
        line-height: 1;
        box-shadow: 0 16px 28px rgba(16, 185, 129, 0.35);
    }
    .grade-icon-wrapper.is-exploding .bomb-icon {
        animation: bombPopOnce 0.75s cubic-bezier(0.2, 0.9, 0.25, 1.35) both;
    }
    .grade-icon-wrapper.is-checking .check {
        animation: checkPopOnce 0.75s cubic-bezier(0.2, 0.9, 0.25, 1.35) both;
    }
    @keyframes bombPopOnce {
        0% {
            transform: scale(0.35) rotate(-20deg);
            filter: drop-shadow(0 0 0 rgba(220, 38, 38, 0));
        }
        45% {
            transform: scale(1.18) rotate(8deg);
            filter: drop-shadow(0 16px 28px rgba(220, 38, 38, 0.55));
        }
        70% {
            transform: scale(0.96) rotate(-4deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
        }
    }
    @keyframes bombShockwave {
        0% {
            opacity: 0.75;
            transform: translate(-50%, -50%) scale(0.2);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(1.3);
        }
    }
    @keyframes checkPopOnce {
        0% {
            transform: scale(0.35) rotate(-12deg);
        }
        45% {
            transform: scale(1.12) rotate(5deg);
        }
        70% {
            transform: scale(0.96) rotate(-2deg);
        }
        100% {
            transform: scale(1) rotate(0deg);
        }
    }
    @keyframes checkShockwave {
        0% {
            opacity: 0.75;
            transform: translate(-50%, -50%) scale(0.2);
        }
        100% {
            opacity: 0;
            transform: translate(-50%, -50%) scale(1.25);
        }
    }
    @keyframes bombPulseWiggle {
        0% {
            transform: rotate(-10deg) scale(0.95);
        }
        100% {
            transform: rotate(10deg) scale(1.08);
        }
    }
    .qv-grade-card.correct {
        background: #eefdf5;
        border-color: #10b981;
    }
    .qv-grade-card.wrong {
        background: #fef2f2;
        border-color: #ef4444;
    }
    .qv-grade-card.timeout {
        background: #f8fafc;
        border-color: #64748b;
    }
    .qv-grade-title {
        font-size: 30px;
        font-weight: 800;
        margin: 0 0 10px;
    }
    .qv-grade-card.correct .qv-grade-title { color: #059669; }
    .qv-grade-card.wrong .qv-grade-title { color: #dc2626; }
    .qv-grade-card.timeout .qv-grade-title { color: #475569; }
    .qv-grade-pts {
        font-size: 15px;
        font-weight: 500;
        color: #334155;
        margin-bottom: 12px;
        line-height: 1.5;
    }
    .qv-grade-explain-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px 24px;
        text-align: left;
        max-width: 100%;
        margin: 0 auto 20px;
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.01);
    }
    .qv-grade-explain-box .ex-label {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .qv-grade-explain-box .ex-text {
        font-size: 15px;
        color: #334155;
        line-height: 1.6;
    }
    .qv-grade-continue-btn {
        display: inline-block;
        background: #10b981;
        color: white;
        font-size: 18px;
        font-weight: 800;
        padding: 14px 60px;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        transition: all 0.2s ease;
        margin-bottom: 14px;
        font-family: inherit;
    }
    .qv-grade-continue-btn:hover:not(:disabled) {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.4);
    }
    .qv-grade-continue-btn:disabled {
        background: #94a3b8;
        color: #e2e8f0;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
        opacity: 0.6;
    }
    .qv-grade-tip {
        font-size: 13px;
        color: #64748b;
        font-style: italic;
        margin-top: 12px;
        line-height: 1.4;
    }
    .qv-grade-score-summary { display: none; }

    /* ---- STATE 3: ENDED RESULTS SUMMARY ---- */
    .qv-ended-card {
        background: white;
        border-radius: 28px;
        padding: 50px 40px;
        max-width: 1000px;
        margin: 40px auto 0;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }
    .qv-ended-trophy {
        margin-bottom: 16px;
        display: flex;
        justify-content: center;
        animation: trophyBounce 1.2s infinite alternate ease-in-out;
    }
    .qv-ended-trophy i {
        font-size: 7.5rem;
        color: #facc15;
        filter: drop-shadow(0 6px 8px rgba(250, 204, 21, 0.3));
    }
    @keyframes trophyBounce {
        0% { transform: translateY(0); }
        100% { transform: translateY(-8px); }
    }
    .qv-ended-card h2 {
        font-size: 48px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 12px;
        letter-spacing: -1px;
    }
    .qv-ended-subtitle {
        font-size: 18px;
        color: #475569;
        margin-bottom: 6px;
        font-weight: 500;
    }
    .qv-ended-subtitle .code {
        color: #0f172a;
        font-weight: 800;
    }
    .qv-ended-matchid {
        font-size: 14px;
        color: #94a3b8;
        font-family: monospace;
        margin-bottom: 35px;
    }

    /* Leaderboard container matching the image */
    .qv-ended-leaderboard-card {
        background: #f8fafc;
        border-radius: 20px;
        border: 1.5px solid #e2e8f0;
        padding: 0;
        overflow: hidden;
        margin-bottom: 35px;
        text-align: left;
    }
    .qv-ended-lb-header {
        display: flex;
        justify-content: space-between;
        padding: 16px 24px;
        border-bottom: 1.5px solid #e2e8f0;
        font-size: 14px;
        font-weight: 800;
        color: #64748b;
        letter-spacing: 1px;
    }
    .qv-ended-lb-list {
        background: white;
    }
    .qv-ended-lb-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s;
    }
    .qv-ended-lb-row:last-child {
        border-bottom: none;
    }
    .qv-ended-lb-row.first-place {
        background: #fefbeb; /* Light gold background for 1st place */
    }
    .qv-ended-lb-row.is-me {
        border-left: 4px solid #3b82f6;
    }
    .qv-ended-rank-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
    }
    .qv-ended-rank-badge.gold {
        background: #fef08a;
        color: #a16207;
        border: 1.5px solid #facc15;
    }
    .qv-ended-rank-badge.silver {
        background: #f1f5f9;
        color: #475569;
        border: 1.5px solid #cbd5e1;
    }
    .qv-ended-rank-badge.bronze {
        background: #ffedd5;
        color: #c2410c;
        border: 1.5px solid #fdba74;
    }
    .qv-ended-rank-badge.normal {
        background: #f1f5f9;
        color: #64748b;
    }
    .qv-ended-player-name {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }
    .qv-ended-player-sub {
        font-size: 15px;
        color: #64748b;
        margin-top: 2px;
        font-weight: 500;
    }
    .qv-ended-player-score {
        font-size: 28px;
        font-weight: 900;
        color: #0284c7;
        letter-spacing: -0.5px;
    }

    /* Action Buttons Row */
    .qv-ended-actions {
        display: flex;
        justify-content: center;
        gap: 16px;
    }
    .qv-ended-btn-exit, .qv-ended-btn-lobby {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px 32px;
        border-radius: 50px;
        font-size: 17px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.25s ease;
        box-sizing: border-box;
        border: 2px solid transparent;
    }
    .qv-ended-btn-exit {
        background: white;
        color: #334155;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .qv-ended-btn-exit:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
    }
    .qv-ended-btn-lobby {
        background: #10b981;
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    .qv-ended-btn-lobby:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }

    /* ---- KEYFRAMES & ANIMATIONS ---- */
    @keyframes lobbyPulse {
        0%, 100% { transform: scale(1); box-shadow: 0 10px 28px rgba(59, 130, 246, 0.4); }
        50% { transform: scale(1.05); box-shadow: 0 10px 35px rgba(59, 130, 246, 0.6); }
    }
    @keyframes textBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    @keyframes bounce {
        from { transform: translateY(0); }
        to { transform: translateY(-10px); }
    }
    @keyframes qvSlideUp {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ---- IN-ROOM LIVE LEADERBOARD ---- */
    .qv-room-layout {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1100px;
        margin: 0 auto;
    }
    .qv-leaderboard-panel {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        overflow: hidden;
        width: 100%;
    }
    .qv-lb-panel-header {
        padding: 16px 20px;
        text-align: center;
        border-bottom: 1px solid #e8f0fe;
    }
    .qv-lb-panel-header h4 {
        color: #2563eb;
        font-size: 13px;
        font-weight: 800;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    /* Table leaderboard */
    .qv-lb-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .qv-lb-table thead th {
        background: #f0f6ff;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        padding: 10px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .qv-lb-table thead th.col-score { text-align: right; color: #2563eb; }
    .qv-lb-table thead th.col-right,
    .qv-lb-table thead th.col-time { text-align: center; }
    .qv-lb-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .qv-lb-table tbody tr:hover { background: #f8fafc; }
    .qv-lb-table tbody tr.is-me { background: #eff6ff; }
    .qv-lb-table tbody tr:last-child { border-bottom: none; }
    .qv-lb-table td {
        padding: 12px 16px;
        vertical-align: middle;
    }
    .qv-lb-td-rank {
        font-weight: 800;
        color: #1e293b;
        font-size: 14px;
        width: 36px;
    }
    .qv-lb-td-name {
        font-weight: 700;
        color: #1e293b;
        font-size: 14px;
    }
    .qv-lb-td-name.is-me { font-weight: 800; }
    .qv-lb-td-right,
    .qv-lb-td-time {
        text-align: center;
        color: #475569;
        font-weight: 600;
    }
    .qv-lb-td-score {
        text-align: right;
        font-weight: 800;
        color: #2563eb;
        font-size: 15px;
    }
    .qv-lb-footer {
        padding: 10px 16px;
        text-align: center;
        font-size: 12px;
        color: #94a3b8;
        border-top: 1px solid #f1f5f9;
    }
    .qv-lb-footer strong { color: #475569; }
    /* My score pill */
    .qv-my-score-pill {
        display: flex;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #1e3a8a, #2563eb);
        border-radius: 16px;
        padding: 14px 20px;
        margin-bottom: 20px;
        color: white;
    }
    .qv-my-score-pill .label { font-size: 11px; font-weight: 700; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px; }
    .qv-my-score-pill .value { font-size: 28px; font-weight: 900; }
    .qv-my-score-pill .correct { font-size: 12px; opacity: 0.85; margin-top: 2px; }

    /* ---- NEW LOBBY DESIGN ---- */
    .qv-lobby-new {
        background: #eef7fb;
        border-radius: 24px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .qv-lobby-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0px;
    }

    .qv-lobby-back {
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        border: none;
        color: #64748b;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        padding: 8px 0;
        text-decoration: none;
        transition: all 0.2s;
    }

    .qv-lobby-back:hover {
        color: #2563eb;
        transform: translateX(-4px);
    }

    .qv-lobby-code-badge {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        background: white;
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    .qv-lobby-code-badge .label {
        font-size: 11px;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 800;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    
    .qv-lobby-code-badge .code-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .qv-lobby-code-badge .code {
        font-size: 28px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: 2px;
    }

    .qv-lobby-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 40px;
    }

    .qv-lobby-center {
        text-align: center;
        margin-top: -30px;
    }

    .qv-lobby-icon-pulse {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .qv-lobby-title-large {
        font-size: 48px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 16px;
        letter-spacing: -0.5px;
    }

    .qv-lobby-info-badges {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .qv-info-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
        padding: 10px 24px;
        border-radius: 50px;
        font-size: 17px;
        font-weight: 700;
        color: #64748b;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .qv-lobby-two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        width: 100%;
    }

    .qv-lobby-column {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .qv-lobby-col-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .qv-lobby-section-title {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .qv-lobby-section-count {
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 16px;
        color: #64748b;
        font-weight: 700;
        margin: 0;
    }

    .qv-lobby-players-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 300px;
        overflow-y: auto;
    }

    .qv-lobby-player-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #eef8fc;
        padding: 16px 20px;
        border-radius: 12px;
    }

    .qv-lobby-player-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #fef08a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }
    
    .qv-lobby-player-avatar.avatar-normal {
        background: #e2e8f0;
        color: #64748b;
        font-weight: 800;
        font-size: 18px;
    }

    .qv-lobby-player-info {
        flex: 1;
        margin-left: 16px;
    }

    .qv-lobby-player-name {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .qv-lobby-player-status {
        font-size: 15px;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }
    
    .qv-lobby-player-status .dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
    }

    .qv-lobby-player-rank {
        background: white;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 800;
        color: #64748b;
        font-size: 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    .qv-lobby-status-box {
        background: #fafaf9;
        border-radius: 12px;
        padding: 24px 20px;
        border: 2px dashed #e2e8f0;
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .qv-lobby-status-message {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 17px;
        color: #64748b;
        font-weight: 600;
    }

    .qv-lobby-play-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        background: #22c55e;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 18px;
        font-size: 20px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
    }

    .qv-lobby-play-btn:hover:not(:disabled) {
        background: #16a34a;
        transform: translateY(-2px);
    }

    .qv-lobby-play-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    @media (max-width: 900px) {
        .qv-lb-panel-body { max-height: 250px; }
        .qv-lobby-two-column {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    @media (max-width: 768px) {
        .qv-answers-grid { grid-template-columns: 1fr; }
        .qv-playing-header { flex-direction: column; gap: 12px; text-align: center; }
        .qv-question-text { font-size: 20px; }
        .qv-lobby-title { font-size: 24px; }
        .qv-lobby-title-large { font-size: 32px; }
        .qv-lobby-header {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }
        .qv-lobby-code-badge {
            align-self: flex-end;
        }
        .qv-lobby-two-column {
            grid-template-columns: 1fr;
        }
        .qv-lobby-icon-pulse {
            width: 80px;
            height: 80px;
        }
    }
    /* User dropdown profile in top right corner */
    .qv-user-wrapper {
        position: relative;
        z-index: 100;
        margin-left: auto;
        width: fit-content;
        margin-bottom: 20px;
        margin-top: -10px;
    }
    .qv-user-pill { background: white; border: 1.5px solid #e2e8f0; border-radius: 50px; padding: 5px 14px 5px 5px; display: flex; align-items: center; gap: 10px; cursor: pointer; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04); transition: all 0.2s ease; }
    .qv-user-pill:hover { background: #f8fafc; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08); border-color: #cbd5e1; }
    .qv-user-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2); overflow: hidden; }
    .qv-user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .qv-user-info { text-align: left; }
    .qv-user-name { font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1.2; }
    .qv-user-role { font-size: 11px; color: #64748b; font-weight: 500; }
    .qv-user-arrow { color: #64748b; margin-left: 2px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s ease; }
    .qv-user-dropdown { position: absolute; top: 54px; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 16px; width: 280px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1); padding: 20px; display: none; z-index: 101; pointer-events: auto; }
    .qv-dropdown-header { padding-bottom: 4px; }
    .qv-dropdown-name { font-size: 16px; font-weight: 800; color: #0f172a; }
    .qv-dropdown-email { font-size: 13px; color: #64748b; margin-top: 3px; }
    .qv-dropdown-divider { height: 1px; background: #f1f5f9; margin: 15px -20px; }
    .qv-dropdown-item { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-radius: 12px; color: #334155; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s ease; }
    .qv-dropdown-item:hover { background: #f1f5f9; color: #2563eb; }
    .qv-item-icon { color: #3b82f6; }
    .qv-modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 1000; }
    .qv-modal-content { background: white; border-radius: 24px; padding: 35px 30px; max-width: 400px; width: 90%; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15); text-align: center; border: 1px solid #e2e8f0; }
    .qv-modal-content h3 { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0 0 10px; }
    .qv-modal-content p { font-size: 14px; color: #64748b; margin: 0 0 20px; }
    .qv-modal-input { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1.5px solid #cbd5e1; font-size: 15px; font-weight: 600; color: #1e293b; outline: none; transition: all 0.2s; box-sizing: border-box; text-align: center; margin-bottom: 24px; }
    .qv-modal-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .qv-modal-actions { display: flex; gap: 12px; justify-content: center; }
    .qv-modal-btn { padding: 12px 24px; border-radius: 50px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: all 0.2s; }
    .qv-modal-btn.btn-cancel { background: #f1f5f9; color: #475569; }
    .qv-modal-btn.btn-cancel:hover { background: #e2e8f0; }
    .qv-modal-btn.btn-confirm { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }
    .qv-modal-btn.btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3); }
</style>
