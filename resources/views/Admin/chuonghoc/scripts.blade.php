<script>
    // Hàm mở modal thêm mới
    function openModalAdd() {
        const modal = document.getElementById('modalChuongHoc');
        const form = document.getElementById('formChuongHoc');

        if (modal) {
            document.getElementById('modalTitle').innerText = "Thêm chương học mới";
            if (form) {
                form.reset();
                document.getElementById('input_id').value = "";
                form.action = "{{ route('admin.chuonghoc.save') }}";
            }
            modal.classList.add('show');
        }
    }

    function editChuong(data) {
        const modal = document.getElementById('modalChuongHoc');
        if (modal) {
            document.getElementById('modalTitle').innerText = "Chỉnh sửa chương học";
            document.getElementById('input_id').value = data.id;
            document.getElementById('input_ten_chuong').value = data.ten_chuong;
            document.getElementById('input_mon_hoc_id').value = data.mon_hoc_id;
            document.getElementById('input_thu_tu').value = data.thu_tu;
            document.getElementById('input_trang_thai').value = data.trang_thai ? 1 : 0;

            modal.classList.add('show');
        }
    }

    // Hàm đóng Modal dùng chung
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = "none";
        }
    }

    // Khởi tạo các hiệu ứng thông báo giống Khối học
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll(".alert");
            alerts.forEach((alert) => {
                alert.style.transition = "all 0.5s ease";
                alert.style.opacity = "0";
                alert.style.transform = "translateY(-10px)";
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
