# 🐛 BUG TRACKER - MonHoc Module (31/03/2026)

## ✅ BUG #1: Xóa Môn Học (FIXED)

**Vấn đề:** Khi xóa môn học từ giao diện, dữ liệu biến mất khỏi UI nhưng **vẫn còn trong database**

**Nguyên nhân:** Model `MonHoc` sử dụng trait `SoftDeletes` → chỉ đánh dấu xóa (set `deleted_at`), không xóa thực sự

**Giải pháp:**

- ❌ Xóa: `use SoftDeletes;` từ [app/Models/MonHoc.php](app/Models/MonHoc.php)
- ✅ Giữ: `use HasFactory;`

**Kết quả:** Xóa môn học = xóa hoàn toàn khỏi database

---

## ⚠️ BUG #2: Thêm Môn Học - Không Hiện Thông Báo Thành Công (IN PROGRESS)

**Vấn đề:** Form thêm môn học không hiển thị thông báo "Thành công" sau khi submit

**Nguyên nhân:** Có thể là một trong các lỗi:

1. **Route name sai** ✅ (Fixed)
2. **Form không submit** → Kiểm tra Console browser (F12)
3. **Server error** → Controller return error response
4. **JavaScript error** → Dừng execution trước khi hiện thông báo

**Các file liên quan:**

- [resources/views/Admin/monhoc/modal.blade.php](resources/views/Admin/monhoc/modal.blade.php) - Form HTML
- [resources/views/Admin/monhoc/index.blade.php](resources/views/Admin/monhoc/index.blade.php) - Form JavaScript
- [app/Http/Controllers/Admin/MonHocController.php](app/Http/Controllers/Admin/MonHocController.php) - Backend logic

**Kiểm tra chi tiết:**

### Route Name ✅ (FIXED)

```blade
{{-- Đúng: --}}
{{ route('admin.monhoc.storeOrUpdate') }}

{{-- Sai: (Không dùng) --}}
{{ route('monhoc.storeOrUpdate') }}
```

**Lý do:** Routes nằm trong group:

```php
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
  └── Route::prefix('mon-hoc')->name('monhoc.')->group(...);
       └── Route::post('/storeOrUpdate/{id?}', ...)->name('storeOrUpdate');
           = route('admin.monhoc.storeOrUpdate') ✅
```

### Form Validation

- [app/Http/Requests/Admin/MonHocRequest.php](app/Http/Requests/Admin/MonHocRequest.php)
- Bắt buộc: `ten_mon_hoc`, `so_tin_chi`, `muc_do_mon_hoc`
- Nếu validation fail → hiện lỗi dưới từng field

### Debugging Steps

**1. Mở Browser Console (F12 → Console tab)**

```javascript
// Sẽ thấy log sau khi submit form:
// 📝 Form submit triggered
// 📤 Gửi đến: http://... Method: POST
// ✅ Thành công: ...hoặc
// ❌ Lỗi: ...
```

**2. Kiểm tra Network (F12 → Network tab)**

- Click "Preserve log"
- Submit form
- Tìm request `/admin/mon-hoc/storeOrUpdate`
- Xem Response tab → phải là JSON: `{"success": true, ...}`

**3. Kiểm tra Status Code**

- 200 OK + `success: true` → reload page
- 422 Unprocessable Entity → validation error
- 500 Server Error → bug ở controller

### Latest Improvements

✅ Thêm console logging chi tiết
✅ Thêm toast notification (nếu adminUI available)
✅ Cải thiện error handling
✅ Log response status và data

---

## 📋 Todo List

- [ ] User test thêm môn học mà không có chương → lỗi gì?
- [ ] Kiểm tra browser console log khi submit form
- [ ] Test validate: để trống tên môn → có hiện lỗi?
- [ ] Test upload image → có lưu vào storage?
- [ ] Test edit môn học → có pre-fill dữ liệu cũ?

---

## 🔧 Các Routes Liên Quan

```php
Route::prefix('admin')->name('admin.')->group(function() {
    Route::prefix('mon-hoc')->name('monhoc.')->group(function() {
        GET    /                          → index()        (admin.monhoc.index)
        POST   /storeOrUpdate/{id?}       → storeOrUpdate()  (admin.monhoc.storeOrUpdate) ✅
        DELETE /destroy/{id}              → destroy()      (admin.monhoc.destroy)
        GET    /{id}                      → show()         (admin.monhoc.show)
        POST   /toggle-featured/{id}      → toggleFeatured()
        POST   /toggle-popular/{id}       → togglePopular()
    });
});
```

---

## 📌 Files Modified

| File                                                                                         | Line    | Thay đổi                                   |
| -------------------------------------------------------------------------------------------- | ------- | ------------------------------------------ |
| [app/Models/MonHoc.php](app/Models/MonHoc.php)                                               | 5-10    | Xóa `SoftDeletes` trait                    |
| [resources/views/Admin/monhoc/index.blade.php](resources/views/Admin/monhoc/index.blade.php) | 450-495 | Cải thiện error handling + console logging |
| -                                                                                            | 310-320 | (No change needed - route name đúng)       |
