@extends('layouts.app')
@section('title', 'Nous contacter')

@section('content')
<div class="min-h-screen bg-slate-950 text-white py-12 px-4">
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="text-center mb-10">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center mx-auto mb-4 shadow-xl">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-extrabold tracking-tight">Nous contacter</h1>
        <p class="text-slate-400 mt-2 text-sm">Un problème, une question ou une suggestion ? Nous vous répondrons rapidement.</p>
    </div>

    {{-- Success --}}
    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/30 text-green-300 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-sm">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-slate-900 border border-white/5 rounded-2xl p-6 sm:p-8">
        <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Nom --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Nom complet</label>
                    <input type="text" name="name"
                           value="{{ old('name', Auth::user()->name ?? '') }}"
                           placeholder="Votre nom"
                           class="w-full bg-slate-800 border @error('name') border-red-500 @else border-white/10 @enderror rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Adresse email</label>
                    <input type="email" name="email"
                           value="{{ old('email', Auth::user()->email ?? '') }}"
                           placeholder="votre@email.com"
                           class="w-full bg-slate-800 border @error('email') border-red-500 @else border-white/10 @enderror rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Sujet --}}
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Sujet</label>
                <select name="subject"
                        class="w-full bg-slate-800 border @error('subject') border-red-500 @else border-white/10 @enderror rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Choisissez un sujet…</option>
                    <option value="Problème technique" {{ old('subject') === 'Problème technique' ? 'selected' : '' }}>Problème technique</option>
                    <option value="Question sur mon compte" {{ old('subject') === 'Question sur mon compte' ? 'selected' : '' }}>Question sur mon compte</option>
                    <option value="Question sur les forfaits" {{ old('subject') === 'Question sur les forfaits' ? 'selected' : '' }}>Question sur les forfaits</option>
                    <option value="Suggestion d'amélioration" {{ old('subject') === "Suggestion d'amélioration" ? 'selected' : '' }}>Suggestion d'amélioration</option>
                    <option value="Signaler un bug" {{ old('subject') === 'Signaler un bug' ? 'selected' : '' }}>Signaler un bug</option>
                    <option value="Autre" {{ old('subject') === 'Autre' ? 'selected' : '' }}>Autre</option>
                </select>
                @error('subject')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Message</label>
                <textarea name="message" rows="6"
                          placeholder="Décrivez votre problème ou votre question en détail…"
                          class="w-full bg-slate-800 border @error('message') border-red-500 @else border-white/10 @enderror rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none">{{ old('message') }}</textarea>
                @error('message')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-slate-600 text-xs mt-1 text-right">Max. 3000 caractères</p>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-500 hover:text-white transition">← Retour au dashboard</a>
                <button type="submit"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-6 py-2.5 rounded-xl transition text-sm shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Envoyer le message
                </button>
            </div>
        </form>
    </div>

    {{-- Info --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-slate-900/50 border border-white/5 rounded-xl px-4 py-3 text-center">
            <div class="text-lg mb-1">⚡</div>
            <div class="text-xs font-semibold text-white">Réponse rapide</div>
            <div class="text-xs text-slate-500 mt-0.5">Sous 24–48h ouvrées</div>
        </div>
        <div class="bg-slate-900/50 border border-white/5 rounded-xl px-4 py-3 text-center">
            <div class="text-lg mb-1">🔒</div>
            <div class="text-xs font-semibold text-white">Confidentiel</div>
            <div class="text-xs text-slate-500 mt-0.5">Vos données sont protégées</div>
        </div>
        <div class="bg-slate-900/50 border border-white/5 rounded-xl px-4 py-3 text-center">
            <div class="text-lg mb-1">🛠️</div>
            <div class="text-xs font-semibold text-white">Support technique</div>
            <div class="text-xs text-slate-500 mt-0.5">Équipe dédiée</div>
        </div>
    </div>

</div>
</div>
@endsection
