@extends('layouts.app')
@section('title', 'Mes espaces de travail')

@section('content')
<style>
    body { overflow: hidden; }
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div class="relative h-screen overflow-hidden flex flex-col">

    {{-- Image de fond plein écran --}}
    <img src="{{ asset('images/photos2.jpg') }}" alt="" class="fixed inset-0 w-full h-full object-cover object-center z-0">
    <div class="fixed inset-0 bg-black/50 z-0"></div>

    {{-- Barre supérieure --}}
    <div class="relative z-10 flex-shrink-0 flex items-center justify-between px-6 py-4 bg-black/30 backdrop-blur-sm border-b border-white/10">
        <div>
            <h1 class="text-xl font-bold text-white">Mes espaces de travail</h1>
            <div class="flex items-center gap-2 mt-0.5">
                <p class="text-white/50 text-xs">Faites défiler horizontalement pour voir tous vos espaces.</p>
                @if($maxWorkspaces === PHP_INT_MAX)
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-white/10 text-white/50">
                        {{ $workspaceCount }} espaces
                    </span>
                @else
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                        {{ $workspaceCount >= $maxWorkspaces ? 'bg-red-500/30 text-red-300 border border-red-400/30' : 'bg-white/10 text-white/50' }}">
                        {{ $workspaceCount }}/{{ $maxWorkspaces }} espaces
                    </span>
                @endif
            </div>
        </div>
        @if($maxWorkspaces === PHP_INT_MAX || $workspaceCount < $maxWorkspaces)
            <a href="{{ route('workspaces.create') }}"
               class="flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white px-4 py-2 rounded-xl transition font-semibold text-sm border border-white/20 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nouvel espace
            </a>
        @else
            <div class="flex items-center gap-2 bg-white/5 text-white/30 px-4 py-2 rounded-xl text-sm border border-white/10 cursor-not-allowed whitespace-nowrap" title="Limite du plan gratuit atteinte">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Limite atteinte
            </div>
        @endif
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="relative z-10 flex-shrink-0 px-6 pt-3">
        <div class="max-w-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-sm px-4 py-2.5 rounded-xl">
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('limit_error'))
    <div class="relative z-10 flex-shrink-0 px-6 pt-3">
        <div class="max-w-lg bg-amber-500/20 border border-amber-400/30 text-amber-300 text-sm px-4 py-2.5 rounded-xl flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            {{ session('limit_error') }}
        </div>
    </div>
    @endif

    {{-- Zone de défilement horizontal --}}
    <div class="relative z-10 flex-1 overflow-hidden">
        <div class="h-full flex items-start gap-5 px-6 overflow-x-auto overflow-y-hidden hide-scrollbar"
             style="padding-top: 24px; padding-bottom: 110px; align-items: flex-start;">

            @if($workspaces->isEmpty())
                <div class="flex items-center justify-center w-full">
                    <div class="bg-black/40 backdrop-blur-sm rounded-2xl border border-white/20 p-12 text-center w-80">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-white font-semibold mb-1">Aucun espace</h3>
                        <p class="text-white/40 text-xs mb-4">Créez votre premier espace de travail.</p>
                        <a href="{{ route('workspaces.create') }}"
                           class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-semibold hover:bg-indigo-500 transition">
                            Créer un espace
                        </a>
                    </div>
                </div>
            @else
                @foreach($workspaces as $workspace)
                @php $colors = $workspace->colorClasses; @endphp

                {{-- Carte workspace --}}
                <div class="flex-shrink-0 w-72 flex flex-col bg-black/45 backdrop-blur-sm rounded-2xl border border-white/15 overflow-hidden group hover:border-white/30 transition-all duration-200 hover:shadow-2xl hover:scale-[1.01]" style="max-height: calc(100vh - 180px);">

                    {{-- Header : image ou couleur --}}
                    <div class="relative flex-shrink-0 h-28 flex items-end overflow-hidden"
                         @if($workspace->background_url)
                             style="background-image: url('{{ $workspace->background_url }}'); background-size: cover; background-position: center;"
                         @endif>
                        @if($workspace->background_url)
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-black/10"></div>
                        @else
                            <div class="absolute inset-0 {{ $colors['bg'] }} opacity-90"></div>
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                        @endif
                        <div class="relative px-4 pb-3 flex items-end justify-between w-full">
                            <div class="min-w-0 flex-1">
                                <h2 class="text-white font-bold text-base leading-tight truncate drop-shadow">{{ $workspace->name }}</h2>
                                @if($workspace->description)
                                <p class="text-white/65 text-xs mt-0.5 truncate">{{ $workspace->description }}</p>
                                @endif
                            </div>
                            <a href="{{ route('workspaces.edit', $workspace) }}"
                               class="ml-2 flex-shrink-0 w-7 h-7 bg-white/20 hover:bg-white/35 rounded-lg flex items-center justify-center transition opacity-0 group-hover:opacity-100" title="Modifier">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>
                        </div>
                    </div>

                    {{-- Corps : liste projets --}}
                    <div class="flex-1 px-4 py-3 min-h-0">
                        @forelse($workspace->projects as $project)
                            <a href="{{ route('projects.show', $project) }}"
                               class="flex items-center gap-2 hover:bg-white/10 rounded-lg px-2 py-2 mb-1 transition group/item">
                                <span class="w-2 h-2 rounded-full {{ $colors['bg'] }} flex-shrink-0"></span>
                                <span class="text-white/80 text-xs font-medium truncate flex-1">{{ $project->title }}</span>
                                <svg class="w-3 h-3 text-white/20 group-hover/item:text-white/60 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @empty
                            <p class="text-white/25 text-xs text-center py-4">Aucun projet</p>
                        @endforelse
                    </div>

                    {{-- Footer --}}
                    <div class="flex-shrink-0 px-4 py-3 border-t border-white/10">
                        <a href="{{ route('workspaces.show', $workspace) }}"
                           class="flex items-center gap-2 text-white/50 hover:text-white text-xs font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                            Ouvrir l'espace · {{ $workspace->projects_count }} projet(s)
                        </a>
                    </div>
                </div>
                @endforeach

                {{-- Carte ajout nouvel espace --}}
                @if($workspaceCount < 5)
                <a href="{{ route('workspaces.create') }}"
                   class="flex-shrink-0 w-72 flex flex-col items-center justify-center gap-3 bg-black/20 hover:bg-black/35 border border-white/15 border-dashed rounded-2xl transition text-white/40 hover:text-white group">
                    <div class="w-12 h-12 bg-white/10 group-hover:bg-white/20 rounded-2xl flex items-center justify-center transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold">Nouvel espace</span>
                </a>
                @endif
            @endif
        </div>
    </div>

    {{-- Barre dock fixe en bas --}}
    <div class="fixed bottom-0 left-0 right-0 z-20 flex justify-center pb-5 px-4">
        <nav class="flex items-center gap-1 bg-black/60 backdrop-blur-xl border border-white/15 rounded-2xl px-3 py-2 shadow-2xl">

            <a href="{{ route('dashboard') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl text-white/50 hover:text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-semibold">Accueil</span>
            </a>

            <a href="{{ route('workspaces.index') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl text-white bg-white/15">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="text-[10px] font-semibold">Espaces</span>
            </a>

            <div class="w-px h-8 bg-white/10 mx-1"></div>

            <a href="{{ route('calendar.index') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl text-white/50 hover:text-white hover:bg-white/10 transition relative">
                <div class="relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-indigo-400 rounded-full"></span>
                </div>
                <span class="text-[10px] font-semibold">Calendrier</span>
            </a>

            <a href="{{ route('projects.index') }}"
               class="flex flex-col items-center gap-1 px-4 py-2 rounded-xl text-white/50 hover:text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="text-[10px] font-semibold">Projets</span>
            </a>

        </nav>
    </div>
</div>
@endsection
