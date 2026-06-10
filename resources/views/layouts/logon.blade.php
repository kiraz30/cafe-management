<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafe Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background-color: #f8f9fa;">

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-4">

            {{-- Card Login --}}
            <div class="card shadow-sm border-0">
                <div class="card-body p-5">

                    {{-- Logo --}}
                    <div class="text-center mb-4">
                        <div style="font-size: 48px;">☕</div>
                        <h4 class="fw-bold">Cafe Management</h4>
                        <p class="text-muted" style="font-size: 13px;">Masuk ke sistem</p>
                    </div>

                    {{-- Alert Error --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Form Login --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="font-size: 13px;">Email</label>
                            <input type="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}"
                                   placeholder="admin@cafe.com"
                                   required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size: 13px;">Password</label>
                            <input type="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••"
                                   required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark btn-lg">
                                Masuk
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>