@extends('layouts.app')

@section('title', 'À propos')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white py-16 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-medium px-4 py-1.5 rounded-full mb-4">À propos</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-4">Notre histoire</h1>
        <p class="text-slate-300 text-base sm:text-lg">SARCIS est née d'une idée simple : rendre la gestion de projets accessible à tous, gratuitement.</p>
    </div>
</section>

{{-- Histoire --}}
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Qui sommes-nous ?</h2>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    SARCIS est une plateforme de gestion de projets créée par un étudiant passionné par la technologie et l'entrepreneuriat. Notre mission est de fournir aux équipes, petites et grandes, les outils nécessaires pour organiser leur travail efficacement.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    La plateforme est actuellement en cours de développement actif. Nous ajoutons constamment de nouvelles fonctionnalités basées sur les retours de nos utilisateurs.
                </p>
            </div>
            <div class="bg-indigo-50 rounded-2xl p-8 text-center">
                <div class="text-5xl font-extrabold text-indigo-600 mb-2">SARCIS</div>
                <p class="text-gray-500 text-sm">Plateforme de gestion de projets</p>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <p class="text-2xl font-bold text-indigo-600">100%</p>
                        <p class="text-xs text-gray-500">Gratuit</p>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm">
                        <p class="text-2xl font-bold text-indigo-600">2026</p>
                        <p class="text-xs text-gray-500">Année de création</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Valeurs --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Nos valeurs</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['🎯', 'Simplicité', 'Une interface claire et intuitive, accessible à tous sans formation.'],
                ['🤝', 'Collaboration', 'Travailler ensemble, partager et avancer vers un objectif commun.'],
                ['🚀', 'Innovation', 'Amélioration continue grâce aux retours de notre communauté.'],
            ] as [$icon, $title, $desc])
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                <div class="text-4xl mb-3">{{ $icon }}</div>
                <h3 class="font-bold text-gray-800 mb-2">{{ $title }}</h3>
                <p class="text-gray-500 text-sm">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-12 bg-white">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Rejoignez l'aventure</h2>
        <p class="text-gray-500 mb-6 text-sm">La plateforme est gratuite et ouverte à tous. Créez votre compte et commencez dès aujourd'hui.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @guest
            <a href="{{ route('register') }}" class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-indigo-700 transition">
                Créer un compte gratuit
            </a>
            @endguest
            <a href="{{ route('contact.create') }}" class="border border-indigo-300 text-indigo-600 font-semibold px-6 py-3 rounded-xl hover:bg-indigo-50 transition">
                Nous contacter
            </a>
        </div>
    </div>
</section>

@endsection
