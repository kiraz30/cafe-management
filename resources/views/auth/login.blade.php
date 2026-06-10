<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafe Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>

<div class="login-card">

    {{-- Header --}}
    <div class="login-header">
        <div style="font-size: 52px; margin-bottom: 10px;">☕</div>
        <h4 class="text-white fw-bold mb-1">Cafe Management</h4>
        <p class="text-white-50 mb-0" style="font-size: 13px;">Sistem Manajemen Cafe</p>
    </div>

    {{-- Body --}}
    <div class="login-body">

        <h6 class="fw-bold mb-4" style="color: #2c3e50;">Masuk ke Akun Anda</h6>

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <span class="input-group-text">✉️</span>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="contoh@email.com"
                           required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">🔒</span>
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>

            <div class="mb-4 d-flex align-items-center">
                <input type="checkbox" name="remember" id="remember" class="form-check-input me-2">
                <label for="remember" class="form-check-label" style="font-size: 13px; color: #666;">
                    Ingat saya
                </label>
            </div>

            <button type="submit" class="btn-login">
                Masuk →
            </button>

        </form>

        <div class="copyright">
            © {{ date('Y') }} Cafe Management System
        </div>

    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>