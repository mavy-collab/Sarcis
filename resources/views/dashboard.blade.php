@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Bonjour' : ($hour < 18 ? 'Bon après-midi' : 'Bonsoir');
    $urgentTasks = $myTasks->filter(fn($t) => $t->is_overdue && $t->status !== 'done');
    $pendingTasks = $myTasks->filter(fn($t) => $t->status !== 'done');
    $totalTasksForDonut = array_sum($tasksByStatus);
@endphp

<div class="min-h-screen bg-slate-950 text-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            @if(Auth::user()->avatar)
                <img src="{{ str_starts_with(Auth::user()->avatar, 'http') ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar) }}"
                     alt="{{ Auth::user()->name }}"
                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl object-cover ring-2 ring-white/10 flex-shrink-0">
            @else
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-base sm:text-lg font-extrabold ring-2 ring-white/10 flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <p class="text-slate-500 text-xs truncate">{{ $greeting }}, <span class="text-slate-300 font-semibold">{{ Auth::user()->name }}</span></p>
                <p class="text-slate-600 text-xs mt-0.5 hidden sm:block">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
                <p class="text-slate-600 text-xs mt-0.5 sm:hidden">{{ now()->locale('fr')->isoFormat('D MMM YYYY') }}</p>
            </div>
        </div>
        <a href="{{ route('projects.create') }}"
           class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-3 py-2 rounded-xl text-xs sm:text-sm transition shadow-lg shadow-indigo-900/40 flex-shrink-0">
            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Nouveau projet</span>
            <span class="sm:hidden">Nouveau</span>
        </a>
    </div>

    {{-- ── ROW 1 : STATS + DONUT ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Stats 2x2 --}}
        <div class="grid grid-cols-2 gap-3 lg:col-span-1">
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 flex flex-col justify-between hover:border-indigo-500/30 transition"
                 x-data="counter({{ $stats['total_projects'] }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Projets</div>
                <div class="text-3xl font-black text-white mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 flex flex-col justify-between hover:border-emerald-500/30 transition"
                 x-data="counter({{ $stats['active_projects'] }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Actifs</div>
                <div class="text-3xl font-black text-emerald-400 mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 flex flex-col justify-between hover:border-amber-500/30 transition"
                 x-data="counter({{ $stats['my_tasks'] }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">En cours</div>
                <div class="text-3xl font-black text-amber-400 mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 flex flex-col justify-between hover:border-violet-500/30 transition"
                 x-data="counter({{ $stats['completed_tasks'] }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Terminées</div>
                <div class="text-3xl font-black text-violet-400 mt-2" x-text="value"></div>
            </div>
        </div>

        {{-- Donut : tâches par statut --}}
        <div class="bg-slate-900 border border-white/5 rounded-2xl p-5 lg:col-span-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Répartition des tâches</div>
            @if($totalTasksForDonut > 0)
            <div class="flex items-center gap-6">
                <div class="relative w-32 h-32 flex-shrink-0">
                    <canvas id="donutChart" width="128" height="128"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-black text-white">{{ $totalTasksForDonut }}</span>
                        <span class="text-[10px] text-slate-500">tâches</span>
                    </div>
                </div>
                <div class="space-y-2 flex-1">
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-slate-600 inline-block"></span><span class="text-slate-400">À faire</span></div>
                        <span class="font-bold text-slate-300">{{ $tasksByStatus['todo'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span><span class="text-slate-400">En cours</span></div>
                        <span class="font-bold text-slate-300">{{ $tasksByStatus['in_progress'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span><span class="text-slate-400">En révision</span></div>
                        <span class="font-bold text-slate-300">{{ $tasksByStatus['review'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span><span class="text-slate-400">Terminées</span></div>
                        <span class="font-bold text-slate-300">{{ $tasksByStatus['done'] }}</span>
                    </div>
                </div>
            </div>
            @else
            <div class="flex items-center justify-center h-32 text-slate-700 text-sm">Aucune tâche assignée</div>
            @endif
        </div>

        {{-- Activité 7 jours --}}
        <div class="bg-slate-900 border border-white/5 rounded-2xl p-5 lg:col-span-1">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Activité — 7 derniers jours</div>
            <canvas id="activityChart" height="110"></canvas>
        </div>
    </div>

    {{-- ── ROW 2 : PROJETS + PRIORITÉS + URGENTES ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Prochaines échéances --}}
        <div class="lg:col-span-3 bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-white/5">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-sm font-bold text-white">Prochaines échéances</h2>
                </div>
                <span class="text-[10px] font-semibold text-slate-500 bg-slate-800 px-2 py-0.5 rounded-full">{{ $upcomingTasks->count() }} tâche(s)</span>
            </div>

            @if($upcomingTasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-slate-400 font-medium text-sm">Aucune échéance à venir</p>
                <p class="text-slate-600 text-xs mt-1">Toutes les tâches sont à jour.</p>
            </div>
            @else
            <div class="divide-y divide-white/[0.04]">
                @foreach($upcomingTasks as $task)
                @php
                    $daysLeft = now()->startOfDay()->diffInDays($task->due_date->startOfDay(), false);
                    $isOverdue = $daysLeft < 0;
                    $isToday   = $daysLeft === 0;
                    $isSoon    = $daysLeft > 0 && $daysLeft <= 3;
                @endphp
                <a href="{{ route('projects.tasks.show', [$task->project, $task]) }}"
                   class="flex items-center gap-3 px-5 py-3 hover:bg-white/[0.03] transition group">

                    {{-- Urgence indicator --}}
                    <div class="w-1.5 flex-shrink-0 self-stretch rounded-full
                        {{ $isOverdue ? 'bg-rose-500' : ($isToday ? 'bg-amber-400' : ($isSoon ? 'bg-amber-500/60' : 'bg-slate-700')) }}">
                    </div>

                    {{-- Priorité badge --}}
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-[10px] font-black
                        @if($task->priority === 'urgent') bg-rose-500/20 text-rose-400
                        @elseif($task->priority === 'high') bg-orange-500/20 text-orange-400
                        @elseif($task->priority === 'medium') bg-amber-500/20 text-amber-400
                        @else bg-slate-700 text-slate-500 @endif">
                        {{ strtoupper(substr($task->priority, 0, 1)) }}
                    </div>

                    {{-- Contenu --}}
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-slate-200 group-hover:text-white transition truncate">{{ $task->title }}</div>
                        <div class="text-[10px] text-slate-600 truncate mt-0.5">{{ $task->project->title }}</div>
                    </div>

                    {{-- Assigné --}}
                    @if($task->assignedTo)
                    <div class="w-6 h-6 rounded-full bg-indigo-500/20 flex items-center justify-center text-[9px] font-bold text-indigo-400 flex-shrink-0"
                         title="{{ $task->assignedTo->name }}">
                        {{ strtoupper(substr($task->assignedTo->name, 0, 1)) }}
                    </div>
                    @endif

                    {{-- Date --}}
                    <div class="text-right flex-shrink-0 w-16">
                        @if($isOverdue)
                            <div class="text-[10px] font-bold text-rose-400">{{ abs($daysLeft) }}j retard</div>
                        @elseif($isToday)
                            <div class="text-[10px] font-bold text-amber-400">Aujourd'hui</div>
                        @else
                            <div class="text-[10px] font-semibold text-slate-500">dans {{ $daysLeft }}j</div>
                        @endif
                        <div class="text-[9px] text-slate-700 mt-0.5">{{ $task->due_date->format('d/m') }}</div>
                    </div>
                </a>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Colonne droite : priorités + urgentes --}}
        <div class="lg:col-span-2 flex flex-col gap-4">

            {{-- Priorités --}}
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-5">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Tâches par priorité</div>
                @php $maxPrio = max(1, max($tasksByPriority)); @endphp
                <div class="space-y-3">
                    @foreach([
                        ['urgent', 'Urgente',  'bg-rose-500',   'text-rose-400'],
                        ['high',   'Haute',     'bg-orange-500', 'text-orange-400'],
                        ['medium', 'Moyenne',   'bg-amber-500',  'text-amber-400'],
                        ['low',    'Faible',    'bg-slate-600',  'text-slate-400'],
                    ] as [$key, $label, $barColor, $textColor])
                    <div x-data="{ show: false }" x-intersect="show = true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs {{ $textColor }} font-medium">{{ $label }}</span>
                            <span class="text-xs font-bold text-slate-400 tabular-nums">{{ $tasksByPriority[$key] }}</span>
                        </div>
                        <div class="bg-slate-800 rounded-full h-2">
                            <div class="{{ $barColor }} h-2 rounded-full transition-all duration-700 ease-out opacity-80"
                                 :style="show ? 'width: {{ $tasksByPriority[$key] / $maxPrio * 100 }}%' : 'width: 0%'"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Tâches urgentes --}}
            @if($urgentTasks->count() > 0)
            <div class="bg-rose-950/50 border border-rose-500/20 rounded-2xl p-4 flex-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
                    <span class="text-xs font-bold text-rose-300">{{ $urgentTasks->count() }} tâche(s) en retard</span>
                </div>
                <div class="space-y-2">
                    @foreach($urgentTasks->take(3) as $task)
                    <a href="{{ route('projects.tasks.show', [$task->project, $task]) }}"
                       class="flex items-center gap-2.5 bg-rose-900/30 hover:bg-rose-900/50 border border-rose-500/10 rounded-xl px-3 py-2 transition group">
                        <div class="w-1.5 h-1.5 rounded-full bg-rose-500 flex-shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs font-semibold text-rose-100 truncate">{{ $task->title }}</div>
                            <div class="text-[10px] text-rose-400/60 truncate">{{ $task->project->title }}</div>
                        </div>
                        <span class="text-[10px] text-rose-400 flex-shrink-0 font-semibold">{{ $task->due_date->format('d/m') }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @else
            {{-- Tâches assignées --}}
            <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden flex-1">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/5">
                    <h2 class="text-xs font-bold text-white">Mes tâches</h2>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-800 px-2 py-0.5 rounded-full">{{ $pendingTasks->count() }}</span>
                </div>
                @if($pendingTasks->isEmpty())
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-slate-500 text-xs">Tout est à jour !</p>
                </div>
                @else
                <div class="divide-y divide-white/[0.04] max-h-52 overflow-y-auto">
                    @foreach($pendingTasks->take(6) as $task)
                    <div class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-white/[0.03] transition group"
                         x-data="quickTask('{{ route('projects.tasks.status', [$task->project, $task]) }}', '{{ $task->status }}')"
                         :class="done ? 'opacity-50' : ''">
                        <button @click="toggle()" :disabled="loading"
                                class="flex-shrink-0 w-4 h-4 rounded-full border-2 flex items-center justify-center transition-all focus:outline-none"
                                :class="done ? 'bg-emerald-500 border-emerald-500' : 'border-slate-600 hover:border-emerald-400'">
                            <svg x-show="done" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                        <a href="{{ route('projects.tasks.show', [$task->project, $task]) }}" class="flex-1 min-w-0 flex items-center gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs transition truncate" :class="done ? 'line-through text-slate-600' : 'text-slate-300 group-hover:text-white'">{{ $task->title }}</div>
                                <div class="text-[10px] text-slate-600 truncate">{{ $task->project->title }}</div>
                            </div>
                            @if($task->due_date)
                            <span class="text-[10px] flex-shrink-0 {{ $task->is_overdue ? 'text-rose-400 font-bold' : 'text-slate-600' }}">{{ $task->due_date->format('d/m') }}</span>
                            @endif
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ── ROW 3 : COLLABORATIONS ── --}}
    @if($memberProjects->count() > 0)
    <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden">
        <div class="px-5 py-4 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white">Projets où je collabore</h2>
            <span class="text-[10px] font-semibold text-slate-500 bg-slate-800 px-2 py-0.5 rounded-full">{{ $memberProjects->count() }}</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-white/[0.04]">
            @foreach($memberProjects as $project)
            <a href="{{ route('projects.show', $project) }}"
               class="flex items-center gap-3 px-5 py-4 hover:bg-white/[0.03] transition group">
                <div class="w-9 h-9 rounded-xl bg-violet-500/10 flex items-center justify-center text-xs font-extrabold text-violet-400 flex-shrink-0">
                    {{ strtoupper(substr($project->title, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-300 group-hover:text-white transition truncate">{{ $project->title }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] text-slate-500 truncate">{{ $project->owner->name }}</span>
                        <span class="text-[10px] font-semibold text-violet-400 bg-violet-500/10 px-1.5 py-px rounded-md uppercase tracking-wide flex-shrink-0">{{ $project->pivot->role }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Couleurs Chart.js
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.05)';

// ── Donut : tâches par statut ──
@if($totalTasksForDonut > 0)
new Chart(document.getElementById('donutChart'), {
    type: 'doughnut',
    data: {
        labels: ['À faire', 'En cours', 'En révision', 'Terminées'],
        datasets: [{
            data: [
                {{ $tasksByStatus['todo'] }},
                {{ $tasksByStatus['in_progress'] }},
                {{ $tasksByStatus['review'] }},
                {{ $tasksByStatus['done'] }}
            ],
            backgroundColor: ['#475569', '#3b82f6', '#f59e0b', '#10b981'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.label} : ${ctx.parsed}`
                }
            }
        },
        animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' }
    }
});
@endif

// ── Ligne : activité 7 jours ──
new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($activityLabels) !!},
        datasets: [{
            label: 'Actions',
            data: {!! json_encode($activityData) !!},
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.12)',
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: '#6366f1',
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` ${ctx.parsed.y} action(s)`
                }
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 } } },
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 10 } },
                grid: { color: 'rgba(255,255,255,0.04)' }
            }
        },
        animation: { duration: 900, easing: 'easeInOutQuart' }
    }
});
</script>
@endpush
