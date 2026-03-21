<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            SecurityLog::log('google_login_failed', 'Erreur OAuth Google: ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => 'La connexion avec Google a échoué. Veuillez réessayer.']);
        }

        // Chercher un compte existant lié à ce Google ID
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Chercher un compte avec le même email (compte existant classique)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Lier le compte existant à Google seulement si email déjà vérifié
                if (!$user->hasVerifiedEmail()) {
                    return redirect()->route('login')
                        ->withErrors(['email' => 'Veuillez d\'abord vérifier votre email avant de lier votre compte Google.']);
                }
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            } else {
                // Créer un nouveau compte (email vérifié car provient de Google)
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => null,
                    'plan'              => 'free',
                    'email_verified_at' => now(),
                ]);
            }
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        SecurityLog::log('google_login', 'Connexion Google: ' . $user->email, $user->id);

        return redirect()->intended(route('dashboard'));
    }
}
