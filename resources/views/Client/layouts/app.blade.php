<!doctype html>
<html lang="vi">
<head>
    @include('Client.layouts.partials.head')
    @yield('styles')
</head>
<body>
    
    <header class="site-header">
        @include('Client.layouts.partials.header')
        @include('Client.layouts.partials.main_menu')
        @include('Client.layouts.partials.toast')
    </header>
    
    <main>
        @yield('content')
    </main>

    @include('Client.layouts.partials.footer')

</body>
</html>