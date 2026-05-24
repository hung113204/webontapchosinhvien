/**
 * Hàm xóa dữ liệu dùng chung bằng Ajax & SweetAlert2
 * @param {number} id - ID của bản ghi cần xóa
 * @param {string} name - Tên hiển thị của bản ghi (để hiện thông báo)
 * @param {string} url - Route xóa (ví dụ: /admin/mon-hoc/delete/1)
 */
function deleteDataAjax(id, name, url) {
    Swal.fire({
        title: "Xác nhận xóa?",
        text: `Bạn có chắc chắn muốn xóa "${name}" không? Thao tác này không thể hoàn tác!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4f46e5", // Màu tím primary theo phong cách của bạn
        cancelButtonColor: "#ef4444",
        confirmButtonText: "Đồng ý xóa",
        cancelButtonText: "Hủy",
    }).then((result) => {
        if (result.isConfirmed) {
            // Hiển thị trạng thái đang xử lý
            Swal.showLoading();

            fetch(url, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            title: "Đã xóa!",
                            text: data.message || "Dữ liệu đã được loại bỏ.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => {
                            location.reload(); // Tải lại trang để cập nhật danh sách
                        });
                    } else {
                        Swal.fire(
                            "Lỗi!",
                            data.message ||
                                "Không thể xóa do ràng buộc dữ liệu.",
                            "error",
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire(
                        "Thất bại",
                        "Đã có lỗi hệ thống xảy ra!",
                        "error",
                    );
                });
        }
    });
}
