@extends('layouts.app')
@section('title', 'Plans & Tarifs')

@section('content')
<div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white px-6 py-10 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block bg-white/10 border border-white/20 text-xs font-semibold px-4 py-1.5 rounded-full mb-4 tracking-wider uppercase">Plans & Tarifs</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-3">Choisissez votre plan</h1>
        <p class="text-indigo-200 text-base">Débloquez des fonctionnalités avancées pour booster votre productivité.</p>

        @if(session('success'))
            <div class="mt-4 bg-emerald-500/20 border border-emerald-400/40 text-emerald-100 px-4 py-2.5 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="mt-4 bg-amber-500/20 border border-amber-400/40 text-amber-100 px-4 py-2.5 rounded-xl text-sm">
                {{ session('info') }}
            </div>
        @endif
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

    {{-- Current plan badge --}}
    <div class="text-center mb-8">
        <span class="inline-flex items-center gap-2 bg-white border border-indigo-100 shadow-sm px-5 py-2 rounded-full text-sm font-semibold text-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Plan actuel : {{ auth()->user()->planLabel() }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">

        {{-- FREE --}}
        <div class="bg-white rounded-2xl border {{ $currentPlan === 'free' ? 'border-indigo-300 ring-2 ring-indigo-200' : 'border-gray-100' }} shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-50">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-bold text-gray-800">Gratuit</h2>
                    @if($currentPlan === 'free')
                        <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">Actuel</span>
                    @endif
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">0 FCFA<span class="text-base font-normal text-gray-400">/mois</span></div>
            </div>
            <div class="p-6 space-y-3">
                @foreach([
                    [$freeWorkspaces . ' espaces de travail', true],
                    [$freeProjects . ' projet par espace', true],
                    [$freeMembers . ' membres par projet', true],
                    ['Tâches basiques', true],
                    ['Sous-tâches', false],
                    ['Pièces jointes', false],
                    ['Export PDF', false],
                    ['Diagramme de Gantt', false],
                    ['Rapports & Statistiques', false],
                ] as [$feature, $included])
                <div class="flex items-center gap-2.5 text-sm {{ $included ? 'text-gray-700' : 'text-gray-300 line-through' }}">
                    @if($included)
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-4 h-4 text-gray-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                    {{ $feature }}
                </div>
                @endforeach
            </div>
            <div class="px-6 pb-6">
                @if($currentPlan !== 'free')
                <form method="POST" action="{{ route('plans.downgrade') }}"
                      x-data @submit.prevent="if(confirm('Rétrograder vers le plan Gratuit ?')) $el.submit()">
                    @csrf
                    <button type="submit"
                            class="w-full border border-gray-200 text-gray-500 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                        Rétrograder
                    </button>
                </form>
                @else
                <button disabled class="w-full bg-gray-100 text-gray-400 py-2.5 rounded-xl text-sm font-medium cursor-not-allowed">
                    Plan actuel
                </button>
                @endif
            </div>
        </div>

        {{-- PRO --}}
        <div class="bg-white rounded-2xl border {{ $currentPlan === 'pro' ? 'border-indigo-400 ring-2 ring-indigo-300' : 'border-indigo-100' }} shadow-lg overflow-hidden relative">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-violet-500"></div>
            <div class="absolute top-4 right-4">
                <span class="text-xs font-bold bg-gradient-to-r from-indigo-500 to-violet-500 text-white px-3 py-1 rounded-full">Populaire</span>
            </div>
            <div class="p-6 border-b border-gray-50">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-bold text-gray-800">Pro</h2>
                    @if($currentPlan === 'pro')
                        <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">Actuel</span>
                    @endif
                </div>
                <div class="text-3xl font-extrabold text-gray-900 mt-2">5 000 FCFA<span class="text-base font-normal text-gray-400">/mois</span></div>
                @if($currentPlan === 'pro' && $planExpiresAt)
                    <p class="text-xs mt-1 {{ $planExpiresAt->isPast() ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                        {{ $planExpiresAt->isPast() ? 'Expiré le ' : 'Expire le ' }}{{ $planExpiresAt->format('d/m/Y') }}
                    </p>
                @endif
            </div>
            <div class="p-6 space-y-3">
                @foreach([
                    ['Espaces de travail illimités', true],
                    ['Projets illimités', true],
                    ['Membres illimités', true],
                    ['Tâches & priorités', true],
                    ['Sous-tâches', true],
                    ['Pièces jointes (20 Mo/fichier)', true],
                    ['Export PDF', true],
                    ['Diagramme de Gantt', true],
                    ['Rapports & Statistiques', true],
                ] as [$feature, $included])
                <div class="flex items-center gap-2.5 text-sm text-gray-700">
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ $feature }}
                </div>
                @endforeach
            </div>
            <div class="px-6 pb-6 space-y-3" x-data="{ open: false }">
                @if(!$isPro)
                    <button @click="open = true"
                            class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white py-2.5 rounded-xl text-sm font-semibold transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V10h16v8zm0-10H4V6h16v2z"/></svg>
                        Payer avec Wave
                    </button>

                    {{-- Modal Wave --}}
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-xl max-w-sm w-full p-6">
                            <div class="text-center mb-4">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Paiement Wave</h3>
                                <p class="text-sm text-gray-500 mt-1">Plateforme en cours de construction</p>
                            </div>

                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-center">
                                <p class="text-xs text-blue-600 font-medium mb-1">Envoyez <strong>5 000 FCFA</strong> sur Wave au :</p>
                                <p class="text-2xl font-extrabold text-blue-700 tracking-wider">+221 77 870 37 81</p>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 text-xs text-amber-700">
                                ⚠️ Après le paiement, contactez-nous via le formulaire de contact avec votre <strong>numéro de transaction Wave</strong>. Votre compte sera activé manuellement sous 24h.
                            </div>

                            <div class="space-y-2">
                                <a href="{{ route('contact.create') }}"
                                   class="block w-full bg-indigo-600 text-white text-center py-2.5 rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                                    Nous contacter après paiement
                                </a>
                                <button @click="open = false"
                                        class="w-full border border-gray-200 text-gray-500 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
                                    Fermer
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(!$trialUsed)
                    <form method="POST" action="{{ route('plans.trial') }}">
                        @csrf
                        <button type="submit"
                                class="w-full border border-indigo-300 text-indigo-600 hover:bg-indigo-50 py-2.5 rounded-xl text-sm font-semibold transition">
                            ✨ Essayer Pro gratuitement — 7 jours
                        </button>
                    </form>
                    @endif
                @else
                <button disabled class="w-full bg-indigo-100 text-indigo-400 py-2.5 rounded-xl text-sm font-medium cursor-not-allowed">
                    Plan actuel
                </button>
                @endif
            </div>
        </div>


    </div>

    {{-- FAQ --}}
    <div class="mt-12 text-center">
        <p class="text-gray-500 text-sm">
            💡 Les plans sont simulés pour la démonstration. Aucun paiement réel n'est requis.
        </p>
    </div>

</div>
@endsection
