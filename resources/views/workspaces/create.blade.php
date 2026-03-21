@extends('layouts.app')
@section('title', 'Créer un espace de travail')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">
    <div class="mb-6">
        <a href="{{ route('workspaces.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Mes espaces de travail
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-5">
            <h1 class="text-xl font-bold text-white">Créer un espace de travail</h1>
            <p class="text-indigo-200 text-sm mt-1">Un espace regroupe vos projets par thème ou équipe.</p>
        </div>

        <form method="POST" action="{{ route('workspaces.store') }}" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nom de l'espace <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="Ex: Projets Infrastructure, Équipe Santé..."
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                <textarea name="description" rows="3"
                          placeholder="Décrivez l'objectif de cet espace..."
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Couleur d'accentuation</label>
                <div class="flex gap-3 flex-wrap">
                    @foreach(['indigo' => 'bg-indigo-600', 'emerald' => 'bg-emerald-600', 'rose' => 'bg-rose-600', 'amber' => 'bg-amber-500', 'violet' => 'bg-violet-600', 'sky' => 'bg-sky-600', 'teal' => 'bg-teal-600', 'orange' => 'bg-orange-500'] as $color => $class)
                    <label class="cursor-pointer">
                        <input type="radio" name="color" value="{{ $color }}" class="sr-only peer" {{ old('color', 'indigo') === $color ? 'checked' : '' }}>
                        <div class="{{ $class }} w-9 h-9 rounded-full peer-checked:ring-4 peer-checked:ring-offset-2 peer-checked:ring-gray-400 transition hover:scale-110"></div>
                    </label>
                    @endforeach
                </div>
                @error('color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Sélecteur d'image de fond --}}
            <div x-data="{ selected: '{{ old('background_image', '') }}' }">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-700">Image de fond</label>
                    <button type="button" @click="selected = ''"
                            class="text-xs text-gray-400 hover:text-red-500 transition">
                        Aucune image
                    </button>
                </div>

                <input type="hidden" name="background_image" :value="selected">

                <div class="grid grid-cols-3 gap-3">
                    @foreach($backgrounds as $bg)
                    <label class="cursor-pointer group relative" @click="selected = '{{ $bg['key'] }}'">
                        <div class="relative rounded-xl overflow-hidden border-2 transition-all duration-200"
                             :class="selected === '{{ $bg['key'] }}' ? 'border-indigo-500 shadow-lg scale-[1.02]' : 'border-transparent hover:border-gray-300'">
                            <img src="{{ $bg['url'] }}" alt="{{ $bg['label'] }}"
                                 class="w-full h-20 object-cover">
                            <div class="absolute inset-0 bg-black/30 flex items-end p-2">
                                <span class="text-white text-xs font-semibold drop-shadow">{{ $bg['label'] }}</span>
                            </div>
                            {{-- Coche sélection --}}
                            <div x-show="selected === '{{ $bg['key'] }}'"
                                 class="absolute top-1.5 right-1.5 w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center shadow">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Aperçu --}}
                <div x-show="selected !== ''" class="mt-4 rounded-xl overflow-hidden h-24 relative shadow-sm border border-gray-100">
                    @foreach($backgrounds as $bg)
                    <div x-show="selected === '{{ $bg['key'] }}'"
                         class="absolute inset-0 bg-cover bg-center"
                         style="background-image: url('{{ $bg['url'] }}')">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">Aperçu du fond</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-semibold py-2.5 rounded-xl hover:bg-indigo-700 transition text-sm">
                    Créer l'espace de travail
                </button>
                <a href="{{ route('workspaces.index') }}"
                   class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition font-medium">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
