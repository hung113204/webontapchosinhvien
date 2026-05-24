@extends('Admin.layouts.admin')
@section('title', 'Danh sách Classroom Exercise')

@section('content')
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger"
            style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <strong>✗</strong> {{ session('error') }}
        </div>
    @endif
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Lớp học & Bài tập đã giao</h3>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <td width="60">Id</td>
                            <td>Lớp học</td>
                            <td>Task (Đề thi)</td>
                            <td>Ngày tạo</td>
                            <td>Ngày sửa</td>
                            <td width="100">Hành động</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    <div style="font-weight: 600; color: #4f46e5;">
                                        🏫 {{ $item->ten_lop }}
                                    </div>
                                </td>
                                <td>
                                    <div style="color: var(--gray-700);">
                                        📄 {{ $item->ten_bai }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: var(--gray-500);">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div style="font-size: 12px; color: var(--gray-500);">
                                        {{ \Carbon\Carbon::parse($item->updated_at)->format('d-m-Y H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- Nút xóa phân công --}}
                                        <button class="btn-action btn-delete"
                                            onclick="deleteAssignment({{ $item->id }})" title="Thu hồi bài tập">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                style="width: 16px; height: 16px;">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: var(--gray-500);">
                                    Chưa có bài tập nào được phân công cho lớp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                {{ $assignments->appends(request()->query())->links() }}
            </div>
        </div>
    </section>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function deleteAssignment(id) {
                Swal.fire({
                    title: "Thu hồi bài tập?",
                    text: "Lớp học này sẽ không thấy bài tập này nữa. Dữ liệu đề thi gốc vẫn được giữ nguyên!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#4f46e5", // Màu tím đồng bộ với hệ thống
                    cancelButtonColor: "#ef4444",
                    confirmButtonText: "Đồng ý thu hồi",
                    cancelButtonText: "Hủy"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hiển thị loading trong khi chờ server xử lý
                        Swal.showLoading();

                        // Tạo URL xóa dựa trên Route đã định nghĩa
                        const url = "{{ route('admin.phancongbaitap.destroy', ':id') }}".replace(':id', id);

                        fetch(url, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        "content"),
                                    "Content-Type": "application/json",
                                    "Accept": "application/json",
                                },
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Đã thu hồi!",
                                        text: data.message,
                                        icon: "success",
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload(); // Tải lại trang để cập nhật danh sách
                                    });
                                } else {
                                    Swal.fire("Lỗi!", data.message || "Không thể thực hiện thao tác.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Thất bại", "Đã có lỗi hệ thống xảy ra!", "error");
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
