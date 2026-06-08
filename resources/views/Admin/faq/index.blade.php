@extends('Admin.layouts.admin')
@section('title', 'Quản lý Câu hỏi thường gặp (FAQ)')

@section('header_action')
    <button class="btn btn-primary" onclick="openModalAdd()"
        style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm FAQ mới</span>
    </button>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger"
            style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="filters-section">
        <form action="{{ route('admin.faq.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>Đang hiển thị</option>
                    <option value="0" {{ request('trang_thai') == '0' ? 'selected' : '' }}>Đang ẩn</option>
                </select>
            </div>

            <div class="filter-item" style="grid-column: span 2">
                <label>Tìm kiếm</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm câu hỏi hoặc câu trả lời..." />
                </div>
            </div>
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách FAQ</h3>
                    <span class="count-badge">Tổng: {{ $faqs->total() }} bản ghi</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th>Câu hỏi</th>
                            <th>Câu trả lời</th>
                            <th width="100">Thứ tự</th>
                            <th width="120">Trạng thái</th>
                            <th width="140">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($faqs as $item)
                            <tr>
                                <td><input type="checkbox" class="checkbox" /></td>
                                <td><strong>{{ $item->question }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($item->answer, 100) }}</td>
                                <td><strong>{{ $item->order }}</strong></td>
                                <td>
                                    <span class="badge {{ $item->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $item->is_active ? 'Hiển thị' : 'Đang ẩn' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" title="Sửa"
                                            onclick="editFaq({{ $item }})">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>

                                        <button class="btn-action btn-delete" title="Xóa"
                                            onclick="deleteFaq({{ $item->id }}, '{{ addslashes($item->question) }}')">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                {{ $faqs->appends(request()->query())->links() }}
            </div>
        </div>
    </section>

    <div id="modalFaq" class="modal-overlay" onclick="dongModalFaq('modalFaq')">
        <div class="modal-card" style="max-width: 600px; max-height: 90vh; overflow-y: auto;"
            onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm FAQ mới</h3>
                <button type="button" class="close-btn" onclick="dongModalFaq('modalFaq')">&times;</button>
            </div>

            <form id="formFaq" action="{{ route('admin.faq.save') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="id" id="input_id">

                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Câu hỏi *</label>
                        <input type="text" name="question" id="input_question" class="form-input" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"
                            placeholder="Nhập câu hỏi...">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Câu trả lời *</label>
                        <textarea name="answer" id="input_answer" class="form-input" rows="5" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;" placeholder="Nhập câu trả lời..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select name="is_active" id="input_is_active" class="form-select"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                <option value="1">Đang hiển thị</option>
                                <option value="0">Đang ẩn</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Thứ tự hiển thị</label>
                            <input type="number" name="order" id="input_order" class="form-input" value="0"
                                min="0"
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer"
                    style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee;">
                    <button type="button" class="btn btn-secondary"
                        onclick="dongModalFaq('modalFaq')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu FAQ</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModalAdd() {
            const modal = document.getElementById('modalFaq');
            const form = document.getElementById('formFaq');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Thêm FAQ mới";
                if (form) {
                    form.reset();
                    document.getElementById('input_id').value = "";
                }
                modal.classList.add('show');
                modal.style.display = "flex";
            }
        }

        function editFaq(data) {
            const modal = document.getElementById('modalFaq');
            const form = document.getElementById('formFaq');

            if (modal) {
                document.getElementById('modalTitle').innerText = "Chỉnh sửa FAQ";
                form.reset();

                document.getElementById('input_id').value = data.id;
                document.getElementById('input_question').value = data.question;
                document.getElementById('input_answer').value = data.answer;
                document.getElementById('input_order').value = data.order;
                document.getElementById('input_is_active').value = data.is_active ? 1 : 0;

                modal.classList.add('show');
                modal.style.display = "flex";
            }
        }

        function dongModalFaq(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('show');
                modal.style.display = "none";
                const form = document.getElementById('formFaq');
                if (form) form.reset();
            }
        }

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

        function deleteFaq(id, title) {
            const url = `{{ route('admin.faq.destroy', '') }}/${id}`;
            deleteDataAjax(id, title, url);
        }
    </script>
@endsection
