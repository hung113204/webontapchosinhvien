<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('permission:module,action,model')
     * 
     * Example:
     *   ->middleware('permission:exam,update,exam_id')
     *   ->middleware('permission:question,delete')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $module, $action = 'view', $modelIdParam = null)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // ✅ Admin/Teacher có thể truy cập backend
        if (!$user || !$user->isAdmin()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền!'], 403);
            }
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // ✅ Kiểm tra xem user có permission cho module + action
        if (!$user->hasPermission($module, $action)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này!'], 403);
            }
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // ✅ KIỂM TRA OWNERSHIP: Nếu là Teacher và có modelIdParam
        // → Kiểm tra xem dữ liệu đó có phải của Teacher này không
        if ($user->isTeacher() && $modelIdParam) {
            $this->checkResourceOwnership($request, $user, $module, $modelIdParam);
        }

        return $next($request);
    }

    /**
     * Kiểm tra xem Teacher có quyền truy cập resource này không
     * Resource phải do Teacher tạo ra
     */
    private function checkResourceOwnership(Request $request, $user, $module, $modelIdParam)
    {
        // Lấy ID từ route parameter (e.g., /admin/exams/5 → id=5)
        $resourceId = $request->route($modelIdParam);

        if (!$resourceId) {
            return; // Không có ID trong route, skip check
        }

        $isOwner = false;

        // Kiểm tra từng module
        if ($module === 'exam') {
            $exam = \App\Models\BaiKiemTra::find($resourceId);
            $isOwner = $exam && $exam->nguoi_tao_id === $user->id;
        } elseif ($module === 'question') {
            $question = \App\Models\CauHoi::find($resourceId);
            $isOwner = $question && $question->nguoi_tao_id === $user->id;
        }

        // Nếu không phải chủ → từ chối
        if (!$isOwner) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có thể quản lý dữ liệu của mình!',
                ], 403);
            }
            abort(403, 'Bạn chỉ có thể quản lý dữ liệu của mình!');
        }
    }
}