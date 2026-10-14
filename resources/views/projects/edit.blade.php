@extends('layouts.app')

@section('title', 'Modifier le projet')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:text-indigo-600">&larr; Retour</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Modifier le projet</h1>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre du projet <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $project->title) }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('title') border-red-400 @enderror">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(['planning' => 'Planification', 'active' => 'Actif', 'on_hold' => 'En pause', 'completed' => 'Terminé', 'cancelled' => 'Annulé'] as $val => $label)
                            <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                           min="{{ min(now()->toDateString(), $project->start_date?->format('Y-m-d') ?? now()->toDateString()) }}" max="{{ now()->addYears(10)->toDateString() }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('start_date') border-red-400 @enderror">
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                           min="{{ min(now()->toDateString(), $project->end_date?->format('Y-m-d') ?? now()->toDateString()) }}" max="{{ now()->addYears(10)->toDateString() }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('end_date') border-red-400 @enderror">
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('projects.show', $project) }}"
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
