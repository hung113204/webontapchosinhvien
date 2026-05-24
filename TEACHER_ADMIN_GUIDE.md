# 🎓 HỆ THỐNG QUẢN LÝ QUYỀN CHO GIÁO VIÊN

## 📋 TỔNG QUAN

Hệ thống đã được cấu hình để **Giáo viên (Teacher)** có thể:

- ✅ Đăng nhập vào backend admin
- ✅ Tạo/Sửa/Xóa bài kiểm tra của mình
- ✅ Tạo/Sửa/Xóa câu hỏi của mình
- ✅ Xem kết quả thi của học sinh
- ❌ Không thể xem/chỉnh sửa dữ liệu của giáo viên khác
- ❌ Không thể quản lý môn học, người dùng, quyền (chỉ Admin)

## 🔧 CÁC THAY ĐỔI ĐÃ THỰC HIỆN

### 1. **Seeder Permissions** (`database/seeders/PermissionSeeder.php`)

- Tạo 13 permissions mới cho các module: Exam, Question, Result, Subject, User, Permission
- Tự động cấp quyền cho Teacher role
- Admin được cấp tất cả quyền

### 2. **Models - Scopes**

- **BaiKiemTra.php**: Thêm `scopeForTeacher()`
    - Teacher chỉ xem bài thi của mình
    - Admin xem tất cả

- **CauHoi.php**: Thêm `scopeForTeacher()`
    - Teacher chỉ xem câu hỏi của mình
    - Admin xem tất cả

### 3. **Middleware - CheckPermission** (`app/Http/Middleware/CheckPermission.php`)

- Kiểm tra user có permission cho module + action
- **KIỂM TRA OWNERSHIP**: Nếu là Teacher, xác minh dữ liệu là của họ
- Teacher cố tác truy cập dữ liệu của người khác → 403 Forbidden

### 4. **Controllers - Áp dụng Scope**

- **BaikiemtraController@index**: Dùng `->forTeacher()`
- **CauHoiController@index**: Dùng `->forTeacher()`

### 5. **Kernel.php**

- Đăng ký middleware: `'permission' => CheckPermission::class`

## 🚀 CÁCH CHẠY

### Step 1: Chạy Seeder

```bash
php artisan db:seed --class=PermissionSeeder
# hoặc chạy toàn bộ
php artisan db:seed
```

### Step 2: Kiểm tra Database

```bash
# Vào Tinker hoặc SQL
SELECT * FROM permissions;
SELECT * FROM role_permissions;
```

## 📝 CÁCH SỬ DỤNG MIDDLEWARE TRONG ROUTES

### Ví dụ 1: Kiểm tra xem + sở hữu dữ liệu

```php
Route::get('/admin/exams/{id}', [BaikiemtraController::class, 'show'])
    ->middleware('permission:exam,view,id');
```

### Ví dụ 2: Kiểm tra update + sở hữu dữ liệu

```php
Route::put('/admin/exams/{id}', [BaikiemtraController::class, 'update'])
    ->middleware('permission:exam,update,id');
```

### Ví dụ 3: Kiểm tra delete

```php
Route::delete('/admin/exams/{id}', [BaikiemtraController::class, 'destroy'])
    ->middleware('permission:exam,delete,id');
```

## 🔐 LOGIC HOẠT ĐỘNG

### Khi Teacher truy cập `/admin/exams`:

1. **AdminMiddleware** kiểm tra: `isTeacher()` → ✅ Pass (isAdmin() = true)
2. **forTeacher()** scope tự động chỉ lấy exams `nguoi_tao_id = user.id`
3. Kết quả: Teacher chỉ thấy bài thi của mình

### Khi Teacher cố truy cập exam của giáo viên khác:

1. **CheckPermission** middleware kiểm tra: `permission:exam,view,id`
2. Xác minh ownership: `exam.nguoi_tao_id != user.id` → ❌ Fail
3. Kết quả: 403 Forbidden - "Bạn chỉ có thể quản lý dữ liệu của mình!"

## 📊 PERMISSION MATRIX

| Permission        | Admin | Teacher           |
| ----------------- | ----- | ----------------- |
| exam.view         | ✅    | ✅ (chỉ của mình) |
| exam.create       | ✅    | ✅                |
| exam.update       | ✅    | ✅ (chỉ của mình) |
| exam.delete       | ✅    | ✅ (chỉ của mình) |
| question.view     | ✅    | ✅ (chỉ của mình) |
| question.create   | ✅    | ✅                |
| question.update   | ✅    | ✅ (chỉ của mình) |
| question.delete   | ✅    | ✅ (chỉ của mình) |
| result.view       | ✅    | ✅                |
| subject.view      | ✅    | ✅                |
| subject.manage    | ✅    | ❌                |
| user.view         | ✅    | ❌                |
| user.manage       | ✅    | ❌                |
| permission.manage | ✅    | ❌                |

## 🧪 TEST CÁC TÌNH HUỐNG

### Test 1: Teacher đăng nhập và xem bài thi của mình

```
1. Đăng nhập: teacher@ontapcntt.edu.vn / teacher123
2. Vào /admin/dashboard → ✅ Pass
3. Vào /admin/bai-kiem-tra → ✅ Xem chỉ bài của mình
```

### Test 2: Teacher cố xem bài thi của Admin

```
1. Lấy ID của bài thi do Admin tạo
2. Vào /admin/bai-kiem-tra/{id} → ❌ 403 Forbidden
```

### Test 3: Admin xem tất cả bài thi

```
1. Đăng nhập: admin@ontapcntt.edu.vn / admin123
2. Vào /admin/bai-kiem-tra → ✅ Xem tất cả bài (từ tất cả teacher)
```

### Test 4: Teacher cố tạo câu hỏi mà không có permission

```
- Nếu admin chưa cấp quyền question.create
- Vào /admin/cau-hoi/create → ❌ 403 Forbidden
```

## 🔄 ADMIN CẤP QUYỀN CHO TEACHER

### Qua Giao Diện (Nếu đã build):

1. Vào **Quản Lý → Phân Quyền**
2. Chọn role **Giảng Viên**
3. Tick các permission muốn cấp:
    - ✅ exam.view, exam.create, exam.update, exam.delete
    - ✅ question.view, question.create, question.update, question.delete
    - ✅ result.view

### Qua Tinker:

```php
$teacher = Role::find(2);
$perm = Permission::where('slug', 'exam.create')->first();
$teacher->permissions()->attach($perm->id, [
    'can_create' => 1,
]);
```

## 📌 LƯU Ý QUAN TRỌNG

1. **Khi tạo bài thi/câu hỏi**: Luôn ghi lại `nguoi_tao_id = auth()->user()->id`
2. **Xóa mềm (Soft Delete)**: Dữ liệu bị xóa vẫn được bảo vệ bởi ownership check
3. **Shared Resources**: Bài thi của Teacher nhưng học sinh có thể làm → không cần check ownership
4. **Super Admin (id=1)**: Bypass tất cả ownership checks, xem tất cả dữ liệu

## 🎯 NHỮNG ĐIỀU CÓ THỂ THÊM SAU

- [ ] Middleware quản lý teams (cho phép multiple teachers quản lý chung)
- [ ] Audit logs - ghi lại hành động của Teacher
- [ ] Teacher dashboard - thống kê bài thi, học sinh
- [ ] Khóa quyền - Admin có thể khóa/mở quyền individual cho 1 teacher
- [ ] Roles tùy chỉnh - Admin tạo role mới với permission tùy chỉnh

---

**Tạo: April 18, 2026**
