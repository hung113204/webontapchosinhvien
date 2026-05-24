<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Tạo các vai trò (Roles) với tên cột ten_nhom_quyen
        $adminRole = Role::firstOrCreate(
            ['id' => 1],
            [
                'ten_nhom_quyen' => 'Admin', // Đã sửa theo yêu cầu của bạn
                'mo_ta' => 'Quản trị viên hệ thống',
                'trang_thai' => true,
            ],
        );

        $teacherRole = Role::firstOrCreate(
            ['id' => 2],
            [
                'ten_nhom_quyen' => 'Giảng Viên', // Đã sửa theo yêu cầu của bạn
                'mo_ta' => 'Giáo viên dạy học',
                'trang_thai' => true,
            ],
        );

        $studentRole = Role::firstOrCreate(
            ['id' => 3],
            [
                'ten_nhom_quyen' => 'Sinh Viên', // Đã sửa theo yêu cầu của bạn
                'mo_ta' => 'Học sinh, sinh viên',
                'trang_thai' => true,
            ],
        );
        // Tạo tài khoản Admin demo
        User::firstOrCreate(
            ['email' => 'admin@ontapcntt.edu.vn'],
            [
                'ma_sv' => 'ADMIN001',
                'ho_ten' => 'Quản Trị Viên Hệ Thống',
                'mat_khau' => Hash::make('admin123'),
                'email' => 'admin@ontapcntt.edu.vn',
                'vai_tro_id' => 1,
                'trang_thai' => true,
                'gioi_tinh' => 'Nam',
                'ngay_sinh' => '1990-01-01',
                'so_dien_thoai' => '0123456789',
            ],
        );

        // Tạo tài khoản Giáo viên demo
        User::firstOrCreate(
            ['email' => 'teacher@ontapcntt.edu.vn'],
            [
                'ma_sv' => 'GV001',
                'ho_ten' => 'Giáo Viên Demo',
                'mat_khau' => Hash::make('teacher123'),
                'email' => 'teacher@ontapcntt.edu.vn',
                'vai_tro_id' => 2,
                'trang_thai' => true,
                'gioi_tinh' => 'Nữ',
                'ngay_sinh' => '1995-05-15',
                'so_dien_thoai' => '0987654321',
            ],
        );

        // Tạo tài khoản Sinh viên demo
        User::firstOrCreate(
            ['email' => 'student@ontapcntt.edu.vn'],
            [
                'ma_sv' => 'B20DCCN001',
                'ho_ten' => 'Sinh Viên Demo',
                'mat_khau' => Hash::make('student123'),
                'email' => 'student@ontapcntt.edu.vn',
                'vai_tro_id' => 3,
                'trang_thai' => true,
                'gioi_tinh' => 'Nam',
                'ngay_sinh' => '2002-03-20',
                'so_dien_thoai' => '0911111111',
            ],
        );

        // ✅ Tạo permissions và cấp quyền cho roles
        $this->call(PermissionSeeder::class);
    }
}