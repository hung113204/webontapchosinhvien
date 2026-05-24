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
                ->with('monHoc')
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
        ]);

        $userPrompt = $request->message;
        $cauHoiId = $request->cau_hoi_id;
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

        // Xây dựng ngữ cảnh cho AI
        $context = 'Bạn là trợ giảng thông minh của hệ thống IT Study Support. ';
        $context .= 'Quy tắc: Trả lời trực tiếp, ngắn gọn, đi thẳng vào vấn đề người dùng hỏi. ';
        $context .= 'Không tự ý suy diễn hoặc so sánh với các công nghệ khác nếu không được yêu cầu. ';
        $context .= 'Chỉ tập trung phân tích các phương án này, không mở rộng ra ngoài nội dung câu hỏi.';
        if ($cauHoiId) {
            $quiz = CauHoi::with('dapAns')->find($cauHoiId);
            $context .= "Giải thích câu hỏi trắc nghiệm sau: '" . $quiz->noi_dung . "'. ";
            $context .= 'Các lựa chọn: ';
            foreach ($quiz->dapAns as $index => $ans) {
                $context .= chr(65 + $index) . '. ' . $ans->noi_dung . ($ans->is_correct ? ' (Đáp án đúng)' : '') . '; ';
            }
            $context .= 'Hãy phân tích tại sao đáp án đúng lại chính xác và chỉ ra lỗi sai của các phương án còn lại.';
        } else {
            $context .= 'Hãy hỗ trợ sinh viên giải đáp thắc mắc về lĩnh vực Công nghệ thông tin.';
        }

        try {
            // Gọi API Groq
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'system', 'content' => $context], ['role' => 'user', 'content' => $userPrompt]],
                'temperature' => 0.2,
                'max_tokens' => 1000,
            ]);

            if ($response->failed()) {
                return response()->json(['error' => 'AI đang bận, thử lại sau nhé!'], 500);
            }

            $data = $response->json();
            $aiAnswer = $data['choices'][0]['message']['content'] ?? 'Không tìm thấy câu trả lời.';

            // Lưu lịch sử (Bỏ phần streak)
            $conversationId = null;

            if (auth()->check()) {
                $chat = LichSuHoiDapAi::create([
                    'user_id' => auth()->id(),
                    'session_key' => $sessionKey,
                    'chat_title' => $existingTitle,
                    'mon_hoc_id' => $request->mon_hoc_id,
                    'cau_hoi_id' => $cauHoiId,
                    'cau_hoi' => $userPrompt,
                    'cau_tra_loi' => $aiAnswer,
                    'model_ai' => 'llama-3.3-70b-versatile',
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
