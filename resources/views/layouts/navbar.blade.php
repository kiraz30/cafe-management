<nav class="navbar navbar-light bg-white border-bottom px-4">
    <span class="navbar-text fw-bold">
        @yield('page-title', 'Dashboard')
    </span>
    <span class="navbar-text" style="font-size: 13px; color: #6c757d;">
        {{ now()->isoFormat('dddd, D MMMM Y') }}
    </span>
</nav>