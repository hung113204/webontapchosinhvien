@extends('Client.layouts.app')

@section('styles')
@include('Client.phongquiz.partials.join_css')
@endsection

@section('content')
<div class="qv-container">

    @include('Client.phongquiz.partials.top_bar')

    {{-- EXIT BUTTON (visible only in fullscreen) --}}
    <button class="qv-fs-exit-btn" onclick="toggleFullscreen()" title="Thoát toàn màn hình (ESC)">
        ×
    </button>

    {{-- MAIN PANEL --}}
    <div class="qv-main-panel" id="qv-main-panel">
        
        @include('Client.phongquiz.partials.user_profile')

        @include('Client.phongquiz.partials.rename_modal')

        @include('Client.phongquiz.partials.lobby_main')

        @include('Client.phongquiz.partials.create_room')

        @include('Client.phongquiz.partials.join_pin')

    </div>

</div>

@include('Client.phongquiz.partials.join_scripts')
@endsection