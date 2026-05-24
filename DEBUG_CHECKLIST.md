# Debug Checklist - Lưu Bài Kiểm Tra

## ✅ Các fix đã áp dụng:

### 1. Frontend (create.blade.php)

- [x] Fix typo: `so_lan_lam_lai` → `so_lan_lam_bai`
- [x] Thêm các field thiếu: `cach_tinh_diem`, `trang_thai`
- [x] sessionStorage lưu đủ dữ liệu (with console.log debug)

### 2. Frontend (soanthao.blade.php)

- [x] Thêm `dynamicContainer` variable
- [x] Fill đủ hidden fields từ sessionStorage:
    - `so_lan_lam_bai`, `cach_tinh_diem`, `trang_thai`
- [x] Submit form dạng string `cau_hoi_ids` (không array)
- [x] Chi tiết console.log để debug dữ liệu gửi

### 3. Validation (BaiKiemTraRequest.php)

- [x] `tu_dong_lay_de` kiểm tra đúng (0 vs 1)
- [x] Conditional validation: auto mode require số câu, manual mode require cau_hoi_ids
- [x] Xóa 'lop_hoc_ids.required' (vì nullable)
- [x] Thêm messages cho cau_hoi_ids.min

### 4. Backend (BaikiemtraController.php)

- [x] Thêm debug logging: `\Log::info()` vào đầu insertOrUpdate
- [x] Xem request data và validated data
- [x] Chi tiết error logging trong catch block

---

## 🔍 Hướng dẫn debug:

### Bước 1: Kiểm tra Browser Console (F12)

Khi tạo bài kiểm tra ở create.blade.php:

- Nhấp "Tiếp tục soạn đề thủ công"
- Mở DevTools (F12) → Console
- Xem 2 dòng log:
    ```
    Dữ liệu đã lưu create.blade: {...}
    [xem đủ các field trên]
    ```

Khi lưu ở soanthao.blade.php:

- Chọn câu hỏi
- Nhấp "Lưu đề thi"
- Console show:
    ```
    FormData từ sessionStorage: {...}
    cau_hoi_ids string: 1,2,3
    Form data sẽ submit: {...}
    ```
- **Kiểm tra:**
    - `cau_hoi_ids string` có giá trị không? (ví dụ "1,2,3")
    - `tu_dong_lay_de` = "0" không?
    - `ten_bai`, `mon_hoc_id`, `chuong_hoc_ids` có giá trị?

### Bước 2: Kiểm tra Laravel Log

Khi submit fail:

```bash
cd c:\xampp\htdocs\dethitracnghiem
# Xem real-time log
tail -f storage/logs/laravel.log
# hoặc
type storage/logs/laravel.log | tail -100
```

Tìm dòng:

```
BaiKiemTra submit data: [...]
BaiKiemTra insertOrUpdate error: [...]
```

### Bước 3: Validation Error

Nếu form submit fail, kiểm tra lỗi validation:

- Ở soanthao.blade.php, phần `@if ($errors->any())`
- Xem message lỗi nào:
    - "Bạn phải chọn ít nhất một câu hỏi" → `cau_hoi_ids` rỗng hoặc không string
    - "Vui lòng nhập..." → field required không có
    - "Ngày kết thúc phải sau ngày bắt đầu" → time validation fail

### Bước 4: Kiểm tra Route

Confirm route `admin.baikiemtra.save` tồn tại:

```bash
cd c:\xampp\htdocs\dethitracnghiem
php artisan route:list | grep save
# Tìm dòng:
# POST /admin/baikiemtra/save  BaikiemtraController@insertOrUpdate
```

### Bước 5: Database Check

Nếu submit success nhưng dữ liệu sai:

```bash
cd c:\xampp\htdocs\dethitracnghiem
php artisan tinker

# Kiểm tra bài thi vừa lưu
>>> $exam = \App\Models\BaiKiemTra::latest()->first();
>>> $exam->ten_bai;
>>> $exam->tu_dong_lay_de; // phải là 0 cho manual
>>> $exam->cauHois()->count(); // số câu hỏi
>>> exit
```

---

## ⚠️ Có thể gặp lỗi:

| Lỗi                                  | Nguyên nhân           | Fix                              |
| ------------------------------------ | --------------------- | -------------------------------- |
| `cau_hoi_ids field must be a string` | Gửi dưới dạng array   | Soanthao gửi string: "1,2,3" ✓   |
| `tu_dong_lay_de` không 0/1           | Form gửi sai          | Soanthao set `value="0"` ✓       |
| SessionStorage mất                   | Refresh tab           | Đừng refresh, chỉ navigate       |
| `mon_hoc_id` errors                  | ID không tồn tại DB   | Chọn môn học hợp lệ              |
| Division by zero                     | `total_questions = 0` | Check `array_filter()` loại rỗng |

---

## 📋 Test Script:

1. **Tạo bài kiểm tra tự động** (test route hoạt động):
    - Tạo với chế độ "Tự động lấy đề"
    - Nhấp "Lưu nháp" (phía create)
    - Kiểm tra được lưu không?

2. **Tạo bài kiểm tra thủ công**:
    - Tạo với chế độ "Chọn câu hỏi thủ công"
    - Nhấp "Tiếp tục soạn đề thủ công"
    - Chọn 3-5 câu hỏi
    - Nhấp "Lưu đề thi"
    - **Expected**: Redirect to index page, show success message

3. **Nếu fail**:
    - Mở F12 → Console, ghi lại log
    - Kiểm tra `storage/logs/laravel.log`
    - Share error message

---

Generated: 2026-02-12 (Auto-generated debug file)
