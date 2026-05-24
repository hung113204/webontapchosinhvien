{{-- 
    PARTIAL: resources/views/Client/layouts/partials/breadcrumb.blade.php
    
    Cách dùng trong bất kỳ trang nào:
    
    @include('Client.layouts.partials.breadcrumb', [
        'breadcrumbs' => $breadcrumbs
    ])
    
    Hoặc truyền từ Controller:
        $breadcrumbs = [
            ['label' => 'Trang chủ',  'url' => route('home')],
            ['label' => 'Học phần',   'url' => route('client.subjects.index')],
            ['label' => $monHoc->ten_mon_hoc], // Không có 'url' = trang hiện tại
        ];
--}}

@if(!empty($breadcrumbs))
<div class="breadcrumb-bar">
    <div class="container">
        <nav class="breadcrumb">
            @foreach($breadcrumbs as $crumb)

                {{-- Dấu phân cách (bỏ qua item đầu tiên) --}}
                @if(!$loop->first)
                    <i class="fas fa-chevron-right sep"></i>
                @endif

                @if(!empty($crumb['url']))
                    {{-- Có URL → luôn là thẻ <a>, kể cả item cuối --}}
                    <a href="{{ $crumb['url'] }}">
                        @if($loop->first)<i class="fas fa-home"></i>@endif
                        {{ $crumb['label'] }}
                    </a>
                @else
                    {{-- Không có URL → trang hiện tại, chỉ là text --}}
                    <span>{{ $crumb['label'] }}</span>
                @endif

            @endforeach
        </nav>
    </div>
</div>
@endif