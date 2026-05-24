{{-- Modal thêm/sửa môn học - ĐÃ FIX --}}
<div id="modalMonHoc" class="modal-overlay" onclick="closeModal('modalMonHoc')">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm lớp học mới</h3>
            <button type="button" class="close-btn" onclick="closeModal('modalMonHoc')">&times;</button>
        </div>
        <form id="formMonHoc" action="{{ route('admin.monhoc.storeOrUpdate') }}" method="POST"
    enctype="multipart/form-data" novalidate data-no-loading>
            @csrf
            <input type="hidden" name="id" id="input_id">
            <input type="hidden" name="old_hinh_anh" id="old_hinh_anh">

            <div class="modal-body" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px">
                <div class="form-group">
                    <label>Tên môn học <span style="color: red">*</span></label>
                    <input type="text" name="ten_mon_hoc" id="input_ten_mon_hoc" class="form-input" required>
                    <small class="text-danger" id="error_ten_mon_hoc"></small>
                </div>

                <div class="form-group">
                    <label>Mã môn học (Để trống tự tạo)</label>
                    <input type="text" name="ma_mon_hoc" id="input_ma_mon_hoc" class="form-input">
                    <small class="text-danger" id="error_ma_mon_hoc"></small>
                </div>
                <div class="form-group">
                    <label>Mức độ môn học <span style="color: red">*</span></label>
                    <select name="muc_do_mon_hoc" id="input_muc_do_mon_hoc" class="form-select" required>
                        <option value="1">Dễ</option>
                        <option value="2">Trung bình</option>
                        <option value="3">Khó</option>
                        <option value="4">Rất khó</option>
                    </select>
                    <small class="text-danger" id="error_muc_do_mon_hoc"></small>
                </div>

                <div class="form-group">
                    <label>Số tín chỉ <span style="color: red">*</span></label>
                    <input type="number" name="so_tin_chi" id="input_so_tin_chi" class="form-input" value="3"
                        min="1" required>
                    <small class="text-danger" id="error_so_tin_chi"></small>
                </div>

                <div class="form-group">
                    <label>Màu sắc đại diện</label>
                    <input type="color" name="mau_sac" id="input_mau_sac" class="form-input"
                        style="height: 42px; padding: 2px" value="#4f46e5">
                </div>

                <div class="form-group" style="grid-column: span 2">
                    <label>Mô tả ngắn</label>
                    <textarea name="mo_ta_ngan" id="input_mo_ta_ngan" class="form-input" rows="2" maxlength="500"></textarea>
                    <small class="text-muted">Tối đa 500 ký tự</small>
                </div>

                <div class="form-group" style="grid-column: span 2">
                    <label>Hình ảnh</label>
                    <input type="file" name="hinh_anh" id="hinh_anh_input" class="form-input" accept="image/*"
                        onchange="previewImage(event)">
                    <small class="text-muted">Định dạng: jpeg, png, jpg, gif. Kích thước tối đa: 2MB</small>
                    <small class="text-danger" id="error_hinh_anh"></small>
                    <div id="image_preview" style="display: none; margin-top: 10px;">
                        <img id="preview_image" src=""
                            style="max-width: 150px; border-radius: 8px; border: 1px solid #ddd; padding: 5px;">
                    </div>
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <div style="display: flex; gap: 15px; margin-top: 8px;">
                        <label style="cursor: pointer;">
                            <input type="radio" name="trang_thai" value="1" checked> Hoạt động
                        </label>
                        <label style="cursor: pointer;">
                            <input type="radio" name="trang_thai" value="0"> Tạm ẩn
                        </label>
                    </div>
                </div>
                {{-- Thêm các field bị thiếu từ migration --}}
                <div class="form-group" style="grid-column: span 2">
                    <label>Mô tả chi tiết/Đề cương môn học</label>
                    <textarea name="mo_ta_chi_tiet" id="input_mo_ta_chi_tiet" class="form-input" rows="4"></textarea>
                    <small class="text-muted">Có thể sử dụng HTML để định dạng</small>
                </div>

                <div class="form-group">
                    <label>Thứ tự hiển thị</label>
                    <input type="number" name="thu_tu" id="input_thu_tu" class="form-input" value="0"
                        min="0">
                    <small class="text-muted">Số nhỏ hơn sẽ hiển thị trước</small>
                </div>

                <div class="form-group">
                    <label>Icon class (FontAwesome)</label>
                    <input type="text" name="icon_class" id="input_icon_class" class="form-input"
                        placeholder="Ví dụ: fas fa-book, fas fa-calculator">
                    <small class="text-muted">Sử dụng class FontAwesome, thêm "fas fa-" trước icon</small>
                </div>

                <div class="form-group">
                    <label>Tùy chọn khác</label>
                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                        <label style="cursor: pointer;">
                            <input type="checkbox" name="is_featured" id="input_is_featured" value="1"> Nổi bật
                        </label>
                        <label style="cursor: pointer;">
                            <input type="checkbox" name="is_popular" id="input_is_popular" value="1"> Phổ biến
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="margin-top:20px; text-align:right">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalMonHoc')">
                    Hủy
                </button>
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    <i class="fas fa-save"></i> Lưu thông tin
                </button>
            </div>
        </form>
    </div>
</div>
