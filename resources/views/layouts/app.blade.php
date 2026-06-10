<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Cafe Management') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Sidebar --}}
<div class="d-flex" style="min-height: 100vh;">
    @include('layouts.sidebar')

    {{-- Main Content --}}
    <div class="flex-grow-1 d-flex flex-column">

        {{-- Navbar --}}
        @include('layouts.navbar')

        {{-- Content --}}
        <main class="p-4 flex-grow-1" style="background-color: #f8f9fa;">
            {{-- Alert --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>

</body>
</html>