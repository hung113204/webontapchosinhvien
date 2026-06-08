<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Models\CauHoi;
use App\Models\User;
use App\Models\Tiendobaihoc; // Thêm Model này để tính tiến độ

class HomeController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $danhSachMon = MonHoc::where('trang_thai', 1)
            ->with([
                'chuongHocs.baiHocs' => function ($q) {
                    $q->where('trang_thai', 1);
                },
            ])
            ->withCount(['chuongHocs', 'cauHois'])
            ->orderBy('ten_mon_hoc')
            ->take(4)
            ->get();
        // Load thêm quan hệ chuongHocs.baiHocs để phục vụ tính toán tiến độ
        $monHocNoiBat = MonHoc::where('trang_thai', 1)
            ->where('is_featured', 1)
            ->with([
                'chuongHocs.baiHocs' => function ($q) {
                    $q->where('trang_thai', 1);
                },
            ])
            ->withCount(['chuongHocs', 'cauHois'])
            ->orderBy('thu_tu', 'asc')
            ->take(4)
            ->get();
        $chuDe = MonHoc::where('trang_thai', 1)->withCount('cauHois')->orderBy('thu_tu', 'asc')->take(6)->get();
        
        $deThu = \App\Models\BaiKiemTra::where('trang_thai', 1)
            ->with(['monHoc'])
            ->withCount('cauHois')
            ->orderBy('id', 'desc')
            ->take(4)
            ->get();

        // Lịch sử thi của user
        $lichSuThi = [];
        if (auth()->check()) {
            $lichSuThi = \App\Models\KetQuaThi::where('user_id', auth()->id())
                ->latest()
                ->get()
                ->groupBy('bai_kiem_tra_id')
                ->toArray();
        }
        // 4. Lấy danh mục trang chủ
        $danhMucTrangChu = \App\Models\danhmuctrangchu::activeAndOrdered()
            ->with([
                'monHocs' => function ($q) {
                    $q->where('trang_thai', 1);
                },
                'monHocs.chuongHocs.baiHocs' => function ($q) {
                    $q->where('trang_thai', 1);
                }
            ])
            ->get();

        // 2. Tính toán tiến độ cho từng môn học nếu User đã đăng nhập
        if (auth()->check()) {
            $userId = auth()->id();

            $calculateProgress = function ($mon) use ($userId) {
                $tongBai = $mon->chuongHocs ? $mon->chuongHocs->sum(fn($ch) => $ch->baiHocs ? $ch->baiHocs->count() : 0) : 0;
                $mon->tong_bai_hoc = $tongBai;

                if ($tongBai > 0) {
                    $done = Tiendobaihoc::where('user_id', $userId)
                        ->where('trang_thai', 2)
                        ->whereHas('baiHoc.chuongHoc', function ($q) use ($mon) {
                            $q->where('mon_hoc_id', $mon->id);
                        })
                        ->count();
                    $mon->progress_percent = (int) round(($done / $tongBai) * 100);
                } else {
                    $mon->progress_percent = 0;
                }
            };

            $monHocNoiBat->each($calculateProgress);
            $danhSachMon->each($calculateProgress);
            $danhMucTrangChu->each(function ($dm) use ($calculateProgress) {
                if ($dm->loai_danh_muc === 'course_list' && $dm->monHocs) {
                    $dm->monHocs->each($calculateProgress);
                }
            });
        } else {
            $defaultProgress = function ($mon) {
                $mon->tong_bai_hoc = $mon->chuongHocs ? $mon->chuongHocs->sum(fn($ch) => $ch->baiHocs ? $ch->baiHocs->count() : 0) : 0;
                $mon->progress_percent = 0;
            };

            $monHocNoiBat->each($defaultProgress);
            $danhSachMon->each($defaultProgress);
            $danhMucTrangChu->each(function ($dm) use ($defaultProgress) {
                if ($dm->loai_danh_muc === 'course_list' && $dm->monHocs) {
                    $dm->monHocs->each($defaultProgress);
                }
            });
        }

        // 3. Thống kê chung
        $thongKe = [
            'tong_mon_hoc' => MonHoc::where('trang_thai', 1)->count(),
            'tong_cau_hoi' => CauHoi::count(),
            'tong_sinh_vien' => User::count(),
        ];

        // 5. Lấy danh sách FAQ
        $faqs = \App\Models\Faq::where('is_active', 1)->orderBy('order', 'asc')->get();

        return view('Client.home.home', compact('danhSachMon','monHocNoiBat', 'deThu', 'chuDe', 'thongKe', 'lichSuThi', 'danhMucTrangChu', 'faqs'));
    }
}