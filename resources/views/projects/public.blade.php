@extends('layouts.app')

@section('title', 'Projets publics')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Projets publics</h1>
        <p class="text-gray-500 mt-1">Découvrez les projets partagés par la communauté.</p>
    </div>

    @if($projects->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
            <p class="text-gray-400">Aucun projet public pour l'instant.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
            <div class="bg-white rounded-xl border border-gray-200 hover:shadow-md transition overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            @auth
                            <a href="{{ route('projects.show', $project) }}"
                               class="text-lg font-semibold text-gray-800 hover:text-indigo-600 block">
                                {{ $project->title }}
                            </a>
                            @else
                            <span class="text-lg font-semibold text-gray-800 block">{{ $project->title }}</span>
                            @endauth
                            <span class="text-xs text-gray-400">Par {{ $project->owner->name }}</span>
                        </div>
                        <span class="text-xs px-2 py-1 rounded-full font-medium ml-2 shrink-0
                            @if($project->status === 'active') bg-green-100 text-green-700
                            @elseif($project->status === 'planning') bg-blue-100 text-blue-700
                            @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-700
                            @elseif($project->status === 'completed') bg-gray-100 text-gray-600
                            @else bg-red-100 text-red-700 @endif">
                            {{ $project->status_label }}
                        </span>
                    </div>

                    @if($project->description)
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $project->description }}</p>
                    @endif

                    <div class="mb-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>Progression</span>
                            <span>{{ $project->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $project->progress }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>{{ $project->tasks_count }} tâches</span>
                        @if($project->end_date)
                            <span>Fin: {{ $project->end_date->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
</div>
@endsection
