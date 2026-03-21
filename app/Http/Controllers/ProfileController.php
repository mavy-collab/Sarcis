<?php

namespace App\Http\Controllers;

use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email:rfc,dns', Rule::unique('users')->ignore($user->id)],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->update($validated);

        if ($emailChanged) {
            SecurityLog::log('email_changed', 'Email modifié vers : ' . $validated['email']);
        }

        return back()->with('success', 'Informations mises à jour.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user->password) {
            $request->validate([
                'current_password' => 'required',
                'password'         => 'required|string|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.'])->with('tab', 'password');
            }
        } else {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        SecurityLog::log('password_changed', 'Mot de passe modifié.');

        return back()->with('success', 'Mot de passe mis à jour.')->with('tab', 'password');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user->password) {
            $request->validate(['password' => 'required']);

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Mot de passe incorrect.'])->with('tab', 'danger');
            }
        } else {
            $request->validate(['confirm_name' => 'required']);
            if ($request->confirm_name !== $user->name) {
                return back()->withErrors(['confirm_name' => 'Le nom ne correspond pas.'])->with('tab', 'danger');
            }
        }

        $email = $user->email;
        SecurityLog::log('account_deleted', 'Compte supprimé : ' . $email, $user->id);

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Votre compte a été supprimé.');
    }
}
