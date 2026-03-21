@extends('layouts.app')
@section('title', 'Admin — Projets')

@section('content')

{{-- Hero Header --}}
<div class="relative bg-gradient-to-br from-slate-900 via-rose-950 to-red-950 text-white overflow-hidden">
    <div class="absolute top-0 left-1/4 w-72 h-72 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex items-center justify-between gap-6 flex-wrap">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center shadow-xl border border-white/10 flex-shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-rose-300 uppercase tracking-widest mb-1">Administration</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Gestion des projets</h1>
                    <p class="text-sm text-slate-400 mt-1">
                        <span class="text-rose-300 font-medium">{{ $projects->total() }} projet(s)</span> au total
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/15 backdrop-blur-sm text-white font-semibold px-4 py-2.5 rounded-xl transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Retour
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- Filtres --}}
    <form method="GET" class="mb-6 flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un projet..."
               class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 min-w-48">
        <select name="status" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
            <option value="">Tous les statuts</option>
            <option value="active"    @selected($status === 'active')>En cours</option>
            <option value="planning"  @selected($status === 'planning')>Planification</option>
            <option value="on_hold"   @selected($status === 'on_hold')>En pause</option>
            <option value="completed" @selected($status === 'completed')>Terminé</option>
            <option value="cancelled" @selected($status === 'cancelled')>Annulé</option>
        </select>
        <button type="submit" class="bg-rose-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-700 transition">
            Filtrer
        </button>
        @if($search || $status)
        <a href="{{ route('admin.projects') }}" class="border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
            Effacer
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50/60 to-white">
            <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Tous les projets</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Projet</th>
                        <th class="px-6 py-3 text-left">Propriétaire</th>
                        <th class="px-6 py-3 text-left">Statut</th>
                        <th class="px-6 py-3 text-left">Progression</th>
                        <th class="px-6 py-3 text-left">Tâches</th>
                        <th class="px-6 py-3 text-left">Créé</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($projects as $project)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0
                                    @if($project->status === 'active') bg-emerald-100 text-emerald-700
                                    @elseif($project->status === 'planning') bg-blue-100 text-blue-700
                                    @elseif($project->status === 'on_hold') bg-amber-100 text-amber-700
                                    @else bg-gray-100 text-gray-500 @endif">
                                    {{ strtoupper(substr($project->title, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="font-semibold text-gray-800 hover:text-indigo-600 transition">
                                        {{ $project->title }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        @if($project->is_public)
                                            <span class="text-xs text-emerald-500">Public</span>
                                        @else
                                            <span class="text-xs text-gray-400">Privé</span>
                                        @endif
                                        @if($project->is_locked)
                                            <span class="text-xs text-amber-500">Verrouillé</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($project->owner->name, 0, 1)) }}
                                </div>
                                <span class="text-gray-600 text-sm">{{ $project->owner->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($project->status === 'active') bg-emerald-100 text-emerald-700
                                @elseif($project->status === 'planning') bg-blue-100 text-blue-700
                                @elseif($project->status === 'on_hold') bg-amber-100 text-amber-700
                                @elseif($project->status === 'completed') bg-gray-100 text-gray-600
                                @else bg-red-100 text-red-700 @endif">
                                {{ $project->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 min-w-[80px]">
                                <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full
                                        @if($project->progress >= 100) bg-emerald-500
                                        @elseif($project->progress >= 50) bg-indigo-500
                                        @else bg-amber-400 @endif"
                                        style="width: {{ $project->progress }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-gray-600">{{ $project->progress }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-semibold">{{ $project->tasks_count }}</td>
                        <td class="px-6 py-4 text-xs text-gray-400">{{ $project->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-indigo-50 border border-indigo-200 text-indigo-600 hover:bg-indigo-100 transition">
                                    Voir
                                </a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                      onsubmit="return confirm('Supprimer définitivement ce projet et toutes ses tâches ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $projects->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
