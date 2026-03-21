<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Rate limiting : max 5 tentatives par IP+email toutes les 15 minutes
        $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            SecurityLog::log('login_blocked', 'Email: ' . $request->email . ' — IP: ' . $request->ip());
            return back()->withErrors([
                'email' => 'Trop de tentatives échouées. Réessayez dans ' . ceil($seconds / 60) . ' minute(s).',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            SecurityLog::log('login_success', 'Email: ' . $request->email);

            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        // Échec : incrémenter le compteur (expire après 15 minutes)
        RateLimiter::hit($key, 900);
        $attempts = RateLimiter::attempts($key);
        SecurityLog::log('login_failed', 'Email: ' . $request->email . ' — tentative ' . $attempts . '/5', null);

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent à aucun compte.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        SecurityLog::log('logout', 'Email: ' . Auth::user()?->email);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
