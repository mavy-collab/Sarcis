@extends('layouts.app')

@section('title', 'Historique — ' . $project->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-indigo-600">
                &larr; {{ $project->title }}
            </a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Historique d'activité
            </h1>
        </div>
        <span class="text-sm text-gray-400">{{ $activities->count() }} événement(s)</span>
    </div>

    @if($activities->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-sm">Aucune activité enregistrée pour ce projet.</p>
            <p class="text-gray-300 text-xs mt-1">Les actions futures apparaîtront ici.</p>
        </div>
    @else
        {{-- Groupement par date --}}
        @php
            $grouped = $activities->groupBy(fn($a) => $a->created_at->format('Y-m-d'));
        @endphp

        <div class="space-y-6">
            @foreach($grouped as $date => $items)
            <div>
                {{-- Date header --}}
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        @php
                            $d = \Carbon\Carbon::parse($date);
                            if ($d->isToday()) echo "Aujourd'hui";
                            elseif ($d->isYesterday()) echo "Hier";
                            else echo $d->translatedFormat('l d F Y');
                        @endphp
                    </span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                {{-- Événements du jour --}}
                <div class="bg-white rounded-2xl border border-gray-200 divide-y divide-gray-50">
                    @foreach($items as $activity)
                    @php $ic = $activity->icon; @endphp
                    <div class="flex items-start gap-4 px-5 py-4">
                        {{-- Icône --}}
                        <div class="w-8 h-8 rounded-full {{ $ic['bg'] }} flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 {{ $ic['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ic['icon'] }}"/>
                            </svg>
                        </div>

                        {{-- Contenu --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700">{{ $activity->description }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($activity->user)
                                    <span class="w-4 h-4 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $activity->user->name }}</span>
                                    <span class="text-gray-300">·</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $activity->created_at->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @if($activities->hasPages())
        <div class="mt-6">
            {{ $activities->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
