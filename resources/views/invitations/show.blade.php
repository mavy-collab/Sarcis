@extends('layouts.app')

@section('title', 'Invitation au projet')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 px-6 py-8 text-center">
            <div class="text-5xl mb-3">📩</div>
            <h1 class="text-2xl font-bold text-white">Invitation à rejoindre un projet</h1>
        </div>

        <div class="px-6 py-8">
            <p class="text-gray-700 text-center mb-6">
                <span class="font-semibold text-indigo-600">{{ $invitation->inviter->name }}</span>
                vous invite à rejoindre le projet
                <span class="font-semibold">"{{ $invitation->project->title }}"</span>
                en tant que
                <span class="font-semibold">{{ $invitation->role }}</span>.
            </p>

            @if($invitation->expires_at)
                <p class="text-sm text-gray-500 text-center mb-6">
                    Cette invitation expire le {{ $invitation->expires_at->format('d/m/Y à H:i') }}.
                </p>
            @endif

            <div class="flex gap-4">
                <form method="POST" action="{{ route('invitations.accept', $invitation) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition">
                        ✅ Accepter
                    </button>
                </form>

                <form method="POST" action="{{ route('invitations.decline', $invitation) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-4 rounded-xl transition">
                        ❌ Refuser
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
