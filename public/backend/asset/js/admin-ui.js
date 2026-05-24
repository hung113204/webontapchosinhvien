/**
 * Admin UI Helper Functions
 * Fixed version with all modal functions
 */
(function () {
    "use strict";

    // ===== SIDEBAR TOGGLE (Mobile) =====
    function initSidebarToggle() {
        const header = document.querySelector(".top-header");
        if (!header || window.innerWidth > 768) return;

        // Kiểm tra tránh tạo lặp nút bấm
        if (document.querySelector(".sidebar-toggle")) return;

        const toggleBtn = document.createElement("button");
        toggleBtn.className = "sidebar-toggle";
        toggleBtn.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12" />
                <line x1="3" y1="6" x2="21" y2="6" />
                <line x1="3" y1="18" x2="21" y2="18" />
            </svg>`;

        header.insertBefore(toggleBtn, header.firstChild);

        // Tạo lớp phủ overlay
        let overlay = document.querySelector(".sidebar-overlay");
        if (!overlay) {
            overlay = document.createElement("div");
            overlay.className = "sidebar-overlay";
            document.body.appendChild(overlay);
        }

        toggleBtn.onclick = function () {
            document.querySelector(".sidebar").classList.toggle("show");
            document.body.classList.toggle("sidebar-open");
        };

        overlay.onclick = function () {
            document.querySelector(".sidebar").classList.remove("show");
            document.body.classList.remove("sidebar-open");
        };
    }

    // ===== DROPDOWN MENU =====
    function initDropdowns() {
        const dropdowns = document.querySelectorAll(".dropdown");
        dropdowns.forEach((dropdown) => {
            const toggle = dropdown.querySelector(".dropdown-toggle");
            if (toggle) {
                toggle.addEventListener("click", function (e) {
                    e.stopPropagation();
                    document.querySelectorAll(".dropdown").forEach((d) => {
                        if (d !== dropdown) d.classList.remove("active");
                    });
                    dropdown.classList.toggle("active");
                });
            }
        });

        document.addEventListener("click", () => {
            document
                .querySelectorAll(".dropdown")
                .forEach((d) => d.classList.remove("active"));
        });
    }

    // ===== TOAST NOTIFICATION =====
    function showToast(message, type = "info", duration = 3000) {
        const icons = {
            success: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12" /></svg>`,
            error: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><line x1="15" y1="9" x2="9" y2="15" /><line x1="9" y1="9" x2="15" y2="15" /></svg>`,
            warning: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>`,
            info: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" /></svg>`,
        };

        let container = document.querySelector(".toast-container");
        if (!container) {
            container = document.createElement("div");
            container.className = "toast-container";
            document.body.appendChild(container);
        }

        const toast = document.createElement("div");
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-message">${message}</div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>`;

        container.appendChild(toast);
        setTimeout(() => toast.classList.add("show"), 10);
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // ===== MULTI-SELECT CHAPTER TAGS =====
    function addChapterTag() {
        const dropdown = document.getElementById("chapter-dropdown");
        const container = document.getElementById("selected-chapters");
        if (!dropdown || !container) return;

        const selectedValue = dropdown.value;
        const selectedText = dropdown.options[dropdown.selectedIndex].text;
        if (selectedValue === "") return;

        const existingTags = container.querySelectorAll(".chapter-tag");
        for (let tag of existingTags) {
            if (tag.dataset.value === selectedValue) {
                dropdown.value = "";
                return;
            }
        }

        const tag = document.createElement("div");
        tag.className = "chapter-tag";
        tag.dataset.value = selectedValue;
        tag.innerHTML = `
            <span class="remove-tag" onclick="this.parentElement.remove()">×</span>
            <span>${selectedText}</span>`;

        container.appendChild(tag);
        dropdown.value = "";
    }

    // ===== TABLE CHECKBOX =====
    function initTableCheckbox() {
        const selectAll = document.querySelector(".data-table thead .checkbox");
        if (selectAll) {
            selectAll.addEventListener("change", function () {
                document
                    .querySelectorAll(".data-table tbody .checkbox")
                    .forEach((cb) => (cb.checked = this.checked));
            });
        }
    }

    // ===== MODAL FUNCTIONS =====
    function openModal(modalId) {
        // Ẩn sidebar overlay nếu có
        const sidebarOverlay = document.querySelector(".sidebar-overlay");
        if (sidebarOverlay) {
            sidebarOverlay.style.display = "none";
        }

        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error("Modal not found:", modalId);
            return;
        }

        modal.classList.add("show");
        modal.style.zIndex = "10001";
        document.body.style.overflow = "hidden";
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove("show");
        }
        document.body.style.overflow = "auto";
    }

    // ===== CONFIRM DELETE =====
    function confirmDelete(form, message) {
        if (confirm(message || "Bạn có chắc chắn muốn xóa?")) {
            return true;
        }
        return false;
    }

    // ===== INIT =====
    function init() {
        initSidebarToggle();
        initDropdowns();
        initTableCheckbox();

        // Xử lý resize để hiện/ẩn nút toggle mobile
        window.addEventListener("resize", () => {
            const toggle = document.querySelector(".sidebar-toggle");
            if (window.innerWidth <= 768) {
                if (!toggle) initSidebarToggle();
            } else if (toggle) {
                toggle.remove();
                document.querySelector(".sidebar-overlay")?.remove();
            }
        });
    }

    // Export functions to window
    window.adminUI = {
        showToast,
        addChapterTag,
        init,
    };

    // Export modal functions globally
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.confirmDelete = confirmDelete;

    // Init when ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
