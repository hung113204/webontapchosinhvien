@extends('Admin.layouts.admin')
@section('title', 'Chi tiết tiến độ: ' . ($monHoc->ten_mon_hoc ?? 'Môn học'))

@section('content')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #818cf8;
        --primary-dark: #4338ca;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .bf-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .bf-btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-sm);
        color: var(--gray-600);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .bf-btn-back:hover {
        background: var(--gray-50);
        border-color: var(--gray-300);
        color: var(--gray-800);
    }

    .subject-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: 28px 32px;
        margin-bottom: 28px;
        color: white;
    }

    .progress-summary {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .progress-stat {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 12px 24px;
        border-radius: var(--radius-md);
        text-align: center;
        min-width: 100px;
    }

    .progress-stat-value {
        font-size: 28px;
        font-weight: 700;
    }

    .progress-stat-label {
        font-size: 12px;
        opacity: 0.85;
        margin-top: 4px;
    }

    .overall-progress {
        margin-top: 20px;
    }

    .overall-progress-bar {
        height: 8px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        overflow: hidden;
    }

    .overall-progress-fill {
        height: 100%;
        background: white;
        border-radius: 20px;
        transition: width 0.5s ease;
    }

    .chapter-card {
        background: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .chapter-card:hover {
        box-shadow: var(--shadow-md);
    }

    .chapter-header {
        padding: 20px 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--gray-50);
        transition: background 0.2s;
    }

    .chapter-header:hover {
        background: var(--gray-100);
    }

    .chapter-title {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
    }

    .chapter-number {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
    }

    .chapter-info {
        flex: 1;
    }

    .chapter-name {
        font-weight: 600;
        font-size: 16px;
        color: var(--gray-800);
        margin-bottom: 6px;
    }

    .chapter-stats {
        display: flex;
        gap: 16px;
        font-size: 12px;
        color: var(--gray-500);
    }

    .chapter-stats span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .chapter-progress {
        width: 120px;
    }

    .chapter-progress-bar {
        height: 6px;
        background: var(--gray-200);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .chapter-progress-fill {
        height: 100%;
        background: var(--success);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .chapter-progress-text {
        font-size: 11px;
        color: var(--gray-500);
        text-align: right;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
        color: var(--gray-400);
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    .lesson-list {
        display: none;
        padding: 0 24px 24px 24px;
        background: white;
    }

    .lesson-list.active {
        display: block;
    }

    .lesson-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        margin-top: 12px;
        background: var(--gray-50);
        border-radius: var(--radius-md);
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .lesson-item:hover {
        border-color: var(--gray-200);
        background: white;
    }

    .lesson-item.completed {
        background: #ecfdf5;
        border-left: 3px solid var(--success);
    }

    .lesson-info {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
    }

    .lesson-status {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .lesson-status.completed {
        background: var(--success);
        color: white;
    }

    .lesson-status.pending {
        background: var(--gray-200);
        color: var(--gray-400);
    }

    .lesson-details {
        flex: 1;
    }

    .lesson-name {
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 4px;
    }

    .lesson-item.completed .lesson-name {
        color: var(--success);
    }

    .lesson-meta {
        font-size: 11px;
        color: var(--gray-400);
        display: flex;
        gap: 12px;
    }

    .btn-complete {
        padding: 6px 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-complete:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
    }

    .btn-complete:disabled {
        background: var(--gray-400);
        cursor: not-allowed;
        transform: none;
    }

    .badge-completed {
        background: var(--success);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .toast-message {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 12px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 500;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        box-shadow: var(--shadow-lg);
    }

    .toast-success {
        background: var(--success);
    }

    .toast-error {
        background: var(--danger);
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .empty-chapter {
        padding: 40px;
        text-align: center;
        color: var(--gray-400);
        background: var(--gray-50);
        border-radius: var(--radius-md);
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        background: var(--gray-100);
        border-radius: 20px;
        font-size: 11px;
        color: var(--gray-600);
    }
</style>

<div class="bf-container" style="padding: 20px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.tiendo.index', ['user_id' => $user->id]) }}" class="bf-btn-back">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Quay lại tổng quan
        </a>
    </div>

    @php
        $totalLessons = 0;
        $completedLessons = 0;
        foreach($monHoc->chuongHocs as $chuong) {
            $lessons = $chuong->baiHocs ?? collect();
            $totalLessons += $lessons->count();
            $completedLessons += $lessons->filter(fn($lesson) => in_array($lesson->id, $baiHocDaXongIds ?? []))->count();
        }
        $overallPercent = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100) : 0;
    @endphp

    <!-- Header môn học -->
    <div class="subject-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="font-size: 28px; margin-bottom: 12px;">{{ $monHoc->ten_mon_hoc }}</h1>
                @if($monHoc->mo_ta_ngan)
                    <p style="opacity: 0.9; margin: 0;">{{ $monHoc->mo_ta_ngan }}</p>
                @endif
            </div>
            <div class="progress-summary">
                <div class="progress-stat">
                    <div class="progress-stat-value">{{ $totalLessons }}</div>
                    <div class="progress-stat-label">Tổng bài học</div>
                </div>
                <div class="progress-stat">
                    <div class="progress-stat-value">{{ $completedLessons }}</div>
                    <div class="progress-stat-label">Đã hoàn thành</div>
                </div>
                <div class="progress-stat">
                    <div class="progress-stat-value">{{ $overallPercent }}%</div>
                    <div class="progress-stat-label">Tiến độ</div>
                </div>
            </div>
        </div>
        <div class="overall-progress">
            <div class="overall-progress-bar">
                <div class="overall-progress-fill" style="width: {{ $overallPercent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Danh sách chương -->
    @foreach($monHoc->chuongHocs as $index => $chuong)
        @php
            $lessons = $chuong->baiHocs ?? collect();
            $totalInChapter = $lessons->count();
            $completedInChapter = $lessons->filter(fn($lesson) => in_array($lesson->id, $baiHocDaXongIds ?? []))->count();
            $chapterPercent = $totalInChapter > 0 ? round(($completedInChapter / $totalInChapter) * 100) : 0;
        @endphp
        <div class="chapter-card">
            <div class="chapter-header" onclick="toggleChapter(this)">
                <div class="chapter-title">
                    <div class="chapter-number">{{ $index + 1 }}</div>
                    <div class="chapter-info">
                        <div class="chapter-name">{{ $chuong->ten_chuong }}</div>
                        <div class="chapter-stats">
                            <span>
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                </svg>
                                {{ $totalInChapter }} bài học
                            </span>
                            @if($completedInChapter > 0)
                                <span>
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Đã hoàn thành {{ $completedInChapter }}/{{ $totalInChapter }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    @if($totalInChapter > 0)
                        <div class="chapter-progress">
                            <div class="chapter-progress-bar">
                                <div class="chapter-progress-fill" style="width: {{ $chapterPercent }}%"></div>
                            </div>
                            <div class="chapter-progress-text">{{ $chapterPercent }}%</div>
                        </div>
                    @endif
                    <svg class="toggle-icon" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </div>
            <div class="lesson-list">
                @forelse($lessons as $lesson)
                    @php $isCompleted = in_array($lesson->id, $baiHocDaXongIds ?? []); @endphp
                    <div class="lesson-item {{ $isCompleted ? 'completed' : '' }}" data-lesson-id="{{ $lesson->id }}">
                        <div class="lesson-info">
                            <div class="lesson-status {{ $isCompleted ? 'completed' : 'pending' }}">
                                @if($isCompleted)
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                    </svg>
                                @endif
                            </div>
                            <div class="lesson-details">
                                <div class="lesson-name">{{ $lesson->ten_bai_hoc ?? $lesson->tieu_de ?? 'Bài học' }}</div>
                                <div class="lesson-meta">
                                    @if($lesson->thoi_luong)
                                        <span>⏱ {{ $lesson->thoi_luong }} phút</span>
                                    @endif
                                    @if($lesson->thu_tu)
                                        <span>#{{ $lesson->thu_tu }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(!$isCompleted)
                            <button class="btn-complete" onclick="markComplete({{ $lesson->id }}, this)">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Hoàn thành
                            </button>
                        @else
                            <span class="badge-completed">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Đã hoàn thành
                            </span>
                        @endif
                    </div>
                @empty
                    <div class="empty-chapter">
                        <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 12px;">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                        <p>Chưa có bài học nào trong chương này</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach

    @if($monHoc->chuongHocs->count() == 0)
        <div class="chapter-card">
            <div class="empty-chapter" style="padding: 60px;">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 16px; color: var(--gray-400);">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <h3 style="color: var(--gray-600); margin-bottom: 8px;">Chưa có chương học nào</h3>
                <p style="color: var(--gray-400);">Môn học này chưa được cấu trúc thành các chương và bài học.</p>
            </div>
        </div>
    @endif
</div>

<script>
    // Hàm toggle đóng/mở chương
    function toggleChapter(header) {
        const lessonList = header.nextElementSibling;
        const icon = header.querySelector('.toggle-icon');
        
        if (lessonList) {
            lessonList.classList.toggle('active');
        }
        if (icon) {
            icon.classList.toggle('rotated');
        }
    }

    // Hàm hiển thị toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-message toast-${type}`;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="white" stroke-width="2">
                    ${type === 'success' ? '<polyline points="20 6 9 17 4 12"/>' : '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}
                </svg>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Hàm cập nhật thống kê tổng quan
    function updateOverallStats() {
        const lessonItems = document.querySelectorAll('.lesson-item');
        const total = lessonItems.length;
        const completed = Array.from(lessonItems).filter(item => item.classList.contains('completed')).length;
        const percent = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        const totalEl = document.querySelector('.progress-stat:first-child .progress-stat-value');
        const completedEl = document.querySelector('.progress-stat:nth-child(2) .progress-stat-value');
        const percentEl = document.querySelector('.progress-stat:last-child .progress-stat-value');
        const progressFill = document.querySelector('.overall-progress-fill');
        
        if (totalEl) totalEl.textContent = total;
        if (completedEl) completedEl.textContent = completed;
        if (percentEl) percentEl.textContent = percent + '%';
        if (progressFill) progressFill.style.width = percent + '%';
    }

    // Hàm cập nhật thống kê cho một chương
    function updateChapterStats(chapterCard) {
        const lessonItems = chapterCard.querySelectorAll('.lesson-item');
        const total = lessonItems.length;
        const completed = Array.from(lessonItems).filter(item => item.classList.contains('completed')).length;
        const percent = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        const statsDiv = chapterCard.querySelector('.chapter-stats');
        const progressFill = chapterCard.querySelector('.chapter-progress-fill');
        const progressText = chapterCard.querySelector('.chapter-progress-text');
        
        if (statsDiv && total > 0) {
            const existingCompleteSpan = Array.from(statsDiv.querySelectorAll('span')).find(
                span => span.textContent.includes('Đã hoàn thành')
            );
            if (completed > 0) {
                if (existingCompleteSpan) {
                    existingCompleteSpan.innerHTML = `
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Đã hoàn thành ${completed}/${total}
                    `;
                } else {
                    const newSpan = document.createElement('span');
                    newSpan.innerHTML = `
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Đã hoàn thành ${completed}/${total}
                    `;
                    statsDiv.appendChild(newSpan);
                }
            }
        }
        
        if (progressFill) progressFill.style.width = percent + '%';
        if (progressText) progressText.textContent = percent + '%';
    }

    // Hàm đánh dấu hoàn thành bài học qua AJAX
    function markComplete(lessonId, button) {
        // Disable button và hiển thị trạng thái loading
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-right: 4px;"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 1 0 10 10"/></svg> Đang xử lý...';
        
        // Thêm style spin nếu chưa có
        if (!document.querySelector('#spin-style')) {
            const style = document.createElement('style');
            style.id = 'spin-style';
            style.textContent = '@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }
        
        fetch('{{ route("admin.tiendo.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bai_hoc_id: lessonId,
                user_id: {{ $user->id }}
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Cập nhật UI cho bài học
                const lessonItem = button.closest('.lesson-item');
                lessonItem.classList.add('completed');
                
                // Cập nhật trạng thái icon
                const statusDiv = lessonItem.querySelector('.lesson-status');
                statusDiv.classList.remove('pending');
                statusDiv.classList.add('completed');
                statusDiv.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
                
                // Thay thế button bằng badge
                button.outerHTML = `
                    <span class="badge-completed">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Đã hoàn thành
                    </span>
                `;
                
                // Cập nhật thống kê chương
                const chapterCard = lessonItem.closest('.chapter-card');
                if (chapterCard) {
                    updateChapterStats(chapterCard);
                }
                
                // Cập nhật thống kê tổng quan
                updateOverallStats();
                
                // Hiển thị thông báo thành công
                showToast('✓ Đã ghi nhận hoàn thành bài học!', 'success');
            } else {
                button.disabled = false;
                button.innerHTML = originalText;
                showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
                console.error('Response errors:', data.errors);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.disabled = false;
            button.innerHTML = originalText;
            showToast('Lỗi kết nối hoặc server: ' + error.message, 'error');
        });
    }

    // Mở chương đầu tiên theo mặc định
    document.addEventListener('DOMContentLoaded', function() {
        const firstChapterList = document.querySelector('.chapter-card .lesson-list');
        const firstChapterIcon = document.querySelector('.chapter-card .toggle-icon');
        if (firstChapterList) {
            firstChapterList.classList.add('active');
        }
        if (firstChapterIcon) {
            firstChapterIcon.classList.add('rotated');
        }
    });
</script>
@endsection