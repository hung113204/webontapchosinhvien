<!doctype html>
<html lang="vi">
<head>
  @include('Admin.parts.head') </head>
<body>
    <aside class="sidebar">
       @include('Admin.parts.sidebar')
    </aside>

    <main class="main-content">
        @include('Admin.parts.header')

        <div class="content-body">
            @yield('content')
        </div>
    </main>
     @include('Admin.parts.modal-monhoc')
    @include('Admin.parts.footer')
    @stack('scripts')
</body>
</html>