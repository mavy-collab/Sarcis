@extends('layouts.app')
@section('title', 'Messages de contact')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-extrabold text-white">Messages de contact</h1>
                <p class="text-slate-400 text-xs mt-0.5">{{ $openCount }} message(s) ouvert(s)</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="?status=open"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ request('status', 'open') === 'open' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white border border-white/10' }}">
                Ouverts <span class="ml-1 bg-white/10 rounded-full px-1.5">{{ $openCount }}</span>
            </a>
            <a href="?status=closed"
               class="px-4 py-2 rounded-xl text-xs font-semibold transition {{ request('status') === 'closed' ? 'bg-slate-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white border border-white/10' }}">
                Fermés
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-500/10 border border-green-500/30 text-green-300 rounded-xl px-4 py-3 mb-4 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Liste --}}
    @if($messages->isEmpty())
    <div class="bg-slate-900 border border-white/5 rounded-2xl p-12 text-center">
        <div class="text-4xl mb-3">📭</div>
        <p class="text-slate-400 text-sm">Aucun message {{ request('status') === 'closed' ? 'fermé' : 'ouvert' }}.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($messages as $msg)
        <div class="bg-slate-900 border border-white/5 rounded-2xl overflow-hidden" x-data="{ open: false }">
            <div class="flex items-center gap-4 px-5 py-4 cursor-pointer hover:bg-white/5 transition" @click="open = !open">
                {{-- Statut --}}
                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $msg->isOpen() ? 'bg-indigo-400' : 'bg-slate-600' }}"></div>

                {{-- Avatar initial --}}
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-sm font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($msg->name, 0, 1)) }}
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-white text-sm">{{ $msg->name }}</span>
                        <span class="text-slate-500 text-xs">{{ $msg->email }}</span>
                        @if($msg->user)
                        <span class="bg-indigo-500/20 text-indigo-300 text-[10px] font-semibold px-2 py-0.5 rounded-full">Utilisateur enregistré</span>
                        @endif
                    </div>
                    <div class="text-slate-300 text-xs mt-0.5 truncate">{{ $msg->subject }}</div>
                </div>

                {{-- Date --}}
                <div class="text-slate-500 text-xs flex-shrink-0">{{ $msg->created_at->format('d/m/Y H:i') }}</div>

                {{-- Chevron --}}
                <svg class="w-4 h-4 text-slate-500 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>

            {{-- Contenu développé --}}
            <div x-show="open" x-transition class="border-t border-white/5 px-5 py-4 bg-slate-800/30">
                <p class="text-sm text-slate-300 whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>

                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-white/5">
                    @if($msg->isOpen())
                    <form method="POST" action="{{ route('admin.contact.close', $msg) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Marquer comme traité
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('admin.contact.reopen', $msg) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="flex items-center gap-1.5 bg-indigo-600/30 hover:bg-indigo-600/50 text-indigo-300 text-xs font-semibold px-4 py-2 rounded-xl transition">
                            Réouvrir
                        </button>
                    </form>
                    @endif

                    <a href="mailto:{{ $msg->email }}?subject=Re: {{ urlencode($msg->subject) }}"
                       class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Répondre par email
                    </a>

                    <form method="POST" action="{{ route('admin.contact.destroy', $msg) }}" onsubmit="return confirm('Supprimer ce message ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-xs font-semibold px-3 py-2 rounded-xl hover:bg-red-500/10 transition">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $messages->withQueryString()->links() }}</div>
    @endif

</div>
@endsection
