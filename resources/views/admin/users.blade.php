@extends('layouts.app')
@section('title', 'Admin — Utilisateurs')

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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-rose-300 uppercase tracking-widest mb-1">Administration</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Gestion des utilisateurs</h1>
                    <p class="text-sm text-slate-400 mt-1">
                        <span class="text-rose-300 font-medium">{{ $users->total() }} utilisateur(s)</span> au total
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

    {{-- Barre de recherche --}}
    <form method="GET" class="mb-6">
        <div class="flex gap-3 max-w-md">
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un utilisateur..."
                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-400">
            <button type="submit" class="bg-rose-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-rose-700 transition">
                Rechercher
            </button>
            @if($search)
            <a href="{{ route('admin.users') }}" class="border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm hover:bg-gray-50 transition">
                Effacer
            </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-rose-50/60 to-white">
            <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <h2 class="text-sm font-bold text-gray-800">Tous les utilisateurs</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Utilisateur</th>
                        <th class="px-6 py-3 text-left">Projets</th>
                        <th class="px-6 py-3 text-left">Inscrit</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0
                                    {{ $user->is_admin ? 'bg-rose-100 text-rose-700' : 'bg-indigo-100 text-indigo-700' }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700 font-semibold">{{ $user->projects_count }}</span>
                            <span class="text-gray-400 text-xs ml-1">propre(s)</span>
                            @if($user->member_projects_count > 0)
                                <span class="text-gray-400 text-xs ml-1">+ {{ $user->member_projects_count }} collab.</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Supprimer définitivement {{ addslashes($user->name) }} ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs px-3 py-1.5 rounded-lg font-semibold bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 transition">
                                            Supprimer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">Vous</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
