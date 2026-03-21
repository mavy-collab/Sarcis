@extends('layouts.app')
@section('title', 'Calendrier')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-indigo-950 to-purple-950 pb-28">

    {{-- Header --}}
    <div class="relative px-6 pt-8 pb-6 max-w-5xl mx-auto">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('workspaces.index') }}" class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-white/50 text-xs uppercase tracking-widest font-medium">Calendrier</p>
                    <h1 class="text-2xl font-extrabold text-white capitalize">
                        {{ $current->translatedFormat('F Y') }}
                    </h1>
                </div>
            </div>

            {{-- Navigation mois --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}"
                   class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <a href="{{ route('calendar.index') }}"
                   class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl transition">
                    Aujourd'hui
                </a>
                <a href="{{ route('calendar.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}"
                   class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="max-w-5xl mx-auto px-6 mb-4">
        <div class="bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-sm px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    </div>
    @endif

    <div class="max-w-5xl mx-auto px-6" x-data="{
        modal: false,
        selectedDate: '',
        selectedLabel: '',
        noteColor: 'indigo',
        open(day, label) {
            this.selectedDate = day;
            this.selectedLabel = label;
            this.noteColor = 'indigo';
            this.modal = true;
        }
    }">

        {{-- Grille calendrier --}}
        <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden">

            {{-- Jours de la semaine --}}
            <div class="grid grid-cols-7 border-b border-white/10">
                @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $day)
                <div class="text-center text-white/40 text-xs font-semibold py-3 uppercase tracking-wider">{{ $day }}</div>
                @endforeach
            </div>

            {{-- Jours du mois --}}
            @php
                $startDow = ($current->copy()->startOfMonth()->dayOfWeek + 6) % 7; // lundi = 0
                $daysInMonth = $current->daysInMonth;
                $today = now()->toDateString();
            @endphp

            <div class="grid grid-cols-7">
                {{-- Cellules vides avant le 1er --}}
                @for($i = 0; $i < $startDow; $i++)
                <div class="border-b border-r border-white/5 min-h-[90px]"></div>
                @endfor

                {{-- Jours --}}
                @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr = $current->format('Y-m-') . str_pad($d, 2, '0', STR_PAD_LEFT);
                    $isToday = $dateStr === $today;
                    $dayNotes = $notes->get($d, collect());
                    $col = ($startDow + $d - 1) % 7; // 0=lun, 6=dim
                    $isWeekend = $col >= 5;
                    $isLast = $d === $daysInMonth;
                @endphp
                <div class="border-b border-r border-white/5 min-h-[90px] p-2 {{ $isWeekend ? 'bg-white/[0.02]' : '' }} hover:bg-white/[0.04] transition group cursor-pointer relative"
                     @click="open('{{ $dateStr }}', '{{ \Carbon\Carbon::parse($dateStr)->translatedFormat('d F Y') }}')">

                    {{-- Numéro du jour --}}
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full
                            {{ $isToday ? 'bg-indigo-500 text-white' : 'text-white/50' }}">
                            {{ $d }}
                        </span>
                        @if($dayNotes->isNotEmpty())
                        <span class="text-white/40 text-xs">{{ $dayNotes->count() }}</span>
                        @endif
                    </div>

                    {{-- Notes du jour --}}
                    @foreach($dayNotes->take(2) as $note)
                    @php
                        $noteColors = [
                            'indigo'  => 'bg-indigo-500/80',
                            'emerald' => 'bg-emerald-500/80',
                            'rose'    => 'bg-rose-500/80',
                            'amber'   => 'bg-amber-500/80',
                            'violet'  => 'bg-violet-500/80',
                            'sky'     => 'bg-sky-500/80',
                            'teal'    => 'bg-teal-500/80',
                            'orange'  => 'bg-orange-500/80',
                        ];
                        $noteBg = $noteColors[$note->color] ?? 'bg-indigo-500/80';
                    @endphp
                    <div class="{{ $noteBg }} text-white text-[10px] font-medium px-1.5 py-0.5 rounded-md mb-0.5 truncate">
                        {{ $note->content }}
                    </div>
                    @endforeach
                    @if($dayNotes->count() > 2)
                    <div class="text-white/30 text-[10px] pl-1">+{{ $dayNotes->count() - 2 }} autres</div>
                    @endif

                    {{-- Tâches deadline du jour --}}
                    @php $dayTasks = $taskDeadlines->get($d, collect()); @endphp
                    @foreach($dayTasks->take(2) as $task)
                    <div class="bg-rose-500/70 text-white text-[10px] font-medium px-1.5 py-0.5 rounded-md mb-0.5 truncate"
                         title="{{ ($task->project->title ?? '') . ' — ' . $task->title }}">
                        ⏰ {{ $task->title }}
                    </div>
                    @endforeach
                    @if($dayTasks->count() > 2)
                    <div class="text-white/30 text-[10px] pl-1">+{{ $dayTasks->count() - 2 }} tâches</div>
                    @endif
                </div>
                @endfor

                {{-- Cellules vides après le dernier jour --}}
                @php $remaining = (7 - ($startDow + $daysInMonth) % 7) % 7; @endphp
                @for($i = 0; $i < $remaining; $i++)
                <div class="border-b border-r border-white/5 min-h-[90px]"></div>
                @endfor
            </div>
        </div>

        {{-- Liste des notes du mois --}}
        @php $allNotes = $notes->flatten()->sortBy('date'); @endphp
        @if($allNotes->isNotEmpty())
        <div class="mt-6 bg-white/5 backdrop-blur-sm rounded-2xl border border-white/10 overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10">
                <h2 class="text-white font-bold text-sm">Notes du mois</h2>
            </div>
            <div class="divide-y divide-white/5">
                @foreach($allNotes as $note)
                @php $noteBg2 = ['indigo'=>'bg-indigo-500','emerald'=>'bg-emerald-500','rose'=>'bg-rose-500','amber'=>'bg-amber-500','violet'=>'bg-violet-500','sky'=>'bg-sky-500','teal'=>'bg-teal-500','orange'=>'bg-orange-500'][$note->color] ?? 'bg-indigo-500'; @endphp
                <div class="flex items-start gap-3 px-5 py-3">
                    <div class="w-1.5 h-1.5 {{ $noteBg2 }} rounded-full mt-1.5 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white/40 text-xs mb-0.5 capitalize">{{ $note->date->translatedFormat('l d F') }}</p>
                        <p class="text-white/80 text-sm">{{ $note->content }}</p>
                    </div>
                    <form method="POST" action="{{ route('calendar.destroy', $note) }}" onsubmit="return confirm('Supprimer cette note ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-white/20 hover:text-rose-400 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Modal ajout de note --}}
        <div x-show="modal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @click.self="modal = false">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="relative bg-slate-900 border border-white/10 rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <p class="text-white/40 text-xs uppercase tracking-wider">Nouvelle note</p>
                        <h3 class="text-white font-bold text-lg capitalize" x-text="selectedLabel"></h3>
                    </div>
                    <button @click="modal = false" class="text-white/30 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('calendar.store') }}">
                    @csrf
                    <input type="hidden" name="date" :value="selectedDate">

                    <textarea name="content" rows="4" required
                              placeholder="Écrivez votre note..."
                              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white text-sm placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none mb-4"></textarea>

                    {{-- Couleur --}}
                    <div class="mb-5">
                        <p class="text-white/40 text-xs uppercase tracking-wider mb-2">Couleur</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach(['indigo' => 'bg-indigo-500', 'emerald' => 'bg-emerald-500', 'rose' => 'bg-rose-500', 'amber' => 'bg-amber-500', 'violet' => 'bg-violet-500', 'sky' => 'bg-sky-500', 'teal' => 'bg-teal-500', 'orange' => 'bg-orange-500'] as $c => $cls)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $c }}" class="sr-only peer"
                                       x-model="noteColor" value="{{ $c }}" {{ $c === 'indigo' ? 'checked' : '' }}>
                                <div class="{{ $cls }} w-7 h-7 rounded-full peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-offset-slate-900 peer-checked:ring-white transition hover:scale-110"></div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                        Enregistrer la note
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
