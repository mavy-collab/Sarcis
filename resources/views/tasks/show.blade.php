@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-indigo-600">
            &larr; {{ $project->title }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Détails de la tâche --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">{{ $task->title }}</h1>
                        @if($task->is_recurring)
                        <span class="inline-flex items-center gap-1 mt-1 text-xs font-semibold text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            {{ $task->recurrence_label }}
                            @if($task->recurrence_ends_at)
                                · jusqu'au {{ $task->recurrence_ends_at->format('d/m/Y') }}
                            @endif
                        </span>
                        @endif
                    </div>
                    @if(Auth::id() === $project->user_id || Auth::id() === $task->created_by)
                    <div class="flex gap-2">
                        <a href="{{ route('projects.tasks.edit', [$project, $task]) }}"
                           class="text-sm px-3 py-1.5 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}"
                              x-data @submit.prevent="if(confirm('Supprimer cette tâche ?')) $el.submit()">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="text-sm px-3 py-1.5 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition">
                                Supprimer
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                @if($task->description)
                    <p class="text-gray-600 mb-4">{{ $task->description }}</p>
                @endif

                {{-- Changer le statut --}}
                <div class="mb-4">
                    <label class="text-sm font-medium text-gray-700 mb-2 block">Statut</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['todo' => 'À faire', 'in_progress' => 'En cours', 'review' => 'En révision', 'done' => 'Terminé'] as $status => $label)
                        <form method="POST" action="{{ route('projects.tasks.status', [$project, $task]) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button type="submit"
                                    class="text-xs px-3 py-1.5 rounded-full border transition
                                    @if($task->status === $status)
                                        @if($status === 'todo') bg-gray-600 text-white border-gray-600
                                        @elseif($status === 'in_progress') bg-blue-600 text-white border-blue-600
                                        @elseif($status === 'review') bg-yellow-500 text-white border-yellow-500
                                        @else bg-green-600 text-white border-green-600 @endif
                                    @else
                                        bg-white text-gray-600 border-gray-300 hover:bg-gray-50
                                    @endif">
                                {{ $label }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sous-tâches (Pro) --}}
            @if($project->hasPro())
            <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6"
                 x-data="{
                     subTasks: {{ $task->subTasks->map(fn($s) => ['id'=>$s->id,'title'=>$s->title,'is_done'=>$s->is_done])->values()->toJson() }},
                     newTitle: '',
                     loading: false,
                     async add() {
                         if (!this.newTitle.trim()) return;
                         this.loading = true;
                         const res = await fetch('{{ route('projects.tasks.subtasks.store', [$project, $task]) }}', {
                             method: 'POST',
                             headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                             body: JSON.stringify({title: this.newTitle})
                         });
                         const data = await res.json();
                         if (data.id) { this.subTasks.push(data); this.newTitle = ''; }
                         this.loading = false;
                     },
                     async toggle(st) {
                         const res = await fetch(`{{ url('projects/' . $project->id . '/tasks/' . $task->id . '/subtasks') }}/${st.id}/toggle`, {
                             method: 'PATCH',
                             headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                         });
                         const data = await res.json();
                         st.is_done = data.is_done;
                     },
                     async remove(st, idx) {
                         await fetch(`{{ url('projects/' . $project->id . '/tasks/' . $task->id . '/subtasks') }}/${st.id}`, {
                             method: 'DELETE',
                             headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                         });
                         this.subTasks.splice(idx, 1);
                     },
                     get progress() {
                         if (!this.subTasks.length) return 0;
                         return Math.round((this.subTasks.filter(s=>s.is_done).length / this.subTasks.length) * 100);
                     }
                 }">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Sous-tâches
                        <span class="text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-bold" x-text="subTasks.length"></span>
                    </h2>
                    <span class="text-xs text-gray-400 font-medium" x-text="progress + '% complété'"></span>
                </div>

                {{-- Progress bar --}}
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                    <div class="bg-violet-500 h-1.5 rounded-full transition-all" :style="'width:' + progress + '%'"></div>
                </div>

                {{-- Sub-task list --}}
                <div class="space-y-2 mb-4">
                    <template x-for="(st, idx) in subTasks" :key="st.id">
                        <div class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-gray-50 group transition">
                            <button @click="toggle(st)"
                                    :class="st.is_done ? 'bg-violet-500 border-violet-500 text-white' : 'border-gray-300 hover:border-violet-400'"
                                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition">
                                <svg x-show="st.is_done" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <span :class="st.is_done ? 'line-through text-gray-400' : 'text-gray-700'"
                                  class="flex-1 text-sm" x-text="st.title"></span>
                            <button @click="remove(st, idx)"
                                    class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition text-xs">
                                ✕
                            </button>
                        </div>
                    </template>
                    <div x-show="subTasks.length === 0" class="text-center py-4 text-sm text-gray-400">Aucune sous-tâche</div>
                </div>

                {{-- Add sub-task --}}
                @if(!$project->is_locked)
                <div class="flex gap-2">
                    <input x-model="newTitle" @keydown.enter.prevent="add()" type="text"
                           placeholder="Ajouter une sous-tâche..."
                           class="flex-1 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <button @click="add()" :disabled="loading || !newTitle.trim()"
                            class="bg-violet-600 hover:bg-violet-700 disabled:opacity-50 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                        <span x-show="!loading">+</span>
                        <span x-show="loading">…</span>
                    </button>
                </div>
                @endif
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Sous-tâches — Plan Pro</p>
                        <p class="text-xs text-amber-600">Décomposez vos tâches en étapes.</p>
                    </div>
                </div>
                <a href="{{ route('plans.index') }}" class="flex-shrink-0 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Passer au Pro
                </a>
            </div>
            @endif

            {{-- Suivi du temps --}}
            @if($project->hasPro())
            <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6"
                 x-data="{
                     logs: {{ $task->timeLogs->map(fn($l) => [
                         'id'                 => $l->id,
                         'duration'           => $l->duration,
                         'duration_formatted' => $l->duration_formatted,
                         'note'               => $l->note ?? '',
                         'user'               => $l->user->name,
                         'created_at'         => $l->created_at->format('d/m/Y H:i'),
                     ])->values()->toJson() }},
                     totalFormatted: '{{ $task->total_time_formatted }}',
                     running: false,
                     elapsed: 0,
                     timer: null,
                     hours: 0,
                     minutes: 0,
                     note: '',
                     loading: false,
                     get displayTimer() {
                         let h = Math.floor(this.elapsed / 3600);
                         let m = Math.floor((this.elapsed % 3600) / 60);
                         let s = this.elapsed % 60;
                         return (h > 0 ? h + 'h ' : '') + String(m).padStart(2,'0') + 'm ' + String(s).padStart(2,'0') + 's';
                     },
                     startTimer() {
                         this.running = true;
                         this.timer = setInterval(() => this.elapsed++, 1000);
                     },
                     async stopTimer() {
                         clearInterval(this.timer);
                         this.running = false;
                         if (this.elapsed < 1) { this.elapsed = 0; return; }
                         await this.saveLog(this.elapsed, 'Chronomètre');
                         this.elapsed = 0;
                     },
                     async saveManual() {
                         let secs = (parseInt(this.hours) || 0) * 3600 + (parseInt(this.minutes) || 0) * 60;
                         if (secs < 60) return;
                         await this.saveLog(secs, this.note);
                         this.hours = 0; this.minutes = 0; this.note = '';
                     },
                     async saveLog(duration, note) {
                         this.loading = true;
                         const res = await fetch('{{ url('projects/' . $project->id . '/tasks/' . $task->id . '/timelogs') }}', {
                             method: 'POST',
                             headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                             body: JSON.stringify({duration, note})
                         });
                         const data = await res.json();
                         if (data.id) {
                             this.logs.unshift(data);
                             this.totalFormatted = data.total_formatted;
                         }
                         this.loading = false;
                     },
                     async removeLog(log, idx) {
                         const res = await fetch('{{ url('projects/' . $project->id . '/tasks/' . $task->id . '/timelogs') }}/' + log.id, {
                             method: 'DELETE',
                             headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                         });
                         const data = await res.json();
                         if (data.success) {
                             this.logs.splice(idx, 1);
                             this.totalFormatted = data.total_formatted;
                         }
                     }
                 }">

                {{-- En-tête --}}
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Suivi du temps
                    </h2>
                    <span class="text-sm font-semibold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                        Total : <span x-text="totalFormatted"></span>
                    </span>
                </div>

                {{-- Chronomètre --}}
                <div class="bg-gray-50 rounded-xl p-4 mb-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Chronomètre</p>
                        <p class="text-2xl font-mono font-bold text-gray-800" x-text="running ? displayTimer : '00m 00s'"></p>
                    </div>
                    <div class="flex gap-2">
                        <button x-show="!running" @click="startTimer()"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Démarrer
                        </button>
                        <button x-show="running" @click="stopTimer()"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                            Arrêter &amp; Sauvegarder
                        </button>
                    </div>
                </div>

                {{-- Saisie manuelle --}}
                <div class="border border-gray-200 rounded-xl p-4 mb-4">
                    <p class="text-xs text-gray-500 font-medium mb-3">Saisie manuelle</p>
                    <div class="flex gap-2 flex-wrap">
                        <div class="flex items-center gap-1">
                            <input type="number" x-model="hours" min="0" max="99" placeholder="0"
                                   class="w-16 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-sm text-gray-500">h</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <input type="number" x-model="minutes" min="0" max="59" placeholder="0"
                                   class="w-16 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-sm text-gray-500">min</span>
                        </div>
                        <input type="text" x-model="note" placeholder="Note (optionnel)"
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <button @click="saveManual()" :disabled="loading || (parseInt(hours)||0)*60+(parseInt(minutes)||0) < 1"
                                class="bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">
                            Ajouter
                        </button>
                    </div>
                </div>

                {{-- Historique --}}
                <div class="space-y-2">
                    <template x-for="(log, idx) in logs" :key="log.id">
                        <div class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-gray-50 group transition text-sm">
                            <span class="font-semibold text-emerald-600 w-16 shrink-0" x-text="log.duration_formatted"></span>
                            <span class="text-gray-500 flex-1" x-text="log.note || '—'"></span>
                            <span class="text-gray-400 text-xs" x-text="log.user"></span>
                            <span class="text-gray-400 text-xs" x-text="log.created_at"></span>
                            <button @click="removeLog(log, idx)"
                                    class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition text-xs">✕</button>
                        </div>
                    </template>
                    <div x-show="logs.length === 0" class="text-center py-3 text-sm text-gray-400">Aucune entrée de temps</div>
                </div>
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⏱️</span>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Suivi du temps — Plan Pro</p>
                        <p class="text-xs text-amber-600">Chronomètre et saisie manuelle du temps passé.</p>
                    </div>
                </div>
                <a href="{{ route('plans.index') }}" class="flex-shrink-0 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Passer au Pro
                </a>
            </div>
            @endif

            {{-- Commentaires --}}
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Commentaires ({{ $task->comments->count() }})</h2>

                @auth
                <form method="POST" action="{{ route('projects.tasks.comments.store', [$project, $task]) }}" class="mb-6">
                    @csrf
                    <textarea name="content" rows="3" placeholder="Écrire un commentaire..." required
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('content') border-red-400 @enderror"></textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="mt-2 text-right">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
                            Commenter
                        </button>
                    </div>
                </form>
                @endauth

                @if($task->comments->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-4">Aucun commentaire.</p>
                @else
                    <div class="space-y-4">
                        @foreach($task->comments->sortByDesc('created_at') as $comment)
                        <div class="flex gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-sm font-semibold text-indigo-600 shrink-0">
                                {{ substr($comment->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-gray-800">{{ $comment->user->name }}</div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if(Auth::id() === $comment->user_id || Auth::id() === $project->user_id)
                                        <form method="POST" action="{{ route('projects.tasks.comments.destroy', [$project, $task, $comment]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600">Supprimer</button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $comment->content }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar tâche --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informations</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Priorité</dt>
                        <dd class="mt-0.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                @if($task->priority === 'urgent') bg-red-100 text-red-700
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-700
                                @elseif($task->priority === 'medium') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ $task->priority_label }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Assignée à</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">
                            {{ $task->assignedTo?->name ?? 'Non assignée' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Créée par</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $task->createdBy->name }}</dd>
                    </div>
                    @if($task->due_date)
                    <div>
                        <dt class="text-gray-500">Échéance</dt>
                        <dd class="font-medium mt-0.5 {{ $task->is_overdue ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $task->due_date->format('d/m/Y') }}
                            @if($task->is_overdue) <span class="text-xs">(En retard)</span> @endif
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-gray-500">Créée le</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $task->created_at->format('d/m/Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Projet</h3>
                <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:underline">
                    {{ $project->title }}
                </a>
            </div>

            {{-- Labels --}}
            @if($project->hasPro())
            <div class="bg-white rounded-xl border border-gray-200 p-5"
                 x-data="{
                     taskLabels: {{ $task->labels->map(fn($l) => ['id'=>$l->id,'name'=>$l->name,'color'=>$l->color,'text_color'=>$l->text_color])->values()->toJson() }},
                     projectLabels: {{ $project->labels->map(fn($l) => ['id'=>$l->id,'name'=>$l->name,'color'=>$l->color,'text_color'=>$l->text_color])->values()->toJson() }},
                     newName: '',
                     newColor: '#6366f1',
                     showPicker: false,
                     palette: ['#ef4444','#f97316','#eab308','#22c55e','#14b8a6','#3b82f6','#6366f1','#a855f7','#ec4899','#6b7280'],
                     hasLabel(id) { return this.taskLabels.some(l => l.id === id); },
                     async toggleLabel(label) {
                         const has = this.hasLabel(label.id);
                         if (has) {
                             await fetch('{{ url('projects/'.$project->id.'/tasks/'.$task->id.'/labels') }}/' + label.id + '/detach', {
                                 method: 'DELETE',
                                 headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                             });
                             this.taskLabels = this.taskLabels.filter(l => l.id !== label.id);
                         } else {
                             await fetch('{{ url('projects/'.$project->id.'/tasks/'.$task->id.'/labels/attach') }}', {
                                 method: 'POST',
                                 headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                                 body: JSON.stringify({label_id: label.id})
                             });
                             this.taskLabels.push(label);
                         }
                     },
                     async createLabel() {
                         if (!this.newName.trim()) return;
                         const res = await fetch('{{ url('projects/'.$project->id.'/labels') }}', {
                             method: 'POST',
                             headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                             body: JSON.stringify({name: this.newName, color: this.newColor})
                         });
                         const data = await res.json();
                         if (data.id) { this.projectLabels.push(data); this.newName = ''; }
                     },
                     async deleteProjectLabel(label, idx) {
                         await fetch('{{ url('projects/'.$project->id.'/labels') }}/' + label.id, {
                             method: 'DELETE',
                             headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                         });
                         this.projectLabels.splice(idx, 1);
                         this.taskLabels = this.taskLabels.filter(l => l.id !== label.id);
                     }
                 }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700">Étiquettes</h3>
                    <button @click="showPicker = !showPicker" class="text-xs text-indigo-600 hover:underline">Gérer</button>
                </div>

                {{-- Labels actifs sur la tâche --}}
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <template x-for="label in taskLabels" :key="label.id">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"
                              :style="'background:' + label.color + ';color:' + label.text_color">
                            <span x-text="label.name"></span>
                        </span>
                    </template>
                    <span x-show="taskLabels.length === 0" class="text-xs text-gray-400">Aucune étiquette</span>
                </div>

                {{-- Sélecteur de labels --}}
                <div x-show="showPicker" x-transition class="border-t border-gray-100 pt-3 space-y-1">
                    <template x-for="(label, idx) in projectLabels" :key="label.id">
                        <div class="flex items-center justify-between group">
                            <label class="flex items-center gap-2 cursor-pointer flex-1">
                                <input type="checkbox"
                                       :checked="hasLabel(label.id)"
                                       @change="toggleLabel(label)"
                                       class="rounded border-gray-300 text-indigo-600">
                                <span class="inline-block w-3 h-3 rounded-full shrink-0" :style="'background:' + label.color"></span>
                                <span class="text-xs text-gray-700" x-text="label.name"></span>
                            </label>
                            <button @click="deleteProjectLabel(label, idx)"
                                    class="text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 text-xs transition">✕</button>
                        </div>
                    </template>
                    <div x-show="projectLabels.length === 0" class="text-xs text-gray-400 py-1">Aucune étiquette créée</div>

                    {{-- Créer une nouvelle étiquette --}}
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Nouvelle étiquette</p>
                        <div class="flex gap-1.5 mb-2 flex-wrap">
                            <template x-for="c in palette" :key="c">
                                <button @click="newColor = c"
                                        :style="'background:' + c"
                                        :class="newColor === c ? 'ring-2 ring-offset-1 ring-gray-800' : ''"
                                        class="w-5 h-5 rounded-full border border-gray-200 transition"></button>
                            </template>
                        </div>
                        <div class="flex gap-1.5">
                            <input type="text" x-model="newName" @keydown.enter.prevent="createLabel()"
                                   placeholder="Nom..."
                                   class="flex-1 border border-gray-200 rounded-lg px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button @click="createLabel()" :disabled="!newName.trim()"
                                    class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 text-white px-3 py-1 rounded-lg text-xs font-medium transition">
                                +
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
