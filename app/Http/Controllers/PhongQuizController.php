<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Client\FrontendController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhongQuiz;
use App\Models\ThanhVienPhong;
use App\Models\ChiTietTraLoiRealtime;
use App\Models\MonHoc;
use App\Models\CauHoi;
use App\Models\DapAn;
use App\Http\Requests\PhongQuiz\CreateRoomRequest;
use App\Http\Requests\PhongQuiz\JoinRoomRequest;
use App\Http\Requests\PhongQuiz\SubmitAnswerRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PhongQuizController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function restoreSessionFromCookies()
    {
        if (!session()->has('quiz_nickname') && request()->hasCookie('quiz_nickname')) {
            session(['quiz_nickname' => request()->cookie('quiz_nickname')]);
        }

        if (!session()->has('quiz_guest_user_id') && request()->hasCookie('quiz_guest_user_id')) {
            $guestId = request()->cookie('quiz_guest_user_id');
            if (\App\Models\User::where('id', $guestId)->exists()) {
                session(['quiz_guest_user_id' => $guestId]);
            }
        }
    }

    protected function getQuizUserId()
    {
        $this->restoreSessionFromCookies();

        if (auth()->check()) {
            return auth()->id();
        }
        if (session()->has('quiz_guest_user_id')) {
            return (int) session('quiz_guest_user_id');
        }
        $user = \App\Models\User::create([
            'ma_sv' => 'GUEST_' . mt_rand(10000, 99999),
            'ho_ten' => 'Khách_' . mt_rand(1000, 9999),
            'email' => 'guest_' . uniqid() . '@quizvui.local',
            'mat_khau' => bcrypt(uniqid()),
            'vai_tro_id' => 3 // Sinh viên/Khách
        ]);
        session(['quiz_guest_user_id' => $user->id]);
        cookie()->queue('quiz_guest_user_id', $user->id, 2628000); // 5 years
        return $user->id;
    }
    /**
     * Danh sách tất cả các phòng Quiz
     */
    public function index(Request $request)
    {
        $query = PhongQuiz::with(['monHoc', 'chuPhong'])
            ->withCount('thanhVien');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ma_phong', 'like', "%{$search}%")
                  ->orWhere('ten_phong', 'like', "%{$search}%");
            });
        }

        $rooms = $query->orderBy('created_at', 'desc')->paginate(10);
        $monHocs = MonHoc::where('trang_thai', 1)->orderBy('ten_mon_hoc')->get();

        return view('Admin.phongquiz.index', compact('rooms', 'monHocs'));
    }

    /**
     * Tạo phòng Quiz mới
     */
    public function store(CreateRoomRequest $request)
    {
        // 1. Tạo mã phòng độc nhất và ngẫu nhiên gồm 6 chữ số
        do {
            $ma_phong = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (PhongQuiz::where('ma_phong', $ma_phong)->where('trang_thai', '!=', 3)->exists());

        // 2. Lấy danh sách câu hỏi phù hợp từ ngân hàng câu hỏi
        $questionsQuery = CauHoi::whereHas('chuongHoc', function($q) use ($request) {
            $q->where('mon_hoc_id', $request->mon_hoc_id);
        })->where('trang_thai', 1)->has('dapAns');

        if ($request->filled('muc_do_cau_hoi')) {
            $questionsQuery->where('muc_do', $request->muc_do_cau_hoi);
        }

        $tong_so_cau = $request->tong_so_cau ?? 10;
        $questions = $questionsQuery->inRandomOrder()->limit($tong_so_cau)->get();

        if ($questions->count() < 5) {
            return response()->json([
                'success' => false,
                'message' => 'Môn học này không đủ câu hỏi trắc nghiệm hợp lệ (tối thiểu 5 câu) ở mức độ đã chọn để tạo phòng.'
            ], 422);
        }

        // 3. Khởi tạo phòng
        $room = PhongQuiz::create([
            'ma_phong' => $ma_phong,
            'ten_phong' => $request->ten_phong ?? ('Phòng thi đấu #' . $ma_phong),
            'mon_hoc_id' => $request->mon_hoc_id,
            'muc_do_cau_hoi' => $request->muc_do_cau_hoi,
            'chu_phong_id' => auth()->id(),
            'trang_thai' => 1, // Đang đợi thành viên
            'tong_so_cau' => $questions->count(),
            'thoi_gian_tra_loi_cau_hoi' => $request->thoi_gian_tra_loi_cau_hoi ?? 30,
            'danh_sach_cau_hoi' => $questions->pluck('id')->toJson()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo phòng thi đấu Realtime thành công!',
            'redirect' => route('admin.phongquiz.room', $room->ma_phong)
        ]);
    }

    /**
     * Giao diện điều phối phòng Quiz dành cho Admin
     */
    public function adminRoom($ma_phong)
    {
        $room = PhongQuiz::with(['monHoc', 'chuPhong'])
            ->where('ma_phong', $ma_phong)
            ->firstOrFail();

        // Chỉ chủ phòng hoặc Admin cấp cao mới điều khiển được phòng
        if ($room->chu_phong_id !== auth()->id() && (!auth()->check() || !auth()->user()->isSuperAdmin())) {
            return redirect()->route('admin.phongquiz.index')->with('error', 'Bạn không có quyền quản lý phòng chơi này.');
        }

        return view('Admin.phongquiz.room', compact('room'));
    }

    /**
     * API Lấy trạng thái Realtime của phòng (dùng cho AJAX polling từ Admin hoặc Student)
     */
    public function roomStatus(Request $request, $ma_phong)
    {
        $room = PhongQuiz::with(['monHoc'])
            ->where('ma_phong', $ma_phong)
            ->firstOrFail();

        // Mark members as offline if they haven't polled in the last 6 seconds
        $threshold = Carbon::now()->subSeconds(6);
        ThanhVienPhong::where('phong_quiz_id', $room->id)
            ->where('is_online', true)
            ->where('updated_at', '<', $threshold)
            ->update(['is_online' => false]);

        if ($request->routeIs('admin.phongquiz.status')) {
            if ($room->chu_phong_id !== auth()->id() && (!auth()->check() || !auth()->user()->isSuperAdmin())) {
                abort(403, 'Bạn không có quyền xem trạng thái phòng chơi này.');
            }
        } else {
            $userId = $this->getQuizUserId();
            $member = ThanhVienPhong::where('phong_quiz_id', $room->id)
                ->where('user_id', $userId)
                ->first();

            if (!$member) {
                abort(403, 'Bạn cần tham gia phòng trước khi xem trạng thái.');
            }

            // Update current user's last seen and online status
            $member->update([
                'is_online' => true,
                'updated_at' => Carbon::now()
            ]);
        }

        // 1. Lấy danh sách thành viên hiện tại cùng điểm số
        $members = ThanhVienPhong::with('user')
            ->where('phong_quiz_id', $room->id)
            ->orderBy('tong_diem', 'desc')
            ->orderBy('so_cau_dung', 'desc')
            ->get()
            ->map(function($m) {
                return [
                    'user_id' => $m->user_id,
                    'name' => $m->biem_danh ?: ($m->user->ho_ten ?? 'Học sinh'),
                    'avatar' => $m->user->avatar_url ?? null,
                    'score' => $m->tong_diem,
                    'correct_count' => $m->so_cau_dung,
                    'is_online' => (bool)$m->is_online,
                    'is_ready' => (bool)$m->is_ready,
                ];
            });

        // 2. Xử lý câu hỏi hiện tại nếu đang thi đấu
        $currentQuestion = null;
        $answerStats = null;
        $answeredUsers = [];
        $secondsRemaining = 0;
        $allAnswered = false;
        $canRevealAnswer = $request->routeIs('admin.phongquiz.status');

        if ($room->trang_thai === 2 && $room->cau_hoi_hien_tai_id) {
            $question = CauHoi::with('dapAns')->find($room->cau_hoi_hien_tai_id);
            if ($question) {
                // Xác định vị trí câu hiện tại
                $questionIds = json_decode($room->danh_sach_cau_hoi, true) ?: [];
                $currentIndex = array_search($room->cau_hoi_hien_tai_id, $questionIds);
                $questionNumber = $currentIndex !== false ? ($currentIndex + 1) : 1;

                $startTime = Carbon::parse($room->thoi_gian_bat_dau_cau_hoi);
                $now = Carbon::now();
                $elapsedSeconds = $now->diffInSeconds($startTime, true);
                $secondsRemaining = max(0, $room->thoi_gian_tra_loi_cau_hoi - $elapsedSeconds);
                $canRevealAnswer = $canRevealAnswer || $secondsRemaining === 0;

                // Lấy danh sách ID học sinh đã trả lời câu này
                $realtimeAnswers = ChiTietTraLoiRealtime::where('phong_quiz_id', $room->id)
                    ->where('cau_hoi_id', $room->cau_hoi_hien_tai_id)
                    ->get();
                
                $answeredUsers = $realtimeAnswers->pluck('user_id')->toArray();

                // Kiểm tra xem tất cả thành viên Online đã trả lời hết chưa
                $onlineMembers = $members->filter(fn($m) => $m['is_online'])->pluck('user_id')->toArray();
                if (count($onlineMembers) > 0 && count(array_intersect($onlineMembers, $answeredUsers)) === count($onlineMembers)) {
                    $allAnswered = true;
                }

                $canRevealAnswer = $canRevealAnswer || $secondsRemaining === 0 || $allAnswered;

                // Xác định xem người dùng hiện tại đã trả lời câu hỏi này chưa
                $myUserId = $request->routeIs('admin.phongquiz.status') ? null : $this->getQuizUserId();
                $hasAnswered = $myUserId ? in_array($myUserId, $answeredUsers) : false;
                $revealToThisUser = $canRevealAnswer || $hasAnswered;

                $currentQuestion = [
                    'id' => $question->id,
                    'noi_dung' => $question->noi_dung,
                    'hinh_anh' => $question->hinh_anh ? asset('storage/' . $question->hinh_anh) : null,
                    'number' => $questionNumber,
                    'giai_thich' => $revealToThisUser ? $question->giai_thich : null,
                    'choices' => $question->dapAns->map(function($d) use ($revealToThisUser) {
                        return [
                            'id' => $d->id,
                            'noi_dung' => $d->noi_dung,
                            'ky_hieu' => $d->ky_hieu, // A, B, C, D
                            'is_dung' => $revealToThisUser ? (bool)$d->is_dung : false,
                        ];
                    })
                ];

                // Thống kê bình chọn đáp án cho câu hỏi này
                $stats = [];
                foreach ($question->dapAns as $d) {
                    $count = $realtimeAnswers->where('dap_an_id', $d->id)->count();
                    $stats[$d->id] = [
                        'id' => $d->id,
                        'ky_hieu' => $d->ky_hieu,
                        'count' => $count,
                    ];
                }
                $answerStats = array_values($stats);
            }
        }

        $myAnswerIsCorrect = false;
        $myAnswerId = null;
        if ($room->trang_thai === 2 && !$request->routeIs('admin.phongquiz.status')) {
            $myRealtimeAnswer = ChiTietTraLoiRealtime::where('phong_quiz_id', $room->id)
                ->where('cau_hoi_id', $room->cau_hoi_hien_tai_id)
                ->where('user_id', $this->getQuizUserId())
                ->first();
            if ($myRealtimeAnswer) {
                $myAnswerId = $myRealtimeAnswer->dap_an_id;
                $myAnswerIsCorrect = $myRealtimeAnswer->is_chinh_xac;
            }
        }

        return response()->json([
            'success' => true,
            'room' => [
                'id' => $room->id,
                'ma_phong' => $room->ma_phong,
                'ten_phong' => $room->ten_phong,
                'mon_hoc' => [
                    'id' => $room->monHoc?->id,
                    'ten_mon_hoc' => $room->monHoc?->ten_mon_hoc,
                ],
                'muc_do_cau_hoi' => $room->muc_do_cau_hoi,
                'trang_thai' => $room->trang_thai,
                'tong_so_cau' => $room->tong_so_cau,
                'thoi_gian_tra_loi' => $room->thoi_gian_tra_loi_cau_hoi,
                'cau_hoi_hien_tai_id' => $room->cau_hoi_hien_tai_id,
            ],
            'members' => $members,
            'current_question' => $currentQuestion,
            'seconds_remaining' => $secondsRemaining,
            'answered_users' => $answeredUsers,
            'all_answered' => $allAnswered,
            'answer_stats' => $answerStats,
            'my_answer' => $myAnswerId,
            'my_answer_correct' => $myAnswerIsCorrect,
        ]);
    }

    /**
     * Admin Bắt đầu trận đấu (Đang đợi -> Đang thi đấu)
     */
    public function adminStartRoom($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();
        
        if ($room->trang_thai !== 1) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi đã bắt đầu hoặc đã kết thúc.']);
        }

        // Lấy câu hỏi đầu tiên
        $questionIds = json_decode($room->danh_sach_cau_hoi, true) ?: [];
        if (empty($questionIds)) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi không có câu hỏi hợp lệ.']);
        }

        $room->update([
            'trang_thai' => 2,
            'cau_hoi_hien_tai_id' => $questionIds[0],
            'thoi_gian_bat_dau_cau_hoi' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã bắt đầu trận đấu! Chúc các học sinh thi đấu tốt.',
        ]);
    }

    /**
     * Admin Chuyển sang câu hỏi tiếp theo
     */
    public function adminNextQuestion($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();

        if ($room->trang_thai !== 2) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi không ở trong trạng thái thi đấu.']);
        }

        $questionIds = json_decode($room->danh_sach_cau_hoi, true) ?: [];
        $currentIndex = array_search($room->cau_hoi_hien_tai_id, $questionIds);

        if ($currentIndex === false || $currentIndex + 1 >= count($questionIds)) {
            // Không còn câu hỏi tiếp theo -> Kết thúc game luôn
            $room->update([
                'trang_thai' => 3
            ]);
            return response()->json([
                'success' => true,
                'ended' => true,
                'message' => 'Đã hoàn thành toàn bộ câu hỏi. Đang chuyển sang màn hình kết quả chung cuộc!',
            ]);
        }

        // Chuyển sang câu kế tiếp
        $room->update([
            'cau_hoi_hien_tai_id' => $questionIds[$currentIndex + 1],
            'thoi_gian_bat_dau_cau_hoi' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'ended' => false,
            'message' => 'Đã chuyển sang câu hỏi tiếp theo!',
        ]);
    }

    /**
     * Admin kết thúc phòng thi đấu thủ công
     */
    public function adminEndRoom($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();
        $room->update(['trang_thai' => 3]);

        return response()->json([
            'success' => true,
            'message' => 'Đã kết thúc phòng chơi chơi thành công!',
        ]);
    }

    /**
     * Xóa phòng Quiz
     */
    public function destroy($id)
    {
        $room = PhongQuiz::findOrFail($id);
        $room->delete();

        return redirect()->route('admin.phongquiz.index')->with('success', 'Xóa phòng chơi thành công.');
    }


    // ==========================================
    // CLIENT ACTIONS (STUDENTS)
    // ==========================================

    public function clientJoinView()
    {
        $this->restoreSessionFromCookies();

        // 1. Dọn dẹp: Đánh dấu tất cả thành viên không tương tác trong 10 giây qua là offline
        $threshold = Carbon::now()->subSeconds(10);
        ThanhVienPhong::where('is_online', true)
            ->where('updated_at', '<', $threshold)
            ->update(['is_online' => false]);

        // 2. Tự động đóng các phòng đã tạo quá 1 phút và không có bất kỳ ai online
        $oneMinuteAgo = Carbon::now()->subMinute();
        PhongQuiz::whereIn('trang_thai', [1, 2])
            ->where('created_at', '<', $oneMinuteAgo)
            ->whereDoesntHave('thanhVien', function($q) {
                $q->where('is_online', true);
            })
            ->update(['trang_thai' => 3]);

        $monHocs = MonHoc::where('trang_thai', 1)->orderBy('ten_mon_hoc')->get();
        
        $activeRooms = \App\Models\PhongQuiz::with(['monHoc', 'chuPhong'])
            ->withCount('thanhVien')
            ->whereIn('trang_thai', [1, 2])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Client.phongquiz.join', compact('monHocs', 'activeRooms'));
    }

    /**
     * Client tạo phòng Quiz mới (người dùng bất kỳ đã đăng nhập)
     */
    public function clientStore(CreateRoomRequest $request)
    {
        // 1. Tạo mã phòng độc nhất
        do {
            $ma_phong = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (PhongQuiz::where('ma_phong', $ma_phong)->where('trang_thai', '!=', 3)->exists());

        // 2. Lấy câu hỏi phù hợp
        $questionsQuery = CauHoi::whereHas('chuongHoc', function($q) use ($request) {
            $q->where('mon_hoc_id', $request->mon_hoc_id);
        })->where('trang_thai', 1)->has('dapAns');

        if ($request->filled('muc_do_cau_hoi')) {
            $questionsQuery->where('muc_do', $request->muc_do_cau_hoi);
        }

        $tong_so_cau = $request->tong_so_cau ?? 10;
        $questions = $questionsQuery->inRandomOrder()->limit($tong_so_cau)->get();

        if ($questions->count() < 5) {
            return response()->json([
                'success' => false,
                'message' => 'Môn học này không đủ câu hỏi trắc nghiệm hợp lệ (tối thiểu 5 câu) ở mức độ đã chọn để tạo phòng.'
            ], 422);
        }

        // 3. Tạo phòng
        $room = PhongQuiz::create([
            'ma_phong' => $ma_phong,
            'ten_phong' => $request->ten_phong ?? ('Phòng thi đấu #' . $ma_phong),
            'mon_hoc_id' => $request->mon_hoc_id,
            'muc_do_cau_hoi' => $request->muc_do_cau_hoi,
            'chu_phong_id' => $this->getQuizUserId(),
            'trang_thai' => 1,
            'tong_so_cau' => $questions->count(),
            'thoi_gian_tra_loi_cau_hoi' => $request->thoi_gian_tra_loi_cau_hoi ?? 30,
            'danh_sach_cau_hoi' => $questions->pluck('id')->toJson()
        ]);

        // 4. Tự động thêm người tạo phòng vào danh sách thành viên
        ThanhVienPhong::updateOrCreate(
            [
                'phong_quiz_id' => $room->id,
                'user_id' => $this->getQuizUserId()
            ],
            [
                'biem_danh' => session('quiz_nickname'),
                'tong_diem' => 0,
                'so_cau_dung' => 0,
                'is_ready' => true,
                'is_online' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tạo phòng thi đấu thành công! Mã phòng: ' . $ma_phong,
            'redirect' => route('client.phongquiz.room', $room->ma_phong)
        ]);
    }

    /**
     * Học sinh gửi yêu cầu vào phòng
     */
    public function clientJoin(JoinRoomRequest $request)
    {
        $room = PhongQuiz::where('ma_phong', $request->ma_phong)
            ->whereIn('trang_thai', [1, 2])
            ->first();

        if (!$room) {
            return back()->withErrors(['ma_phong' => 'Phòng chơi không tồn tại hoặc trận đấu đã kết thúc.']);
        }

        $userId = $this->getQuizUserId();

        // Check if member already exists
        $member = ThanhVienPhong::where('phong_quiz_id', $room->id)
            ->where('user_id', $userId)
            ->first();

        if (!$member) {
            if ($room->trang_thai === 2) {
                return back()->withErrors(['ma_phong' => 'Trận đấu đang diễn ra, bạn không thể tham gia giữa chừng.']);
            }

            ThanhVienPhong::create([
                'phong_quiz_id' => $room->id,
                'user_id' => $userId,
                'biem_danh' => session('quiz_nickname'),
                'tong_diem' => 0,
                'so_cau_dung' => 0,
                'is_ready' => true,
                'is_online' => true,
            ]);
        } else {
            // Reconnecting - DO NOT reset score or correct count!
            $member->update([
                'is_online' => true,
                'biem_danh' => session('quiz_nickname') ?: $member->biem_danh
            ]);
        }

        return redirect()->route('client.phongquiz.room', $room->ma_phong);
    }

    /**
     * Giao diện màn hình thi đấu Realtime dành cho học sinh
     */
    public function clientRoom($ma_phong)
    {
        $this->restoreSessionFromCookies();

        $room = PhongQuiz::with(['monHoc'])
            ->where('ma_phong', $ma_phong)
            ->firstOrFail();

        // Xác nhận xem học sinh đã là thành viên phòng chưa, nếu chưa bắt vào lại
        $member = ThanhVienPhong::where('phong_quiz_id', $room->id)
            ->where('user_id', $this->getQuizUserId())
            ->first();

        if (!$member) {
            return redirect()->route('client.phongquiz.join')
                ->withErrors(['ma_phong' => 'Bạn cần nhập mã PIN để vào phòng thi đấu.']);
        }

        // Đánh dấu online lại khi họ tải trang
        $member->update([
            'is_online' => true,
            'biem_danh' => $member->biem_danh ?: session('quiz_nickname'),
            'updated_at' => Carbon::now()
        ]);

        return view('Client.phongquiz.room', compact('room', 'member'));
    }

    /**
     * Học sinh nộp đáp án Realtime cho câu hỏi hiện tại
     */
    public function clientSubmitAnswer(SubmitAnswerRequest $request, $ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)
            ->where('id', $request->phong_quiz_id)
            ->where('trang_thai', 2)
            ->firstOrFail();

        // 1. Kiểm tra xem câu hỏi này có khớp với câu hỏi hiện tại của phòng không
        if ($room->cau_hoi_hien_tai_id !== (int)$request->cau_hoi_id) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi! Câu hỏi này đã trôi qua hoặc chưa bắt đầu.'
            ], 422);
        }

        // 2. Kiểm tra xem học sinh đã trả lời câu này chưa
        $existing = ChiTietTraLoiRealtime::where('phong_quiz_id', $room->id)
            ->where('user_id', $this->getQuizUserId())
            ->where('cau_hoi_id', $request->cau_hoi_id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã nộp đáp án cho câu hỏi này rồi!'
            ], 422);
        }

        // 3. Tính toán thời gian trả lời
        $startTime = Carbon::parse($room->thoi_gian_bat_dau_cau_hoi);
        $now = Carbon::now();
        $elapsedMs = $now->diffInMilliseconds($startTime);
        $elapsedSeconds = $elapsedMs / 1000;

        // Phòng thủ: Nếu nộp trễ hơn giới hạn thời gian quá nhiều (do lag mạng) thì cho 0 điểm
        $isOvertime = $elapsedSeconds > ($room->thoi_gian_tra_loi_cau_hoi + 3); // nới rộng 3s delay mạng

        // 4. Kiểm tra đáp án chính xác
        $isCorrect = false;
        $points = 0;

        if ($request->filled('dap_an_id')) {
            $dapAn = DapAn::where('id', $request->dap_an_id)
                ->where('cau_hoi_id', $request->cau_hoi_id)
                ->first();
            
            if ($dapAn && $dapAn->is_dung) {
                $isCorrect = true;
                
                if (!$isOvertime) {
                    // Công thức tính điểm Kahoot: max = 1000đ, min = 500đ. Nộp càng nhanh điểm càng cao!
                    $limit = $room->thoi_gian_tra_loi_cau_hoi;
                    $points = max(500, intval(500 + 500 * (($limit - min($elapsedSeconds, $limit)) / $limit)));
                } else {
                    $points = 500; // Trễ giờ nhưng vẫn đúng thì thưởng cứng 500đ
                }
            }
        }

        // 5. Lưu kết quả chi tiết
        $detail = ChiTietTraLoiRealtime::create([
            'phong_quiz_id' => $room->id,
            'user_id' => $this->getQuizUserId(),
            'cau_hoi_id' => $request->cau_hoi_id,
            'dap_an_id' => $request->dap_an_id,
            'thoi_gian_tra_loi' => $elapsedMs,
            'diem_dat_duoc' => $points,
            'is_chinh_xac' => $isCorrect
        ]);

        // 6. Cập nhật bảng xếp hạng thành viên phòng
        $member = ThanhVienPhong::where('phong_quiz_id', $room->id)
            ->where('user_id', $this->getQuizUserId())
            ->first();

        if ($member) {
            $member->tong_diem += $points;
            if ($isCorrect) {
                $member->so_cau_dung += 1;
            }
            $member->save();
        }

        $question = CauHoi::find($request->cau_hoi_id);
        $correctDapAn = DapAn::where('cau_hoi_id', $request->cau_hoi_id)->where('is_dung', true)->first();

        return response()->json([
            'success' => true,
            'message' => 'Nộp câu trả lời thành công!',
            'is_correct' => $isCorrect,
            'points_gained' => $points,
            'total_score' => $member ? $member->tong_diem : 0,
            'giai_thich' => $question ? $question->giai_thich : null,
            'correct_choice' => $correctDapAn ? [
                'id' => $correctDapAn->id,
                'ky_hieu' => $correctDapAn->ky_hieu,
                'noi_dung' => $correctDapAn->noi_dung
            ] : null
        ]);
    }

    /**
     * Học sinh là chủ phòng bắt đầu trận đấu (Đang đợi -> Đang thi đấu)
     */
    public function clientStartRoom($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();
        
        if ($room->chu_phong_id !== $this->getQuizUserId()) {
            return response()->json(['success' => false, 'message' => 'Bạn không phải là chủ phòng đấu này.'], 403);
        }

        if ($room->trang_thai !== 1) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi đã bắt đầu hoặc đã kết thúc.']);
        }

        // Lấy câu hỏi đầu tiên
        $questionIds = json_decode($room->danh_sach_cau_hoi, true) ?: [];
        if (empty($questionIds)) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi không có câu hỏi hợp lệ.']);
        }

        $room->update([
            'trang_thai' => 2,
            'cau_hoi_hien_tai_id' => $questionIds[0],
            'thoi_gian_bat_dau_cau_hoi' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã bắt đầu trận đấu! Chúc các bạn thi đấu tốt.',
        ]);
    }

    /**
     * Học sinh là chủ phòng chuyển sang câu hỏi tiếp theo
     */
    public function clientNextQuestion($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();

        if ($room->chu_phong_id !== $this->getQuizUserId()) {
            return response()->json(['success' => false, 'message' => 'Bạn không phải là chủ phòng đấu này.'], 403);
        }

        if ($room->trang_thai !== 2) {
            return response()->json(['success' => false, 'message' => 'Phòng chơi không ở trong trạng thái thi đấu.']);
        }

        $questionIds = json_decode($room->danh_sach_cau_hoi, true) ?: [];
        $currentIndex = array_search($room->cau_hoi_hien_tai_id, $questionIds);

        if ($currentIndex === false || $currentIndex + 1 >= count($questionIds)) {
            // Không còn câu hỏi tiếp theo -> Kết thúc game luôn
            $room->update([
                'trang_thai' => 3
            ]);
            return response()->json([
                'success' => true,
                'ended' => true,
                'message' => 'Đã hoàn thành toàn bộ câu hỏi. Đang chuyển sang màn hình kết quả chung cuộc!',
            ]);
        }

        // Chuyển sang câu kế tiếp
        $room->update([
            'cau_hoi_hien_tai_id' => $questionIds[$currentIndex + 1],
            'thoi_gian_bat_dau_cau_hoi' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'ended' => false,
            'message' => 'Đã chuyển sang câu hỏi tiếp theo!',
        ]);
    }

    /**
     * Cập nhật biệt danh tạm thời của người chơi vào Session phục vụ Realtime Quiz
     */
    public function updateSessionNickname(Request $request)
    {
        $request->validate([
            'ho_ten' => 'required|string|max:30'
        ]);

        session(['quiz_nickname' => $request->ho_ten]);
        cookie()->queue('quiz_nickname', $request->ho_ten, 2628000); // 5 years

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật biệt danh tạm thời thành công!',
            'nickname' => $request->ho_ten
        ]);
    }

    /**
     * Lấy dữ liệu bảng xếp hạng thật từ database
     */
    public function getLeaderboard(Request $request)
    {
        $period = $request->input('period', 'week');
        $since = null;

        if ($period === 'day') {
            $since = Carbon::today();
        } elseif ($period === 'week') {
            $since = Carbon::now()->startOfWeek();
        } elseif ($period === 'month') {
            $since = Carbon::now()->startOfMonth();
        }

        $query = ThanhVienPhong::select('thanh_vien_phong.user_id', DB::raw('SUM(thanh_vien_phong.tong_diem) as total_score'))
            ->join('users', 'thanh_vien_phong.user_id', '=', 'users.id');

        if ($since) {
            $query->where('thanh_vien_phong.created_at', '>=', $since);
        }

        $results = $query->groupBy('user_id')
            ->orderBy('total_score', 'desc')
            ->limit(10)
            ->get();

        $data = $results->map(function($row) {
            $latestMember = ThanhVienPhong::where('user_id', $row->user_id)
                ->orderBy('created_at', 'desc')
                ->first();

            $name = $latestMember?->biem_danh ?: ($latestMember?->user?->ho_ten ?? 'Học sinh');
            return [
                'name' => $name,
                'score' => (int)$row->total_score
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Học sinh rời khỏi phòng thi đấu
     */
    public function clientLeaveRoom(Request $request, $ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->first();
        if ($room) {
            $userId = $this->getQuizUserId();
            
            // Đánh dấu thành viên offline
            ThanhVienPhong::where('phong_quiz_id', $room->id)
                ->where('user_id', $userId)
                ->update([
                    'is_online' => false
                ]);
                
            // Nếu người rời đi là chủ phòng và bấm nút thoát rõ ràng, kết thúc phòng luôn
            if ($request->input('explicit') == 1 && $room->chu_phong_id == $userId) {
                $room->update(['trang_thai' => 3]);
            } else {
                // Kiểm tra xem phòng còn ai online không
                $hasOnline = ThanhVienPhong::where('phong_quiz_id', $room->id)
                    ->where('is_online', true)
                    ->exists();
                if (!$hasOnline) {
                    $room->update(['trang_thai' => 3]);
                }
            }
        }
        return response()->json(['success' => true]);
    }

    /**
     * Tái thiết lập phòng chơi để chơi lại (chỉ chủ phòng mới có quyền)
     */
    public function clientResetRoom($ma_phong)
    {
        $room = PhongQuiz::where('ma_phong', $ma_phong)->firstOrFail();
        
        if ($room->chu_phong_id !== $this->getQuizUserId()) {
            return response()->json(['success' => false, 'message' => 'Bạn không phải là chủ phòng đấu này.'], 403);
        }

        // 1. Reset trạng thái phòng về 1 (chờ) và xóa câu hỏi hiện tại
        $room->update([
            'trang_thai' => 1,
            'cau_hoi_hien_tai_id' => null,
            'thoi_gian_bat_dau_cau_hoi' => null
        ]);

        // 2. Reset điểm số và số câu đúng của tất cả thành viên trong phòng về 0
        ThanhVienPhong::where('phong_quiz_id', $room->id)->update([
            'tong_diem' => 0,
            'so_cau_dung' => 0
        ]);

        // 3. Xóa các chi tiết trả lời câu hỏi của phòng này để chơi lại từ đầu
        ChiTietTraLoiRealtime::where('phong_quiz_id', $room->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã thiết lập lại phòng chơi thành công!'
        ]);
    }
}
