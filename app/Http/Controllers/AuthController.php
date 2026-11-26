<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route(match (Auth::user()->role) {
                'admin'  => 'admin.dashboard',
                'seller' => 'seller.dashboard',
                default  => 'customer.home',
            });
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'seller' => redirect()->route('seller.dashboard'),
            'customer' => redirect()->route('customer.home'),
            default => $this->rejectUnsupportedRole($request),
        };
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => bcrypt($data['password']),
            'role' => 'customer',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    private function rejectUnsupportedRole(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->withErrors([
            'email' => 'Akun ini belum memiliki akses.',
        ])->onlyInput('email');
    }
}
