<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact.create');
    }

    public function store(Request $request)
    {
        // 3 messages maximum par heure par utilisateur
        $key = 'contact:' . Auth::id();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['message' => 'Trop de messages envoyés. Réessayez dans ' . ceil($seconds / 60) . ' minute(s).']);
        }
        RateLimiter::hit($key, 3600);

        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:3000',
        ], [
            'name.required'    => 'Le nom est obligatoire.',
            'email.required'   => 'L\'adresse email est obligatoire.',
            'email.email'      => 'Adresse email invalide.',
            'subject.required' => 'Le sujet est obligatoire.',
            'message.required' => 'Le message est obligatoire.',
            'message.max'      => 'Le message ne doit pas dépasser 3000 caractères.',
        ]);

        ContactMessage::create([
            'user_id' => Auth::id(),
            'name'    => $request->name,
            'email'   => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Notification email à l'admin
        Mail::raw(
            "Nouveau message de contact\n\n" .
            "Nom : {$request->name}\n" .
            "Email : {$request->email}\n" .
            "Sujet : {$request->subject}\n\n" .
            "Message :\n{$request->message}",
            function ($mail) use ($request) {
                $mail->to(config('mail.from.address'))
                     ->replyTo($request->email, $request->name)
                     ->subject('[TËRAL] Nouveau message : ' . $request->subject);
            }
        );

        return back()->with('success', 'Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.');
    }
}
