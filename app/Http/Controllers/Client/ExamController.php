<?php

namespace App\Http\Controllers\Client;

use App\Models\BaiKiemTra;
use App\Models\MonHoc;
use App\Models\CauHoi;
use App\Models\ChiTietBaiLam;
use App\Jobs\GenerateAiFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExamController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    // ── BƯỚC 0: Danh sách đề thi ──────────────────────────────────────
    public function index(Request $request)
    {
        $now = Carbon::now();

        $query = BaiKiemTra::with('monHoc')
            ->withCount('cauHois')
            ->where('trang_thai', 1)
            ->where(function ($q) use ($now) {
                // Đề không đặt thời gian mở/đóng thì luôn hiện
                $q->whereNull('thoi_gian_bat_dau')->orWhere('thoi_gian_bat_dau', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('thoi_gian_ket_thuc')->orWhere('thoi_gian_ket_thuc', '>=', $now);
            });

        if ($request->filled('tu_khoa')) {
            $query->where('ten_bai', 'like', '%' . $request->tu_khoa . '%');
        }

        if ($request->filled('mon_hoc_id')) {
            $query->where('mon_hoc_id', $request->mon_hoc_id);
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'az') {
                $query->orderBy('ten_bai', 'asc');
            } elseif ($request->sort === 'za') {
                $query->orderBy('ten_bai', 'desc');
            } elseif ($request->sort === 'moi') {
                $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $baiKiemTras = $query->paginate(12);
        $monHocs = MonHoc::where('trang_thai', 1)->orderBy('ten_mon_hoc')->get();

        // Lịch sử thi của user
        $lichSuThi = [];
        if (auth()->check()) {
            $lichSuThi = \App\Models\KetQuaThi::where('user_id', auth()->id())
                ->latest()
                ->get()
                ->groupBy('bai_kiem_tra_id')
                ->toArray();
        }

        $breadcrumbs = [['label' => 'Trang chủ', 'url' => route('home')], ['label' => 'Thi thử']];

        return view('Client.exams.index', compact('baiKiemTras', 'monHocs', 'lichSuThi', 'breadcrumbs'));
    }

    // ── BƯỚC 1: Chi tiết đề thi — xác nhận trước khi vào thi ─────────
    public function show($id)
    {
        $now = Carbon::now();

        $baiKiemTra = BaiKiemTra::with(['monHoc', 'nguoiTao'])
            ->withCount('cauHois')
            ->where('trang_thai', 1)
            ->findOrFail($id);

        // Kiểm tra thời gian mở/đóng
        $coTheThi = true;
        $lyDoKhongThe = null;

        if ($baiKiemTra->thoi_gian_bat_dau && $now->lt($baiKiemTra->thoi_gian_bat_dau)) {
            $coTheThi = false;
            $lyDoKhongThe = 'Đề thi chưa mở. Mở lúc ' . $baiKiemTra->thoi_gian_bat_dau->format('H:i d/m/Y');
        }

        if ($baiKiemTra->thoi_gian_ket_thuc && $now->gt($baiKiemTra->thoi_gian_ket_thuc)) {
            $coTheThi = false;
            $lyDoKhongThe = 'Đề thi đã đóng lúc ' . $baiKiemTra->thoi_gian_ket_thuc->format('H:i d/m/Y');
        }

        // Kiểm tra số lần làm bài
        $soLanDaLam = 0;
        if (auth()->check()) {
            $soLanDaLam = \App\Models\KetQuaThi::where('user_id', auth()->id())
                ->where('bai_kiem_tra_id', $id)
                ->count();

            if ($soLanDaLam >= $baiKiemTra->so_lan_lam_bai) {
                $coTheThi = false;
                $lyDoKhongThe = 'Bạn đã dùng hết ' . $baiKiemTra->so_lan_lam_bai . ' lần làm bài.';
            }
        }

        // Lịch sử thi
        $lichSuThi = [];
        if (auth()->check()) {
            $lichSuThi = \App\Models\KetQuaThi::where('user_id', auth()->id())
                ->where('bai_kiem_tra_id', $id)
                ->latest()
                ->take(5)
                ->get();
        }

        $breadcrumbs = [['label' => 'Trang chủ', 'url' => route('home')], ['label' => 'Thi thử', 'url' => route('client.exams.index')], ['label' => $baiKiemTra->ten_bai]];

        return view('Client.exams.show', compact('baiKiemTra', 'lichSuThi', 'coTheThi', 'lyDoKhongThe', 'soLanDaLam', 'breadcrumbs'));
    }

    // ── BƯỚC 2: Bắt đầu thi — tạo phiên ─────────────────────────────
    public function start($id)
    {
        $now = Carbon::now();

        $baiKiemTra = BaiKiemTra::with([
            'cauHois' => function ($q) {
                $q->where('trang_thai', 1)->orderBy('bai_kiem_tra_cau_hoi.thu_tu');
            },
        ])
            ->where('trang_thai', 1)
            ->findOrFail($id);

        // Validate thời gian
        if ($baiKiemTra->thoi_gian_bat_dau && $now->lt($baiKiemTra->thoi_gian_bat_dau)) {
            return redirect()->route('client.exams.show', $id)->with('error', 'Đề thi chưa mở!');
        }

        if ($baiKiemTra->thoi_gian_ket_thuc && $now->gt($baiKiemTra->thoi_gian_ket_thuc)) {
            return redirect()->route('client.exams.show', $id)->with('error', 'Đề thi đã đóng!');
        }

        // Validate số lần làm bài
        if (auth()->check()) {
            $soLanDaLam = \App\Models\KetQuaThi::where('user_id', auth()->id())
                ->where('bai_kiem_tra_id', $id)
                ->count();

            if ($soLanDaLam >= $baiKiemTra->so_lan_lam_bai) {
                return redirect()->route('client.exams.show', $id)->with('error', 'Bạn đã dùng hết số lần làm bài cho phép!');
            }
        }

        if ($baiKiemTra->cauHois->isEmpty()) {
            return redirect()->route('client.exams.show', $id)->with('error', 'Đề thi này chưa có câu hỏi. Vui lòng thử lại sau!');
        }

        // Lấy danh sách ID câu hỏi
        $questionIds = $baiKiemTra->cauHois->pluck('id')->toArray();

        // Đảo thứ tự câu hỏi nếu bật
        if ($baiKiemTra->dao_cau_hoi) {
            shuffle($questionIds);
        }

        // Tạo session phiên thi
        $sessionId = Str::random(12);

        session()->put('exam_' . $sessionId, [
            'bai_kiem_tra_id' => $baiKiemTra->id,
            'question_ids' => $questionIds,
            'thoi_gian_phut' => $baiKiemTra->thoi_gian_phut,
            'tong_so_cau' => count($questionIds),
            'dao_dap_an' => $baiKiemTra->dao_dap_an,
            'nop_khi_chuyen_tab' => $baiKiemTra->nop_khi_chuyen_tab,
            'xem_diem' => $baiKiemTra->xem_diem,
            'xem_bai_lam' => $baiKiemTra->xem_bai_lam,
            'cach_tinh_diem' => $baiKiemTra->cach_tinh_diem,
            'start_time' => now()->toDateTimeString(),
        ]);

        return redirect()->route('client.exams.workspace', ['session_id' => $sessionId]);
    }

    // ── BƯỚC 3: Màn hình làm bài ─────────────────────────────────────
    public function workspace($session_id)
    {
        $sessionData = session()->get('exam_' . $session_id);

        if (!$sessionData) {
            return redirect()->route('client.exams.index')->with('error', 'Phiên thi đã hết hạn hoặc không tồn tại.');
        }

        $baiKiemTra = BaiKiemTra::findOrFail($sessionData['bai_kiem_tra_id']);
        $questionIds = $sessionData['question_ids'];
        $thoiGianPhut = $sessionData['thoi_gian_phut'];
        $tongSoCau = $sessionData['tong_so_cau'];
        $daoDapAn = $sessionData['dao_dap_an'];
        $nopKhiChuyenTab = $sessionData['nop_khi_chuyen_tab'];

        $cauHois = CauHoi::with([
            'dapAns' => function ($q) use ($daoDapAn) {
                // Sử dụng một số cố định (ví dụ: 123) làm "hạt giống" (seed)
                // để thứ tự đảo luôn giống nhau ở các trang
                $daoDapAn ? $q->inRandomOrder(123) : $q->orderBy('thu_tu');
            },
        ])
            /*
        $cauHois = CauHoi::with([
            'dapAns' => function ($q) use ($daoDapAn) {
                $daoDapAn ? $q->inRandomOrder() : $q->orderBy('thu_tu');
            },
        ]) */
            ->whereIn('id', $questionIds)
            ->get()
            ->sortBy(fn($model) => array_search($model->id, $questionIds))
            ->values();

        return view('Client.exams.workspace', compact('baiKiemTra', 'cauHois', 'thoiGianPhut', 'tongSoCau', 'session_id', 'nopKhiChuyenTab'));
    }

    // ── BƯỚC 4: Nộp bài & chấm điểm ─────────────────────────────────
    public function submit(Request $request)
    {
        $session_id = $request->session_id;
        $userAnswers = $request->answers ?? [];
        $ketQuaId = null; // Khởi tạo biến để lưu ID kết quả thi

        $sessionData = session()->get('exam_' . $session_id);

        if (!$sessionData) {
            return redirect()->route('client.exams.index')->with('error', 'Phiên làm bài đã hết hạn.');
        }

        $baiKiemTraId = $sessionData['bai_kiem_tra_id'];
        $questionIds = $sessionData['question_ids'];
        $cachTinhDiem = $sessionData['cach_tinh_diem'];

        // Lấy điểm từng câu từ bảng pivot
        $diemTungCau = \App\Models\BaiKiemTra::findOrFail($baiKiemTraId)->cauHois()->whereIn('cau_hoi.id', $questionIds)->withPivot('diem')->get()->pluck('pivot.diem', 'id')->toArray();

        // Lấy đáp án đúng (chỉ cho câu trắc nghiệm đơn lẻ)
        $correctAnswers = \App\Models\DapAn::whereIn('cau_hoi_id', $questionIds)->where('is_dung', 1)->get()->groupBy('cau_hoi_id');

        // Lấy loại câu hỏi để xử lý riêng
        $loaiCauHoi = \App\Models\CauHoi::whereIn('id', $questionIds)->pluck('loai_cau_hoi', 'id')->toArray();

        $tongDiem = 0;
        $soCauDung = 0;
        $chiTietBaiLam = [];

        foreach ($questionIds as $qId) {
            $userAns = $userAnswers[$qId] ?? null;
            $type = $loaiCauHoi[$qId] ?? 0;
            $diemCau = $diemTungCau[$qId] ?? 0;
            
            $isCorrect = false;
            $tuLuanText = null;
            $selectedId = null;
            $correctData = null;

            if ($type == 4) { // Tự luận
                $isCorrect = false;
                $tuLuanText = $userAns;
            } elseif ($type == 3) { // Điền khuyết
                // Lấy danh sách đáp án đúng cho câu này
                $corrects = $correctAnswers->get($qId);
                if ($corrects && is_array($userAns)) {
                    $matchCount = 0;
                    $totalBlanks = $corrects->count();
                    
                    foreach ($corrects as $idx => $cAns) {
                        $sAns = $userAns[$idx] ?? '';
                        if (trim(strtolower($sAns)) === trim(strtolower($cAns->noi_dung))) {
                            $matchCount++;
                        }
                    }
                    // Chấm đúng nếu tất cả các ô đều đúng
                    if ($matchCount === $totalBlanks && $totalBlanks > 0) {
                        $isCorrect = true;
                    }
                }
                $tuLuanText = is_array($userAns) ? json_encode($userAns) : $userAns;
                $correctData = $corrects ? $corrects->pluck('id')->toArray() : null;
            } else { // Trắc nghiệm, Đúng/Sai
                $corrects = $correctAnswers->get($qId);
                $correctAnsId = $corrects ? $corrects->first()->id : null;
                $isCorrect = $userAns && $userAns == $correctAnsId;
                $selectedId = $userAns;
                $correctData = $correctAnsId;
            }

            if ($isCorrect) {
                $tongDiem += $diemCau;
                $soCauDung++;
            }

            $chiTietBaiLam[$qId] = [
                'selected'    => $selectedId,
                'tu_luan_text'=> $tuLuanText,
                'correct'     => $correctData,
                'is_correct'  => $isCorrect,
                'diem'        => $isCorrect ? $diemCau : 0,
            ];
        }

        // Ép tất cả về thang điểm 10
        $tongDiemToiDa = array_sum($diemTungCau);
        if ($tongDiemToiDa > 0) {
            $diem = round(($tongDiem / $tongDiemToiDa) * 10, 2);
        } else {
            $diem = 0;
        }

        $tongSoCau = count($questionIds);
        $startTime = Carbon::parse($sessionData['start_time']);
        $thoiGianLam = now()->diffInSeconds($startTime);

        // Lưu kết quả vào DB
        if (auth()->check()) {
            // Định dạng tong_thoi_gian_lam thành chuỗi "X phút Y giây"
            $phut = floor($thoiGianLam / 60);
            $giay = $thoiGianLam % 60;
            $thoiGianStr = $phut > 0 ? "{$phut} phút {$giay} giây" : "{$giay} giây";

            // 1. LƯU VÀO BIẾN $ketQua ĐỂ LẤY ĐƯỢC ID
            $ketQua = \App\Models\KetQuaThi::create([
                'user_id' => auth()->id(),
                'bai_kiem_tra_id' => $baiKiemTraId,
                'diem' => $diem,
                'so_cau_dung' => $soCauDung,
                'tong_so_cau' => $tongSoCau,
                'thoi_gian_vao_thi' => Carbon::parse($sessionData['start_time']),
                'thoi_gian_nop_bai' => now(),
                'tong_thoi_gian_lam' => $thoiGianStr,
                'so_lan_vi_pham_tab' => $request->so_lan_vi_pham_tab ?? 0,
                'trang_thai' => 2, // 2: Đang chấm (theo comment của bạn: 1: Đã nộp bài, 2: Đang chấm, 3: Bị hủy)
                'ai_feedback' => null,
            ]);

            $ketQuaId = $ketQua->id;

            // 3. LƯU CHI TIẾT BÀI LÀM VÀO DB (để admin backend có thể lấy được)
            foreach ($chiTietBaiLam as $qId => $chiTiet) {
                ChiTietBaiLam::create([
                    'ket_qua_id'        => $ketQuaId,
                    'cau_hoi_id'        => $qId,
                    // Câu tự luận KHÔNG lưu text vào dap_an_chon_id (cột integer)
                    'dap_an_chon_id'    => !empty($chiTiet['selected']) ? (int) $chiTiet['selected'] : null,
                    'cau_tra_loi_tu_luan' => $chiTiet['tu_luan_text'] ?? null, // Lưu bài làm tự luận
                    'is_correct'        => $chiTiet['is_correct'],
                    'diem'              => $chiTiet['diem'] ?? 0, // Lưu điểm từng câu
                    'dap_an_dung_ids'   => is_array($chiTiet['correct']) ? $chiTiet['correct'] : [$chiTiet['correct']], 
                    'thu_tu_tra_loi'    => array_search($qId, $questionIds) + 1,
                    'is_marked'         => $request->input("marked.$qId") ?? false,
                    'thoi_gian_tra_loi' => $request->input("times.$qId") ?? 0,
                ]);
            }

            // 4. GỌI JOB ĐỂ AI PHÂN TÍCH CHẠY NGẦM
            \App\Jobs\GenerateAiFeedback::dispatch($ketQua->id);
        }

        // Cập nhật thống kê bài kiểm tra
        $baiKiemTra = \App\Models\BaiKiemTra::findOrFail($baiKiemTraId);
        $baiKiemTra->increment('so_luot_thi');

        // Tính lại điểm trung bình
        $diemTb = \App\Models\KetQuaThi::where('bai_kiem_tra_id', $baiKiemTraId)->avg('diem');
        $baiKiemTra->update(['diem_trung_binh' => round($diemTb, 2)]);

        // Lưu kết quả vào session
        $sessionData['result'] = [
            'ket_qua_id' => $ketQuaId, // 4. TRUYỀN THÊM ID VÀO SESSION ĐỂ TRANG RESULT LẤY ĐƯỢC
            'diem' => $diem,
            'tong_diem_toi_da' => $tongDiemToiDa,
            'so_cau_dung' => $soCauDung,
            'tong_so_cau' => $tongSoCau,
            'thoi_gian_lam' => $thoiGianLam,
            'chi_tiet' => $chiTietBaiLam,
            'thoi_gian_nop' => now()->toDateTimeString(),
        ];
        session()->put('exam_' . $session_id, $sessionData);

        return redirect()->route('client.exams.result', ['session_id' => $session_id]);
    }
    // ── BƯỚC 5: Trang kết quả ────────────────────────────────────────
    public function result($session_id)
    {
        // 1. Lấy đúng khóa mà chúng ta đã lưu ở hàm submit
        $sessionData = session('exam_' . $session_id);

        // 2. Kiểm tra nếu không có dữ liệu (Session hết hạn hoặc chưa nộp bài)
        if (!$sessionData || !isset($sessionData['result'])) {
            // Hỗ trợ xem lại bài từ Lịch sử (khi session_id là ID của kết quả thi)
            if (is_numeric($session_id) && auth()->check()) {
                $ketQuaThi = \App\Models\KetQuaThi::with(['baiKiemTra', 'chiTietBaiLam'])->where('user_id', auth()->id())->find($session_id);
                if ($ketQuaThi) {
                    $baiKiemTra = $ketQuaThi->baiKiemTra;
                    
                    // Lấy lại thời gian làm bài (dạng số giây)
                    $seconds = 0;
                    if (preg_match_all('/(\d+)/', $ketQuaThi->tong_thoi_gian_lam, $m)) {
                        if (str_contains($ketQuaThi->tong_thoi_gian_lam, 'phút') && str_contains($ketQuaThi->tong_thoi_gian_lam, 'giây')) {
                            $seconds = ($m[0][0] * 60) + ($m[0][1] ?? 0);
                        } elseif (str_contains($ketQuaThi->tong_thoi_gian_lam, 'phút')) {
                            $seconds = $m[0][0] * 60;
                        } elseif (isset($m[0][0])) {
                            $seconds = $m[0][0];
                        }
                    }

                    // Tái tạo lại sessionData cần thiết từ Database
                    $sessionData = [
                        'bai_kiem_tra_id' => $baiKiemTra->id,
                        'dao_dap_an' => $baiKiemTra->dao_dap_an,
                        'xem_bai_lam' => $baiKiemTra->xem_bai_lam,
                        'question_ids' => $ketQuaThi->chiTietBaiLam->sortBy('thu_tu_tra_loi')->pluck('cau_hoi_id')->toArray(),
                        'result' => [
                            'ket_qua_id' => $ketQuaThi->id,
                            'diem' => $ketQuaThi->diem,
                            'so_cau_dung' => $ketQuaThi->so_cau_dung,
                            'tong_so_cau' => $ketQuaThi->tong_so_cau,
                            'thoi_gian_lam' => $seconds, 
                            'chi_tiet' => [], // Được điền lại ở phần dưới
                        ]
                    ];
                } else {
                    return redirect()->route('client.exams.index')->with('error', 'Không tìm thấy kết quả bài thi.');
                }
            } else {
                return redirect()->route('client.exams.index')->with('error', 'Không tìm thấy kết quả bài thi hoặc phiên làm việc đã hết hạn.');
            }
        }

        // 3. Lấy bài kiểm tra từ session data (không phải từ session_id)
        $baiKiemTra = BaiKiemTra::findOrFail($sessionData['bai_kiem_tra_id']);

        $result = $sessionData['result'];
        $cauHois = collect();

        // Lấy kết quả từ database nếu có ket_qua_id
        $ketQuaThi = null;
        if (isset($result['ket_qua_id']) && $result['ket_qua_id']) {
            $ketQuaThi = \App\Models\KetQuaThi::with('chiTietBaiLam')->find($result['ket_qua_id']);
            
            // Ưu tiên lấy điểm và số câu đúng từ database (vì AI có thể đã cập nhật lại)
            if ($ketQuaThi) {
                $result['diem'] = $ketQuaThi->diem;
                $result['so_cau_dung'] = $ketQuaThi->so_cau_dung;
                
                // Đồng bộ lại chi tiết bài làm từ DB vào biến $result['chi_tiet']
                $dbDetails = [];
                foreach ($ketQuaThi->chiTietBaiLam as $dt) {
                    $dbDetails[$dt->cau_hoi_id] = [
                        'selected' => $dt->dap_an_chon_id,
                        'tu_luan_text' => $dt->cau_tra_loi_tu_luan,
                        // Lấy từ DB, nếu chưa có thì lấy từ session cũ để đảm bảo hiển thị đúng màu sắc
                        'correct' => $dt->dap_an_dung_ids ?? ($result['chi_tiet'][$dt->cau_hoi_id]['correct'] ?? null),
                        'is_correct' => $dt->is_correct,
                        'diem' => $dt->diem,
                        'ai_feedback' => $dt->phan_tich_sai, // Lấy nhận xét riêng từng câu của AI
                    ];
                }
                $result['chi_tiet'] = $dbDetails;
            }
        }

        // 3. Lấy danh sách câu hỏi nếu được phép xem bài làm
        if ($sessionData['xem_bai_lam']) {
            $daoDapAn = $sessionData['dao_dap_an'] ?? false;

            $cauHois = CauHoi::with([
                'dapAns' => function ($q) use ($daoDapAn) {
                    // Dùng hạt giống 123 như đã thống nhất để vị trí đáp án đứng yên
                    $daoDapAn ? $q->inRandomOrder(123) : $q->orderBy('thu_tu');
                },
            ])
                ->whereIn('id', $sessionData['question_ids'])
                ->get()
                ->sortBy(fn($model) => array_search($model->id, $sessionData['question_ids']))
                ->values();
        }
        /*  if ($sessionData['xem_bai_lam']) {
            $cauHois = CauHoi::with('dapAns')->whereIn('id', $sessionData['question_ids'])->get()->sortBy(fn($model) => array_search($model->id, $sessionData['question_ids']))->values();
        } */

        // Ép tất cả về thang điểm 10
        $thangDiem = 10;

        // Xếp loại (tính theo %)
        $phanTram = $thangDiem > 0 ? ($result['diem'] / $thangDiem) * 100 : 0;
        $result['xep_loai'] = match (true) {
            $phanTram >= 90 => ['label' => 'Xuất sắc', 'class' => 'excellent'],
            $phanTram >= 80 => ['label' => 'Giỏi', 'class' => 'good'],
            $phanTram >= 65 => ['label' => 'Khá', 'class' => 'fair'],
            $phanTram >= 50 => ['label' => 'Trung bình', 'class' => 'average'],
            default => ['label' => 'Yếu', 'class' => 'poor'],
        };

        $breadcrumbs = [['label' => 'Trang chủ', 'url' => route('home')], ['label' => 'Thi thử', 'url' => route('client.exams.index')], ['label' => $baiKiemTra->ten_bai, 'url' => route('client.exams.show', $baiKiemTra->id)], ['label' => 'Kết quả']];

        return view('Client.exams.result', compact('baiKiemTra', 'result', 'cauHois', 'thangDiem', 'session_id', 'breadcrumbs', 'ketQuaThi'));
    }

    public function checkAiStatus($id)
    {
        $ketQua = \App\Models\KetQuaThi::findOrFail($id);
        return response()->json([
            'trang_thai' => $ketQua->trang_thai,
            'ai_feedback' => $ketQua->ai_feedback,
        ]);
    }
}