@extends('layouts.app')
@section('title', 'Admin — Logs de sécurité')

@section('content')

{{-- Hero Header --}}
<div class="relative bg-gradient-to-br from-slate-900 via-red-950 to-orange-950 text-white overflow-hidden">
    <div class="absolute top-0 left-1/4 w-72 h-72 bg-red-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-orange-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex items-center justify-between gap-6 flex-wrap">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-red-400 to-orange-600 flex items-center justify-center shadow-xl border border-white/10 flex-shrink-0">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-red-300 uppercase tracking-widest mb-1">Administration</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Logs de sécurité</h1>
                    <p class="text-sm text-slate-400 mt-1">
                        <span class="text-red-300 font-medium">{{ $logs->total() }} événement(s)</span> enregistré(s)
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
    <form method="GET" class="mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher IP, détails..."
               class="border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 min-w-[220px]">
        <select name="event" class="border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-400">
            <option value="">Tous les événements</option>
            @foreach($events as $e)
                <option value="{{ $e }}" @selected($event === $e)>{{ $e }}</option>
            @endforeach
        </select>
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition">
            Filtrer
        </button>
        @if($event || $search)
            <a href="{{ route('admin.security_logs') }}" class="border border-slate-300 text-slate-600 hover:bg-slate-100 text-sm font-medium px-4 py-2.5 rounded-xl transition">
                Réinitialiser
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-5 py-3.5 text-left">Date</th>
                    <th class="px-5 py-3.5 text-left">Événement</th>
                    <th class="px-5 py-3.5 text-left">Utilisateur</th>
                    <th class="px-5 py-3.5 text-left">IP</th>
                    <th class="px-5 py-3.5 text-left">Détails</th>
                    <th class="px-5 py-3.5 text-left">User Agent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 text-slate-500 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-5 py-3">
                            @php $color = $log->event_color; @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                {{ $log->event_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-700">
                            @if($log->user)
                                <span class="font-medium">{{ $log->user->name }}</span>
                                <span class="text-slate-400 text-xs block">{{ $log->user->email }}</span>
                            @else
                                <span class="text-slate-400 italic">Anonyme</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-600 text-xs">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-600 max-w-xs truncate" title="{{ $log->details }}">
                            {{ $log->details ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs max-w-[180px] truncate" title="{{ $log->user_agent }}">
                            {{ $log->user_agent ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Aucun événement de sécurité trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
        <div class="mt-6">{{ $logs->appends(request()->query())->links() }}</div>
    @endif

</div>
@endsection
