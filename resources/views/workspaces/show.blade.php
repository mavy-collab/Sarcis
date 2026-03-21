@extends('layouts.app')
@section('title', $workspace->name)

@section('content')
@php
    $colors = $workspace->colorClasses;
    $projectCount = $workspace->projects->count();
    $projectLimitReached = $maxProjects !== PHP_INT_MAX && $projectCount >= $maxProjects;
@endphp

{{-- Header espace --}}
<div class="relative text-white overflow-hidden"
     @if($workspace->background_url)
         style="background-image: url('{{ $workspace->background_url }}'); background-size: cover; background-position: center;"
     @else
         style=""
     @endif>
    @if($workspace->background_url)
        <div class="absolute inset-0 bg-black/25"></div>
    @else
        <div class="absolute inset-0 {{ $colors['bg'] }}"></div>
    @endif
    <div class="relative max-w-7xl mx-auto px-4 py-8 flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('workspaces.index') }}" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white/60 text-xs font-medium uppercase tracking-wider">Espace de travail</p>
                <h1 class="text-2xl font-extrabold">{{ $workspace->name }}</h1>
                @if($workspace->description)
                    <p class="text-white/70 text-sm mt-0.5">{{ $workspace->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspaces.edit', $workspace) }}"
               class="flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Paramètres
            </a>
            @if(!$projectLimitReached)
            <a href="{{ route('projects.create', ['workspace_id' => $workspace->id]) }}"
               class="flex items-center gap-2 bg-white text-gray-800 text-sm font-semibold px-4 py-2 rounded-xl hover:bg-gray-50 transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouveau projet
            </a>
            @else
            <div class="flex items-center gap-2 bg-white/10 text-white/40 text-sm px-4 py-2 rounded-xl border border-white/20 cursor-not-allowed"
                 title="{{ $maxProjects }} projet(s) max par espace (plan gratuit)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                {{ $maxProjects }} projet(s) max
            </div>
            @endif
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 py-8">

    @if(session('limit_error'))
    <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        {{ session('limit_error') }}
    </div>
    @endif

    @if($workspace->projects->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-16 text-center">
            <div class="w-14 h-14 {{ $colors['light'] }} rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-gray-700 font-semibold mb-2">Aucun projet dans cet espace</h3>
            <p class="text-gray-400 text-sm mb-5">Créez votre premier projet pour commencer.</p>
            <a href="{{ route('projects.create', ['workspace_id' => $workspace->id]) }}"
               class="{{ $colors['bg'] }} text-white px-6 py-2.5 rounded-xl hover:opacity-90 transition font-semibold text-sm inline-block">
                Créer un projet
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($workspace->projects as $project)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
                <div class="h-1.5 {{ $colors['bg'] }}"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <a href="{{ route('projects.show', $project) }}"
                           class="font-semibold text-gray-800 hover:text-indigo-600 transition text-sm leading-snug flex-1">
                            {{ $project->title }}
                        </a>
                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full font-medium flex-shrink-0
                            @if($project->status === 'active') bg-emerald-100 text-emerald-700
                            @elseif($project->status === 'planning') bg-blue-100 text-blue-700
                            @elseif($project->status === 'on_hold') bg-amber-100 text-amber-700
                            @elseif($project->status === 'completed') bg-gray-100 text-gray-600
                            @else bg-red-100 text-red-700 @endif">
                            {{ $project->status_label }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                        </svg>
                        {{ $project->tasks_count }} tâche(s)
                    </div>

                    <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                        <div class="{{ $colors['bg'] }} h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-400">
                        <span>Progression</span>
                        <span class="font-semibold {{ $colors['text'] }}">{{ $project->progress }}%</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
