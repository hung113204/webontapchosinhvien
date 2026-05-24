/**
 * subjects-filter.js
 * Lọc học phần bằng AJAX — Đã loại bỏ Khối học
 */
(function () {
    "use strict";

    // ── DOM refs ───────────────────────────────────────────────────
    const grid = document.getElementById("subjects-grid");
    const inputSearch = document.getElementById("search-subject");
    const btnSearch = document.getElementById("btn-search");
    // ĐÃ XÓA: selectKhoi
    const selectLevel = document.getElementById("level-filter");
    const btnClear = document.getElementById("clear-filters");
    const noResults = document.getElementById("no-results");

    if (!grid) return;

    // ── Debounce helper ────────────────────────────────────────────
    let debounceTimer;
    function debounce(fn, ms) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fn, ms);
    }

    // ── Level config ───────────────────────────────────────────────
    const LEVEL_TEXT = { 1: "Dễ", 2: "Trung bình", 3: "Khó", 4: "Rất khó" };
    const LEVEL_CLASS = {
        1: "basic",
        2: "intermediate",
        3: "advanced",
        4: "advanced",
    };

    // ── Build card HTML (Đã xóa Badge Khối) ────────────────────────
    function buildCardHTML(mon) {
        const color = mon.mau_sac || "#3b82f6";
        const bgIcon = color + "20";
        const iconCls = mon.icon_class || "fas fa-book";
        const lvl = mon.muc_do_mon_hoc || 1;
        const lvlCls = LEVEL_CLASS[lvl] || "basic";
        const lvlTxt = LEVEL_TEXT[lvl] || "Dễ";

        // ĐÃ XÓA: logic lấy khoiTxt

        const desc = mon.mo_ta_ngan || "Chưa có mô tả cho học phần này.";
        const tinChi = mon.so_tin_chi || 3;
        const baiHoc = mon.so_luong_bai_hoc;
        const pct = mon.progress_percent || 0;
        const pctText = pct > 0 ? pct + "%" : "Chưa bắt đầu";
        const fillCls = pct > 0 ? "" : "zero";

        const base =
            (document.querySelector('meta[name="base-url"]') || {}).content ||
            window.location.origin;
        const url = base + "/hoc-phan/" + mon.id;

        const baiHocChip = baiHoc
            ? `<span class="subject-stat-chip"><i class="fas fa-book"></i> ${baiHoc} bài học</span>`
            : "";

        return `
        <a href="${url}" class="subject-item-card">
            <div class="subject-card-header">
                <div class="subject-card-icon" style="color:${color};background:${bgIcon};">
                    <i class="${iconCls}"></i>
                </div>
                <div class="subject-card-badges">
                    <span class="badge-level ${lvlCls}">${lvlTxt}</span>
                </div>
            </div>
            <h3>${mon.ten_mon_hoc}</h3>
            <p>${desc}</p>
            <div class="subject-stat-row">
                <span class="subject-stat-chip">
                    <i class="fas fa-graduation-cap"></i> ${tinChi} tín chỉ
                </span>
                ${baiHocChip}
            </div>
            <div class="subject-progress-wrap">
                <div class="subject-progress-top">
                    <span class="subject-progress-label">Tiến độ học tập</span>
                    <span class="subject-progress-pct">${pctText}</span>
                </div>
                <div class="subject-progress-bar">
                    <div class="subject-progress-fill ${fillCls}" style="width:${pct}%"></div>
                </div>
            </div>
            <div class="subject-card-actions">
                <span class="btn-theory"><i class="fas fa-book-open"></i> Ôn lý thuyết</span>
                <span class="btn-practice"><i class="fas fa-pencil-alt"></i> Luyện tập</span>
            </div>
        </a>`;
    }

    // ── Fetch từ server (Đã xóa params khoi_id) ────────────────────
    function fetchSubjects() {
        const keyword = inputSearch ? inputSearch.value.trim() : "";
        const level = selectLevel ? selectLevel.value : "all";

        const params = new URLSearchParams();
        if (keyword) params.set("keyword", keyword);
        // ĐÃ XÓA: params.set("khoi_id", ...)
        if (level !== "all") params.set("level", level);

        grid.classList.add("loading-state");
        if (noResults) noResults.style.display = "none";

        fetch(window.location.pathname + "?" + params.toString(), {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        })
            .then((res) => {
                if (!res.ok) throw new Error("HTTP " + res.status);
                return res.json();
            })
            .then((data) => {
                grid.classList.remove("loading-state");

                Array.from(grid.children).forEach((el) => {
                    if (el.id !== "no-results") el.remove();
                });

                const subjects = data.subjects || [];
                if (subjects.length === 0) {
                    if (noResults) noResults.style.display = "";
                    return;
                }

                const fragment = document.createDocumentFragment();
                subjects.forEach((mon) => {
                    const tmp = document.createElement("div");
                    tmp.innerHTML = buildCardHTML(mon).trim();
                    fragment.appendChild(tmp.firstChild);
                });
                grid.insertBefore(fragment, noResults);
            })
            .catch((err) => {
                grid.classList.remove("loading-state");
                console.error("[subjects-filter] Lỗi AJAX:", err);
            });
    }

    // ── Event listeners ────────────────────────────────────────────
    if (btnSearch) btnSearch.addEventListener("click", fetchSubjects);

    if (inputSearch) {
        inputSearch.addEventListener("keydown", (e) => {
            if (e.key === "Enter") fetchSubjects();
        });
        inputSearch.addEventListener("input", () =>
            debounce(fetchSubjects, 400),
        );
    }

    // ĐÃ XÓA listener cho selectKhoi
    if (selectLevel) selectLevel.addEventListener("change", fetchSubjects);

    if (btnClear) {
        btnClear.addEventListener("click", () => {
            if (inputSearch) inputSearch.value = "";
            // ĐÃ XÓA reset selectKhoi
            if (selectLevel) selectLevel.value = "all";
            fetchSubjects();
        });
    }
})();
