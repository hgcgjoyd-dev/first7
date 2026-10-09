<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('dashboard siswa.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                ->onlyInput('email');
        }

        $user = $request->user();

        if (! $user instanceof User || ! $user->status_aktif || ! $this->hasValidProfile($user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Email atau kata sandi tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route($user->dashboardRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar.');
    }

    private function hasValidProfile(User $user): bool
    {
        return match ($user->role) {
            'admin' => true,
            'siswa' => $user->siswa()->exists(),
            'guru' => $user->guru()
                ->whereDoesntHave('guruBk', fn ($query) => $query->where('status_aktif', true))
                ->exists(),
            'guru_bk' => $user->guru()
                ->whereHas('guruBk', fn ($query) => $query->where('status_aktif', true))
                ->exists(),
            default => false,
        };
    }
}
