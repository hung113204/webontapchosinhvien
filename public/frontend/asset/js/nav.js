/**
 * nav.js — Mobile hamburger menu (dùng chung cho tất cả trang)
 */
(function () {
    const btn = document.getElementById("mobile-menu-btn");
    const nav = document.getElementById("main-nav");
    if (!btn || !nav) return;

    btn.addEventListener("click", () => {
        const open = nav.classList.toggle("mobile-open");
        btn.setAttribute("aria-expanded", open);
        btn.innerHTML = open
            ? '<i class="fas fa-times"></i>'
            : '<i class="fas fa-bars"></i>';
    });

    // Đóng menu khi click bên ngoài
    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove("mobile-open");
            btn.setAttribute("aria-expanded", "false");
            btn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });

    // Đóng menu khi chọn một link
    nav.querySelectorAll(".nav-menu a").forEach((link) => {
        link.addEventListener("click", () => {
            nav.classList.remove("mobile-open");
            btn.setAttribute("aria-expanded", "false");
            btn.innerHTML = '<i class="fas fa-bars"></i>';
        });
    });
})();
(function () {
    const btn = document.getElementById("mobile-menu-btn");
    const nav = document.getElementById("main-nav");
    if (!btn || !nav) return;

    // --- 1. XỬ LÝ ĐÓNG/MỞ MENU MOBILE ---
    btn.addEventListener("click", () => {
        const open = nav.classList.toggle("mobile-open");
        btn.setAttribute("aria-expanded", open);
        btn.innerHTML = open
            ? '<i class="fas fa-times"></i>'
            : '<i class="fas fa-bars"></i>';
    });

    // Đóng menu khi click bên ngoài
    document.addEventListener("click", (e) => {
        if (!btn.contains(e.target) && !nav.contains(e.target)) {
            nav.classList.remove("mobile-open");
            btn.setAttribute("aria-expanded", "false");
            btn.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });

    // --- 2. XỬ LÝ ACTIVE MENU VÀ ĐÓNG MENU KHI CLICK LINK ---
    const currentPath = window.location.pathname; // VD: '/' hoặc '/hoc-phan'
    const navLinks = nav.querySelectorAll(".nav-menu a");

    navLinks.forEach((link) => {
        // Lấy pathname từ thuộc tính href của thẻ <a>
        const urlObject = new URL(link.href);
        const linkPath = urlObject.pathname;

        // Xóa class active cũ (nếu có) để setup lại từ đầu
        link.classList.remove("active");

        // Logic thêm class 'active'
        // Trường hợp 1: Trùng khớp hoàn toàn (VD: trang chủ '/' === '/')
        if (currentPath === linkPath) {
            link.classList.add("active");
        }
        // Trường hợp 2: Đang ở trang con (VD: '/hoc-phan/c-plus' sẽ làm sáng menu '/hoc-phan')
        // Phải đảm bảo linkPath !== '/' để trang chủ không bị sáng liên tục
        else if (linkPath !== "/" && currentPath.startsWith(linkPath)) {
            link.classList.add("active");
        }

        // Bắt sự kiện click để đóng menu mobile
        link.addEventListener("click", () => {
            nav.classList.remove("mobile-open");
            btn.setAttribute("aria-expanded", "false");
            btn.innerHTML = '<i class="fas fa-bars"></i>';
        });
    });
})();
