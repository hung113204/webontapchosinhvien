<?php

namespace App\Jobs;

use App\Models\KetQuaThi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAiFeedback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ketQuaId;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct($ketQuaId)
    {
        $this->ketQuaId = $ketQuaId;
    }

    public function handle(): void
    {
        Log::info('=== BẮT ĐẦU JOB AI FEEDBACK & GRADING (GROQ) ===');
        Log::info('KetQua ID: ' . $this->ketQuaId);

        $ketQua = KetQuaThi::with(['baiKiemTra.monHoc', 'chiTietBaiLam.cauHoi.dapAns', 'chiTietBaiLam.cauHoi.chuongHoc'])->find($this->ketQuaId);
        if (!$ketQua) {
            Log::error('Không tìm thấy KetQuaThi với ID: ' . $this->ketQuaId);
            return;
        }

        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            Log::error('GROQ_API_KEY không được cấu hình trong .env');
            $ketQua->update(['trang_thai' => 1, 'ai_feedback' => 'Chưa cấu hình API Key Groq.']);
            return;
        }

        $url = 'https://api.groq.com/openai/v1/chat/completions';

        // --- PHÁT HIỆN NGÔN NGỮ CỦA MÔN HỌC ---
        $monHoc = $ketQua->baiKiemTra->monHoc;
        $tenMon = mb_strtolower($monHoc->ten_mon_hoc ?? '');
        $maMon = mb_strtolower($monHoc->ma_mon_hoc ?? '');
        
        // Kiểm tra xem tên hoặc mã môn có chứa các từ khóa tiếng Anh không
        $isEnglish = preg_match('/\b(anh|english|toeic|ielts|toefl|eng|sat)\b/i', $tenMon) ||
                     preg_match('/\b(eng|en|el)\d*/i', $maMon);
        
        $langPrompt = $isEnglish ? "English" : "Vietnamese (Tiếng Việt)";
        $teacherRole = $isEnglish ? "professional English teacher" : "giáo viên chuyên nghiệp và tận tâm";

        // --- BƯỚC 1: TỰ ĐỘNG CHẤM ĐIỂM CÁC CÂU TỰ LUẬN ---
        $details = $ketQua->chiTietBaiLam;
        $hasEssay = false;

        foreach ($details as $chiTiet) {
            $cauHoi = $chiTiet->cauHoi;
            $type = $cauHoi->loai_cau_hoi;
            $studentAnswer = $chiTiet->cau_tra_loi_tu_luan;
            $plainQuestion = strip_tags($cauHoi->noi_dung);
            $isEnglishForQuestion = $isEnglish || preg_match('/\b(write|essay|text message|email|letter|paragraph|story|words?|invite|tell|describe|explain|weekend|friend|should)\b/i', $plainQuestion);

            if (($type == 4 || $type == 3) && ($type == 4 || !empty($studentAnswer))) {
                $hasEssay = true; // Đánh dấu là có câu cần AI chấm lại
                $studentAnswerForAi = trim((string) $studentAnswer) !== '' ? $studentAnswer : '(Học sinh chưa trả lời)';
                
                if ($type == 4) {
                    $referenceAnswer = $cauHoi->dapAns->first()->noi_dung ?? 'Không có đáp án tham khảo';
                    $typeName = $isEnglishForQuestion ? "ESSAY" : "TỰ LUẬN";
                } else {
                    $referenceAnswer = $cauHoi->dapAns->where('is_dung', 1)->pluck('noi_dung')->implode(', ');
                    $typeName = $isEnglishForQuestion ? "FILL IN THE BLANK" : "ĐIỀN KHUYẾT";
                }

                Log::info("Đang chấm điểm câu {$typeName} ID: {$cauHoi->id}");

                if ($isEnglishForQuestion) {
                    $gradingPrompt = "You are a professional English teacher for Vietnamese students. Please grade the student's {$typeName} answer and explain it in Vietnamese.
QUESTION: " . strip_tags($cauHoi->noi_dung) . "
REFERENCE ANSWER: " . strip_tags($referenceAnswer) . "
STUDENT'S ANSWER: " . strip_tags($studentAnswerForAi) . "

Requirements for {$typeName}:
1. Determine if the answer is correct or incorrect (is_correct: true/false).
2. For FILL IN THE BLANK, flexibly accept synonyms or similar spellings if the meaning is unchanged. Only mark as true if all blanks are reasonably filled.
3. For ESSAY, mark as true if at least 60% of the main points are covered.
4. Feedback MUST be in Vietnamese and include 4 distinct parts: [Ưu điểm], [Lỗi sai & Hạn chế], [Gợi ý cải thiện], [Bài mẫu tham khảo].
5. If the essay answer is blank, too short, or incomplete, still write a complete sample answer that fits the question.
6. For English essay questions, the sample answer in [Bài mẫu tham khảo] MUST be written in English and match the required word limit or expected length.
7. In [Bài mẫu tham khảo], write ONLY the sample answer content in English. Do NOT translate the sample answer into Vietnamese.

Return JSON: {\"is_correct\": boolean, \"feedback\": \"Nhận xét bằng tiếng Việt, có đủ các phần yêu cầu\"}";
                } else {
                    $gradingPrompt = "Bạn là một giáo viên chuyên nghiệp và tận tâm. Hãy chấm điểm và đưa ra nhận xét CHI TIẾT cho bài làm {$typeName} của học sinh.
CÂU HỎI: " . strip_tags($cauHoi->noi_dung) . "
ĐÁP ÁN THAM KHẢO: " . strip_tags($referenceAnswer) . "
BÀI LÀM CỦA HỌC SINH: " . strip_tags($studentAnswerForAi) . "

Yêu cầu đối với {$typeName}:
1. Xác định bài làm đúng hay sai (is_correct: true/false). 
2. Với ĐIỀN KHUYẾT, hãy linh hoạt chấp nhận các từ đồng nghĩa hoặc cách viết tương tự nếu ý nghĩa không đổi. Chỉ tính là true nếu tất cả các ô trống đều được điền hợp lý.
3. Với TỰ LUẬN, tính là true nếu đạt ít nhất 60% ý chính.
4. Nếu bài tự luận bỏ trống, quá ngắn hoặc chưa hoàn chỉnh, vẫn phải tạo một bài mẫu phù hợp với đề để học sinh học theo.
5. Nhận xét (feedback) phải viết bằng tiếng Việt và bao gồm 4 phần rõ rệt: [Ưu điểm], [Lỗi sai & Hạn chế], [Gợi ý cải thiện], [Bài mẫu tham khảo].
6. [Bài mẫu tham khảo] phải là một câu trả lời mẫu đầy đủ, đúng trọng tâm và phù hợp với yêu cầu đề bài. Nếu đề yêu cầu một ngôn ngữ cụ thể thì bài mẫu phải dùng đúng ngôn ngữ đó.

Trả về JSON: {\"is_correct\": boolean, \"feedback\": \"Chuỗi nhận xét chi tiết của bạn\"}";
                }

                try {
                    $response = Http::timeout(30)->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])->post($url, [
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => [['role' => 'user', 'content' => $gradingPrompt]],
                        'response_format' => ['type' => 'json_object'],
                        'temperature' => 0.2,
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        $content = json_decode($result['choices'][0]['message']['content'], true);
                        
                        $isCorrect = $content['is_correct'] ?? false;
                        $feedback = $content['feedback'] ?? '';
                        
                        // Nếu AI trả về mảng, chuyển thành chuỗi để không gây lỗi htmlspecialchars
                        if (is_array($feedback)) {
                            $feedback = json_encode($feedback, JSON_UNESCAPED_UNICODE);
                        }

                        // Lấy điểm câu hỏi từ bảng pivot
                        $diemCau = \DB::table('bai_kiem_tra_cau_hoi')
                            ->where('bai_kiem_tra_id', $ketQua->bai_kiem_tra_id)
                            ->where('cau_hoi_id', $cauHoi->id)
                            ->value('diem') ?? 0;

                        $chiTiet->update([
                            'is_correct' => $isCorrect,
                            'phan_tich_sai' => $feedback,
                            'diem' => $isCorrect ? $diemCau : 0
                        ]);
                        
                        Log::info("Câu {$cauHoi->id} verdict: " . ($isCorrect ? 'ĐÚNG' : 'SAI'));
                    }
                } catch (\Exception $e) {
                    Log::error("Lỗi khi chấm câu {$cauHoi->id}: " . $e->getMessage());
                }
            }
        }

        // Nếu có chấm lại tự luận, tính lại tổng điểm của bài thi
        if ($hasEssay) {
            $newSoCauDung = $ketQua->chiTietBaiLam()->where('is_correct', true)->count();
            $newTongDiem = $ketQua->chiTietBaiLam()->sum('diem');
            
            // Quy đổi điểm theo cách tính điểm (thang 100 nếu cần)
            $cachTinhDiem = $ketQua->baiKiemTra->cach_tinh_diem ?? 1;
            if ($cachTinhDiem == 2) {
                $newTongDiem = $newTongDiem * 10;
            }

            $ketQua->update([
                'so_cau_dung' => $newSoCauDung,
                'diem' => round($newTongDiem, 2)
            ]);
            
            Log::info("Đã cập nhật lại tổng điểm bài thi: {$ketQua->diem}/10");
        }

        // --- BƯỚC 2: TẠO NHẬN XÉT TỔNG QUÁT (LOGIC CŨ) ---
        $soCauDung = $ketQua->so_cau_dung;
        $tongSoCau = $ketQua->tong_so_cau;
        $phanTramDung = $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 100, 2) : 0;

        // Lấy danh sách các chương học sinh làm sai
        $chuongSai = [];
        foreach ($ketQua->chiTietBaiLam as $chiTiet) {
            // Câu tự luận (loại 4) chưa chấm tự động cũng sẽ lấy từ chiTietBaiLam
            if (!$chiTiet->is_correct && $chiTiet->cauHoi && $chiTiet->cauHoi->chuongHoc) {
                $tenChuong = trim($chiTiet->cauHoi->chuongHoc->ten_chuong);
                if (!empty($tenChuong) && !in_array($tenChuong, $chuongSai)) {
                    $chuongSai[] = $tenChuong;
                }
            }
        }
        $chuongSaiText = count($chuongSai) > 0 ? implode(', ', $chuongSai) : 'Không có';

        if ($isEnglish) {
            $prompt = "You are an English language instructor. Write a concise feedback in English (150-200 words) for the student:
- Score: {$ketQua->diem}/10
- Correct: {$soCauDung}/{$tongSoCau} ({$phanTramDung}%)
- Time spent: {$ketQua->tong_thoi_gian_lam}
- Tab switching violations: {$ketQua->so_lan_vi_pham_tab} times
- Chapters with incorrect answers to review: {$chuongSaiText}

Requirements: Evaluate the result, attitude, provide encouragement, suggest improvements (specifically advising to review the mentioned chapters if any), and warn about tab violations if any. Tone: Warm and encouraging.";
            $systemRole = "You are a professional English teacher. Write in English.";
        } else {
            $prompt = "Bạn là giảng viên IT. Viết nhận xét ngắn gọn bằng tiếng Việt (150-200 từ) cho sinh viên:
- Điểm: {$ketQua->diem}/10
- Đúng: {$soCauDung}/{$tongSoCau} ({$phanTramDung}%)
- Thời gian: {$ketQua->tong_thoi_gian_lam}
- Vi phạm tab: {$ketQua->so_lan_vi_pham_tab} lần
- Các chương làm sai cần ôn lại: {$chuongSaiText}

Yêu cầu: Đánh giá kết quả, thái độ, động viên, khuyên cải thiện (nhắc cụ thể việc ôn lại các chương làm sai), nhắc nhở nếu vi phạm tab. Giọng ấm áp, khích lệ.";
            $systemRole = "Bạn là giảng viên IT, viết bằng tiếng Việt.";
        }

        // Trang kết quả cần nhận xét tổng quát bằng tiếng Việt cho mọi môn học.
        $subjectName = $ketQua->baiKiemTra->monHoc->ten_mon_hoc ?? 'môn học';
        $prompt = "Bạn là cố vấn học tập thân thiện, thực tế và nói tiếng Việt. Viết nhận xét ngắn gọn bằng tiếng Việt (120-180 từ) cho sinh viên sau khi làm bài thi:
- Môn học: {$subjectName}
- Điểm: {$ketQua->diem}/10
- Đúng: {$soCauDung}/{$tongSoCau} ({$phanTramDung}%)
- Thời gian: {$ketQua->tong_thoi_gian_lam}
- Vi phạm tab: {$ketQua->so_lan_vi_pham_tab} lần
- Các phần kiến thức/chương làm sai cần ôn lại: {$chuongSaiText}

Yêu cầu bắt buộc:
1. Toàn bộ nhận xét phải bằng tiếng Việt, không dùng tiếng Anh.
2. Mở đầu bằng đánh giá kết quả học tập dựa trên điểm và số câu đúng.
3. Nêu rõ điểm mạnh và phần cần cải thiện. Nếu có chương làm sai, phải chỉ đích danh các chương đó và khuyên sinh viên ôn tập lại nội dung của những chương này.
4. Đưa ra 2-3 gợi ý học tiếp thật cụ thể.
5. Nếu có vi phạm tab, nhắc nhở nghiêm túc nhưng lịch sự.
6. Không dùng markdown, không dùng tiêu đề dài, không liệt kê quá máy móc.";
        $systemRole = "Bạn là cố vấn học tập. Luôn trả lời bằng tiếng Việt tự nhiên, rõ ràng và phù hợp với học sinh/sinh viên Việt Nam.";

        try {
            $response = Http::timeout(60)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'system', 'content' => $systemRole], ['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.7,
                'max_tokens' => 800,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['choices'][0]['message']['content'] ?? null;

                if ($aiText) {
                    $ketQua->update([
                        'ai_feedback' => trim($aiText),
                        'trang_thai' => 1,
                    ]);
                    Log::info('Cập nhật AI feedback tổng quát thành công!');
                }
            } else {
                throw new \Exception('API trả về lỗi ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi Groq: ' . $e->getMessage());
            $ketQua->update([
                'trang_thai' => 1,
                'ai_feedback' => 'Điểm của bạn: ' . $ketQua->diem . '/10. AI đã hoàn tất chấm điểm, nhưng gặp lỗi khi tạo nhận xét tổng quát.',
            ]);
        }

        Log::info('=== KẾT THÚC JOB AI FEEDBACK & GRADING ===');
    }
}
