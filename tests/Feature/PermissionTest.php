<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\CauHoi;
use App\Models\ChuongHoc;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $teacher;
    private $teacher2;
    private $student;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo roles
        Role::create(['id' => 1, 'ten_nhom_quyen' => 'Admin']);
        Role::create(['id' => 2, 'ten_nhom_quyen' => 'Giáo Viên']);
        Role::create(['id' => 3, 'ten_nhom_quyen' => 'Sinh Viên']);

        // Tạo users
        $this->admin = User::create([
            'email' => 'admin@test.com',
            'ho_ten' => 'Admin User',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => 1,
        ]);

        $this->teacher = User::create([
            'email' => 'teacher1@test.com',
            'ho_ten' => 'Teacher 1',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => 2,
        ]);

        $this->teacher2 = User::create([
            'email' => 'teacher2@test.com',
            'ho_ten' => 'Teacher 2',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => 2,
        ]);

        $this->student = User::create([
            'email' => 'student@test.com',
            'ho_ten' => 'Student User',
            'mat_khau' => bcrypt('password'),
            'vai_tro_id' => 3,
        ]);

        // Tạo permissions
        $permissions = [
            ['slug' => 'question.view', 'name' => 'View Questions'],
            ['slug' => 'question.create', 'name' => 'Create Questions'],
            ['slug' => 'question.update', 'name' => 'Update Questions'],
            ['slug' => 'question.delete', 'name' => 'Delete Questions'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        // Cấp quyền cho teacher role
        $teacherRole = Role::find(2);
        $teacherPerms = Permission::whereIn('slug', [
            'question.view',
            'question.create',
            'question.update',
            'question.delete',
        ])->get();

        foreach ($teacherPerms as $perm) {
            $teacherRole->permissions()->attach($perm->id, [
                'can_view' => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
            ]);
        }

        // Cấp quyền cho admin role
        $adminRole = Role::find(1);
        foreach (Permission::all() as $perm) {
            $adminRole->permissions()->attach($perm->id, [
                'can_view' => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
            ]);
        }
    }

    /** @test */
    public function teacher_can_create_question()
    {
        $this->actingAs($this->teacher);

        $permission = $this->teacher->hasPermission('question', 'create');

        $this->assertTrue($permission, 'Teacher should have permission to create question');
    }

    /** @test */
    public function teacher_can_view_own_question()
    {
        $this->actingAs($this->teacher);

        $chuongHoc = ChuongHoc::create([
            'ten_chuong' => 'Chapter 1',
            'trang_thai' => 1,
        ]);

        $cauHoi = CauHoi::create([
            'chuong_hoc_id' => $chuongHoc->id,
            'noi_dung' => 'Test Question',
            'nguoi_tao_id' => $this->teacher->id,
        ]);

        // Teacher có thể truy cập câu hỏi của chính họ
        $canAccess = $this->teacher->canPermission('question', 'view', $cauHoi);
        $this->assertTrue($canAccess, 'Teacher should access their own question');
    }

    /** @test */
    public function teacher_cannot_view_other_teacher_question()
    {
        $chuongHoc = ChuongHoc::create([
            'ten_chuong' => 'Chapter 1',
            'trang_thai' => 1,
        ]);

        $cauHoi = CauHoi::create([
            'chuong_hoc_id' => $chuongHoc->id,
            'noi_dung' => 'Test Question',
            'nguoi_tao_id' => $this->teacher2->id, // Tạo bởi teacher khác
        ]);

        $this->actingAs($this->teacher);

        // Teacher không thể truy cập câu hỏi của teacher khác
        $canAccess = $this->teacher->canPermission('question', 'view', $cauHoi);
        $this->assertFalse($canAccess, 'Teacher should NOT access other teacher question');
    }

    /** @test */
    public function teacher_can_view_admin_created_question()
    {
        $chuongHoc = ChuongHoc::create([
            'ten_chuong' => 'Chapter 1',
            'trang_thai' => 1,
        ]);

        $cauHoi = CauHoi::create([
            'chuong_hoc_id' => $chuongHoc->id,
            'noi_dung' => 'Test Question',
            'nguoi_tao_id' => $this->admin->id, // Tạo bởi admin
        ]);

        $this->actingAs($this->teacher);

        // Teacher CÓ thể truy cập câu hỏi do admin tạo
        $canAccess = $this->teacher->canPermission('question', 'view', $cauHoi);
        $this->assertTrue($canAccess, 'Teacher should access admin-created question');
    }

    /** @test */
    public function admin_can_access_any_question()
    {
        $this->actingAs($this->admin);

        $chuongHoc = ChuongHoc::create([
            'ten_chuong' => 'Chapter 1',
            'trang_thai' => 1,
        ]);

        $cauHoi = CauHoi::create([
            'chuong_hoc_id' => $chuongHoc->id,
            'noi_dung' => 'Test Question',
            'nguoi_tao_id' => $this->teacher->id,
        ]);

        // Admin có thể truy cập bất kỳ câu hỏi nào
        $canAccess = $this->admin->canPermission('question', 'view', $cauHoi);
        $this->assertTrue($canAccess, 'Admin should access any question');
    }

    /** @test */
    public function student_cannot_create_question()
    {
        $this->actingAs($this->student);

        $permission = $this->student->hasPermission('question', 'create');

        $this->assertFalse($permission, 'Student should NOT have permission to create question');
    }
}