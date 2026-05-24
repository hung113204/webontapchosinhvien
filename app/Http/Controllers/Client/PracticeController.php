<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MonHoc;
use App\Models\ChuongHoc;
use App\Models\CauHoi;
use App\Models\PhienLuyenTap;
use App\Models\danhmuctrangchu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PracticeController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();

        // Sử dụng middleware client.auth đồng bộ với route của bạn
        $this->middleware('client.auth')->except(['index', 'setup']);
    }

    /**
     * Hiển thị danh sách các môn học luyện tập
     */
    public function index(Request $request)
    {
        // Thêm withCount để đếm số lượng câu hỏi và chương học của mỗi môn
        $query = MonHoc::where('trang_thai', 1)->withCount([
            'cauHois',
            'chuongHocs' => function ($q) {
                $q->where('trang_thai', 1);
            },
        ]);
        if ($request->filled('tu_khoa')) {
            $query->where('ten_mon_hoc', 'like', '%' . $request->tu_khoa . '%');
        }
        if ($request->filled('danh_muc_id') && $request->danh_muc_id !== 'all') {
            $query->where('danh_muc_id', $request->danh_muc_id);
        }
        $monHocs = $query->orderBy('thu_tu')->orderBy('ten_mon_hoc')->get();
        $danhMucs = danhmuctrangchu::where('trang_thai', 1)->get();

        return view('Client.practice.index', compact('monHocs', 'danhMucs'));
    }

    /**
     * Bước 1: Trang thiết lập thông số
     */
    public function setup($mon_hoc_id)
    {
        $monHoc = MonHoc::with([
            'chuongHocs' => fn($q) => $q->where('trang_thai', 1)->orderBy('thu_tu'),
        ])->findOrFail($mon_hoc_id);
        $mucDoMacDinh = request('muc_do', ''); 
        return view('Client.practice.setup', compact('monHoc', 'mucDoMacDinh'));
    }

    /**
     * Bước 2: Xử lý tạo bài làm (Lưu mảng ID vào JSON)
     */
    /* public function generate(Request $request)
    {
        $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'so_luong' => 'required|integer|min:1|max:100',
            'che_do' => 'required|in:learn,test',
            'chuong_id' => 'required',
            'muc_do' => 'required',
        ]);

        $query = CauHoi::where('trang_thai', 1);

        if ($request->chuong_id !== 'all') {
            $query->where('chuong_hoc_id', $request->chuong_id);
        } else {
            $query->whereHas('chuongHoc', fn($q) => $q->where('mon_hoc_id', $request->mon_hoc_id));
        }

        if ($request->muc_do !== 'all') {
            $query->where('muc_do', $request->muc_do);
        }

        $questionIds = $query->inRandomOrder()->limit($request->so_luong)->pluck('id')->toArray();

        if (empty($questionIds)) {
            return back()->with('error', 'Không tìm thấy câu hỏi phù hợp!')->withInput();
        }

        DB::beginTransaction();
        try {
            $phien = PhienLuyenTap::create([
                'user_id' => Auth::id(),
                'mon_hoc_id' => $request->mon_hoc_id,
                'chuong_hoc_id' => $request->chuong_id === 'all' ? null : $request->chuong_id,
                'so_cau_hoi' => count($questionIds),
                'che_do' => $request->che_do === 'learn' ? 1 : 2,
                'thoi_gian_bat_dau' => now(),
                'trang_thai' => 0,
                'danh_sach_cau_hoi' => json_encode($questionIds),
            ]);

            DB::commit();
            return redirect()->route('client.practice.workspace', ['session_id' => $phien->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo phiên luyện tập: ' . $e->getMessage());
            return back()->with('error', 'Lỗi hệ thống khi tạo bài làm.');
        }
    } */
    /**
     * Bước 2: Xử lý tạo bài làm - Phiên bản CẢI TIẾN (cân bằng mức độ)
     */
    public function generate(Request $request)
    {
        $request->validate([
            'mon_hoc_id' => 'required|exists:mon_hoc,id',
            'so_luong' => 'required|integer|min:5|max:100',
            'che_do' => 'required|in:learn,test',
            'chuong_id' => 'required',
            'muc_do' => 'required',
            'loai_cau_hoi' => 'required|in:all,objective,subjective',
        ]);

        $monHocId = $request->mon_hoc_id;
        $soLuong = (int) $request->so_luong;
        $chuongId = $request->chuong_id;
        $mucDo = $request->muc_do; 
        $loaiCauHoi = $request->loai_cau_hoi;

        // Xây dựng query cơ bản
        $query = CauHoi::where('trang_thai', 1)->whereHas('chuongHoc', function ($q) use ($monHocId, $chuongId) {
            if ($chuongId !== 'all') {
                $q->where('id', $chuongId);
            } else {
                $q->where('mon_hoc_id', $monHocId);
            }
        });

        // Lọc theo loại câu hỏi
        if ($loaiCauHoi === 'objective') {
            $query->whereIn('loai_cau_hoi', [1, 2]); // Trắc nghiệm & Đúng/Sai
        } elseif ($loaiCauHoi === 'subjective') {
            $query->whereIn('loai_cau_hoi', [3, 4]); // Điền khuyết & Tự luận
        }

        $questionIds = [];

        if ($mucDo !== 'all') {
            $questionIds = $query->where('muc_do', $mucDo)->inRandomOrder()->limit($soLuong)->pluck('id')->toArray();
        } else {
            // Phân bổ cân bằng: 40% dễ, 40% trung bình, 20% khó
            $soDe = (int) round($soLuong * 0.4);        
            $soTrungBinh = (int) round($soLuong * 0.4); 
            $soKho = max(1, $soLuong - $soDe - $soTrungBinh); 
            $deIds = (clone $query)->where('muc_do', 1)->inRandomOrder()->limit($soDe)->pluck('id')->toArray();
            $trungBinhIds = (clone $query)->where('muc_do', 2)->inRandomOrder()->limit($soTrungBinh)->pluck('id')->toArray();
            // Lấy câu khó
            $khoIds = (clone $query)->where('muc_do', 3)->inRandomOrder()->limit($soKho)->pluck('id')->toArray();
            $questionIds = array_merge($deIds, $trungBinhIds, $khoIds);
            shuffle($questionIds); 
        }
        //thông báo lỗi 
        if (empty($questionIds)) {
            return back()->with('error', 'Không tìm thấy câu hỏi phù hợp. Vui lòng chọn phạm vi kiến thức hoặc mức độ khác.')->withInput();
        }

        // (Tùy chọn nâng cao) Loại bỏ một số câu đã làm gần đây của user
        // $questionIds = $this->filterRecentQuestions($questionIds, $monHocId);

        DB::beginTransaction();
        try {
            $phien = PhienLuyenTap::create([
                'user_id' => Auth::id(),
                'mon_hoc_id' => $monHocId,
                'chuong_hoc_id' => $chuongId === 'all' ? null : $chuongId,
                'so_cau_hoi' => count($questionIds),
                'che_do' => $request->che_do === 'learn' ? 1 : 2,
                'thoi_gian_bat_dau' => now(),
                'trang_thai' => 0,
                'danh_sach_cau_hoi' => json_encode($questionIds),
            ]);

            DB::commit();

            return redirect()
                ->route('client.practice.workspace', ['session_id' => $phien->id])
                ->with('success', 'Đã tạo bộ câu hỏi thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi tạo phiên luyện tập: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi tạo bài luyện tập.')->withInput();
        }
    }

    /**
     * Bước 3: Màn hình làm bài (Workspace)
     */
    public function workspace($session_id)
    {
        $phien = PhienLuyenTap::where('id', $session_id)->where('user_id', Auth::id())->first();

        if (!$phien) {
            return redirect()->route('client.practice.index')->with('error', 'Phiên làm bài không tồn tại.');
        }

        if ($phien->trang_thai == 1) {
            return redirect()->route('client.practice.result', ['session_id' => $phien->id]);
        }

        $monHoc = MonHoc::findOrFail($phien->mon_hoc_id);

        // Giải mã JSON an toàn, tránh lỗi count()
        $cauHoiIds = json_decode($phien->danh_sach_cau_hoi, true);

        if (!is_array($cauHoiIds) || empty($cauHoiIds)) {
            return redirect()->route('client.practice.index')->with('error', 'Dữ liệu câu hỏi bị lỗi.');
        }

        $cauHois = CauHoi::with('dapAns')->whereIn('id', $cauHoiIds)->get()->sortBy(fn($model) => array_search($model->id, $cauHoiIds))->values();

        $cheDo = $phien->che_do == 1 ? 'learn' : 'test';

        return view('Client.practice.workspace', compact('monHoc', 'cauHois', 'session_id', 'cheDo', 'phien'));
    }

    /**
     * Bước 4: Chấm điểm bài làm
     */
    public function submit(Request $request)
    {
        $phien = PhienLuyenTap::where('id', $request->session_id)->where('user_id', Auth::id())->firstOrFail();

        if ($phien->trang_thai == 1) {
            return redirect()->route('client.practice.result', ['session_id' => $phien->id]);
        }
        $userAnswers = $request->input('answers', []);
        $cauHoiIds = json_decode($phien->danh_sach_cau_hoi, true);
        $cauHois = CauHoi::with('dapAns')->whereIn('id', $cauHoiIds)->get();
        $soCauDung = 0;
        $chiTiet = [];
        foreach ($cauHois as $cau) {
            $correctDapAn = $cau->dapAns->where('is_dung', 1)->first();
            $correctId = $correctDapAn ? $correctDapAn->id : null;
            $selectedId = $userAnswers[$cau->id] ?? null;
            $isCorrect = false;
            $aiFeedback = null;
            
            if ($cau->loai_cau_hoi == 4 || $cau->loai_cau_hoi == 3) {
                // Tự luận hoặc Điền khuyết: Gọi AI chấm điểm cho linh hoạt
                $studentText = is_array($selectedId) ? json_encode($selectedId, JSON_UNESCAPED_UNICODE) : $selectedId;
                
                if ($cau->loai_cau_hoi == 4) {
                    $referenceText = $correctDapAn ? $correctDapAn->noi_dung : $cau->giai_thich;
                } else {
                    $referenceText = $cau->dapAns->where('is_dung', 1)->pluck('noi_dung')->implode(', ');
                }
                
                // KIỂM TRA TRƯỚC: Nếu điền khuyết và khớp chính xác thì không cần gọi AI
                $isStrictMatch = false;
                if ($cau->loai_cau_hoi == 3 && is_array($selectedId)) {
                    $corrects = $cau->dapAns->where('is_dung', 1)->values();
                    $matchCount = 0;
                    foreach ($corrects as $idx => $cAns) {
                        $sAns = $selectedId[$idx] ?? '';
                        if (trim(strtolower($sAns)) === trim(strtolower($cAns->noi_dung))) {
                            $matchCount++;
                        }
                    }
                    if ($matchCount === $corrects->count() && $corrects->count() > 0) {
                        $isStrictMatch = true;
                        $isCorrect = true;
                        $aiFeedback = "Chính xác tuyệt đối!";
                    }
                }

                $shouldGradeWithAi = !$isStrictMatch
                    && !empty($referenceText)
                    && ($cau->loai_cau_hoi == 4 || !empty($studentText));

                if ($shouldGradeWithAi) {
                    try {
                        $typeName = ($cau->loai_cau_hoi == 4) ? "TỰ LUẬN" : "ĐIỀN KHUYẾT";
                        
                        // Phát hiện ngôn ngữ môn học
                        $monHoc = $phien->monHoc ?? MonHoc::find($phien->mon_hoc_id);
                        $tenMon = mb_strtolower($monHoc->ten_mon_hoc ?? '');
                        $maMon = mb_strtolower($monHoc->ma_mon_hoc ?? '');
                        
                        $plainQuestion = strip_tags($cau->noi_dung);
                        $isEnglishQuestion = preg_match('/\b(write|essay|text message|email|letter|paragraph|story|words?|invite|tell|describe|explain|weekend|friend|should)\b/i', $plainQuestion);
                        $isEnglish = preg_match('/\b(anh|english|toeic|ielts|toefl|eng|sat)\b/i', $tenMon) ||
                                     preg_match('/\b(eng|en|el)\d*/i', $maMon) ||
                                     $isEnglishQuestion;
                        
                        $studentTextForAi = trim((string) $studentText) !== '' ? $studentText : '(Học sinh chưa trả lời)';
                        $aiResult = $this->gradeWithAI($cau->noi_dung, $referenceText, $studentTextForAi, $typeName, $isEnglish);
                        if ($aiResult) {
                            $isCorrect = $aiResult['is_correct'] ?? false;
                            $feedback = $aiResult['feedback'] ?? null;
                            $aiFeedback = is_array($feedback) ? json_encode($feedback, JSON_UNESCAPED_UNICODE) : $feedback;
                        }
                    } catch (\Exception $e) {
                        Log::error('AI Grading Error: ' . $e->getMessage());
                    }
                }
                // Chuyển selectedId sang JSON trước khi lưu vào mảng chi tiết
                $storageSelectedId = is_array($selectedId) ? json_encode($selectedId, JSON_UNESCAPED_UNICODE) : $selectedId;
            } else {
                $isCorrect = $selectedId !== null && $selectedId == $correctId;
            }
            
            if ($isCorrect) {
                $soCauDung++;
            }
            $chiTiet[$cau->id] = [
                'selected' => $storageSelectedId ?? $selectedId,
                'correct' => $correctId,
                'is_correct' => $isCorrect,
                'is_marked' => $request->input("marked.{$cau->id}") ?? false,
                'time' => $request->input("times.{$cau->id}") ?? 0,
                'ai_feedback' => $aiFeedback,
            ];
        }
        $total = count($cauHoiIds);
        $diemSo = $total > 0 ? round(($soCauDung / $total) * 10, 2) : 0;
        // Lấy thời gian thực từ JS timer, lưu thẳng vào cột DB
        $elapsedSeconds = (int) $request->input('elapsed_seconds', 0);
        $phien->update([
            'thoi_gian_ket_thuc'  => now(),
            'thoi_gian_phut'      => $elapsedSeconds > 0 ? $elapsedSeconds : null,
            'so_cau_dung'         => $soCauDung,
            'diem_so'             => $diemSo,
            'phan_tram_dung'      => $total > 0 ? round(($soCauDung / $total) * 100, 2) : 0,
            'trang_thai'          => 1,
            'ket_qua_chi_tiet'    => json_encode($chiTiet),
        ]);

        return redirect()->route('client.practice.result', ['session_id' => $phien->id]);
    }

    /**
     * Bước 5: Xem báo cáo kết quả
     */
    public function result($session_id)
    {
        //Lấy thông tin phiên cùng với thông tin môn học thông qua eager loading
        $phien = PhienLuyenTap::with('monHoc')->where('id', $session_id)->where('user_id', Auth::id())->firstOrFail();
        // Kiểm tra trạng thái bài làm
        if ($phien->trang_thai == 0) {
            return redirect()->route('client.practice.workspace', ['session_id' => $phien->id]);
        }
        $monHoc = $phien->monHoc;
        //Lấy dữ liệu câu hỏi và chi tiết kết quả
        $cauHoiIds = json_decode($phien->danh_sach_cau_hoi, true);
        $chiTiet = json_decode($phien->ket_qua_chi_tiet, true) ?? [];
        $cauHois = CauHoi::with('dapAns')->whereIn('id', $cauHoiIds)->get()->sortBy(fn($model) => array_search($model->id, $cauHoiIds))->values();
        $result = [
            'diem' => $phien->diem_so,
            'so_cau_dung' => $phien->so_cau_dung,
            'tong_so_cau' => count($cauHoiIds),
            'chi_tiet' => $chiTiet,
        ];
        return view('Client.practice.result', compact('monHoc', 'cauHois', 'result', 'phien'));
    }

    /**
     * Chấm điểm bằng AI (Sử dụng Groq) - Hỗ trợ Tự luận & Điền khuyết
     */
    private function gradeWithAI($questionText, $referenceText, $studentText, $typeName = "TỰ LUẬN", $isEnglish = false)
    {
        $apiKey = env('GROQ_API_KEY');
        $baseUri = env('GROQ_BASE_URI', 'https://api.groq.com/openai/v1/');
        $model = env('GROQ_DEFAULT_MODEL', 'llama-3.3-70b-versatile');
        $temperature = (float) env('GROQ_DEFAULT_TEMPERATURE', 0.1);

        if (!$apiKey) {
            return null;
        }

        // Loại bỏ HTML tags để gửi text thuần cho AI
        $questionText = strip_tags($questionText);
        $referenceText = strip_tags($referenceText);
        // Nếu studentText là JSON (điền khuyết), giữ nguyên để AI phân tích mảng
        
        if ($isEnglish) {
            $typeName = ($typeName == "TỰ LUẬN") ? "ESSAY" : "FILL IN THE BLANK";
            $prompt = "You are a professional English teacher for Vietnamese students. Your task is to grade the student's {$typeName} answer and explain it in Vietnamese.
Information:
1. QUESTION: {$questionText}
2. REFERENCE ANSWER: {$referenceText}
3. STUDENT'S ANSWER: {$studentText}

Grading Requirements:
- For FILL IN THE BLANK: Flexibly accept synonyms or similar spellings if the meaning is unchanged. All blanks must be correct to mark as true.
- For ESSAY: Mark as true if at least 60% of the main points are covered.

Feedback requirements:
- The feedback MUST be written in Vietnamese so students can read and understand it.
- Keep English words/sentences only when quoting the student's answer, correcting English grammar, or writing the sample answer.
- If the student's essay answer is blank, too short, or incomplete, still explain what is missing and write a complete sample answer that fits the question.
- For ESSAY, include a sample answer in English that matches the required word limit or expected length of the question.
- In [Bài mẫu tham khảo], write ONLY the sample answer content in English. Do NOT translate the sample answer into Vietnamese.
- For FILL IN THE BLANK, only include a sample answer when it helps the student understand the correct response.

Feedback structure MUST include these clear Vietnamese sections:
1. [Ưu điểm]: Những gì học sinh đã làm tốt.
2. [Lỗi sai & Hạn chế]: Lỗi chính tả, ngữ pháp, nội dung còn thiếu hoặc sai.
3. [Gợi ý cải thiện]: Cách sửa cụ thể để lần sau làm tốt hơn.
4. [Bài mẫu tham khảo]: Viết một câu trả lời mẫu phù hợp với đề bài. Với đề tiếng Anh, tiêu đề phần này giữ tiếng Việt nhưng nội dung bài mẫu phải viết hoàn toàn bằng tiếng Anh, không dịch sang tiếng Việt.

Return ONLY JSON (no markdown, no extra text):
{
    \"is_correct\": true/false,
    \"feedback\": \"Nhận xét bằng tiếng Việt, có đủ các phần yêu cầu\"
}";
        } else {
            $prompt = "Bạn là một giáo viên chuyên nghiệp và tận tâm. Nhiệm vụ của bạn là chấm điểm và đưa ra nhận xét CHI TIẾT cho bài làm {$typeName} của học sinh.
Dưới đây là thông tin bài làm:
1. CÂU HỎI: {$questionText}
2. ĐÁP ÁN THAM KHẢO: {$referenceText}
3. BÀI LÀM CỦA HỌC SINH: {$studentText}

Yêu cầu đánh giá và phản hồi:
- Với ĐIỀN KHUYẾT: Chấp nhận các từ đồng nghĩa, biến thể viết tắt hoặc lỗi chính tả nhỏ nếu không đổi nghĩa. Phải đúng tất cả các ô mới tính là true. Giải thích tại sao từ học sinh điền là phù hợp hoặc chưa phù hợp.
- Với TỰ LUẬN: Đúng ý chính ít nhất 60% thì tính là true. 
- Nếu bài tự luận của học sinh bỏ trống, quá ngắn hoặc chưa hoàn chỉnh, vẫn phải tạo một bài mẫu phù hợp với đề để học sinh học theo.

Cấu trúc nhận xét (feedback) phải viết bằng tiếng Việt và bao gồm 4 phần rõ rệt:
1. [Ưu điểm]: Những gì học sinh đã làm tốt (đúng ý, diễn đạt tốt, đúng ngữ pháp...).
2. [Lỗi sai & Hạn chế]: Chỉ ra cụ thể lỗi chính tả, ngữ pháp, hoặc các ý còn thiếu/sai lệch so với đáp án.
3. [Gợi ý cải thiện]: Cách để học sinh làm tốt hơn trong lần tới (ví dụ: bổ sung từ vựng, cấu trúc câu, hoặc tập trung vào ý chính nào).
4. [Bài mẫu tham khảo]: Viết một câu trả lời mẫu đầy đủ, đúng trọng tâm và phù hợp với yêu cầu đề bài. Nếu đề yêu cầu một ngôn ngữ cụ thể thì bài mẫu phải dùng đúng ngôn ngữ đó.

Phản hồi bắt buộc phải ĐÚNG định dạng JSON sau (không chứa markdown, không chứa chữ nào khác ngoài JSON):
{
    \"is_correct\": true/false,
    \"feedback\": \"Nhận xét chi tiết của bạn ở đây\"
}";
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($baseUri . 'chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            $content = $responseData['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);
            return $result;
        }

        Log::error('Groq API Error: ' . $response->body());
        return null;
    }
}
