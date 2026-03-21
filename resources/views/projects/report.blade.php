@extends('layouts.app')
@section('title', 'Rapport – ' . $project->title)

@section('content')
<div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white px-6 py-5">
    <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 text-indigo-200 text-xs mb-1">
                <a href="{{ route('workspaces.index') }}" class="hover:text-white">Espaces</a>
                <span>›</span>
                <a href="{{ route('projects.show', $project) }}" class="hover:text-white">{{ $project->title }}</a>
                <span>›</span>
                <span class="text-white">Rapport</span>
            </div>
            <h1 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Rapport & Statistiques
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-1.5 text-sm px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition">
                ← Retour au projet
            </a>
            <a href="{{ route('projects.export', $project) }}" class="flex items-center gap-1.5 text-sm px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition">
                🖨️ Exporter PDF
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @php $total = array_sum($taskStats); @endphp
        @foreach([
            ['Tâches totales',  $total,              'bg-indigo-50',  'text-indigo-600',  'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['Terminées',       $taskStats['done'],  'bg-emerald-50', 'text-emerald-600', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['En retard',       $overdueTasks->count(), 'bg-red-50',  'text-red-600',     'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Progression',     $project->progress . '%', 'bg-violet-50','text-violet-600','M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
        ] as [$label, $value, $bg, $color, $icon])
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 {{ $bg }} rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-extrabold text-gray-800">{{ $value }}</div>
                <div class="text-xs text-gray-400 font-medium">{{ $label }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Statut pie --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                Répartition par statut
            </h3>
            <div class="flex items-center gap-6">
                <canvas id="statusChart" width="160" height="160" style="max-width:160px;max-height:160px;"></canvas>
                <div class="space-y-2 flex-1">
                    @foreach([
                        ['À faire', $taskStats['todo'], '#9ca3af'],
                        ['En cours', $taskStats['in_progress'], '#3b82f6'],
                        ['Révision', $taskStats['review'], '#f59e0b'],
                        ['Terminé', $taskStats['done'], '#10b981'],
                    ] as [$label, $count, $color])
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $color }}"></span>
                            <span class="text-gray-600">{{ $label }}</span>
                        </span>
                        <span class="font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Priorité bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                Répartition par priorité
            </h3>
            <canvas id="priorityChart" height="150"></canvas>
        </div>

    </div>

    {{-- Charge par membre --}}
    @if($memberWorkload->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
            <span class="w-2 h-2 bg-violet-500 rounded-full"></span>
            Charge de travail par membre
        </h3>
        <div class="space-y-3">
            @foreach($memberWorkload as $memberId => $data)
            @php $pct = $data['count'] > 0 ? round(($data['done'] / $data['count']) * 100) : 0; @endphp
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($data['name'], 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $data['name'] }}</span>
                        <span class="text-xs text-gray-500">{{ $data['done'] }}/{{ $data['count'] }} tâches · {{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-indigo-500 transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Temps par collaborateur --}}
    @if($timeByMember->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" x-data="{ open: null }">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700">Temps passé par collaborateur</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($timeByMember as $uid => $data)
            @php
                $totalFmt = \App\Models\TimeLog::formatSeconds($data['total']);
                $isOpen   = 'member_' . $uid;
            @endphp
            <div>
                {{-- Ligne principale --}}
                <button type="button"
                        class="w-full flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition text-left"
                        @click="open = open === '{{ $isOpen }}' ? null : '{{ $isOpen }}'">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($data['name'], 0, 1)) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ $data['name'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">{{ $totalFmt }}</span>
                        <span class="text-xs text-gray-400">{{ $data['tasks']->count() }} tâche(s)</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform"
                             :class="open === '{{ $isOpen }}' ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </button>
                {{-- Détail par tâche --}}
                <div x-show="open === '{{ $isOpen }}'" x-transition class="bg-gray-50 border-t border-gray-100">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wide">
                                <th class="text-left px-12 py-2 font-medium">Tâche</th>
                                <th class="text-right px-5 py-2 font-medium">Temps</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($data['tasks'] as $tid => $taskData)
                            <tr class="hover:bg-white transition">
                                <td class="px-12 py-2 text-gray-600">{{ $taskData['title'] }}</td>
                                <td class="px-5 py-2 text-right font-semibold text-gray-700">
                                    {{ \App\Models\TimeLog::formatSeconds($taskData['duration']) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Overdue tasks --}}
    @if($overdueTasks->count() > 0)
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-red-50 border-b border-red-100 flex items-center gap-2">
            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h3 class="text-sm font-semibold text-red-700">Tâches en retard ({{ $overdueTasks->count() }})</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($overdueTasks as $task)
            <div class="flex items-center justify-between px-5 py-3">
                <a href="{{ route('projects.tasks.show', [$project, $task]) }}"
                   class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition">
                    {{ $task->title }}
                </a>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="text-red-500 font-medium">{{ $task->due_date->format('d/m/Y') }}</span>
                    @if($task->assignedTo)
                        <span>{{ $task->assignedTo->name }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- Données PHP passées via data-attributes (pas d'inline JS) --}}
<div id="chartData" hidden
    data-todo="{{ $taskStats['todo'] }}"
    data-in-progress="{{ $taskStats['in_progress'] }}"
    data-review="{{ $taskStats['review'] }}"
    data-done="{{ $taskStats['done'] }}"
    data-urgent="{{ $priorityStats['urgent'] }}"
    data-high="{{ $priorityStats['high'] }}"
    data-medium="{{ $priorityStats['medium'] }}"
    data-low="{{ $priorityStats['low'] }}"
></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/project-report-charts.js') }}"></script>
@endsection
