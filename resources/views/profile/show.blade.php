@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
@php
    $tab = session('tab', 'info');
@endphp

<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10" x-data="{ tab: '{{ $tab }}' }">

    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        {{-- Avatar --}}
        <div class="relative flex-shrink-0">
            @if($user->avatar)
                <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}"
                     alt="{{ $user->name }}"
                     class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-lg">
            @else
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-3xl font-extrabold ring-4 ring-white shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800">{{ $user->name }}</h1>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
            <span class="inline-flex items-center mt-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                {{ $user->isPro() ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $user->planLabel() }}
            </span>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl mb-6">
        <button @click="tab = 'info'"
                :class="tab === 'info' ? 'bg-white shadow text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 text-sm px-4 py-2 rounded-lg transition">
            Informations
        </button>
<button @click="tab = 'password'"
                :class="tab === 'password' ? 'bg-white shadow text-indigo-700 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 text-sm px-4 py-2 rounded-lg transition">
            Mot de passe
        </button>
        <button @click="tab = 'danger'"
                :class="tab === 'danger' ? 'bg-white shadow text-rose-600 font-semibold' : 'text-gray-500 hover:text-rose-600'"
                class="flex-1 text-sm px-4 py-2 rounded-lg transition">
            Danger
        </button>
    </div>

    {{-- ── ONGLET INFORMATIONS ── --}}
    <div x-show="tab === 'info'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 mb-5">Informations personnelles</h2>
            <form method="POST" action="{{ route('profile.updateInfo') }}">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if($user->google_id)
                    <p class="flex items-center gap-2 text-xs text-gray-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Compte lié à Google
                    </p>
                    @endif
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── ONGLET MOT DE PASSE ── --}}
    <div x-show="tab === 'password'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-800 mb-1">Mot de passe</h2>
            @if(!$user->password)
                <p class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 mb-5">
                    Votre compte est connecté via Google — vous n'avez pas encore de mot de passe. Vous pouvez en définir un ici.
                </p>
            @else
                <p class="text-sm text-gray-400 mb-5">Choisissez un mot de passe fort (8 caractères minimum).</p>
            @endif

            <form method="POST" action="{{ route('profile.updatePassword') }}">
                @csrf @method('PUT')
                <div class="space-y-4">
                    @if($user->password)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                        <input type="password" name="current_password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('current_password') border-red-400 @enderror">
                        @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── ONGLET DANGER ── --}}
    <div x-show="tab === 'danger'" x-cloak>
        <div class="bg-white rounded-2xl border border-rose-200 shadow-sm p-6">
            <div class="flex items-start gap-3 mb-5">
                <div class="w-9 h-9 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-800">Supprimer mon compte</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Cette action est <strong>irréversible</strong>. Tous vos projets, tâches et données seront supprimés définitivement.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.destroy') }}"
                  onsubmit="return confirm('Supprimer définitivement votre compte ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <div class="space-y-4">
                    @if($user->password)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmez avec votre mot de passe</label>
                        <input type="password" name="password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 @error('password') border-red-400 @enderror">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tapez votre nom (<span class="font-bold text-gray-600">{{ $user->name }}</span>) pour confirmer
                        </label>
                        <input type="text" name="confirm_name" placeholder="{{ $user->name }}"
                               class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 @error('confirm_name') border-red-400 @enderror">
                        @error('confirm_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>
                <div class="mt-6">
                    <button type="submit"
                            class="bg-rose-600 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-700 transition">
                        Supprimer mon compte
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>


@endsection
