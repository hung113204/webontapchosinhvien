<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\SubjectController;
use App\Http\Controllers\Client\PracticeController;
use App\Http\Controllers\Client\ExamController;
use App\Http\Controllers\Client\GoogleController;
use App\Http\Controllers\Client\AiAssistantController;
Route::get('/healthz', fn () => response('ok', 200))->name('healthz');
/*
|--------------------------------------------------------------------------
| WEB ROUTES (Dành cho Sinh viên và Khách truy cập)
|--------------------------------------------------------------------------
*/

// ── Trang chủ & Auth ──────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('client.login');
Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/quen-mat-khau', [AuthController::class, 'showForgotForm'])->name('password.request');

Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.post');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/dat-lai-mat-khau', [AuthController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// ── Google OAuth — ĐỂ NGOÀI middleware, ai cũng truy cập được ──
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// ── Học phần ──────────────────────────────────────────────────
Route::prefix('hoc-phan')
    ->name('client.subjects.')
    ->group(function () {
        Route::get('/', [SubjectController::class, 'index'])->name('index');
        Route::get('/{id}', [SubjectController::class, 'show'])
            ->name('show')
            ->whereNumber('id');
    });

// ── API công khai (không cần auth nhưng controller sẽ check) ──────
Route::get('/api/tien-do-cac-mon', [SubjectController::class, 'getProgressAll'])->name('client.tiendo.getAll');

// ── Luyện tập ─────────────────────────────────────────────────
Route::prefix('luyen-tap')
    ->name('client.practice.')
    ->group(function () {
        Route::get('/', [PracticeController::class, 'index'])->name('index');
        Route::get('/thiet-lap/{mon_hoc_id}', [PracticeController::class, 'setup'])->name('setup');
        Route::post('/tao-bai', [PracticeController::class, 'generate'])->name('generate');
        Route::get('/lam-bai/{session_id}', [PracticeController::class, 'workspace'])->name('workspace');
        Route::post('/nop-bai', [PracticeController::class, 'submit'])->name('submit');
        Route::get('/ket-qua/{session_id}', [PracticeController::class, 'result'])->name('result');
    });

// ── Thi thử ───────────────────────────────────────────────────
Route::prefix('thi-thu')
    ->name('client.exams.')
    ->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/{id}', [ExamController::class, 'show'])
            ->name('show')
            ->whereNumber('id');
        Route::get('/{id}/bat-dau', [ExamController::class, 'start'])
            ->name('start')
            ->whereNumber('id');
        Route::get('/lam-bai/{session_id}', [ExamController::class, 'workspace'])->name('workspace');
        Route::post('/nop-bai', [ExamController::class, 'submit'])->name('submit');
        Route::get('/ket-qua/{session_id}', [ExamController::class, 'result'])->name('result');
        Route::get('/check-ai-status/{id}', [ExamController::class, 'checkAiStatus'])
            ->name('check_ai')
            ->whereNumber('id');
    });

// ── Route yêu cầu đăng nhập ───────────────────────────────────
Route::middleware('client.auth')->group(function () {
    Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('tai-khoan')
        ->name('profile.')
        ->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::post('/cap-nhat-thong-tin', [ProfileController::class, 'updateInfo'])->name('update.info');
            Route::post('/doi-mat-khau', [ProfileController::class, 'updatePassword'])->name('update.password');
            Route::post('/doi-anh', [ProfileController::class, 'updateAvatar'])->name('update.avatar');
            Route::post('/go-anh', [ProfileController::class, 'removeAvatar'])->name('remove.avatar');
            Route::get('/ho-so-hoc-tap', [ProfileController::class, 'history'])->name('history');
        });

    // API: Cập nhật tiến độ học (cho client)
    Route::post('/api/cap-nhat-tien-do', [SubjectController::class, 'updateProgress'])->name('client.tiendo.update');

    // API: Lấy tiến độ tất cả môn học
    Route::get('/api/tien-do-cac-mon', [SubjectController::class, 'getProgressAll'])->name('client.tiendo.getAll');
});

// ── PHÒNG QUIZ REALTIME (CLIENT) ──
Route::prefix('quiz-vui')->group(function () {
    Route::get('/', [\App\Http\Controllers\PhongQuizController::class, 'clientJoinView'])->name('client.phongquiz.join');
    Route::post('/join', [\App\Http\Controllers\PhongQuizController::class, 'clientJoin'])->name('client.phongquiz.join.post');
    Route::post('/store', [\App\Http\Controllers\PhongQuizController::class, 'clientStore'])->name('client.phongquiz.store');
    Route::get('/room/{ma_phong}', [\App\Http\Controllers\PhongQuizController::class, 'clientRoom'])->name('client.phongquiz.room');
    Route::get('/room/{ma_phong}/status', [\App\Http\Controllers\PhongQuizController::class, 'roomStatus'])->name('client.phongquiz.status');
    Route::post('/room/{ma_phong}/submit', [\App\Http\Controllers\PhongQuizController::class, 'clientSubmitAnswer'])->name('client.phongquiz.submit');
    Route::post('/room/{ma_phong}/start', [\App\Http\Controllers\PhongQuizController::class, 'clientStartRoom'])->name('client.phongquiz.start');
    Route::post('/room/{ma_phong}/next', [\App\Http\Controllers\PhongQuizController::class, 'clientNextQuestion'])->name('client.phongquiz.next');
    Route::post('/room/{ma_phong}/leave', [\App\Http\Controllers\PhongQuizController::class, 'clientLeaveRoom'])->name('client.phongquiz.leave');
    Route::post('/room/{ma_phong}/ready', [\App\Http\Controllers\PhongQuizController::class, 'clientReady'])->name('client.phongquiz.ready');
    Route::post('/room/{ma_phong}/reset', [\App\Http\Controllers\PhongQuizController::class, 'clientResetRoom'])->name('client.phongquiz.reset');
    Route::post('/nickname', [\App\Http\Controllers\PhongQuizController::class, 'updateSessionNickname'])->name('client.phongquiz.nickname');
    Route::get('/leaderboard', [\App\Http\Controllers\PhongQuizController::class, 'getLeaderboard'])->name('client.phongquiz.leaderboard');
});


Route::get('/hoi-dap-ai', [AiAssistantController::class, 'index'])->name('client.ai.index');
Route::post('/ask-ai', [AiAssistantController::class, 'ask'])->name('client.ai.ask');
Route::get('/hoi-dap-ai/{id}', [AiAssistantController::class, 'show'])->name('client.ai.show');
Route::patch('/hoi-dap-ai/{id}/rename', [AiAssistantController::class, 'rename'])->name('client.ai.rename');
Route::delete('/hoi-dap-ai/{id}', [AiAssistantController::class, 'destroy'])->name('client.ai.destroy');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
