<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; 
use App\Models\DanhMucTrangChu; 
class FrontendController extends Controller
{
    public function __construct()
    {
        // 1. Lấy dữ liệu danh mục đang hoạt động (chỉ lấy loại 'banner' cho menu)
        $menus = DanhMucTrangChu::where('trang_thai', 1)
                                ->where('loai_danh_muc', 'banner')
                                ->orderBy('thu_tu', 'asc')
                                ->get();
        
        // 2. Chia sẻ biến $menus cho TẤT CẢ các file giao diện (blade)
        View::share('menus', $menus);
    }
}