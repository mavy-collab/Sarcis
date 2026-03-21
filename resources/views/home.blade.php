@extends('layouts.app')

@section('title', 'Accueil')

@section('content')

{{-- Hero --}}
<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(#6366f1 1px, transparent 1px), linear-gradient(to right, #6366f1 1px, transparent 1px); background-size: 40px 40px;"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16 lg:py-24 flex flex-col lg:flex-row items-center gap-8 lg:gap-12">

        {{-- Texte --}}
        <div class="flex-1 text-center lg:text-left w-full">
            <span class="inline-block bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs sm:text-sm font-medium px-3 sm:px-4 py-1.5 rounded-full mb-4 sm:mb-6">
                Plateforme de Gestion de Projet
            </span>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight mb-4 sm:mb-6">
                Pilotez vos projets<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-emerald-400">avec efficacité</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-300 mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0">
                Organisez vos tâches, collaborez avec votre équipe et suivez l'avancement de vos projets en temps réel.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                @guest
                    <a href="{{ route('register') }}" class="bg-indigo-500 hover:bg-indigo-400 text-white font-semibold px-6 py-3 rounded-xl transition shadow-lg shadow-indigo-500/30 text-center">
                        Commencer gratuitement
                    </a>
                    <a href="{{ route('login') }}" class="border border-slate-500 hover:border-indigo-400 text-slate-300 hover:text-white font-semibold px-6 py-3 rounded-xl transition text-center">
                        Se connecter
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="bg-indigo-500 hover:bg-indigo-400 text-white font-semibold px-6 py-3 rounded-xl transition shadow-lg shadow-indigo-500/30 text-center">
                        Mon Dashboard
                    </a>
                @endguest
            </div>

            {{-- Stats --}}
            <div class="mt-8 sm:mt-10 grid grid-cols-3 gap-2 sm:gap-6 max-w-xs sm:max-w-sm mx-auto lg:mx-0">
                <div class="text-center lg:text-left">
                    <p class="text-lg sm:text-2xl font-bold text-white">100%</p>
                    <p class="text-slate-400 text-xs sm:text-sm">Gratuit</p>
                </div>
                <div class="text-center lg:text-left border-x border-slate-700 px-2 sm:px-4">
                    <p class="text-lg sm:text-2xl font-bold text-white">Live</p>
                    <p class="text-slate-400 text-xs sm:text-sm">Temps réel</p>
                </div>
                <div class="text-center lg:text-left">
                    <p class="text-lg sm:text-2xl font-bold text-white">Multi</p>
                    <p class="text-slate-400 text-xs sm:text-sm">Équipes</p>
                </div>
            </div>
        </div>

        {{-- Mockup — masqué sur mobile --}}
        <div class="hidden lg:block flex-1 w-full max-w-xl">
            <div class="bg-slate-800/60 backdrop-blur border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-slate-900/80 px-4 py-3 flex items-center gap-2 border-b border-slate-700/50">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="ml-4 text-slate-400 text-xs">Tableau de bord — Projets</span>
                </div>
                <div class="p-5 space-y-3">
                    @foreach([
                        ['Construction Route Nationale', 'Infrastructures • 12 tâches', 'En cours', 'emerald', 68],
                        ['Rénovation École Primaire', 'Éducation • 8 tâches', 'Planifié', 'yellow', 30],
                        ['Parc Solaire Municipal', 'Énergie • 15 tâches', 'Terminé', 'indigo', 100],
                    ] as [$name, $meta, $status, $color, $pct])
                    <div class="bg-slate-700/50 rounded-xl p-4 border border-slate-600/30">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-white font-semibold text-sm">{{ $name }}</p>
                                <p class="text-slate-400 text-xs mt-0.5">{{ $meta }}</p>
                            </div>
                            <span class="bg-{{ $color }}-500/20 text-{{ $color }}-400 text-xs px-2 py-1 rounded-full font-medium">{{ $status }}</span>
                        </div>
                        <div class="w-full bg-slate-600 rounded-full h-1.5">
                            <div class="bg-{{ $color }}-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="text-slate-400 text-xs mt-1.5">{{ $pct }}% complété</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Features --}}
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-8 sm:mb-12">Tout ce dont vous avez besoin</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['bg-indigo-100', 'text-indigo-600', 'Gestion des tâches', 'Créez, assignez et suivez vos tâches avec des priorités et des deadlines.', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['bg-green-100', 'text-green-600', 'Collaboration d\'équipe', 'Invitez des membres, attribuez des rôles et collaborez en temps réel.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['bg-yellow-100', 'text-yellow-600', 'Suivi de l\'avancement', 'Vue Kanban et statistiques pour suivre l\'avancement de chaque projet.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ] as [$bg, $color, $title, $desc, $icon])
            <div class="text-center p-5 sm:p-6 rounded-2xl bg-gray-50">
                <div class="w-12 h-12 sm:w-14 sm:h-14 {{ $bg }} rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $title }}</h3>
                <p class="text-gray-500 text-sm">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- CTA --}}
@guest
<section class="bg-gray-50 py-12 sm:py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3 sm:mb-4">Prêt à commencer ?</h2>
        <p class="text-gray-500 mb-6 sm:mb-8 text-sm sm:text-base">Créez votre compte gratuitement et gérez vos projets dès aujourd'hui.</p>
        <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-indigo-700 transition">
            Créer mon compte
        </a>
    </div>
</section>
@endguest

@endsection
