@extends('Admin.layouts.admin')
@section('title', 'Quản lý Bài học')

@section('header_action')
    {{-- Chuyển từ button sang thẻ a để link sang trang form --}}
    <a href="{{ route('admin.baihoc.create') }}" class="btn btn-primary" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        <span style="font-weight: 500;">Thêm bài học mới</span>
    </a>
@endsection

@section('content')
    {{-- Hiển thị thông báo --}}
    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;">
            <strong>✓</strong> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
            <strong>✗</strong> {{ session('error') }}
        </div>
    @endif

    <section class="filters-section" style="padding-bottom: 0">
        <div class="stats-mini">
            <div class="stat-mini-card">
                <div class="stat-mini-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $baiHocs->total()) }}</h4>
                    <p>Tổng bài học tìm thấy</p>
                </div>
            </div>
            <div class="stat-mini-card" style="border-left-color: var(--success)">
                <div class="stat-mini-icon" style="background: var(--success)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="stat-mini-info">
                    <h4>{{ sprintf('%02d', $baiHocs->count()) }}</h4>
                    <p>Hiển thị trên trang này</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Form Tìm kiếm --}}
    <section class="filters-section">
        <form action="{{ route('admin.baihoc.index') }}" method="GET" class="filter-group">
            <div class="filter-item" style="grid-column: span 3">
                <label>Tìm kiếm bài học</label>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập tên bài học cần tìm..." />
                </div>
            </div>
            @if(request('search'))
                <div class="filter-item" style="display: flex; align-items: flex-end;">
                    <a href="{{ route('admin.baihoc.index') }}" class="btn btn-secondary" style="height: 42px; display: flex; align-items: center; padding: 0 15px; border-radius: 6px; border: 1px solid #ddd; background: #f9f9f9; color: #ef4444; text-decoration: none;">
                        Xóa lọc
                    </a>
                </div>
            @endif
        </form>
    </section>

    <section class="table-section">
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <h3>Danh sách bài học</h3>
                    <span class="count-badge">Dữ liệu hệ thống</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="50"><input type="checkbox" class="checkbox" /></th>
                            <th width="70">STT</th>
                            <th>Tên bài học</th>
                            <th>Thuộc Môn / Chương</th>
                            <th>Tài nguyên</th>
                            <th>Trạng thái</th>
                            <th width="120">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($baiHocs as $index => $item)
                        <tr>
                            <td><input type="checkbox" class="checkbox" /></td>
                            <td><span class="id-badge">{{ $baiHocs->firstItem() + $index }}</span></td>
                            
                            <td>
                                <strong>{{ $item->ten_bai_hoc }}</strong>
                            </td>
                            
                            <td>
                                <div style="color: #4f46e5; font-weight: 500; margin-bottom: 4px;">{{ $item->chuongHoc->monHoc->ten_mon_hoc ?? 'N/A' }}</div>
                                <div style="font-size: 0.85rem; color: #64748b;">↳ Ch.{{ $item->chuongHoc->thu_tu ?? 0 }}: {{ $item->chuongHoc->ten_chuong ?? 'N/A' }}</div>
                            </td>

                            <td style="font-size: 0.85rem; color: #475569;">
                                @if($item->ngon_ngu_code)
                                    <div style="margin-bottom: 3px;"><i class="fas fa-code"></i> Code: {{ strtoupper($item->ngon_ngu_code) }}</div>
                                @endif
                                @if($item->tai_lieu_dinh_kem)
                                    <div style="margin-bottom: 3px; color: #059669;"><i class="fas fa-file-pdf"></i> Có tài liệu</div>
                                @endif
                            </td>

                            <td>
                                @if($item->trang_thai == 1)
                                    <span class="badge badge-active">Hoạt động</span>
                                @else
                                    <span class="badge badge-draft">Tạm ẩn</span>
                                @endif
                            </td>
                            
                            <td>
                                <div class="action-buttons">
                                    {{-- Nút Sửa - Dùng thẻ a để sang form --}}
                                    <a href="{{ route('admin.baihoc.edit', $item->id) }}" class="btn-action btn-edit" title="Sửa" style="display: inline-flex; text-decoration: none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                        </svg>
                                    </a>
                                    
                                    {{-- Nút Xóa - DÙNG FORM DELETE --}}
                                    <form action="{{ route('admin.baihoc.destroy', $item->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa bài học này? Tất cả file đính kèm sẽ bị xóa!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Xóa">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <svg style="width: 64px; height: 64px; margin: 0 auto; color: #ddd;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <p style="color: #999; margin-top: 16px;">Chưa có bài học nào trong hệ thống</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-footer">
                <div class="showing-info">
                    @if($baiHocs->total() > 0)
                        Hiển thị {{ $baiHocs->firstItem() }}-{{ $baiHocs->lastItem() }} trong tổng số {{ $baiHocs->total() }} bài học
                    @else
                        Không có dữ liệu
                    @endif
                </div>
                <div class="pagination">
                    {{ $baiHocs->appends(request()->all())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </section>

<script>
    // Tự động ẩn alert sau 5 giây (Giống file Năm Học)
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>
@endsection