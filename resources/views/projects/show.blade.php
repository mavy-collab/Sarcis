@extends('layouts.app')

@section('title', $project->title)

@section('content')
@php
    $statusConfig = [
        'active'    => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'label' => $project->status_label],
        'planning'  => ['bg' => 'bg-blue-500',    'light' => 'bg-blue-50',    'text' => 'text-blue-700',    'border' => 'border-blue-200',    'label' => $project->status_label],
        'on_hold'   => ['bg' => 'bg-amber-500',   'light' => 'bg-amber-50',   'text' => 'text-amber-700',   'border' => 'border-amber-200',   'label' => $project->status_label],
        'completed' => ['bg' => 'bg-gray-400',    'light' => 'bg-gray-50',    'text' => 'text-gray-600',    'border' => 'border-gray-200',    'label' => $project->status_label],
        'cancelled' => ['bg' => 'bg-red-500',     'light' => 'bg-red-50',     'text' => 'text-red-700',     'border' => 'border-red-200',     'label' => $project->status_label],
    ];
    $sc = $statusConfig[$project->status] ?? $statusConfig['planning'];
    $totalTasks = collect($tasksByStatus)->flatten()->count();
@endphp

<div x-data="{ showAddTask: false, showAddMember: false, selectedLabel: null }">

{{-- ===== HERO HEADER ===== --}}
<div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-indigo-200 text-sm mb-5">
            <a href="{{ route('workspaces.index') }}" class="hover:text-white transition">Espaces</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @if($project->workspace)
                <a href="{{ route('workspaces.show', $project->workspace) }}" class="hover:text-white transition">{{ $project->workspace->name }}</a>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            @endif
            <span class="text-white font-medium">{{ $project->title }}</span>
        </div>

        {{-- Title + Actions --}}
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 flex-wrap mb-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight truncate">{{ $project->title }}</h1>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm border border-white/30">
                        <span class="w-1.5 h-1.5 rounded-full {{ $sc['bg'] }} bg-white"></span>
                        {{ $project->status_label }}
                    </span>
                    @if($project->is_locked)
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full bg-amber-400/20 border border-amber-300/40 text-amber-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Verrouillé
                        </span>
                    @endif
                </div>

                @if($project->description)
                    <p class="text-indigo-100 text-sm max-w-2xl leading-relaxed mb-3">{{ $project->description }}</p>
                @endif

                <div class="flex items-center gap-4 text-xs text-indigo-200 flex-wrap">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $project->owner->name }}
                    </span>
                    @if($project->start_date)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Début : {{ $project->start_date->format('d/m/Y') }}
                        </span>
                    @endif
                    @if($project->end_date)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Échéance : {{ $project->end_date->format('d/m/Y') }}
                        </span>
                    @endif
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        {{ $totalTasks }} tâche(s)
                    </span>
                </div>
            </div>

{{-- Action buttons --}}
@if($isProjectAdmin)
<div class="flex items-center gap-2 flex-nowrap flex-shrink-0">
    {{-- Toujours visibles (même pour non-propriétaire) --}}
    @if($project->hasPro())
        <!-- Pro features -->
        <a href="{{ route('projects.gantt', $project) }}" title="Gantt" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-3 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="hidden sm:inline">Gantt</span>
        </a>
        <a href="{{ route('projects.report', $project) }}" title="Rapport" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-3 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span class="hidden sm:inline">Rapport</span>
        </a>
        <a href="{{ route('projects.export', $project) }}" title="Export PDF" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-3 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span class="hidden sm:inline">PDF</span>
        </a>
        <a href="{{ route('projects.activity', $project) }}" title="Activité" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-3 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="hidden sm:inline">Activité</span>
        </a>
    @else
        <a href="{{ route('plans.index') }}" class="flex items-center gap-1.5 text-sm px-3 py-2 bg-amber-400/20 hover:bg-amber-400/30 border border-amber-300/40 text-amber-200 rounded-xl transition font-medium">⭐ Fonctions Pro</a>
    @endif

    {{-- Badge admin projet --}}
    @if(Auth::id() !== $project->user_id)
        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-violet-400/20 border border-violet-300/40 text-violet-200">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Admin projet
        </span>
    @endif

    {{-- Boutons standards (Membres et Modifier) --}}
    @if(!$project->is_locked)
        <button @click="showAddMember = !showAddMember" title="Membres" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span class="hidden sm:inline">Membres</span>
        </button>
        <a href="{{ route('projects.edit', $project) }}" title="Modifier" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl transition font-medium backdrop-blur-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span class="hidden sm:inline">Modifier</span>
        </a>
    @endif

    {{-- Boutons propriétaire (Verrouiller et Supprimer) --}}
    @if(Auth::id() === $project->user_id)
        <form method="POST" action="{{ route('projects.toggleLock', $project) }}" class="contents">
            @csrf @method('PATCH')
            <button type="submit" title="{{ $project->is_locked ? 'Déverrouiller' : 'Verrouiller' }}" class="flex items-center gap-1.5 text-sm px-2.5 sm:px-4 py-2 rounded-xl font-medium border transition backdrop-blur-sm {{ $project->is_locked ? 'bg-amber-400/20 border-amber-300/40 text-amber-200 hover:bg-amber-400/40' : 'bg-white/10 border-white/20 text-white hover:bg-white/20' }}">
                @if($project->is_locked)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">Déverrouiller</span>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span class="hidden sm:inline">Verrouiller</span>
                @endif
            </button>
        </form>

        @if(!$project->is_locked)
        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Supprimer définitivement ce projet et toutes ses tâches ?')" class="contents">
            @csrf @method('DELETE')
            <button type="submit" class="flex items-center gap-1.5 text-sm px-4 py-2 bg-red-500/20 hover:bg-red-500/40 border border-red-400/30 text-red-200 rounded-xl transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span class="hidden sm:inline">Supprimer</span>
            </button>
        </form>
        @endif
    @endif
</div>
@endif
        </div>

        {{-- Progress bar inside header --}}
        <div class="mt-6 bg-white/10 rounded-2xl px-5 py-4 backdrop-blur-sm border border-white/10">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-indigo-100 font-medium">Progression globale</span>
                <span class="text-lg font-extrabold">{{ $project->progress }}%</span>
            </div>
            <div class="w-full bg-white/20 rounded-full h-3">
                <div class="bg-white h-3 rounded-full transition-all shadow-sm" style="width: {{ $project->progress }}%"></div>
            </div>
        </div>

    </div>
</div>

{{-- ===== STATS CARDS ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 -mt-4">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'À faire',    'count' => $tasksByStatus['todo']->count(),        'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'text-gray-600',   'bg' => 'bg-gray-100',    'ring' => 'ring-gray-200'],
            ['label' => 'En cours',   'count' => $tasksByStatus['in_progress']->count(), 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',                                                                                                           'color' => 'text-blue-600',   'bg' => 'bg-blue-50',     'ring' => 'ring-blue-200'],
            ['label' => 'Révision',   'count' => $tasksByStatus['review']->count(),       'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'color' => 'text-amber-600',  'bg' => 'bg-amber-50',    'ring' => 'ring-amber-200'],
            ['label' => 'Terminé',    'count' => $tasksByStatus['done']->count(),         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                      'color' => 'text-emerald-600','bg' => 'bg-emerald-50',  'ring' => 'ring-emerald-200'],
        ] as $stat)
        <div class="bg-white rounded-2xl shadow-sm ring-1 {{ $stat['ring'] }} p-4 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $stat['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $stat['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $stat['count'] }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $stat['label'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ===== MAIN CONTENT ===== --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- Bandeau verrouillage --}}
    @if($project->is_locked)
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-3.5 rounded-2xl text-sm font-medium shadow-sm">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        Ce projet est verrouillé — aucune modification, ajout de tâche ou commentaire n'est possible.
    </div>
    @endif

    {{-- Ajouter un membre --}}
    <div x-show="showAddMember" x-cloak
         class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Gérer les membres
            </h3>
            <button @click="showAddMember = false" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('projects.members.add', $project) }}" class="flex gap-3 flex-wrap">
                @csrf
                <input type="email" name="email" placeholder="Email du membre" required
                       class="flex-1 min-w-48 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <select name="role" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                    @if(Auth::id() === $project->user_id)
                        <option value="admin">Admin projet</option>
                    @endif
                    <option value="member">Membre</option>
                    <option value="viewer">Observateur</option>
                </select>
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm hover:bg-indigo-700 transition font-medium shadow-sm">Ajouter</button>
            </form>
            @error('email')  <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            @error('role')   <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            @error('member') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

            @if($project->members->count() > 0)
            <div class="mt-5 border-t border-gray-100 pt-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Membres actuels</h4>
                <div class="space-y-2">
                    @foreach($project->members as $member)
                    @php
                        $roleBadge = match($member->pivot->role) {
                            'admin'  => ['label' => 'Admin projet', 'class' => 'bg-violet-100 border-violet-200 text-violet-700'],
                            'member' => ['label' => 'Membre',       'class' => 'bg-gray-50 border-gray-200 text-gray-600'],
                            'viewer' => ['label' => 'Observateur',  'class' => 'bg-amber-50 border-amber-200 text-amber-600'],
                            default  => ['label' => $member->pivot->role, 'class' => 'bg-gray-50 border-gray-200 text-gray-600'],
                        };
                    @endphp
                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-xl text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">{{ $member->name }}</span>
                                <span class="text-gray-400 ml-1 text-xs">{{ $member->email }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs border px-2 py-0.5 rounded-full font-medium {{ $roleBadge['class'] }}">
                                {{ $roleBadge['label'] }}
                            </span>
                            @if(Auth::id() === $project->user_id || $member->pivot->role !== 'admin')
                            <form method="POST" action="{{ route('projects.members.remove', [$project, $member]) }}"
                                  onsubmit="return confirm('Retirer {{ $member->name }} du projet ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition text-xs font-medium">Retirer</button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Bouton + Formulaire nouvelle tâche --}}
    @auth
    @php
        $currentMember = $project->members->firstWhere('id', Auth::id());
        $currentRole   = Auth::id() === $project->user_id ? 'owner' : ($currentMember?->pivot->role ?? null);
        $canWrite      = in_array($currentRole, ['owner', 'admin', 'member']);
    @endphp
    @if($currentRole !== null)
    @if(!$project->is_locked && $canWrite)

    <div class="flex items-center gap-3">
        <button @click="showAddTask = !showAddTask"
                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle tâche
        </button>
    </div>

    <div x-show="showAddTask" x-cloak
         class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Créer une tâche
            </h3>
            <button @click="showAddTask = false" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5">
            <form method="POST" action="{{ route('projects.tasks.store', $project) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Titre *</label>
                        <input type="text" name="title" placeholder="Titre de la tâche" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Description</label>
                        <textarea name="description" rows="2" placeholder="Description optionnelle..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Statut</label>
                        <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="todo">À faire</option>
                            <option value="in_progress">En cours</option>
                            <option value="review">En révision</option>
                            <option value="done">Terminé</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Priorité</label>
                        <select name="priority" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="low">Faible</option>
                            <option value="medium" selected>Moyenne</option>
                            <option value="high">Haute</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Date d'échéance</label>
                        <input type="date" name="due_date"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Assignée à</label>
                        <select name="assigned_to" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <option value="">Non assignée</option>
                            <option value="{{ $project->owner->id }}">{{ $project->owner->name }} (propriétaire)</option>
                            @foreach($project->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl text-sm hover:bg-indigo-700 transition font-semibold shadow-sm">Créer la tâche</button>
                    <button type="button" @click="showAddTask = false" class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition font-medium">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    @endif
    @endif
    @endauth

    {{-- ===== PIÈCES JOINTES (Pro) ===== --}}
    @if($project->hasPro())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-50 to-indigo-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                Pièces jointes
                @php $attachments = $project->attachments ?? collect(); @endphp
                <span class="bg-violet-100 text-violet-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ $attachments->count() }}</span>
            </h3>
        </div>
        <div class="p-5">
            @if(!$project->is_locked)
            <form method="POST" action="{{ route('projects.attachments.store', $project) }}" enctype="multipart/form-data" class="flex gap-3 flex-wrap mb-4">
                @csrf
                <input type="file" name="file" required
                       class="flex-1 min-w-0 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-violet-50 file:text-violet-700">
                <button type="submit" class="bg-violet-600 text-white px-5 py-2 rounded-xl text-sm hover:bg-violet-700 transition font-medium shadow-sm flex-shrink-0">
                    Uploader
                </button>
            </form>
            @if($errors->has('file'))
                <p class="text-red-500 text-xs mb-3">{{ $errors->first('file') }}</p>
            @endif
            @endif

            @if($attachments->count() > 0)
            <div class="space-y-2">
                @foreach($attachments as $att)
                <div class="flex items-center justify-between py-2.5 px-3 bg-gray-50 rounded-xl group">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 bg-violet-100 text-violet-600 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold">
                            {{ strtoupper(pathinfo($att->file_name, PATHINFO_EXTENSION)) ?: '?' }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ $att->url }}" target="_blank"
                               class="text-sm font-medium text-gray-700 hover:text-violet-600 transition truncate block">
                                {{ $att->file_name }}
                            </a>
                            <span class="text-xs text-gray-400">{{ $att->file_size_formatted }} · {{ $att->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @if(!$project->is_locked && (Auth::id() === $att->user_id || Auth::id() === $project->user_id))
                    <form method="POST" action="{{ route('projects.attachments.destroy', [$project, $att]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 transition text-xs opacity-0 group-hover:opacity-100">Supprimer</button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-400 text-center py-4">Aucune pièce jointe. Uploadez des fichiers pour les partager avec votre équipe.</p>
            @endif
        </div>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📎</span>
            <div>
                <p class="text-sm font-semibold text-amber-800">Pièces jointes — Plan Pro</p>
                <p class="text-xs text-amber-600">Uploadez des fichiers et partagez-les avec votre équipe.</p>
            </div>
        </div>
        <a href="{{ route('plans.index') }}" class="flex-shrink-0 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm">
            Passer au Pro
        </a>
    </div>
    @endif

    {{-- ===== KANBAN BOARD ===== --}}
    <div>
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tableau des tâches</h2>

            {{-- Filtre par étiquette --}}
            @if($project->labels->isNotEmpty())
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-gray-400 font-medium">Filtrer :</span>
                <button @click="selectedLabel = null"
                        :class="selectedLabel === null ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="text-xs px-2.5 py-1 rounded-full font-medium transition">
                    Toutes
                </button>
                @foreach($project->labels as $label)
                <button @click="selectedLabel = selectedLabel === {{ $label->id }} ? null : {{ $label->id }}"
                        :class="selectedLabel === {{ $label->id }} ? 'ring-2 ring-offset-1 ring-gray-400 opacity-100' : 'opacity-70 hover:opacity-100'"
                        class="text-xs px-2.5 py-1 rounded-full font-semibold transition"
                        style="background: {{ $label->color }}; color: {{ $label->text_color }}">
                    {{ $label->name }}
                </button>
                @endforeach
            </div>
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach([
                'todo'        => ['label' => 'À faire',     'color' => 'bg-gray-400',    'light' => 'bg-gray-50',    'text' => 'text-gray-600',   'badge' => 'bg-gray-200 text-gray-600'],
                'in_progress' => ['label' => 'En cours',    'color' => 'bg-blue-500',    'light' => 'bg-blue-50',    'text' => 'text-blue-600',   'badge' => 'bg-blue-100 text-blue-700'],
                'review'      => ['label' => 'En révision', 'color' => 'bg-amber-500',   'light' => 'bg-amber-50',   'text' => 'text-amber-600',  'badge' => 'bg-amber-100 text-amber-700'],
                'done'        => ['label' => 'Terminé',     'color' => 'bg-emerald-500', 'light' => 'bg-emerald-50', 'text' => 'text-emerald-600','badge' => 'bg-emerald-100 text-emerald-700'],
            ] as $status => $col)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">

                {{-- Column header --}}
                <div class="h-1 {{ $col['color'] }}"></div>
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between {{ $col['light'] }}">
                    <span class="font-semibold text-sm text-gray-700 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $col['color'] }}"></span>
                        {{ $col['label'] }}
                    </span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $col['badge'] }}">
                        {{ $tasksByStatus[$status]->count() }}
                    </span>
                </div>

                {{-- Tasks --}}
                <div class="p-3 space-y-2.5 flex-1">
                    @forelse($tasksByStatus[$status] as $task)
                    <div x-show="selectedLabel === null || {{ json_encode($task->labels->pluck('id')->values()->toArray()) }}.includes(selectedLabel)"
                         x-data="quickTask('{{ route('projects.tasks.status', [$project, $task]) }}', '{{ $task->status }}')"
                         :class="done ? 'opacity-60' : ''"
                         class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all group
                                @if($task->is_overdue) border-l-4 border-l-red-400 @endif">
                        <div class="p-3.5">
                            {{-- Titre + checkbox --}}
                            <div class="flex items-start gap-2.5 mb-1.5">
                                @if($canWrite && !$project->is_locked)
                                <button @click.stop="toggle()"
                                        :disabled="loading"
                                        class="flex-shrink-0 mt-0.5 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all focus:outline-none"
                                        :class="done
                                            ? 'bg-emerald-500 border-emerald-500'
                                            : 'border-gray-300 hover:border-emerald-400'"
                                        title="Marquer comme terminée">
                                    <svg x-show="done" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <svg x-show="loading && !done" class="w-3 h-3 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                </button>
                                @endif
                                <a href="{{ route('projects.tasks.show', [$project, $task]) }}"
                                   class="font-semibold text-sm transition leading-snug flex-1"
                                   :class="done ? 'line-through text-gray-400' : 'text-gray-800 hover:text-indigo-600'">
                                    {{ $task->title }}
                                </a>
                            </div>

                            {{-- Labels --}}
                            @if($task->labels->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mb-2 {{ $canWrite ? 'ml-7' : '' }}">
                                @foreach($task->labels as $label)
                                <span class="px-1.5 py-0.5 rounded text-xs font-semibold"
                                      style="background: {{ $label->color }}; color: {{ $label->text_color }}">
                                    {{ $label->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif

                            <div class="flex items-center justify-between {{ $canWrite ? 'ml-7' : '' }}">
                                {{-- Priority badge --}}
                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($task->priority === 'urgent') bg-red-100 text-red-700
                                    @elseif($task->priority === 'high') bg-orange-100 text-orange-700
                                    @elseif($task->priority === 'medium') bg-blue-100 text-blue-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    @if($task->priority === 'urgent') <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    @elseif($task->priority === 'high') <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                                    @elseif($task->priority === 'medium') <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                    @else <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                    @endif
                                    {{ $task->priority_label }}
                                </span>

                                <div class="flex items-center gap-2">
                                    @if($task->is_recurring)
                                    <span title="{{ $task->recurrence_label }}" class="text-indigo-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </span>
                                    @endif
                                    @if($task->assignedTo)
                                    <span class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold" title="{{ $task->assignedTo->name }}">
                                        {{ strtoupper(substr($task->assignedTo->name, 0, 1)) }}
                                    </span>
                                    @endif
                                    @if($task->due_date)
                                    <span class="flex items-center gap-0.5 text-xs {{ $task->is_overdue ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $task->due_date->format('d/m') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-10 h-10 {{ $col['light'] }} rounded-xl flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 {{ $col['text'] }} opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <p class="text-xs text-gray-400">Aucune tâche</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
</div>
@endsection

