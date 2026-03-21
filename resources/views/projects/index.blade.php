@extends('layouts.app')

@section('title', 'Mes Projets')

@section('content')
@php
    $totalProjects  = $projects->count();
    $totalTasks     = $projects->sum('tasks_count');
    $doneCount      = isset($grouped['completed']) ? $grouped['completed']->count() : 0;
    $activeCount    = isset($grouped['active'])    ? $grouped['active']->count()    : 0;
    $planningCount  = isset($grouped['planning'])  ? $grouped['planning']->count()  : 0;
    $holdCount      = isset($grouped['on_hold'])   ? $grouped['on_hold']->count()   : 0;
    $cancelledCount = isset($grouped['cancelled']) ? $grouped['cancelled']->count() : 0;

    $avgProgress = $totalProjects > 0
        ? (int) ($projects->avg(fn($p) => $p->progress))
        : 0;

    $sections = [
        'active'    => ['label' => 'En cours',      'dot' => 'bg-emerald-400', 'text' => 'text-emerald-400', 'ring' => 'ring-emerald-500/30', 'bar' => 'bg-emerald-500', 'badge' => 'bg-emerald-500/10 text-emerald-400'],
        'planning'  => ['label' => 'Planification',  'dot' => 'bg-blue-400',    'text' => 'text-blue-400',    'ring' => 'ring-blue-500/30',    'bar' => 'bg-blue-500',    'badge' => 'bg-blue-500/10 text-blue-400'],
        'on_hold'   => ['label' => 'En pause',       'dot' => 'bg-amber-400',   'text' => 'text-amber-400',   'ring' => 'ring-amber-500/30',   'bar' => 'bg-amber-500',   'badge' => 'bg-amber-500/10 text-amber-400'],
        'completed' => ['label' => 'Terminés',       'dot' => 'bg-slate-500',   'text' => 'text-slate-500',   'ring' => 'ring-slate-500/20',   'bar' => 'bg-slate-500',   'badge' => 'bg-slate-700 text-slate-400'],
        'cancelled' => ['label' => 'Annulés',        'dot' => 'bg-rose-400',    'text' => 'text-rose-400',    'ring' => 'ring-rose-500/30',    'bar' => 'bg-rose-500',    'badge' => 'bg-rose-500/10 text-rose-400'],
    ];
@endphp

<div class="min-h-screen bg-slate-950 text-white">
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- ── HEADER ── --}}
    <div class="flex items-center justify-between gap-4 flex-wrap">
        <div>
            <p class="text-slate-500 text-xs uppercase tracking-widest font-semibold mb-1">Espace de travail</p>
            <h1 class="text-2xl font-extrabold tracking-tight">Mes Projets</h1>
        </div>
        <a href="{{ route('workspaces.index') }}"
           class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition shadow-lg shadow-indigo-900/40">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau projet
        </a>
    </div>

    {{-- ── STATS + CHARTS ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Stats 2x2 animées --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 hover:border-indigo-500/30 transition"
                 x-data="counter({{ $totalProjects }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Total</div>
                <div class="text-3xl font-black text-white mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 hover:border-emerald-500/30 transition"
                 x-data="counter({{ $activeCount }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Actifs</div>
                <div class="text-3xl font-black text-emerald-400 mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 hover:border-violet-500/30 transition"
                 x-data="counter({{ $doneCount }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Terminés</div>
                <div class="text-3xl font-black text-violet-400 mt-2" x-text="value"></div>
            </div>
            <div class="bg-slate-900 border border-white/5 rounded-2xl p-4 hover:border-sky-500/30 transition"
                 x-data="counter({{ $totalTasks }})" x-init="start()">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold">Tâches</div>
                <div class="text-3xl font-black text-sky-400 mt-2" x-text="value"></div>
            </div>
        </div>

        {{-- Donut statuts --}}
        <div class="bg-slate-900 border border-white/5 rounded-2xl p-5">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Statuts des projets</div>
            @if($totalProjects > 0)
            <div class="flex items-center gap-5">
                <div class="relative w-28 h-28 flex-shrink-0">
                    <canvas id="statusDonut" width="112" height="112"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xl font-black text-white">{{ $totalProjects }}</span>
                        <span class="text-[9px] text-slate-500">projets</span>
                    </div>
                </div>
                <div class="space-y-1.5 flex-1 text-xs">
                    @foreach([
                        [$activeCount,    'En cours',     'bg-emerald-500', 'text-emerald-400'],
                        [$planningCount,  'Planification','bg-blue-500',    'text-blue-400'],
                        [$holdCount,      'En pause',     'bg-amber-500',   'text-amber-400'],
                        [$doneCount,      'Terminés',     'bg-slate-500',   'text-slate-400'],
                        [$cancelledCount, 'Annulés',      'bg-rose-500',    'text-rose-400'],
                    ] as [$count, $label, $dot, $text])
                    @if($count > 0)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $dot }} inline-block"></span>
                            <span class="text-slate-400">{{ $label }}</span>
                        </div>
                        <span class="font-bold {{ $text }}">{{ $count }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @else
            <div class="flex items-center justify-center h-28 text-slate-700 text-sm">Aucun projet</div>
            @endif
        </div>

        {{-- Progression moyenne --}}
        <div class="bg-slate-900 border border-white/5 rounded-2xl p-5">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Progression globale</div>
            @if($totalProjects > 0)
            {{-- Gauge --}}
            <div class="flex flex-col items-center justify-center py-2">
                <div class="relative w-32 h-32">
                    <canvas id="gaugeChart" width="128" height="128"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-black text-white" id="gaugeValue">0%</span>
                        <span class="text-[10px] text-slate-500">moyenne</span>
                    </div>
                </div>
            </div>
            {{-- Top projets --}}
            <div class="mt-3 space-y-1.5">
                @foreach($projects->sortByDesc('progress')->take(3) as $p)
                <div class="flex items-center gap-2" x-data="{ show: false }" x-intersect="show = true">
                    <span class="text-[10px] text-slate-500 w-20 truncate flex-shrink-0">{{ Str::limit($p->title, 12) }}</span>
                    <div class="flex-1 bg-slate-800 rounded-full h-1">
                        <div class="h-1 rounded-full bg-indigo-500 transition-all duration-700"
                             :style="show ? 'width: {{ $p->progress }}%' : 'width: 0%'"></div>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-400 tabular-nums w-8 text-right">{{ $p->progress }}%</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex items-center justify-center h-28 text-slate-700 text-sm">Aucun projet</div>
            @endif
        </div>
    </div>

    {{-- ── ÉTAT VIDE ── --}}
    @if($projects->isEmpty())
    <div class="bg-slate-900 border border-white/5 rounded-2xl p-20 text-center">
        <div class="w-14 h-14 rounded-2xl bg-slate-800 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-slate-300 mb-2">Aucun projet pour le moment</h3>
        <p class="text-slate-500 mb-6 text-sm">Créez un espace de travail, puis ajoutez votre premier projet.</p>
        <a href="{{ route('workspaces.index') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Aller à mes espaces
        </a>
    </div>

    @else

    {{-- ── SECTIONS PAR STATUT ── --}}
    @foreach($sections as $statusKey => $section)
        @if(isset($grouped[$statusKey]) && $grouped[$statusKey]->isNotEmpty())
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-2 h-2 rounded-full {{ $section['dot'] }}"></div>
                <h2 class="text-xs font-bold {{ $section['text'] }} uppercase tracking-widest">{{ $section['label'] }}</h2>
                <div class="flex-1 h-px bg-white/5"></div>
                <span class="text-xs font-semibold {{ $section['badge'] }} px-2.5 py-1 rounded-full">
                    {{ $grouped[$statusKey]->count() }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($grouped[$statusKey] as $project)
                <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 hover:shadow-xl hover:shadow-black/30 transition-all duration-300 group flex flex-col"
                     x-data="{ hovered: false }" @mouseenter="hovered = true">

                    <div class="h-0.5 {{ $section['bar'] }} opacity-60"></div>

                    <div class="p-5 flex-1 flex flex-col">

                        {{-- Titre + menu --}}
                        <div class="flex items-start justify-between mb-3 gap-2">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="text-sm font-bold text-slate-200 hover:text-white transition-colors block truncate">
                                    {{ $project->title }}
                                </a>
                                @if($project->is_locked)
                                <span class="inline-flex items-center gap-1 text-[10px] text-amber-400/80 mt-0.5 font-medium">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Verrouillé
                                </span>
                                @endif
                            </div>

                            @if($project->user_id === Auth::id())
                            <div x-data="{ open: false }" class="relative shrink-0">
                                <button @click="open = !open"
                                        class="w-7 h-7 flex items-center justify-center text-slate-600 hover:text-slate-300 hover:bg-white/5 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false" x-cloak
                                     class="absolute right-0 top-8 bg-slate-800 border border-white/10 rounded-xl shadow-2xl z-20 w-36 overflow-hidden">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-300 hover:bg-white/5 hover:text-white transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-300 hover:bg-white/5 hover:text-white transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </a>
                                    <div class="border-t border-white/5 mx-3"></div>
                                    <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                          x-data @submit.prevent="if(confirm('Supprimer ce projet définitivement ?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-400 hover:bg-rose-500/10 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Description --}}
                        @if($project->description)
                        <p class="text-xs text-slate-500 mb-4 line-clamp-2 leading-relaxed">{{ $project->description }}</p>
                        @endif

                        {{-- Progression animée --}}
                        <div class="mb-4" x-data="{ show: false }" x-intersect="show = true">
                            <div class="flex justify-between items-center mb-1.5">
                                <span class="text-[11px] font-medium text-slate-600">Progression</span>
                                <span class="text-[11px] font-extrabold tabular-nums {{ $project->progress >= 100 ? 'text-emerald-400' : ($project->progress >= 50 ? $section['text'] : 'text-slate-500') }}">
                                    {{ $project->progress }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="{{ $section['bar'] }} h-2 rounded-full opacity-80 transition-all duration-1000 ease-out"
                                     :style="show ? 'width: {{ $project->progress }}%' : 'width: 0%'"></div>
                            </div>
                        </div>

                        {{-- Dropdown statut rapide --}}
                        @if($project->user_id === Auth::id() && !$project->is_locked)
                        <form method="POST" action="{{ route('projects.updateStatus', $project) }}" class="mb-4">
                            @csrf @method('PATCH')
                            <div class="relative">
                                <select name="status" onchange="this.form.submit()"
                                        class="w-full text-xs font-semibold bg-slate-800 border border-white/10 text-slate-300 rounded-xl px-3 py-2 pr-7 appearance-none focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer transition hover:border-white/20">
                                    <option value="planning"  @selected($project->status === 'planning')>📋 Planification</option>
                                    <option value="active"    @selected($project->status === 'active')>🚀 En cours</option>
                                    <option value="on_hold"   @selected($project->status === 'on_hold')>⏸ En pause</option>
                                    <option value="completed" @selected($project->status === 'completed')>✅ Terminé</option>
                                    <option value="cancelled" @selected($project->status === 'cancelled')>❌ Annulé</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-2.5 flex items-center">
                                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </form>
                        @endif

                        <div class="flex-1"></div>

                        {{-- Pied --}}
                        <div class="flex items-center justify-between pt-3 border-t border-white/5 mt-2">
                            <span class="text-xs text-slate-600 font-medium">
                                {{ $project->tasks_count }} tâche{{ $project->tasks_count > 1 ? 's' : '' }}
                            </span>
                            @if($project->end_date)
                            <span class="text-[11px] flex items-center gap-1 {{ $project->end_date->isPast() && $project->status !== 'completed' ? 'text-rose-400 font-semibold' : 'text-slate-600' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $project->end_date->format('d/m/Y') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('projects.show', $project) }}"
                       class="block w-full text-center text-xs font-semibold py-2.5 bg-white/[0.02] text-slate-600 hover:bg-white/5 hover:text-slate-300 transition border-t border-white/5">
                        Ouvrir →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach

    @endif

</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = 'rgba(255,255,255,0.04)';

@if($totalProjects > 0)
// ── Donut : statuts ──
new Chart(document.getElementById('statusDonut'), {
    type: 'doughnut',
    data: {
        labels: ['En cours', 'Planification', 'En pause', 'Terminés', 'Annulés'],
        datasets: [{
            data: [{{ $activeCount }}, {{ $planningCount }}, {{ $holdCount }}, {{ $doneCount }}, {{ $cancelledCount }}],
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#64748b', '#f43f5e'],
            borderWidth: 0,
            hoverOffset: 6,
        }]
    },
    options: {
        cutout: '72%',
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label} : ${ctx.parsed}` } }
        },
        animation: { animateRotate: true, duration: 900, easing: 'easeInOutQuart' }
    }
});

// ── Gauge : progression moyenne ──
const avg = {{ $avgProgress }};
const gaugeEl = document.getElementById('gaugeChart');
const gaugeValueEl = document.getElementById('gaugeValue');

const gaugeChart = new Chart(gaugeEl, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [0, 100],
            backgroundColor: [
                avg >= 80 ? '#10b981' : avg >= 50 ? '#6366f1' : '#f59e0b',
                'rgba(255,255,255,0.04)'
            ],
            borderWidth: 0,
            hoverOffset: 0,
        }]
    },
    options: {
        cutout: '75%',
        rotation: -90,
        circumference: 180,
        plugins: { legend: { display: false }, tooltip: { enabled: false } },
        animation: {
            duration: 1000,
            easing: 'easeInOutQuart',
            onProgress(animation) {
                const progress = animation.currentStep / animation.numSteps;
                const current = Math.round(avg * progress);
                gaugeValueEl.textContent = current + '%';
            },
            onComplete() { gaugeValueEl.textContent = avg + '%'; }
        }
    }
});

// Animer les données du gauge
setTimeout(() => {
    gaugeChart.data.datasets[0].data = [avg, 100 - avg];
    gaugeChart.update();
}, 100);
@endif
</script>
@endpush
