@extends('layouts.app')

@section('title', 'Nouveau projet')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="mb-8">
        <a href="{{ route('workspaces.show', $workspace) }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Retour à {{ $workspace->name }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-3">Nouveau projet</h1>
        <p class="text-gray-500 mt-1">Dans l'espace de travail <strong>{{ $workspace->name }}</strong>.</p>
    </div>

    <form method="POST" action="{{ route('projects.store') }}" x-data="taskManager()">
        @csrf
        <input type="hidden" name="workspace_id" value="{{ $workspace->id }}">

        {{-- Bloc : Informations du projet --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-base font-bold text-gray-800 mb-5 flex items-center gap-2">
                <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                Informations du projet
            </h2>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required autofocus
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('title') border-red-400 @enderror"
                       placeholder="Ex : Refonte du site web">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                          placeholder="Décrivez votre projet...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut initial</label>
                    <select name="status" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="planning" {{ old('status', 'planning') === 'planning' ? 'selected' : '' }}>📋 Planification</option>
                        <option value="active"   {{ old('status') === 'active'   ? 'selected' : '' }}>🚀 En cours</option>
                        <option value="on_hold"  {{ old('status') === 'on_hold'  ? 'selected' : '' }}>⏸️ En pause</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('end_date') border-red-400 @enderror">
                    @error('end_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Bloc : Tâches --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                    Tâches du projet
                    <span class="bg-indigo-100 text-indigo-600 text-xs font-bold px-2 py-0.5 rounded-full" x-text="tasks.length"></span>
                </h2>
                <button type="button" @click="addTask()"
                        class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter une tâche
                </button>
            </div>

            {{-- Liste des tâches --}}
            <div class="space-y-4" x-show="tasks.length > 0">
                <template x-for="(task, index) in tasks" :key="task.id">
                    <div class="border border-gray-100 rounded-xl p-4 bg-gray-50 relative group">

                        {{-- Bouton supprimer --}}
                        <button type="button" @click="removeTask(index)"
                                class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        {{-- Titre de la tâche --}}
                        <div class="mb-3 pr-8">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Titre de la tâche <span class="text-red-500">*</span></label>
                            <input type="text"
                                   :name="`tasks[${index}][title]`"
                                   x-model="task.title"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                   placeholder="Ex : Créer la maquette">
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            {{-- Priorité --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Priorité</label>
                                <select :name="`tasks[${index}][priority]`" x-model="task.priority"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    <option value="low">🟢 Faible</option>
                                    <option value="medium">🔵 Moyenne</option>
                                    <option value="high">🟠 Haute</option>
                                    <option value="urgent">🔴 Urgente</option>
                                </select>
                            </div>

                            {{-- Statut initial --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Statut</label>
                                <select :name="`tasks[${index}][status]`" x-model="task.status"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                    <option value="todo">À faire</option>
                                    <option value="in_progress">En cours</option>
                                    <option value="review">En révision</option>
                                    <option value="done">Terminé</option>
                                </select>
                            </div>

                            {{-- Date d'échéance --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 mb-1">Échéance</label>
                                <input type="date"
                                       :name="`tasks[${index}][due_date]`"
                                       x-model="task.due_date"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- État vide --}}
            <div x-show="tasks.length === 0" class="text-center py-10 text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-sm font-medium">Aucune tâche ajoutée</p>
                <p class="text-xs mt-1">Cliquez sur "Ajouter une tâche" pour commencer</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 justify-end">
            <a href="{{ route('workspaces.show', $workspace) }}"
               class="px-5 py-2.5 text-sm border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition font-medium">
                Annuler
            </a>
            <button type="submit"
                    class="px-6 py-2.5 text-sm bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-semibold shadow-md hover:shadow-indigo-200">
                Créer le projet
            </button>
        </div>
    </form>

</div>
</div>

<script src="{{ asset('js/project-create.js') }}"></script>
@endsection
