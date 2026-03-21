@extends('layouts.app')
@section('title', 'Gantt – ' . $project->title)

@section('content')
<div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-700 text-white px-6 py-5">
    <div class="max-w-7xl mx-auto flex items-center justify-between flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 text-indigo-200 text-xs mb-1">
                <a href="{{ route('workspaces.index') }}" class="hover:text-white">Espaces</a>
                <span>›</span>
                <a href="{{ route('projects.show', $project) }}" class="hover:text-white">{{ $project->title }}</a>
                <span>›</span>
                <span class="text-white">Gantt</span>
            </div>
            <h1 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Diagramme de Gantt
            </h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-1.5 text-sm px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition">
                ← Retour au projet
            </a>
            <a href="{{ route('projects.export', $project) }}" class="flex items-center gap-1.5 text-sm px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl transition">
                🖨️ Exporter
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

@if($project->tasks->count() === 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <p class="text-gray-500 font-medium">Aucune tâche avec date d'échéance pour afficher le Gantt.</p>
        <a href="{{ route('projects.show', $project) }}" class="mt-4 inline-block text-indigo-600 hover:underline text-sm">Ajouter des tâches →</a>
    </div>
@else

{{-- Legend --}}
<div class="flex flex-wrap gap-3 mb-4 text-xs font-medium">
    @foreach(['todo'=>['À faire','bg-gray-400'],'in_progress'=>['En cours','bg-blue-500'],'review'=>['En révision','bg-amber-500'],'done'=>['Terminé','bg-emerald-500']] as $s=>[$label,$color])
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm {{ $color }}"></span>{{ $label }}</span>
    @endforeach
    <span class="flex items-center gap-1.5 ml-4"><span class="w-3 h-3 rounded-sm bg-red-400 opacity-50"></span>En retard</span>
</div>

{{-- Gantt Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @php
        $tasks = $project->tasks->filter(fn($t) => $t->due_date);
        $allTasks = $project->tasks;

        // Determine date range
        $dates = $tasks->flatMap(fn($t) => [$t->due_date]);
        if($project->start_date) $dates->push($project->start_date);
        $minDate = ($project->start_date ?? $tasks->min('due_date') ?? now()->startOfMonth())->copy()->startOfWeek();
        $maxDate = ($tasks->max('due_date') ?? now()->endOfMonth())->copy()->addDays(7)->endOfWeek();

        $totalDays = $minDate->diffInDays($maxDate) + 1;
        $weeks = [];
        $cur = $minDate->copy();
        while($cur <= $maxDate) {
            $weeks[] = $cur->copy();
            $cur->addWeek();
        }
    @endphp

    {{-- Header: week labels --}}
    <div class="flex border-b border-gray-100">
        <div class="w-56 flex-shrink-0 px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider border-r border-gray-100 bg-gray-50">
            Tâche
        </div>
        <div class="flex-1 overflow-x-auto">
            <div class="flex" style="min-width: {{ count($weeks) * 120 }}px">
                @foreach($weeks as $week)
                <div class="flex-1 text-center text-xs text-gray-400 py-3 border-r border-gray-50 font-medium">
                    {{ $week->format('d/m') }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Today marker line --}}
    @php
        $todayOffset = now()->startOfDay()->diffInDays($minDate);
        $todayPct = min(100, max(0, ($todayOffset / $totalDays) * 100));
    @endphp

    {{-- Rows --}}
    @foreach($allTasks as $task)
    @php
        $start = $project->start_date ?? $minDate;
        $end   = $task->due_date ?? $maxDate;
        $startOff = max(0, $minDate->diffInDays($start));
        $endOff   = min($totalDays, $minDate->diffInDays($end) + 1);
        $leftPct  = ($startOff / $totalDays) * 100;
        $widthPct = max(1.5, (($endOff - $startOff) / $totalDays) * 100);
        $barColor = match($task->status) {
            'done'        => '#10b981',
            'in_progress' => '#3b82f6',
            'review'      => '#f59e0b',
            default       => '#9ca3af',
        };
        if($task->is_overdue) $barColor = '#f87171';
    @endphp
    <div class="flex border-b border-gray-50 hover:bg-gray-50 transition group">
        {{-- Task name --}}
        <div class="w-56 flex-shrink-0 px-4 py-3 border-r border-gray-100">
            <a href="{{ route('projects.tasks.show', [$project, $task]) }}"
               class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition truncate block">
                {{ $task->title }}
            </a>
            <div class="flex items-center gap-1 mt-0.5">
                <span class="text-xs text-gray-400">{{ $task->status_label }}</span>
                @if($task->is_overdue)
                    <span class="text-xs text-red-500 font-medium">· En retard</span>
                @endif
            </div>
        </div>

        {{-- Bar --}}
        <div class="flex-1 relative overflow-x-auto" style="min-width: {{ count($weeks) * 120 }}px">
            {{-- Grid lines --}}
            <div class="absolute inset-0 flex pointer-events-none">
                @foreach($weeks as $w)
                <div class="flex-1 border-r border-gray-50"></div>
                @endforeach
            </div>

            {{-- Today line --}}
            <div class="absolute top-0 bottom-0 w-px bg-red-400 opacity-40 pointer-events-none z-10" style="left: {{ $todayPct }}%"></div>

            {{-- Task bar --}}
            @if($task->due_date)
            <div class="absolute top-1/2 -translate-y-1/2 h-6 rounded-full shadow-sm flex items-center px-2 z-20 cursor-pointer group-hover:shadow-md transition"
                 style="left: {{ $leftPct }}%; width: {{ $widthPct }}%; background-color: {{ $barColor }}; min-width: 24px;"
                 title="{{ $task->title }} — {{ $task->due_date->format('d/m/Y') }}">
                <span class="text-white text-xs font-medium truncate leading-none">{{ $task->due_date->format('d/m') }}</span>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
@endsection
