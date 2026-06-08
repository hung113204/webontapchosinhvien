<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MonHocController;
use App\Http\Controllers\Admin\ChuongHocController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CauHoiController;
use App\Http\Controllers\Admin\DapAnController;
use App\Http\Controllers\Admin\BaiKiemTraController;
use App\Http\Controllers\Admin\BaiKiemTraCauHoiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\KetQuaThiController;
use App\Http\Controllers\Admin\DanhMucTrangChuController;
use App\Http\Controllers\Admin\BaiHocController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PhienLuyenTapController;
use App\Http\Controllers\Admin\TiendobaihocController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========== AUTH ROUTES (Login/Logout) ==========

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// CHỈ MỞ MỘT GROUP ADMIN DUY NHẤT
Route::middleware('admin')->group(function () {
    // Trang Dashboard
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::prefix('mon-hoc')
        ->name('monhoc.')
        ->group(function () {
            Route::get('/', [MonHocController::class, 'index'])->name('index');
            Route::post('/storeOrUpdate/{id?}', [MonHocController::class, 'storeOrUpdate'])->name('storeOrUpdate');
            Route::delete('/destroy/{id}', [MonHocController::class, 'destroy'])->name('destroy');
            Route::get('{id}', [MonHocController::class, 'show'])->name('show');

            Route::post('/toggle-featured/{id}', [MonHocController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/toggle-popular/{id}', [MonHocController::class, 'togglePopular'])->name('toggle-popular');

            // Thêm dòng này
            Route::post('/toggle-status/{id}', [MonHocController::class, 'toggleStatus'])->name('toggle-status');

            Route::get('/{id}/info', [MonHocController::class, 'getInfo'])->name('info');
        });

    // XÓA ROUTE NÀY - NÓ GÂY XUNG ĐỘT
    // Route::post('/monhoc/store-or-update', [MonHocController::class, 'storeOrUpdate'])->name('admin.monhoc.storeOrUpdate');
    Route::prefix('chuong-hoc')
        ->name('chuonghoc.')
        ->group(function () {
            // API: Lấy danh sách chương theo môn học (JSON)
            Route::get('/get-by-mon-hoc/{mon_hoc_id}', [ChuongHocController::class, 'getByMonHoc'])->name('getByMonHoc');

            // Xem danh sách tất cả chương hoặc theo môn học
            Route::get('/', [ChuongHocController::class, 'index'])->name('index');
            Route::get('/mon-hoc/{mon_hoc_id}', [ChuongHocController::class, 'index'])->name('byMonHoc');

            // Lưu chương học (POST)
            Route::post('/save/{id?}', [ChuongHocController::class, 'save'])->name('save');

            // Xóa chương học (DELETE)
            Route::delete('/delete/{id}', [ChuongHocController::class, 'destroy'])->name('destroy');
        });
    Route::prefix('users')
        ->name('users.')
        ->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('index');
            Route::post('/store-or-update/{id?}', [UsersController::class, 'storeOrUpdate'])->name('storeOrUpdate');

            // FIX 1: Bỏ chữ /users/ thừa trong prefix đã có
            // FIX 2: Đổi tên name thành 'destroy' để đồng bộ (users.destroy)
            Route::delete('/destroy/{id}', [UsersController::class, 'destroy'])->name('destroy');

            Route::post('/toggle-status/{id}', [UsersController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/restore/{id}', [UsersController::class, 'restore'])->name('restore');
            Route::get('/{id}/get-info', [UsersController::class, 'getInfo'])->name('getInfo');
            Route::delete('/force-delete/{id}', [UsersController::class, 'forceDelete'])->name('forceDelete');
        });
    Route::prefix('phanquyen')
        ->name('phanquyen.')
        ->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/save', [RoleController::class, 'save'])->name('save');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-permission', [RoleController::class, 'togglePermission'])->name('togglePermission');
        });
    Route::prefix('permission')
        ->name('permission.')
        ->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index'); // Danh sách quyền
            Route::post('/save', [PermissionController::class, 'save'])->name('save'); // Lưu hoặc cập nhật
            Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('edit'); // Lấy thông tin edit
            Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('destroy'); // Xóa quyền
        });
    Route::prefix('cauhoi')
        ->name('cauhoi.')
        ->group(function () {
            // Danh sách
            Route::get('/', [CauHoiController::class, 'index'])->name('index');

            // Create/Edit Forms
            Route::get('/create', [CauHoiController::class, 'create'])->name('create');

            // AI question generation
            Route::post('/generate-ai', [CauHoiController::class, 'generateAI'])->name('generateAI');
            Route::post('/save-ai', [CauHoiController::class, 'saveAiQuestions'])->name('saveAI');

            // Import questions from files
            Route::post('/import/preview-word', [CauHoiController::class, 'previewWord'])->name('import.preview.word');
            Route::post('/import/word-confirmed', [CauHoiController::class, 'importWordConfirmed'])->name('import.word.confirmed');
            Route::post('/import/preview-excel', [CauHoiController::class, 'previewExcel'])->name('import.preview.excel');
            Route::post('/import/excel-confirmed', [CauHoiController::class, 'importExcelConfirmed'])->name('import.excel.confirmed');

            Route::get('/{id}/edit', [CauHoiController::class, 'edit'])->name('edit');

            // CRUD Actions
            Route::post('/save/{id?}', [CauHoiController::class, 'save'])->name('save');
            Route::delete('/{id}', [CauHoiController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [CauHoiController::class, 'bulkDelete'])->name('bulkDelete');
            Route::get('/{id}/show', [CauHoiController::class, 'show'])->name('show');

            // Special Actions
            Route::post('/{id}/toggle-status', [CauHoiController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{id}/duplicate', [CauHoiController::class, 'duplicate'])->name('duplicate');
            Route::post('/{id}/restore', [CauHoiController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [CauHoiController::class, 'forceDelete'])->name('forceDelete');
            // API lấy dữ liệu để đổ vào Modal khi bấm nút Sửa

            // Xóa đáp án

            // Các thao tác nhanh qua AJAX (Dùng POST để khớp với Script trong View)

            // Tiện ích bổ sung
        });
    Route::prefix('dapan')
        ->name('dapan.')
        ->group(function () {
            Route::get('/', [DapAnController::class, 'index'])->name('index');
            Route::get('/create', [DapAnController::class, 'create'])->name('create');
            Route::post('/save/{id?}', [DapAnController::class, 'save'])->name('save');
            Route::get('/{id}/edit', [DapAnController::class, 'edit'])->name('edit');
            Route::get('/{id}/show', [DapAnController::class, 'show'])->name('show');
            Route::delete('/{id}', [DapAnController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [DapAnController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{id}/mark-correct', [DapAnController::class, 'markCorrect'])->name('mark-correct');
            Route::post('/update-order', [DapAnController::class, 'updateOrder'])->name('update-order');
            Route::post('/copy-from-other', [DapAnController::class, 'copyFromOther'])->name('copy-from-other');
        });
    Route::prefix('baikiemtra')
        ->name('baikiemtra.')
        ->group(function () {
            // Danh sách
            Route::get('/', [BaiKiemTraController::class, 'index'])->name('index');

            // Tạo mới
            Route::get('/create', [BaiKiemTraController::class, 'create'])->name('create');

            // Lưu (cả tạo mới và cập nhật)
            Route::post('/save/{id?}', [BaiKiemTraController::class, 'insertOrUpdate'])->name('save');

            // Sửa
            Route::get('/edit/{id}', [BaiKiemTraController::class, 'edit'])->name('edit');

            // Xóa
            Route::delete('/delete/{id}', [BaiKiemTraController::class, 'destroy'])->name('destroy');

            // Trang soạn thảo thủ công
            Route::get('/soan-thao', [BaiKiemTraController::class, 'soanThao'])->name('soanthao');

            // Nhân bản
            Route::post('/clone/{id}', [BaiKiemTraController::class, 'clone'])->name('clone');

            // Cập nhật trạng thái
            Route::patch('/update-status/{id}', [BaiKiemTraController::class, 'updateStatus'])->name('update-status');

            // ===== API ENDPOINTS =====
            // Lấy thống kê câu hỏi theo chương
            Route::post('/api/question-stats', [BaiKiemTraController::class, 'getQuestionStats'])->name('api.question-stats');

            // Preview đề thi (tự động)
            Route::post('/api/preview', [BaiKiemTraController::class, 'preview'])->name('api.preview');

            // Lấy câu hỏi theo môn học
            Route::get('/api/cau-hoi-by-mon-hoc', [BaiKiemTraController::class, 'getCauHoiByMonHoc'])->name('api.cau-hoi-by-mon-hoc');

            // Lấy câu hỏi theo nhiều chương
            Route::get('/api/cau-hoi-by-chuong-hocs', [BaiKiemTraController::class, 'getCauHoiByChuongHocs'])->name('api.cau-hoi-by-chuong-hocs');
            // Lấy chương học theo môn học
            Route::get('/api/chuong-hoc/{monHocId}', [BaiKiemTraController::class, 'getChuongHocByMonHoc'])->name('api.chuong-hoc');
            Route::post('/restore/{id}', [BaiKiemTraController::class, 'restore'])->name('restore');
            Route::delete('/force-delete/{id}', [BaiKiemTraController::class, 'forceDelete'])->name('force-delete');
            Route::post('/toggle-status/{id}', [BaiKiemTraController::class, 'toggleStatus'])->name('toggle-status');
        });

    Route::prefix('bai-kiem-tra-cau-hoi')
        ->name('baikiemtracauhoi.')
        ->group(function () {
            Route::post('/sync', [BaiKiemTraCauHoiController::class, 'syncQuestions'])->name('sync');
            Route::post('/add-single', [BaiKiemTraCauHoiController::class, 'store'])->name('addSingle');
            Route::delete('/remove/{bai_kiem_tra_id}/{cau_hoi_id}', [BaiKiemTraCauHoiController::class, 'destroy'])->name('destroy');
            Route::post('/reorder', [BaiKiemTraCauHoiController::class, 'updateOrder'])->name('reorder');
        });
    Route::prefix('ketquathi')
        ->name('ketquathi.')
        ->group(function () {
            // Trang tổng hợp chính
            Route::get('/', [KetQuaThiController::class, 'index'])->name('index');

            // AJAX: Lấy danh sách SV của 1 đề (dùng cho Modal tầng 2)
            Route::get('/ajax/{bai_thi_id}', [KetQuaThiController::class, 'ajaxDanhSach'])
                ->name('ajax.danh-sach')
                ->whereNumber('bai_thi_id');

            // AJAX: Lấy chi tiết bài làm SV (dùng cho Modal tầng 3)
            Route::get('/detail/{id}', [KetQuaThiController::class, 'ajaxDetail'])->name('detail');

            // Xuất Excel
            Route::get('/export', [KetQuaThiController::class, 'exportAll'])->name('export');
            Route::get('/export/{bai_thi_id}/{lop_id}', [KetQuaThiController::class, 'exportLop'])
                ->name('export.lop')
                ->whereNumber('bai_thi_id')
                ->whereNumber('lop_id');
        });
    Route::prefix('danhmuctrangchu')
        ->name('danhmuctrangchu.') // Khớp với tên route redirect trong Controller: route('admin.danhmuc-trangchu.index')
        ->group(function () {
            // Trang danh sách: Hiển thị các danh mục trang chủ
            Route::get('/', [DanhMucTrangChuController::class, 'index'])->name('index');

            // Form giao diện thêm / sửa (nếu bạn không dùng Modal popup)
            Route::get('/create', [DanhMucTrangChuController::class, 'create'])->name('create');
            Route::get('/edit/{id}', [DanhMucTrangChuController::class, 'edit'])->name('edit');

            // Route xử lý xóa (Soft delete)
            Route::delete('/delete/{id}', [DanhMucTrangChuController::class, 'destroy'])->name('destroy');

            // Route lưu mới (Sử dụng hàm store đã gộp logic storeOrUpdate)
            Route::post('/save', [DanhMucTrangChuController::class, 'store'])->name('save');

            // Route update riêng cho một ID cụ thể
            Route::put('/update/{id}', [DanhMucTrangChuController::class, 'update'])->name('update');

            // -------------------------------------------------------------------
            // CÁC ROUTE BỔ SUNG (Dùng cho AJAX dựa theo Controller đã viết)
            // -------------------------------------------------------------------

            // Cập nhật trạng thái (ẩn/hiển thị)
            Route::post('/toggle-status/{id}', [DanhMucTrangChuController::class, 'toggleStatus'])->name('toggleStatus');

            // Cập nhật thứ tự hiển thị bằng cách kéo thả
            Route::post('/update-order', [DanhMucTrangChuController::class, 'updateOrder'])->name('updateOrder');

            // Lấy thông tin 1 bản ghi qua AJAX (Thường dùng để fill data vào Modal sửa)
            Route::get('/show/{id}', [DanhMucTrangChuController::class, 'show'])->name('show');

            // Khôi phục bản ghi đã xóa
            Route::post('/restore/{id}', [DanhMucTrangChuController::class, 'restore'])->name('restore');

            // Xóa vĩnh viễn
            Route::delete('/force-delete/{id}', [DanhMucTrangChuController::class, 'forceDelete'])->name('forceDelete');
        });
    Route::group(['prefix' => 'bai-hoc', 'as' => 'baihoc.'], function () {
        // Danh sách bài học
        Route::get('/', [BaiHocController::class, 'index'])->name('index');

        // Form thêm mới
        Route::get('/them-moi', [BaiHocController::class, 'create'])->name('create');

        // Form chỉnh sửa
        Route::get('/sua/{id}', [BaiHocController::class, 'edit'])->name('edit');

        // Xử lý lưu dữ liệu (Dùng chung cho cả Thêm mới và Cập nhật)
        Route::post('/luu', [BaiHocController::class, 'storeOrUpdate'])->name('storeOrUpdate');

        // Xóa bài học
        Route::delete('/xoa/{id}', [BaiHocController::class, 'destroy'])->name('destroy');

        // Gán câu hỏi
        Route::get('/api/available-questions', [BaiHocController::class, 'getAvailableQuestions'])->name('api.available-questions');
        Route::post('/api/assign-questions', [BaiHocController::class, 'assignQuestions'])->name('api.assign-questions');
        Route::post('/api/remove-question', [BaiHocController::class, 'removeQuestion'])->name('api.remove-question');
    });
    Route::group(['prefix' => 'phien-luyen-tap', 'as' => 'phienluyentap.'], function () {
        // Danh sách phiên luyện tập
        Route::get('/', [PhienLuyenTapController::class, 'index'])->name('index');
        /*
        // Form thêm mới
        Route::get('/them-moi', [PhienLuyenTapController::class, 'create'])->name('create');

        // Form chỉnh sửa
        Route::get('/sua/{id}', [PhienLuyenTapController::class, 'edit'])->name('edit');

        // Xử lý lưu dữ liệu (dùng chung cho cả Thêm mới và Cập nhật)
        Route::post('/luu', [PhienLuyenTapController::class, 'storeOrUpdate'])->name('storeOrUpdate');

        // Xóa phiên luyện tập
        Route::delete('/xoa/{id}', [PhienLuyenTapController::class, 'destroy'])->name('destroy'); */
        Route::get('/{id}', [PhienLuyenTapController::class, 'show'])->name('show');
    });
    // === NHÓM ROUTE TIẾN ĐỘ HỌC TẬP (Admin) ===
    Route::group(['prefix' => 'tien-do', 'as' => 'tiendo.'], function () {

        // 1. Danh sách tất cả học sinh (để admin chọn xem tiến độ)
        Route::get('/', [TiendobaihocController::class, 'global'])
             ->name('students');

        // Mới: Bảng tổng hợp tiến độ tất cả học sinh
        Route::get('/tong-hop', [TiendobaihocController::class, 'global'])
             ->name('global');

        // 2. Cập nhật tiến độ bài học (AJAX) - PHẢ ĐẶT TRƯỚC CÁC ROUTE CÓ PARAMETER
        Route::post('/cap-nhat', [TiendobaihocController::class, 'update'])
             ->name('update');

        // 3. Lịch sử luyện tập của sinh viên - PHẢ ĐẶT TRƯỚC /mon-hoc
        Route::get('/{user_id}/lich-su', [TiendobaihocController::class, 'history'])
             ->name('history')
             ->whereNumber('user_id');

        // 4. Xem chi tiết tiến độ theo môn học của sinh viên
        Route::get('/{user_id}/mon-hoc/{mon_hoc_id}', [TiendobaihocController::class, 'show'])
             ->name('show')
             ->whereNumber('user_id')
             ->whereNumber('mon_hoc_id');

        // 5. Tổng quan tiến độ của một sinh viên (TẤT CẢ MÔN HỌC)
        Route::get('/{user_id}', [TiendobaihocController::class, 'index'])
             ->name('index')
             ->whereNumber('user_id');
    });

    // === NHÓM ROUTE PHÒNG QUIZ REALTIME (Admin) ===
    Route::prefix('phong-quiz')
        ->name('phongquiz.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\PhongQuizController::class, 'index'])->name('index');
            Route::post('/store', [\App\Http\Controllers\PhongQuizController::class, 'store'])->name('store');
            Route::get('/room/{ma_phong}', [\App\Http\Controllers\PhongQuizController::class, 'adminRoom'])->name('room');
            Route::get('/room/{ma_phong}/status', [\App\Http\Controllers\PhongQuizController::class, 'roomStatus'])->name('status');
            Route::post('/room/{ma_phong}/start', [\App\Http\Controllers\PhongQuizController::class, 'adminStartRoom'])->name('start');
            Route::post('/room/{ma_phong}/next', [\App\Http\Controllers\PhongQuizController::class, 'adminNextQuestion'])->name('next');
            Route::post('/room/{ma_phong}/end', [\App\Http\Controllers\PhongQuizController::class, 'adminEndRoom'])->name('end');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\PhongQuizController::class, 'destroy'])->name('destroy');
        });

    // === NHÓM ROUTE FAQ (Admin) ===
    Route::prefix('faq')
        ->name('faq.')
        ->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FaqController::class, 'index'])->name('index');
            Route::post('/save', [\App\Http\Controllers\Admin\FaqController::class, 'storeOrUpdate'])->name('save');
            Route::get('/show/{id}', [\App\Http\Controllers\Admin\FaqController::class, 'show'])->name('show');
            Route::delete('/delete/{id}', [\App\Http\Controllers\Admin\FaqController::class, 'destroy'])->name('destroy');
            Route::post('/toggle-status/{id}', [\App\Http\Controllers\Admin\FaqController::class, 'toggleStatus'])->name('toggleStatus');
        });
});
//rogu rdrn wqqg pjiu gmail
