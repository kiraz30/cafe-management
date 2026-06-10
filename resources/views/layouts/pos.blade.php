<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/pos.css') }}">
</head>
<body>

<nav class="pos-navbar">
    <div class="brand">☕ {{ \App\Models\Setting::get('cafe_name', 'Cafe Management') }} — POS</div>
    <div class="nav-actions">
        <span style="font-size: 13px; color: rgba(255,255,255,0.6);">
            {{ auth()->user()->name }}
        </span>
        <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </div>
</nav>

<div class="pos-wrapper">
    @yield('content')
</div>

</body>
</html>