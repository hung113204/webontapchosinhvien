@extends('Client.layouts.app')

@section('styles')
    @include('Client.phongquiz.partials.room_css')
    <!-- MathJax config -->
    <script>
      window.MathJax = {
        tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] },
        startup: { typeset: false }
      };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection

@section('content')
<div class="qv-container">

    {{-- TOP BAR --}}
    <div class="qv-top-bar">
        <div class="qv-brand">QuizVui</div>
        <button class="qv-fullscreen-btn" onclick="toggleFullscreen()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
            </svg>
            Toàn màn hình
        </button>
    </div>

    {{-- EXIT BUTTON (visible only in fullscreen) --}}
    <button class="qv-fs-exit-btn" onclick="toggleFullscreen()" title="Thoát toàn màn hình (ESC)">
        ×
    </button>

    {{-- MAIN PANEL --}}
    <div class="qv-main-panel">
        
        @include('Client.phongquiz.partials.user_profile')
        @include('Client.phongquiz.partials.rename_modal')

        {{-- STATE 1: LOBBY WAITING --}}
        @include('Client.phongquiz.partials.lobby')

        {{-- STATE 2: PLAYING --}}
        @include('Client.phongquiz.partials.playing')

        {{-- STATE 3: ENDED --}}
        @include('Client.phongquiz.partials.ended')

    </div>

</div>

@include('Client.phongquiz.partials.room_scripts')
@endsection