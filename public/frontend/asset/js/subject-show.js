document.addEventListener("DOMContentLoaded", function() {

    // ── Highlight.js ──
    if (typeof hljs !== 'undefined') hljs.highlightAll();

    // ── Copy code ──
    window.copyCode = function(id) {
        const text = document.getElementById(id).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.querySelector(`[onclick="copyCode('${id}')"]`);
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Đã sao chép!';
            btn.style.background = "rgba(16,185,129,0.25)";
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.style.background = "";
            }, 1800);
        });
    };

    // ── Chapter accordion ──
    document.querySelectorAll(".chapter-header").forEach(header => {
        header.addEventListener("click", () => {
            header.closest(".chapter-item").classList.toggle("open");
        });
    });

    // ── Quiz: chọn đáp án & Kiểm tra ngay ──
    const resultArea = document.getElementById("quiz-result");
    const explainArea = document.getElementById("quiz-explanation");
    let quizAnswered = false;

    document.querySelectorAll(".quiz-opt").forEach(opt => {
        opt.addEventListener("click", () => {
            if (quizAnswered) return; // Chỉ cho phép chọn 1 lần
            quizAnswered = true;

            const radio = opt.querySelector("input");
            radio.checked = true;
            const isCorrect = radio.dataset.correct === "true";

            // Vô hiệu hóa tất cả các option
            document.querySelectorAll(".quiz-opt").forEach(o => {
                o.classList.add("disabled");
                
                // Nếu đây là đáp án đúng, tự động highlight xanh lá
                const oRadio = o.querySelector("input");
                if (oRadio.dataset.correct === "true") {
                    o.classList.add("correct");
                }
            });

            // Highlight đáp án vừa click nếu sai
            if (!isCorrect) {
                opt.classList.add("incorrect");
            }

            // Hiển thị kết quả
            if (resultArea) {
                resultArea.innerHTML = isCorrect ?
                    `<div class="alert alert-success"><i class="fas fa-check-circle"></i><span>✨ Chính xác! Bạn đã hiểu bài rồi!</span></div>` :
                    `<div class="alert alert-error"><i class="fas fa-times-circle"></i><span>Chưa đúng! Hãy xem giải thích nhé.</span></div>`;
            }

            // Tự động hiển thị giải thích
            if (explainArea) {
                explainArea.style.display = "block";
                if (window.MathJax) MathJax.typesetPromise();
            }
        });
    });

    // ── Bookmark ──
    const btnBookmark = document.getElementById("btn-bookmark");
    if (btnBookmark) {
        const lessonId = window.SubjectConfig.lessonId;
        if (localStorage.getItem(`bookmark_${lessonId}`) === 'true') {
            btnBookmark.innerHTML = `<i class="fas fa-bookmark" style="color:#eab308;"></i> Đã đánh dấu`;
            btnBookmark.style.cssText = "background:#fef9c3;border-color:#fef08a;color:#a16207;";
        }
        btnBookmark.addEventListener("click", function() {
            const icon = this.querySelector("i");
            if (icon.classList.contains("far")) {
                icon.className = "fas fa-bookmark";
                this.innerHTML =
                    `<i class="fas fa-bookmark" style="color:#eab308;"></i> Đã đánh dấu`;
                this.style.cssText = "background:#fef9c3;border-color:#fef08a;color:#a16207;";
                localStorage.setItem(`bookmark_${lessonId}`, 'true');
                showToast('Đã đánh dấu bài học', 'success');
            } else {
                this.innerHTML = `<i class="far fa-bookmark"></i> Đánh dấu`;
                this.style.cssText = "";
                localStorage.removeItem(`bookmark_${lessonId}`);
                showToast('Đã bỏ đánh dấu', 'info');
            }
        });
    }

    // ════════════════════════════════════════════════════
    // ── NÚT "ĐÁNH DẤU HOÀN THÀNH" ── CẬP NHẬT TIẾN ĐỘ REALTIME
    // ════════════════════════════════════════════════════
    const btnComplete = document.getElementById("btn-complete");
    if (btnComplete) {
        btnComplete.addEventListener("click", function() {
            const baiHocId = this.dataset.baiId;
            const originalContent = this.innerHTML;

            // BẮT ĐẦU LOADING
            this.disabled = true;
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

            fetch(window.SubjectConfig.routes.tiendoUpdate, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": window.SubjectConfig.csrfToken
                    },
                    body: JSON.stringify({
                        bai_hoc_id: baiHocId
                    })
                })
                .then(response => {
                    // Kiểm tra nếu phản hồi từ server không ok (Lỗi 500, 404, 419...)
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text)
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    this.classList.remove('loading');

                    if (data.success) {
                        // 1. Cập nhật nút chính
                        this.className = "btn-complete-lesson done";
                        this.innerHTML = '<i class="fas fa-check-circle"></i> Đã hoàn thành';
                        this.disabled = true;

                        // 2. Đánh dấu bài học trong Sidebar
                        const sidebarLink = document.querySelector(
                            `.chapter-lessons a[data-bai-id="${baiHocId}"]`);
                        if (sidebarLink) {
                            sidebarLink.classList.add('completed');
                            const iconEl = sidebarLink.querySelector('i, span.lesson-check');
                            if (iconEl) {
                                iconEl.outerHTML =
                                    `<span class="lesson-check"><i class="fas fa-check"></i></span>`;
                            }
                        }

                        // 3. TÍNH TOÁN LẠI TIẾN ĐỘ
                        const allLinks = document.querySelectorAll(
                            '.chapter-lessons a[data-bai-id]');
                        const completedLinks = document.querySelectorAll(
                            '.chapter-lessons a.completed');
                        const total = allLinks.length;
                        const completed = completedLinks.length;
                        const percentage = total > 0 ? Math.round((completed / total) * 100) :
                            0;

                        // 4. CẬP NHẬT GIAO DIỆN (Dùng selector an toàn hơn)
                        const fill = document.getElementById('sidebar-prog-fill');
                        const pctLabel = document.getElementById('sidebar-prog-label');
                        const countLabel = document.getElementById('sidebar-count-label');

                        if (fill) fill.style.width = percentage + '%';
                        if (pctLabel) pctLabel.textContent = percentage + '% hoàn thành';

                        // Cập nhật text X/Y BÀI (Tìm thẻ span chứa chữ BÀI)
                        /* const labels = document.querySelectorAll('aside span, aside h3');
                        labels.forEach(el => {
                            if (el.textContent.includes('BÀI')) {
                                el.textContent = `${completed}/${total} BÀI`;
                            }
                        }); */
                        if (countLabel) {
                            countLabel.textContent = `${completed}/${total} BÀI`;
                        } else {
                            // Phương án dự phòng nếu bạn quên chưa thêm ID hoặc sai tên ID
                            const labels = document.querySelectorAll('aside span, aside h3');
                            labels.forEach(el => {
                                if (el.textContent.includes('BÀI')) {
                                    el.textContent = `${completed}/${total} BÀI`;
                                }
                            });
                        }

                        // CẬP NHẬT HERO META CHIP
                        const heroCount = document.getElementById('hero-progress-count');
                        const heroPercent = document.getElementById('hero-progress-percent');
                        if (heroCount) heroCount.textContent = `${completed}/${total} bài`;
                        if (heroPercent) heroPercent.textContent = `${percentage}%`;

                        // 5. Cập nhật Badge của Chương
                        if (sidebarLink) {
                            const chapterItem = sidebarLink.closest('.chapter-item');
                            const chBadge = chapterItem.querySelector('.ch-progress-badge');
                            const chTotal = chapterItem.querySelectorAll('.chapter-lessons a')
                                .length;
                            const chDone = chapterItem.querySelectorAll(
                                '.chapter-lessons a.completed').length;

                            if (chBadge) {
                                if (chDone === chTotal) {
                                    chBadge.innerHTML = '<i class="fas fa-check-circle"></i>';
                                    chBadge.style.cssText =
                                        "background:#f0fdf4;color:#10b981;border-color:#bbf7d0;";
                                } else {
                                    chBadge.textContent = `${chDone}/${chTotal}`;
                                }
                            }
                        }
                        showToast('🎉 Tuyệt vời! Đã cập nhật tiến độ.', 'success');
                    } else {
                        throw new Error(data.message || 'Lỗi xử lý');
                    }
                })
                .catch(error => {
                    console.error("Lỗi chi tiết:", error);
                    this.classList.remove('loading');
                    this.disabled = false;
                    this.innerHTML = originalContent;

                    // Hiện thông báo lỗi cụ thể hơn để debug
                    showToast('Lỗi: Kiểm tra tab Console (F12) để xem chi tiết', 'error');
                });
        });
    }

    // ── Toast helper ──
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notify ${type}`;
        toast.innerHTML =
            `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }

    // ── Scroll to top ──
    const scrollBtn = document.getElementById('scrollTopBtn');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('show', window.scrollY > 300);
        });
        scrollBtn.addEventListener('click', () => window.scrollTo({
            top: 0,
            behavior: 'smooth'
        }));
    }

});
