@extends('Admin.layouts.admin')
@section('title', 'Quản lý Phiên Luyện Tập')

{{-- @section('header_action')
    <a href="{{ route('admin.phienluyentap.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm phiên mới</span>
    </a>
@endsection --}}

@section('content')
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success"
            style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    {{-- Thống kê nhanh (Mini Stats) --}}
    <section class="filters-section" style="padding-bottom: 0; border: none; background: transparent;">
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
            <div
                style="background: white; padding: 15px; border-radius: 10px; border-left: 4px solid #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">TỔNG PHIÊN</span>
                <h3 style="margin: 5px 0 0; color: #1e293b;">{{ $phienLuyenTaps->total() }}</h3>
            </div>
            <div
                style="background: white; padding: 15px; border-radius: 10px; border-left: 4px solid #f59e0b; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">ĐANG LÀM</span>
                <h3 style="margin: 5px 0 0; color: #1e293b;">{{ $soPhienDangLam ?? 0 }}</h3>
            </div>
            <div
                style="background: white; padding: 15px; border-radius: 10px; border-left: 4px solid #10b981; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <span style="color: #64748b; font-size: 0.85rem; font-weight: 600;">HOÀN THÀNH</span>
                <h3 style="margin: 5px 0 0; color: #1e293b;">{{ $soPhienHoanThanh ?? 0 }}</h3>
            </div>
        </div>
    </section>

    {{-- Filters Section --}}
    <section class="filters-section">
        <form action="{{ route('admin.phienluyentap.index') }}" method="GET" class="filter-group">
            <div class="filter-item">
                <label>Môn học</label>
                <select name="mon_hoc_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả môn học</option>
                    @foreach ($monHocs as $mh)
                        <option value="{{ $mh->id }}" {{ request('mon_hoc_id') == $mh->id ? 'selected' : '' }}>
                            {{ $mh->ten_mon_hoc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-item">
                <label>Chế độ</label>
                <select name="che_do" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả chế độ</option>
                    <option value="1" {{ request('che_do') == '1' ? 'selected' : '' }}>🏃 Luyện tập</option>
                    <option value="2" {{ request('che_do') == '2' ? 'selected' : '' }}>📝 Thi thử</option>
                    <option value="3" {{ request('che_do') == '3' ? 'selected' : '' }}>🔁 Ôn yếu</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="0" {{ request('trang_thai') === '0' ? 'selected' : '' }}>⏳ Đang làm</option>
                    <option value="1" {{ request('trang_thai') == '1' ? 'selected' : '' }}>✅ Hoàn thành</option>
                    <option value="2" {{ request('trang_thai') == '2' ? 'selected' : '' }}>❌ Bỏ dở</option>
                </select>
            </div>

            <div class="filter-item">
                <label>Tìm kiếm User</label>
                <div class="search-box">
                    <input type="text" name="search" placeholder="Tên hoặc email..." value="{{ request('search') }}" />
                </div>
            </div>
        </form>
    </section>

    {{-- Table Section --}}
    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách phiên luyện tập</h3>
                    <span class="count-badge">{{ $phienLuyenTaps->total() }} phiên</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50">STT</th>
                            <th>Người dùng</th>
                            <th>Học phần / Phạm vi</th>
                            <th width="120">Chế độ</th>
                            <th width="150">Kết quả</th>
                            <th width="120">Trạng thái</th>
                            <th width="120">Thời gian</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phienLuyenTaps as $index => $item)
                            <tr>
                                <td>{{ $phienLuyenTaps->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $item->user->ho_ten ?? 'N/A' }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">{{ $item->user->email ?? '' }}</div>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #2563eb;">
                                        {{ $item->monHoc->ten_mon_hoc ?? 'N/A' }}</div>
                                    @if ($item->baiKiemTra)
                                        <div style="font-size: 11px; color: #059669;">📄 Đề:
                                            {{ $item->baiKiemTra->ten_bai }}</div>
                                    @elseif($item->chuongHoc)
                                        <div style="font-size: 11px; color: #64748b;">↳ Chương:
                                            {{ $item->chuongHoc->ten_chuong }}</div>
                                    @else
                                        <div style="font-size: 11px; color: #94a3b8; font-style: italic;">↳ Toàn môn</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #f3f4f6; color: #374151;">
                                        {{ $item->che_do == 1 ? 'Luyện tập' : ($item->che_do == 2 ? 'Thi thử' : 'Ôn yếu') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($item->trang_thai == 1)
                                        <div style="font-weight: 700; color: #10b981;">
                                            {{ number_format($item->diem_so, 1) }}đ</div>
                                        <div style="font-size: 11px; color: #64748b;">Đúng:
                                            {{ $item->so_cau_dung }}/{{ $item->so_cau_hoi }}</div>
                                    @else
                                        <span style="color: #94a3b8; font-style: italic;">Chưa có</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            0 => ['label' => 'Đang làm', 'class' => 'badge-medium'],
                                            1 => ['label' => 'Hoàn thành', 'class' => 'badge-easy'],
                                            2 => ['label' => 'Bỏ dở', 'class' => 'badge-hard'],
                                        ];
                                        $curr = $statusBadges[$item->trang_thai] ?? [
                                            'label' => 'N/A',
                                            'class' => 'badge-draft',
                                        ];
                                    @endphp
                                    <span class="badge {{ $curr['class'] }}">{{ $curr['label'] }}</span>
                                </td>
                                <td>
                                    <div style="font-size: 12px; line-height: 1.4;">
                                        {{ $item->thoi_gian_bat_dau ? $item->thoi_gian_bat_dau->format('H:i') : '' }}
                                        <div style="color: #64748b;">
                                            {{ $item->thoi_gian_bat_dau ? $item->thoi_gian_bat_dau->format('d/m/y') : 'N/A' }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        {{-- Chỉ giữ nút xem --}}
                                        <a href="{{ route('admin.phienluyentap.show', $item->id) }}"
                                            class="btn-action btn-edit" title="Xem chi tiết">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                style="width:20px;height:20px;">
                                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5 16.477 5 20.268 7.943 21.542 12 20.268 16.057 16.477 19 12 19 7.523 19 3.732 16.057 2.458 12z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <p style="color: #64748b;">Không tìm thấy phiên luyện tập nào.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-info">
                    Hiển thị {{ $phienLuyenTaps->firstItem() ?? 0 }}-{{ $phienLuyenTaps->lastItem() ?? 0 }} trong tổng số
                    {{ $phienLuyenTaps->total() }} phiên
                </div>
                <div class="pagination" style="list-style: none;">
                    <style>
                        .pagination ul, .pagination li { list-style: none !important; margin: 0; padding: 0; }
                    </style>
                    {{ $phienLuyenTaps->appends(request()->all())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>
@endsection
