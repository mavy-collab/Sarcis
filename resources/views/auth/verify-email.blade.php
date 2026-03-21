@extends('layouts.app')

@section('title', 'Vérifiez votre email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="text-4xl font-extrabold text-indigo-700" style="font-family: serif; letter-spacing: .15em;">SARCIS</a>
            <h2 class="mt-4 text-2xl font-bold text-gray-800">Vérifiez votre email</h2>
            <p class="mt-2 text-gray-500">Un lien de vérification a été envoyé à <strong>{{ auth()->user()->email }}</strong></p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <p class="text-gray-600 text-sm mb-6">
                Vérifiez votre boîte email et cliquez sur le lien pour activer votre compte. Si vous ne trouvez pas l'email, vérifiez vos spams.
            </p>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-indigo-600 text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition">
                    Renvoyer l'email de vérification
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-sm text-gray-500 hover:text-gray-700 py-2">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
