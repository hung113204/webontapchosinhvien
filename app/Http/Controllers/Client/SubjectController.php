<?php

namespace App\Http\Controllers\Client;

use App\Models\MonHoc;
use App\Models\Tiendobaihoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SubjectController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        // ── Helper closure tính tiến độ từ Tiendobaihoc ───────────
        $calcPercent = function ($mon) {
            if (!auth()->check()) {
                return 0;
            }
            // Lấy tổng số bài học từ chuongHocs (load via with)
            $tongBai = 0;
            if ($mon->chuongHocs && $mon->chuongHocs->isNotEmpty()) {
                $tongBai = $mon->chuongHocs->sum(fn($ch) => $ch->baiHocs ? $ch->baiHocs->count() : 0);
            }
            if ($tongBai === 0) {
                return 0;
            }
            // Đếm số bài học đã hoàn thành (trang_thai = 2) của user trong môn này
            $done = Tiendobaihoc::where('user_id', auth()->id())
                ->where('trang_thai', 2)
                ->whereHas('baiHoc.chuongHoc', function ($q) use ($mon) {
                    $q->where('mon_hoc_id', $mon->id);
                })
                ->count();

            return $tongBai > 0 ? (int) round(($done / $tongBai) * 100) : 0;
        };

        $buildQuery = function () use ($request) {
            $q = MonHoc::with([
                'chuongHocs' => fn($q) => $q->where('trang_thai', 1)->with(['baiHocs' => fn($q) => $q->where('trang_thai', 1)]),
                'baiKiemTras',
            ])
                ->withCount('cauHois as cau_hois_count')
                ->withCount([
                    'baiHocs as tong_bai_hoc' => function ($q) {
                        $q->where('bai_hoc.trang_thai', 1);
                    },
                ])
                ->where('trang_thai', 1);

            if ($request->filled('keyword')) {
                $q->where('ten_mon_hoc', 'like', '%' . $request->keyword . '%');
            }

            if ($request->filled('level') && $request->level !== 'all') {
                $q->where('muc_do_mon_hoc', $request->level);
            }

            return $q->orderBy('thu_tu')->orderBy('ten_mon_hoc');
        };

        if ($request->ajax()) {
            $subjects = $buildQuery()
                ->get()
                ->map(function ($mon) use ($calcPercent) {
                    $arr = $mon->toArray();
                    $arr['progress_percent'] = $calcPercent($mon);
                    return $arr;
                });
            return response()->json(['subjects' => $subjects]);
        }

        $monHocs = $buildQuery()
            ->get()
            ->each(function ($mon) use ($calcPercent) {
                $mon->progress_percent = $calcPercent($mon);
            });

        return view('Client.subjects.index', compact('monHocs'));
    }

    // ── Chi tiết học phần 
    public function show($id, Request $request)
    {
        $monHoc = MonHoc::with([
            'chuongHocs' => fn($q) => $q->where('trang_thai', 1)->orderBy('thu_tu'),
            'chuongHocs.baiHocs' => fn($q) => $q->where('trang_thai', 1)->orderBy('thu_tu'),
        ])
            ->where('trang_thai', 1)
            ->findOrFail($id);

        //Lấy danh sách ID bài học đã hoàn thành của user 
        $baiHocDaXongIds = [];
        $progressPercent = 0;

        if (auth()->check()) {
            $baiHocDaXongIds = Tiendobaihoc::where('user_id', auth()->id())
                ->where('trang_thai', 2)
                ->whereHas('baiHoc.chuongHoc', fn($q) => $q->where('mon_hoc_id', $id))
                ->pluck('bai_hoc_id')
                ->toArray();
            // Tính % tiến độ thực tế
            $tongBai = $monHoc->chuongHocs->sum(fn($c) => $c->baiHocs->count());
            if ($tongBai > 0) {
                $progressPercent = round((count($baiHocDaXongIds) / $tongBai) * 100, 1);
            }
        }
        //Xác định bài học đang active 
        $activeBaiHoc = null;
        $hasSelectedLesson = $request->filled('bai_hoc_id');
        if ($hasSelectedLesson) {
            $activeBaiHoc = \App\Models\BaiHoc::with([
                'cauHois' => fn($q) => $q->where('trang_thai', 1),
                'cauHois.dapAns',
            ])->find($request->bai_hoc_id);
        }

        if (!$activeBaiHoc) {
            $firstChapter = $monHoc->chuongHocs->first();
            if ($firstChapter && $firstChapter->baiHocs->isNotEmpty()) {
                $activeBaiHoc = \App\Models\BaiHoc::with([
                    'cauHois' => fn($q) => $q->where('trang_thai', 1),
                    'cauHois.dapAns',
                ])->find($firstChapter->baiHocs->first()->id);
            }
        }
        if (auth()->check() && $activeBaiHoc) {
            Tiendobaihoc::firstOrCreate(['user_id' => auth()->id(), 'bai_hoc_id' => $activeBaiHoc->id], ['trang_thai' => 1]);
        }

        $breadcrumbs = [
            ['label' => 'Trang chủ', 'url' => route('home')],
            ['label' => 'Học phần', 'url' => route('client.subjects.index')],
            [
                'label' => $monHoc->ten_mon_hoc,
                'url' => $hasSelectedLesson && $activeBaiHoc ? route('client.subjects.show', $monHoc->id) : null,
            ],
        ];

        if ($hasSelectedLesson && $activeBaiHoc) {
            $breadcrumbs[] = ['label' => $activeBaiHoc->ten_bai_hoc];
        }

        return view('Client.subjects.show', compact('monHoc', 'breadcrumbs', 'activeBaiHoc', 'baiHocDaXongIds', 'progressPercent'));
    }

    //Cập nhật tiến độ học 
    public function updateProgress(Request $request)
    {
        $userId = auth()->id();
        $baiHocId = $request->bai_hoc_id;

        if (!$userId || !$baiHocId) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.'], 400);
        }

        $tienDo = Tiendobaihoc::updateOrCreate(
            ['user_id' => $userId, 'bai_hoc_id' => $baiHocId],
            [
                'trang_thai' => 2, 
                'phan_tram_hoan_thanh' => 100,
                'ngay_hoan_thanh' => now(),
            ],
        );

        return response()->json(['success' => true, 'message' => 'Đã ghi nhận hoàn thành bài học!']);
    }
    //Lấy tiến độ tất cả môn học
    public function getProgressAll(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['success' => false, 'data' => []], 401);
        }

        $monHocs = MonHoc::with([
            'chuongHocs' => fn($q) => $q->where('trang_thai', 1)->with(['baiHocs' => fn($q) => $q->where('trang_thai', 1)]),
        ])
            ->where('trang_thai', 1)
            ->get()
            ->map(function ($monHoc) use ($userId) {
                // Tính tổng số bài học từ chuongHocs
                $tongBai = 0;
                if ($monHoc->chuongHocs && $monHoc->chuongHocs->isNotEmpty()) {
                    $tongBai = $monHoc->chuongHocs->sum(fn($ch) => $ch->baiHocs ? $ch->baiHocs->count() : 0);
                }

                if ($tongBai === 0) {
                    return [
                        'id' => $monHoc->id,
                        'percent' => 0,
                        'text' => 'Chưa bắt đầu',
                    ];
                }
                $soBaiDaXong = Tiendobaihoc::where('user_id', $userId)
                    ->where('trang_thai', 2)
                    ->whereHas('baiHoc.chuongHoc', function ($q) use ($monHoc) {
                        $q->where('mon_hoc_id', $monHoc->id);
                    })
                    ->count();
                $percent = round(($soBaiDaXong / $tongBai) * 100, 1);
                return [
                    'id' => $monHoc->id,
                    'percent' => (int) $percent,
                    'text' => $percent > 0 ? $percent . '%' : 'Chưa bắt đầu',
                ];
            });

        return response()->json(['success' => true, 'data' => $monHocs]);
    }
}