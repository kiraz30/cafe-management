<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return back()->withErrors(['email' => 'Akun kamu tidak aktif. Hubungi admin.']);
        }

        return match($user->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'kasir'   => redirect()->route('kasir.dashboard'),
            'barista' => redirect()->route('barista.dashboard'),
            'pelayan' => redirect()->route('pelayan.dashboard'),
            default   => redirect()->route('admin.dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}