@extends('layouts.app')

@section('title', 'Modifier la tâche')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Retour</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Modifier la tâche</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['todo' => 'À faire', 'in_progress' => 'En cours', 'review' => 'En révision', 'done' => 'Terminé'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                    <select name="priority" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['low' => 'Faible', 'medium' => 'Moyenne', 'high' => 'Haute', 'urgent' => 'Urgente'] as $val => $label)
                            <option value="{{ $val }}" {{ old('priority', $task->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
                    <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assignée à</label>
                    <select name="assigned_to" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Non assignée</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Récurrence (Pro) --}}
            @if($project->hasPro())
            <div class="mb-5 border border-indigo-100 bg-indigo-50 rounded-xl p-4"
                 x-data="{ recurring: {{ $task->is_recurring ? 'true' : 'false' }} }">
                <div class="flex items-center gap-3 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_recurring" value="1"
                               x-model="recurring"
                               {{ old('is_recurring', $task->is_recurring) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm font-medium text-indigo-800">Tâche récurrente</span>
                    </label>
                    <span class="text-xs bg-indigo-600 text-white px-2 py-0.5 rounded-full font-semibold">Pro</span>
                </div>

                <div x-show="recurring" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Fréquence</label>
                        <select name="recurrence_type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Choisir --</option>
                            @foreach(['daily' => 'Quotidienne', 'weekly' => 'Hebdomadaire', 'monthly' => 'Mensuelle'] as $val => $lbl)
                                <option value="{{ $val }}" {{ old('recurrence_type', $task->recurrence_type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Se termine le (optionnel)</label>
                        <input type="date" name="recurrence_ends_at"
                               value="{{ old('recurrence_ends_at', $task->recurrence_ends_at?->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-indigo-600">
                            ℹ️ Quand cette tâche est marquée <strong>Terminée</strong>, la prochaine occurrence sera créée automatiquement.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex gap-3 justify-end">
                <a href="{{ route('projects.tasks.show', [$project, $task]) }}"
                   class="px-5 py-2.5 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Annuler</a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
