// ===================================================
// NAVIGATION.JS - GLOBAL FORM LOADING & UI HANDLER
// Dùng chung cho TẤT CẢ các form
// ===================================================

/* =======================
   LOADING OVERLAY
======================= */
function showLoading(message = "Đang xử lý...") {
    if (document.getElementById("loadingOverlay")) return;

    const html = `
        <div id="loadingOverlay" class="loading-overlay">
            <div class="loading-content">
                <div class="spinner"></div>
                <p class="loading-text">${message}</p>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML("beforeend", html);
    document.body.style.overflow = "hidden";
}

function hideLoading() {
    const overlay = document.getElementById("loadingOverlay");
    if (!overlay) return;

    overlay.remove();
    document.body.style.overflow = "";
}

/* =======================
   GLOBAL FORM SUBMIT
======================= */
function handleAllForms() {
    document.addEventListener("submit", function (e) {
        const form = e.target;

        // Chỉ xử lý FORM
        if (!form || form.tagName !== "FORM") return;

        // Bỏ qua nếu form không muốn loading
        if (form.hasAttribute("data-no-loading")) return;

        // HTML5 validation
        if (!form.checkValidity()) return;

        const message =
            form.getAttribute("data-loading-text") || "Đang xử lý...";

        showLoading(message);

        // Disable nút submit
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-small"></span> Đang xử lý...';
        }
    });
}

/* =======================
   CONFIRM DELETE
======================= */
function confirmDelete(url, message = "Bạn có chắc muốn xóa?") {
    if (confirm(message)) {
        showLoading("Đang xóa...");
        window.location.href = url;
    }
    return false;
}

/* =======================
   AUTO HIDE ALERT
======================= */
function autoHideAlerts(time = 5000) {
    setTimeout(() => {
        document.querySelectorAll(".alert").forEach((alert) => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            setTimeout(() => alert.remove(), 500);
        });
    }, time);
}

/* =======================
   INIT WHEN DOM READY
======================= */
document.addEventListener("DOMContentLoaded", function () {
    handleAllForms();
    autoHideAlerts();

    // Trường hợp back từ server có loading cũ
    setTimeout(() => hideLoading(), 300);
});

/* =======================
   LOADING CSS (AUTO INJECT)
======================= */
const style = `
<style>
.loading-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.65);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.loading-content {
    background: #fff;
    padding: 35px 55px;
    border-radius: 14px;
    text-align: center;
}
.spinner {
    width: 48px;
    height: 48px;
    border: 4px solid #eee;
    border-top: 4px solid #4f46e5;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}
.spinner-small {
    width: 14px;
    height: 14px;
    border: 2px solid #fff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin .8s linear infinite;
    display: inline-block;
    margin-right: 6px;
}
.loading-text {
    font-weight: 500;
    color: #333;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
button[disabled] {
    opacity: .7;
    cursor: not-allowed;
}
</style>
`;

if (!document.getElementById("global-loading-style")) {
    const div = document.createElement("div");
    div.id = "global-loading-style";
    div.innerHTML = style;
    document.head.appendChild(div.firstElementChild);
}
document.addEventListener("click", function (e) {
    const link = e.target.closest(".ajax-link");
    if (link) {
        e.preventDefault(); // Chặn load lại trang
        const url = link.getAttribute("href");

        showLoading("Đang tải nội dung...");

        fetch(url)
            .then((response) => response.text())
            .then((html) => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");
                const newContent =
                    doc.querySelector(".table-section").innerHTML; // Chọn vùng nội dung cần thay

                document.querySelector(".table-section").innerHTML = newContent;

                // Cập nhật URL trên thanh địa chỉ mà không reload
                window.history.pushState({ path: url }, "", url);
                hideLoading();
            });
    }
});
