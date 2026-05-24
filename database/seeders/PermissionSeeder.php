<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nếu đã tồn tại thì skip
        if (Permission::count() > 0) {
            echo "⚠️  Permissions đã tồn tại, bỏ qua seed.\n";
            return;
        }

        $permissions = [
            // 🎓 Quản lý bài kiểm tra
            [
                'name' => 'Xem bài kiểm tra',
                'slug' => 'exam.view',
                'module' => 'Exam',
                'description' => 'Xem danh sách bài kiểm tra',
                'is_system' => true,
            ],
            [
                'name' => 'Tạo bài kiểm tra',
                'slug' => 'exam.create',
                'module' => 'Exam',
                'description' => 'Tạo mới bài kiểm tra',
                'is_system' => true,
            ],
            [
                'name' => 'Chỉnh sửa bài kiểm tra',
                'slug' => 'exam.update',
                'module' => 'Exam',
                'description' => 'Chỉnh sửa bài kiểm tra',
                'is_system' => true,
            ],
            [
                'name' => 'Xóa bài kiểm tra',
                'slug' => 'exam.delete',
                'module' => 'Exam',
                'description' => 'Xóa bài kiểm tra',
                'is_system' => true,
            ],

            // ❓ Quản lý câu hỏi
            [
                'name' => 'Xem câu hỏi',
                'slug' => 'question.view',
                'module' => 'Question',
                'description' => 'Xem danh sách câu hỏi',
                'is_system' => true,
            ],
            [
                'name' => 'Tạo câu hỏi',
                'slug' => 'question.create',
                'module' => 'Question',
                'description' => 'Tạo mới câu hỏi',
                'is_system' => true,
            ],
            [
                'name' => 'Chỉnh sửa câu hỏi',
                'slug' => 'question.update',
                'module' => 'Question',
                'description' => 'Chỉnh sửa câu hỏi',
                'is_system' => true,
            ],
            [
                'name' => 'Xóa câu hỏi',
                'slug' => 'question.delete',
                'module' => 'Question',
                'description' => 'Xóa câu hỏi',
                'is_system' => true,
            ],

            // 📊 Xem kết quả
            [
                'name' => 'Xem kết quả thi',
                'slug' => 'result.view',
                'module' => 'Result',
                'description' => 'Xem kết quả thi của học sinh',
                'is_system' => true,
            ],

            // 📚 Quản lý môn học (chỉ Admin)
            [
                'name' => 'Xem môn học',
                'slug' => 'subject.view',
                'module' => 'Subject',
                'description' => 'Xem danh sách môn học',
                'is_system' => true,
            ],
            [
                'name' => 'Quản lý môn học',
                'slug' => 'subject.manage',
                'module' => 'Subject',
                'description' => 'Tạo, sửa, xóa môn học',
                'is_system' => true,
            ],

            // 👥 Quản lý người dùng (chỉ Admin)
            [
                'name' => 'Xem người dùng',
                'slug' => 'user.view',
                'module' => 'User',
                'description' => 'Xem danh sách người dùng',
                'is_system' => true,
            ],
            [
                'name' => 'Quản lý người dùng',
                'slug' => 'user.manage',
                'module' => 'User',
                'description' => 'Tạo, sửa, xóa người dùng',
                'is_system' => true,
            ],

            // 🔐 Quản lý quyền (chỉ Super Admin)
            [
                'name' => 'Quản lý quyền',
                'slug' => 'permission.manage',
                'module' => 'Permission',
                'description' => 'Cấp quyền cho vai trò',
                'is_system' => true,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        echo "✅ Đã tạo " . count($permissions) . " permissions.\n";

        // Cấp quyền cho Teacher role
        $teacherRole = Role::find(2);
        if ($teacherRole) {
            $teacherPermissions = Permission::whereIn('slug', [
                'exam.view',
                'exam.create',
                'exam.update',
                'exam.delete',
                'question.view',
                'question.create',
                'question.update',
                'question.delete',
                'result.view',
                'subject.view',
            ])->get();

            foreach ($teacherPermissions as $perm) {
                // can_view, can_create, can_update, can_delete đều = 1
                $teacherRole->permissions()->attach($perm->id, [
                    'can_view' => 1,
                    'can_create' => 1,
                    'can_update' => 1,
                    'can_delete' => 1,
                ]);
            }
            echo "✅ Cấp quyền cho Teacher role.\n";
        }

        // Cấp quyền cho Admin role (Super Admin)
        $adminRole = Role::find(1);
        if ($adminRole) {
            $adminPermissions = Permission::get();

            foreach ($adminPermissions as $perm) {
                $adminRole->permissions()->attach($perm->id, [
                    'can_view' => 1,
                    'can_create' => 1,
                    'can_update' => 1,
                    'can_delete' => 1,
                ]);
            }
            echo "✅ Cấp quyền cho Admin role.\n";
        }
    }
}