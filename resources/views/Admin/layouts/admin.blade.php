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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            
            // Check localStorage for collapsed state
            if (localStorage.getItem('sidebar_collapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
                });
            }
        });
    </script>
</body>
</html>