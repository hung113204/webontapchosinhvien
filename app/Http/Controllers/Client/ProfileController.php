<?php

namespace App\Http\Controllers\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('Client.account.profile');
    }

    public function history()
    {
        $user = Auth::user();

        $phienLuyenTaps = \App\Models\PhienLuyenTap::where('user_id', $user->id)->get();
        $ketQuaThis = \App\Models\KetQuaThi::with(['baiKiemTra.monHoc'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $tienDoMonHoc = $phienLuyenTaps->groupBy('mon_hoc_id')->map(function ($items) {
            $monHoc = $items->first()->monHoc;
            $tongCauMonNay = (int) $items->sum(fn($item) => is_numeric($item->so_cau_hoi) ? $item->so_cau_hoi : 0);
            $diemTbMon = (float) $items->avg(fn($item) => is_numeric($item->diem_so) ? $item->diem_so : 0);

            return [
                'id' => $monHoc->id ?? 0,
                'ten_mon_hoc' => $monHoc->ten_mon_hoc ?? 'N/A',
                'so_bai' => $items->count(),
                'tong_cau' => $tongCauMonNay,
                'diem_tb' => round($diemTbMon, 1),
                'phan_tram' => min($tongCauMonNay, 100),
            ];
        });

        $tongBaiThi = (int) $ketQuaThis->count();
        $tongCauLuyenTap = (int) $phienLuyenTaps->sum(fn($item) => is_numeric($item->so_cau_hoi) ? $item->so_cau_hoi : 0);
        $tongCauBaiThi = (int) $ketQuaThis->sum(fn($item) => is_numeric($item->tong_so_cau) ? $item->tong_so_cau : 0);
        $diemTrungBinh = (float) $ketQuaThis->avg(fn($item) => is_numeric($item->diem) ? $item->diem : 0);
        $tyLeHoanThanh = (float) ($tienDoMonHoc->avg('phan_tram') ?? 0);

        $stats = [
            'tong_so_bai' => $tongBaiThi,
            'tong_cau_da_lam' => $tongCauLuyenTap + $tongCauBaiThi,
            'diem_trung_binh' => round($diemTrungBinh, 1),
            'ty_le_hoan_thanh' => round($tyLeHoanThanh, 1),
            'tong_gio_hoc_tap' => round(($phienLuyenTaps->count() * 10) / 60, 1),
        ];

        $monData = $ketQuaThis
            ->groupBy(fn($kq) => $kq->baiKiemTra->monHoc->ten_mon_hoc ?? 'Khac')
            ->map
            ->count();

        $weekLabels = ['Tuan 1', 'Tuan 2', 'Tuan 3', 'Tuan 4'];
        $weekScores = [0, 0, 0, $stats['diem_trung_binh']];

        // --- THÊM THỐNG KÊ SO SÁNH VỚI MỌI NGƯỜI ---
        $allUsers = \App\Models\User::where('vai_tro_id', '!=', 1)->get();
        $totalSystemLessons = \App\Models\BaiHoc::count();
        
        $globalStats = [];
        foreach ($allUsers as $u) {
            $compCount = \App\Models\TienDoBaiHoc::where('user_id', $u->id)->where('trang_thai', 2)->count();
            $prog = $totalSystemLessons > 0 ? ($compCount / $totalSystemLessons) * 100 : 0;
            $avgS = \App\Models\PhienLuyenTap::where('user_id', $u->id)->avg('diem_so') ?? 0;
            
            $globalStats[] = [
                'user_id' => $u->id,
                'progress' => $prog,
                'avg_score' => $avgS
            ];
        }

        // Tính thứ hạng dựa trên tiến độ
        usort($globalStats, fn($a, $b) => $b['progress'] <=> $a['progress']);
        $rank = 0;
        foreach ($globalStats as $index => $gs) {
            if ($gs['user_id'] == $user->id) {
                $rank = $index + 1;
                break;
            }
        }

        $systemAvgProgress = count($globalStats) > 0 ? array_sum(array_column($globalStats, 'progress')) / count($globalStats) : 0;
        $systemAvgScore = count($globalStats) > 0 ? array_sum(array_column($globalStats, 'avg_score')) / count($globalStats) : 0;

        $comparison = [
            'rank' => $rank,
            'total_users' => count($allUsers),
            'system_avg_progress' => round($systemAvgProgress, 1),
            'system_avg_score' => round($systemAvgScore, 1),
        ];

        return view('Client.account.history', compact('stats', 'tienDoMonHoc', 'ketQuaThis', 'monData', 'weekLabels', 'weekScores', 'comparison'));
    }

    public function updateInfo(Request $request)
    {
        $request->validate(
            [
                'ho_ten' => 'required|string|max:255',
                'so_dien_thoai' => 'nullable|string|max:20',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'ho_ten.required' => 'Vui lòng nhập họ và tên.',
                'avatar.image' => 'File tải lên phải là định dạng ảnh.',
                'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            ],
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->ho_ten = $request->ho_ten;
        $user->so_dien_thoai = $request->so_dien_thoai;

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        $user->save();

        return back()->with('success', 'Đã lưu thay đổi thông tin cá nhân!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(
            [
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ],
            [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
                'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
                'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
            ],
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->mat_khau)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->mat_khau = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(
            [
                'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'avatar.required' => 'Vui lòng chọn một ảnh.',
                'avatar.image' => 'File tải lên phải là ảnh.',
                'avatar.mimes' => 'Ảnh chỉ hỗ trợ định dạng: jpeg, png, jpg.',
                'avatar.max' => 'Kích thước ảnh không được vượt quá 2MB.',
            ],
        );

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
            $user->save();
        }

        return back()->with('success', 'Cập nhật ảnh đại diện thành công!');
    }

    public function removeAvatar()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $user->avatar_url = null;
        $user->save();

        return back()->with('success', 'Đã gỡ ảnh đại diện.');
    }
}
