<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('plans.index', [
            'currentPlan'     => $user->plan,
            'freeWorkspaces'  => 5,
            'freeProjects'    => 1,
            'freeMembers'     => 3,
            'planExpiresAt'   => $user->plan_expires_at,
            'trialUsed'       => $user->trial_used,
            'isPro'           => $user->isPro(),
        ]);
    }

    public function upgrade(Request $request)
    {
        $request->validate(['plan' => 'required|in:pro,enterprise']);

        $user = Auth::user();
        $user->update([
            'plan'            => $request->plan,
            'plan_expires_at' => now()->addYear(),
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Votre plan a été mis à jour vers ' . $user->fresh()->planLabel() . ' !');
    }

    public function activateTrial()
    {
        $user = Auth::user();

        if ($user->trial_used) {
            return redirect()->route('plans.index')->with('info', 'Vous avez déjà utilisé votre essai gratuit.');
        }

        if ($user->plan !== 'free') {
            return redirect()->route('plans.index')->with('info', 'Vous êtes déjà sur un plan payant.');
        }

        $user->update([
            'plan'            => 'pro',
            'plan_expires_at' => now()->addWeek(),
            'trial_used'      => true,
        ]);

        return redirect()->route('plans.index')->with('success', 'Essai Pro activé ! Vous avez 7 jours pour découvrir toutes les fonctionnalités.');
    }

    public function downgrade()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('plans.index')->with('info', 'Le plan Pro est permanent pour un administrateur.');
        }

        Auth::user()->update(['plan' => 'free', 'plan_expires_at' => null]);
        return redirect()->route('plans.index')->with('success', 'Vous êtes repassé au plan Gratuit.');
    }
}
