<?php

namespace App\Http\Controllers\Client;

use App\Models\LichSuHoiDapAi;
use App\Models\CauHoi;
use App\Models\MonHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiAssistantController extends FrontendController
{
    public function index()
    {
        $history = collect();

        if (auth()->check()) {
            $historyRows = LichSuHoiDapAi::where('user_id', auth()->id())
                ->latest()
                ->take(200)
                ->get();

            $history = $historyRows
                ->groupBy(fn ($item) => $item->session_key ?: 'legacy-' . $item->id)
                ->map(function ($messages, $sessionKey) {
                    $firstMessage = $messages->sortBy('created_at')->first();
                    $latestMessage = $messages->sortByDesc('created_at')->first();

                    return (object) [
                        'id' => $sessionKey,
                        'cau_hoi' => $latestMessage->chat_title ?: $firstMessage->cau_hoi,
                        'created_at' => $latestMessage->created_at,
                    ];
                })
                ->sortByDesc('created_at')
                ->take(30)
                ->values();
        }

        $monHocs = MonHoc::orderBy('ten_mon_hoc')->get(['id', 'ten_mon_hoc']);

        return view('Client.AI.index', compact('history', 'monHocs'));
    }

    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'cau_hoi_id' => 'nullable|integer|exists:cau_hoi,id',
            'mon_hoc_id' => 'nullable|integer|exists:mon_hoc,id',
            'chat_session_id' => 'nullable|string|max:80',
            'ai_mode' => 'nullable|string|in:academic,counseling',
        ]);

        $userPrompt = $request->message;
        $cauHoiId = $request->cau_hoi_id;
        $aiMode = $request->ai_mode ?? 'academic';
        $sessionKey = Str::isUuid((string) $request->chat_session_id)
            ? $request->chat_session_id
            : (string) Str::uuid();
        $existingTitle = null;

        if (auth()->check() && $request->chat_session_id && Str::isUuid((string) $request->chat_session_id)) {
            $existingTitle = LichSuHoiDapAi::where('user_id', auth()->id())
                ->where('session_key', $sessionKey)
                ->whereNotNull('chat_title')
                ->latest()
                ->value('chat_title');
        }

        // Xây dựng ngữ cảnh cho AI dựa trên chế độ chat (ai_mode)
        if ($aiMode === 'counseling') {
            $context = 'Bạn là một Chuyên gia Tư vấn Học tập và Định hướng Nghề nghiệp Công nghệ Thông tin (IT Career & Study Advisor) hàng đầu. ';
            $context .= 'Nhiệm vụ của bạn là tư vấn cho sinh viên/người dùng về lộ trình học tập hiệu quả, phương pháp ôn thi, kinh nghiệm thực tế, định hướng chuyên sâu (Web, App, AI, IoT, Cloud, DevOps, Security, Data...), ';
            $context .= 'gợi ý các kỹ năng/công nghệ cần trang bị, tài liệu tham khảo chất lượng, cách làm Portfolio, viết CV và chuẩn bị phỏng vấn xin việc ngành IT. ';
            $context .= 'Quy tắc: Luôn giữ thái độ thân thiện, khích lệ, truyền cảm hứng tích cực. Trả lời trực tiếp, có cấu trúc rõ ràng, sử dụng Markdown (in đậm từ khóa, chia đề mục cụ thể, dùng danh sách/bullets) giúp sinh viên dễ theo dõi. ';
            $context .= 'Nếu câu hỏi nằm ngoài phạm vi tư vấn học tập hoặc định hướng CNTT, hãy lịch sự nhắc nhở người dùng quay lại chủ đề định hướng và tư vấn ngành IT.';
        } else {
            $context = 'Bạn là một gia sư/trợ giảng tận tâm và cực kỳ thông minh của hệ thống IT Study Support. ';
            $context .= 'Quy tắc: Luôn giữ thái độ thân thiện, khích lệ. Trả lời trực tiếp, rõ ràng, phân tích sâu sắc nhưng dễ hiểu. ';
            $context .= 'Sử dụng Markdown để định dạng câu trả lời (in đậm các từ khóa quan trọng, dùng danh sách list, chia đoạn rõ ràng) giúp sinh viên dễ đọc. ';
            $context .= 'TUYỆT ĐỐI QUAN TRỌNG: Phải đảm bảo kiến thức chuyên ngành CNTT chính xác tuyệt đối. '
              . 'Phân loại ngôn ngữ lập trình cần chính xác: '
              . '(1) Ngôn ngữ C là ngôn ngữ bậc TRUNG (middle-level language) — không phải bậc thấp và không phải bậc cao. C có thể thao tác trực tiếp với bộ nhớ (pointer, địa chỉ) nhưng vẫn có cú pháp cấu trúc rõ ràng, khác với Assembly là ngôn ngữ bậc thấp thực sự. '
              . '(2) C++ là ngôn ngữ bậc trung/cao (middle to high-level), hỗ trợ OOP, KHÔNG phải bậc thấp. '
              . '(3) Assembly và mã máy (machine code) mới là ngôn ngữ bậc thấp thực sự. '
              . 'Nếu người dùng hỏi ngắn gọn mập mờ chỉ nhập "C" hoặc "c" mà không rõ ngữ cảnh là hỏi về khái niệm hay ngôn ngữ cụ thể nào, hãy trả lời về ngôn ngữ lập trình C trước, sau đó hỏi thêm nếu cần làm rõ. ';
            
            // Quy tắc xử lý câu hỏi trắc nghiệm
            $context .= 'QUY TẮC CHO CÂU HỎI TRẮC NGHIỆM (A, B, C, D): Nếu người dùng hỏi một câu hỏi dạng trắc nghiệm lựa chọn đáp án (có các phương án A, B, C, D hoặc tương tự): '
              . 'Bạn phải luôn phân tích và đưa ra rõ ràng MỘT ĐÁP ÁN ĐÚNG NHẤT VÀ DUY NHẤT (ví dụ: "Đáp án đúng là D" hoặc "Chọn đáp án D. thời gian") ở ngay đầu hoặc cuối câu trả lời của bạn một cách dứt khoát, không được đưa ra câu trả lời mập mờ hoặc chọn nhiều đáp án cùng lúc. '
              . 'Nếu câu hỏi có sự chồng chéo hoặc tranh cãi lý thuyết giữa các phương án (ví dụ như đánh giá hiệu quả thuật toán dựa trên cả Thời gian và Bộ nhớ), hãy dựa trên chương trình học/giáo trình chuẩn CNTT phổ thông (như Tin học 10) để chọn ra phương án tiêu chuẩn và quan trọng nhất (như Thời gian thực hiện), giải thích rõ lý do tại sao phương án đó là đáp án chính xác nhất được chọn, và giải thích vì sao các phương án khác chưa phải là tối ưu hoặc chỉ là yếu tố phụ.';

            // Ngữ cảnh môn học nếu có
            if ($request->mon_hoc_id) {
                $monHoc = MonHoc::find($request->mon_hoc_id);
                if ($monHoc) {
                    $context .= "Người dùng đang học môn '" . $monHoc->ten_mon_hoc . "'. Hãy ưu tiên sử dụng kiến thức và ví dụ liên quan đến môn học này. ";
                }
            }
        }

        if ($cauHoiId) {
            $context .= 'Chỉ tập trung phân tích các phương án này, không mở rộng ra ngoài nội dung câu hỏi. ';
            $quiz = CauHoi::with('dapAns')->find($cauHoiId);
            $context .= "Giải thích câu hỏi trắc nghiệm sau: '" . $quiz->noi_dung . "'. ";
            $context .= 'Các lựa chọn: ';
            foreach ($quiz->dapAns as $index => $ans) {
                $context .= chr(65 + $index) . '. ' . $ans->noi_dung . ($ans->is_correct ? ' (Đáp án đúng)' : '') . '; ';
            }
            $context .= 'Hãy phân tích chi tiết tại sao đáp án đúng lại chính xác, giải thích cơ chế/nguyên lý đằng sau, và chỉ ra lỗi sai của các phương án còn lại.';
        } elseif ($aiMode !== 'counseling') {
            $context .= 'Hãy hỗ trợ sinh viên giải đáp thắc mắc về lĩnh vực Công nghệ thông tin một cách nhiệt tình nhất.';
        }

        try {
            // Xây dựng chuỗi tin nhắn gửi lên AI (Bao gồm System, History, và Current User Prompt)
            $messages = [];
            $messages[] = ['role' => 'system', 'content' => $context];

            // Lấy lịch sử chat của session này nếu có (Bộ nhớ hội thoại)
            if (auth()->check() && $request->chat_session_id && Str::isUuid((string) $request->chat_session_id)) {
                $historyChats = LichSuHoiDapAi::where('user_id', auth()->id())
                    ->where('session_key', $sessionKey)
                    ->oldest() // Lấy từ cũ đến mới
                    ->take(10) // Tối đa 10 tin nhắn gần nhất để làm ngữ cảnh
                    ->get();
                    
                foreach ($historyChats as $chat) {
                    $messages[] = ['role' => 'user', 'content' => $chat->cau_hoi];
                    $messages[] = ['role' => 'assistant', 'content' => $chat->cau_tra_loi];
                }
            }
            
            // Thêm câu hỏi hiện tại
            $messages[] = ['role' => 'user', 'content' => $userPrompt];

            // Gọi API Groq
            $model = env('GROQ_CHAT_MODEL', 'llama-3.3-70b-versatile');
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2, // Giảm sáng tạo để tăng độ chính xác của sự thật
                'max_tokens' => 1500, // Cho phép trả lời dài hơn một chút
            ]);

            // Nếu gặp lỗi Rate Limit (429) hoặc lỗi bất kỳ, tự động fallback sang llama-3.1-8b-instant để không bị gián đoạn dịch vụ
            if ($response->failed()) {
                $errData = $response->json();
                $errCode = $errData['error']['code'] ?? '';
                
                if ($response->status() == 429 || $errCode === 'rate_limit_exceeded') {
                    $fallbackModel = 'llama-3.1-8b-instant';
                    $response = Http::withoutVerifying()->withHeaders([
                        'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                        'Content-Type' => 'application/json',
                    ])->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => $fallbackModel,
                        'messages' => $messages,
                        'temperature' => 0.2,
                        'max_tokens' => 1500,
                    ]);

                    if ($response->successful()) {
                        $model = $fallbackModel;
                    }
                }
            }

            if ($response->failed()) {
                \Log::error('Groq API Error after fallback: ' . $response->body());
                return response()->json(['error' => 'AI đang bận hoặc hết hạn ngạch hôm nay, thử lại sau nhé!'], 500);
            }

            $data = $response->json();
            $aiAnswer = $data['choices'][0]['message']['content'] ?? 'Không tìm thấy câu trả lời.';

            // Lưu lịch sử
            $conversationId = null;

            if (auth()->check()) {
                $chat = LichSuHoiDapAi::create([
                    'user_id' => auth()->id(),
                    'session_key' => $sessionKey,
                    'chat_title' => $existingTitle,
                    'cau_hoi' => $userPrompt,
                    'cau_tra_loi' => $aiAnswer,
                    'model_ai' => $model,
                ]);

                $conversationId = $chat->id;
            }

            return response()->json([
                'success' => true,
                'answer' => $aiAnswer,
                'conversation_id' => $conversationId,
                'chat_session_id' => $sessionKey,
                'created_at' => isset($chat) ? $chat->created_at->toIso8601String() : now()->toIso8601String(),
                'answered_at' => now()->toIso8601String(),
                'requires_login_for_history' => !auth()->check(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi kết nối: ' . $e->getMessage()], 500);
        }
    }
    
    public function show($id)
    {
        abort_unless(auth()->check(), 403);

        $query = LichSuHoiDapAi::where('user_id', auth()->id());

        if (str_starts_with($id, 'legacy-')) {
            $legacyId = (int) str_replace('legacy-', '', $id);
            $messages = $query->where('id', $legacyId)->oldest()->get();
        } else {
            abort_unless(Str::isUuid($id), 404);
            $messages = $query->where('session_key', $id)->oldest()->get();
        }

        abort_if($messages->isEmpty(), 404);

        return response()->json([
            'title' => $messages->last()->chat_title ?: $messages->first()->cau_hoi,
            'messages' => $messages->map(fn ($chat) => [
                'cau_hoi' => $chat->cau_hoi,
                'cau_tra_loi' => $chat->cau_tra_loi,
                'created_at' => $chat->created_at->toIso8601String(),
                'answered_at' => $chat->updated_at->toIso8601String(),
            ])->values(),
        ]);
    }

    public function rename(Request $request, $id)
    {
        abort_unless(auth()->check(), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:160',
        ]);

        $title = trim($validated['title']);
        abort_if($title === '', 422);

        $query = LichSuHoiDapAi::where('user_id', auth()->id());

        if (str_starts_with($id, 'legacy-')) {
            $legacyId = (int) str_replace('legacy-', '', $id);
            $exists = (clone $query)->where('id', $legacyId)->exists();
            abort_unless($exists, 404);

            (clone $query)->where('id', $legacyId)->update(['chat_title' => $title]);
        } else {
            abort_unless(Str::isUuid($id), 404);
            $exists = (clone $query)->where('session_key', $id)->exists();
            abort_unless($exists, 404);

            (clone $query)->where('session_key', $id)->update(['chat_title' => $title]);
        }

        return response()->json([
            'success' => true,
            'title' => $title,
        ]);
    }

    // Xóa cuộc trò chuyện (Thùng rác)
    public function destroy($id)
    {
        abort_unless(auth()->check(), 403);

        $query = LichSuHoiDapAi::where('user_id', auth()->id());

        if (str_starts_with($id, 'legacy-')) {
            $legacyId = (int) str_replace('legacy-', '', $id);
            $query->where('id', $legacyId)->delete();
        } else {
            // Chỉ xóa nếu session_key không trống
            if (!empty($id)) {
                $query->where('session_key', $id)->delete();
            } else {
                return response()->json(['success' => false, 'error' => 'ID không hợp lệ.'], 400);
            }
        }

        return response()->json(['success' => true]);
    }
}
