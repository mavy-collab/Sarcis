@extends('layouts.app')
@section('title', 'Administration')

@section('content')

{{-- Hero Header --}}
<div class="relative bg-gradient-to-br from-slate-900 via-rose-950 to-red-950 text-white overflow-hidden">
    <div class="absolute top-0 left-1/4 w-72 h-72 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center shadow-xl border border-white/10 flex-shrink-0">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-rose-300 uppercase tracking-widest mb-0.5">Espace administrateur</p>
                <h1 class="text-xl sm:text-3xl font-extrabold tracking-tight leading-tight">Tableau de bord</h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                    {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    &mdash;
                    <span class="text-rose-300 font-medium">{{ $stats['admins'] }} admin(s)</span>
                </p>
            </div>
        </div>

        {{-- Navigation scrollable sur mobile --}}
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide -mx-1 px-1">
            <a href="{{ route('admin.users') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-3 py-2 rounded-xl transition text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Utilisateurs
            </a>
            <a href="{{ route('admin.projects') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-3 py-2 rounded-xl transition text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                Projets
            </a>
            <a href="{{ route('admin.tasks') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-3 py-2 rounded-xl transition text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Tâches
            </a>
            <a href="{{ route('admin.security_logs') }}" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-3 py-2 rounded-xl transition text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Logs
            </a>
            <a href="{{ route('admin.contact') }}" class="relative flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-3 py-2 rounded-xl transition text-xs sm:text-sm whitespace-nowrap flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Messages
                @if($openContactsCount > 0)
                <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-indigo-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $openContactsCount }}</span>
                @endif
            </a>
        </div>

        {{-- Stats hero --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8">
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3.5 border border-white/10">
                <div class="text-2xl font-extrabold">{{ $stats['total_users'] }}</div>
                <div class="text-slate-400 text-xs mt-0.5 font-medium">Utilisateurs</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3.5 border border-white/10">
                <div class="text-2xl font-extrabold text-emerald-300">{{ $stats['active_projects'] }}</div>
                <div class="text-slate-400 text-xs mt-0.5 font-medium">Projets actifs</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3.5 border border-white/10">
                <div class="text-2xl font-extrabold text-sky-300">{{ $stats['total_tasks'] }}</div>
                <div class="text-slate-400 text-xs mt-0.5 font-medium">Tâches totales</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3.5 border border-white/10">
                <div class="text-2xl font-extrabold text-amber-300">{{ $stats['total_workspaces'] }}</div>
                <div class="text-slate-400 text-xs mt-0.5 font-medium">Espaces de travail</div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Stats cards row 1 --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total projets',    'value' => $stats['total_projects'],  'color' => 'text-indigo-600',  'bg' => 'bg-indigo-50',  'ring' => 'ring-indigo-100', 'icon' => 'M3 7h18M3 12h18M3 17h18'],
            ['label' => 'Tâches terminées', 'value' => $stats['completed_tasks'], 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'ring' => 'ring-emerald-100','icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Nouveaux ce mois', 'value' => $stats['new_users_month'], 'color' => 'text-blue-600',    'bg' => 'bg-blue-50',    'ring' => 'ring-blue-100',   'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'],
            ['label' => 'Administrateurs',  'value' => $stats['admins'],          'color' => 'text-rose-600',    'bg' => 'bg-rose-50',    'ring' => 'ring-rose-100',   'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ] as $card)
        <div class="bg-white rounded-2xl shadow-sm ring-1 {{ $card['ring'] }} p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $card['value'] }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $card['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Stats cards row 2 (nouvelles) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

        {{-- Inscrits aujourd'hui --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-violet-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $stats['new_users_today'] }}</div>
                <div class="text-xs text-gray-400 font-medium">Inscrits aujourd'hui</div>
            </div>
        </div>

        {{-- Emails non vérifiés --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-amber-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $stats['unverified_users'] }}</div>
                <div class="text-xs text-gray-400 font-medium">Emails non vérifiés</div>
            </div>
        </div>

        {{-- Taux de complétion --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-teal-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $stats['completion_rate'] }}%</div>
                <div class="text-xs text-gray-400 font-medium">Taux de complétion</div>
            </div>
        </div>

        {{-- Stockage utilisé --}}
        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-slate-100 p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $stats['storage_mb'] }} Mo</div>
                <div class="text-xs text-gray-400 font-medium">Stockage utilisé</div>
            </div>
        </div>

    </div>

    {{-- Inscriptions par méthode + Taux de complétion visuel --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        {{-- Méthode d'inscription --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Méthode d'inscription</h3>
            <div class="space-y-3">
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>📧 Email classique</span>
                        <span class="font-semibold text-gray-700">{{ $stats['email_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php $emailPct = $stats['total_users'] > 0 ? round($stats['email_users'] / $stats['total_users'] * 100) : 0; @endphp
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $emailPct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>🔵 Google OAuth</span>
                        <span class="font-semibold text-gray-700">{{ $stats['google_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php $googlePct = $stats['total_users'] > 0 ? round($stats['google_users'] / $stats['total_users'] * 100) : 0; @endphp
                        <div class="bg-red-400 h-2 rounded-full" style="width: {{ $googlePct }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>⚠️ Non vérifiés</span>
                        <span class="font-semibold text-amber-600">{{ $stats['unverified_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        @php $unverifiedPct = $stats['total_users'] > 0 ? round($stats['unverified_users'] / $stats['total_users'] * 100) : 0; @endphp
                        <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $unverifiedPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progression tâches --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-800 mb-4">Progression des tâches</h3>
            <div class="flex items-center justify-center">
                <div class="relative w-28 h-28">
                    <svg class="w-28 h-28 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3"
                            stroke-dasharray="{{ $stats['completion_rate'] }}, 100"
                            stroke-linecap="round"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-extrabold text-gray-800">{{ $stats['completion_rate'] }}%</span>
                        <span class="text-xs text-gray-400">complétées</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-center text-xs text-gray-500">
                <div class="bg-emerald-50 rounded-lg p-2">
                    <div class="font-bold text-emerald-700 text-base">{{ $stats['completed_tasks'] }}</div>
                    <div>Terminées</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-2">
                    <div class="font-bold text-gray-700 text-base">{{ $stats['total_tasks'] - $stats['completed_tasks'] }}</div>
                    <div>En cours</div>
                </div>
            </div>
        </div>

        {{-- Alertes sécurité --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-800">Alertes sécurité</h3>
                @if($stats['failed_logins_today'] > 0)
                <span class="text-xs bg-red-100 text-red-700 font-semibold px-2 py-1 rounded-full">{{ $stats['failed_logins_today'] }} aujourd'hui</span>
                @else
                <span class="text-xs bg-emerald-100 text-emerald-700 font-semibold px-2 py-1 rounded-full">RAS</span>
                @endif
            </div>
            <div class="space-y-2">
                @forelse($recentFailedLogins as $log)
                <div class="flex items-start gap-3 text-xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mt-1.5 flex-shrink-0"></span>
                    <div class="min-w-0">
                        <div class="text-gray-700 font-medium truncate">{{ $log->details ?: 'Tentative échouée' }}</div>
                        <div class="text-gray-400">{{ $log->ip_address }} &mdash; {{ $log->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Aucune tentative échouée récente</p>
                @endforelse
            </div>
            <a href="{{ route('admin.security_logs') }}" class="mt-4 block text-center text-xs text-rose-600 hover:text-rose-800 font-semibold transition">
                Voir tous les logs →
            </a>
        </div>

    </div>

    {{-- Derniers utilisateurs + Derniers projets --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Derniers utilisateurs --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50/60 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Derniers inscrits</h2>
                </div>
                <a href="{{ route('admin.users') }}" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1 transition">
                    Voir tout <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentUsers as $user)
                <div class="flex items-center gap-4 px-6 py-3.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0
                        {{ $user->is_admin ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</span>
                            @if($user->is_admin)
                                <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded-full font-semibold">Admin</span>
                            @endif
                            @if($user->google_id)
                                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-semibold">Google</span>
                            @endif
                            @if(!$user->email_verified_at)
                                <span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full font-semibold">Non vérifié</span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">{{ $user->email }}</span>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $user->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Derniers projets --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-white">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-800">Derniers projets</h2>
                </div>
                <a href="{{ route('admin.projects') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold flex items-center gap-1 transition">
                    Voir tout <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($recentProjects as $project)
                <div class="flex items-center gap-4 px-6 py-3.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0
                        @if($project->status === 'active') bg-emerald-100 text-emerald-700
                        @elseif($project->status === 'planning') bg-blue-100 text-blue-700
                        @elseif($project->status === 'on_hold') bg-amber-100 text-amber-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ strtoupper(substr($project->title, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-semibold text-gray-800 hover:text-indigo-600 transition truncate block">{{ $project->title }}</a>
                        <span class="text-xs text-gray-400">Par {{ $project->owner->name }}</span>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $project->created_at->diffForHumans() }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
