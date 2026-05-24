<?php

namespace App\Http\Traits;

/**
 * @method bool isSuperAdmin()
 * @method bool isTeacher()
 * @method bool hasPermission(string $module, string $action)
 */
trait PermissionTrait
{
    /**
     * Kiểm tra quyền + ownership
     * Nếu không có quyền → abort(403)
     *
     * ✅ ĐỔI TÊN: authorize() → checkPermission()
     * Tránh xung đột với method authorize() có sẵn trong Laravel Controller
     *
     * @param string $module   - Module slug (bai-kiem-tra, cau-hoi, ket-qua...)
     * @param string $action   - Action (view, create, update, delete)
     * @param mixed  $resource - (Optional) Resource để kiểm tra ownership
     */
    public function checkPermission($module, $action = 'view', $resource = null)
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Chưa đăng nhập.');
        }

        // SuperAdmin (vai_tro_id = 1) bypass tất cả
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Kiểm tra user có permission cho module + action
        if (!$user->hasPermission($module, $action)) {
            abort(403, "Bạn không có quyền {$action} {$module}.");
        }

        // Teacher chỉ được quản lý dữ liệu của mình
        if ($user->isTeacher() && $resource) {
            $this->checkOwnership($user, $resource);
        }

        return true;
    }

    /**
     * Kiểm tra xem resource có phải của user hiện tại không
     */
    private function checkOwnership($user, $resource)
    {
        $ownershipFields = ['nguoi_tao_id', 'user_id', 'owner_id', 'created_by'];

        foreach ($ownershipFields as $field) {
            if (isset($resource->$field)) {
                if ($resource->$field !== $user->id) {
                    abort(403, 'Bạn chỉ có thể quản lý dữ liệu của mình.');
                }
                return true;
            }
        }

        return true;
    }

    /**
     * Kiểm tra quyền mà không abort — trả về true/false
     */
    public function can($module, $action = 'view', $resource = null): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->hasPermission($module, $action)) {
            return false;
        }

        if ($user->isTeacher() && $resource) {
            $ownershipFields = ['nguoi_tao_id', 'user_id', 'owner_id', 'created_by'];
            foreach ($ownershipFields as $field) {
                if (isset($resource->$field)) {
                    return $resource->$field === $user->id;
                }
            }
        }

        return true;
    }
}