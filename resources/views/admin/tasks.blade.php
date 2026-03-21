@extends('layouts.app')
@section('title', 'Admin — Tâches')

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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-rose-300 uppercase tracking-widest mb-1">Administration</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Gestion des tâches</h1>
                    <p class="text-sm text-slate-400 mt-1">
                        <span class="text-rose-300 font-medium">{{ $tasks->total() }} tâche(s)</span> au total
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
        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher une tâche..."
               class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 min-w-52">

        <select name="status" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
            <option value="">Tous les statuts</option>
            <option value="todo"        @selected($status === 'todo')>À faire</option>
            <option value="in_progress" @selected($status === 'in_progress')>En cours</option>
            <option value="review"      @selected($status === 'review')>En révision</option>
            <option value="done"        @selected($status === 'done')>Terminé</option>
        </select>

        <select name="priority" class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400 bg-white">
            <option value="">Toutes les priorités</option>
            <option value="urgent" @selected($priority === 'urgent')>Urgente</option>
            <option value="high"   @selected($priority === 'high')>Haute</option>
            <option value="medium" @selected($priority === 'medium')>Moyenne</option>
            <option value="low"    @selected($priority === 'low')>Faible</option>
        </select>

        <button type="submit" class="bg-rose-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-700 transition">
            Filtrer
        </button>
        @if($search || $status || $priority)
        <a href="{{ route('admin.tasks') }}" class="border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
            Effacer
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50/60 to-white">
            <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Toutes les tâches</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Tâche</th>
                        <th class="px-6 py-3 text-left">Projet</th>
                        <th class="px-6 py-3 text-left">Statut</th>
                        <th class="px-6 py-3 text-left">Priorité</th>
                        <th class="px-6 py-3 text-left">Assignée à</th>
                        <th class="px-6 py-3 text-left">Échéance</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50/60 transition-colors {{ $task->status === 'done' ? 'opacity-70' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                {{-- Indicateur statut --}}
                                @if($task->status === 'done')
                                    <div class="w-5 h-5 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                @elseif($task->status === 'in_progress')
                                    <div class="w-5 h-5 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                    </div>
                                @elseif($task->status === 'review')
                                    <div class="w-5 h-5 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                    </div>
                                @else
                                    <div class="w-5 h-5 bg-gray-100 rounded-full border-2 border-gray-300 flex-shrink-0"></div>
                                @endif
                                <span class="font-semibold text-gray-800 {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }} truncate max-w-[200px]">
                                    {{ $task->title }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->project)
                            <a href="{{ route('projects.show', $task->project) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-medium transition truncate block max-w-[140px]">
                                {{ $task->project->title }}
                            </a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($task->status === 'done')        bg-emerald-100 text-emerald-700
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-700
                                @elseif($task->status === 'review')   bg-amber-100 text-amber-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $task->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                                @if($task->priority === 'urgent') bg-red-100 text-red-700
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-700
                                @elseif($task->priority === 'medium') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-500 @endif">
                                {{ $task->priority_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->assignedTo)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($task->assignedTo->name, 0, 1)) }}
                                    </div>
                                    <span class="text-gray-600 text-xs truncate max-w-[100px]">{{ $task->assignedTo->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">Non assignée</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($task->due_date)
                                <span class="text-xs font-medium flex items-center gap-1
                                    {{ $task->is_overdue && $task->status !== 'done' ? 'text-red-500' : 'text-gray-500' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if($task->is_overdue && $task->status !== 'done')
                                        <span class="text-xs bg-red-100 text-red-600 px-1 rounded">En retard</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($task->project)
                            <a href="{{ route('projects.tasks.show', [$task->project, $task]) }}"
                               class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-indigo-50 border border-indigo-200 text-indigo-600 hover:bg-indigo-100 transition">
                                Voir
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                            Aucune tâche trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $tasks->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
