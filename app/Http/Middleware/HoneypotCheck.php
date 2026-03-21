<?php

namespace App\Http\Middleware;

use App\Models\SecurityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HoneypotCheck
{
    // Champ caché CSS — les humains ne le remplissent pas, les bots si
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->filled('_confirm_email')) {
            SecurityLog::log('bot_detected', 'Honeypot rempli — IP: ' . $request->ip());
            // Simuler un succès pour ne pas alerter le bot
            return redirect()->back();
        }

        return $next($request);
    }
}
