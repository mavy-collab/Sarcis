@extends('layouts.app')

@section('title', 'Fonctionnalités')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white py-16 px-4 text-center">
    <div class="max-w-3xl mx-auto">
        <span class="inline-block bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-medium px-4 py-1.5 rounded-full mb-4">Fonctionnalités</span>
        <h1 class="text-3xl sm:text-4xl font-extrabold mb-4">Tout ce dont vous avez besoin</h1>
        <p class="text-slate-300 text-base sm:text-lg">Des outils puissants pour gérer vos projets, collaborer avec votre équipe et suivre vos progrès.</p>
    </div>
</section>

{{-- Fonctionnalités principales --}}
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['📋', 'Gestion des tâches', 'Créez, assignez et organisez vos tâches avec des priorités (urgente, haute, normale, basse) et des deadlines.', true],
                ['👥', 'Collaboration d\'équipe', 'Invitez des membres dans vos projets, attribuez des rôles (admin, membre) et travaillez ensemble.', true],
                ['📊', 'Vue Kanban', 'Visualisez l\'avancement de vos tâches avec un tableau Kanban (À faire, En cours, Terminé).', true],
                ['🔔', 'Notifications', 'Recevez des notifications en temps réel quand vous êtes assigné à une tâche ou ajouté à un projet.', true],
                ['📎', 'Pièces jointes', 'Attachez des fichiers (images, PDF, documents) directement à vos tâches.', true],
                ['✅', 'Sous-tâches', 'Décomposez vos tâches en sous-tâches pour un suivi plus précis.', true],
                ['📅', 'Calendrier', 'Visualisez toutes vos tâches et deadlines dans un calendrier mensuel.', true],
                ['⏱️', 'Suivi du temps', 'Enregistrez le temps passé sur chaque tâche pour mieux estimer vos projets.', true],
                ['🏷️', 'Labels & Tags', 'Organisez vos tâches avec des labels personnalisés et colorés.', true],
                ['🔄', 'Tâches récurrentes', 'Créez des tâches qui se répètent automatiquement (quotidien, hebdomadaire, mensuel).', true],
                ['📱', 'Responsive mobile', 'Accédez à votre plateforme depuis n\'importe quel appareil — ordinateur, tablette ou téléphone.', true],
                ['🔒', 'Sécurité', 'Connexion sécurisée, authentification Google, protection contre les attaques.', true],
                ['📤', 'Export PDF', 'Exportez vos projets et rapports en PDF.', true],
                ['📈', 'Diagramme de Gantt', 'Visualisation temporelle de l\'avancement de vos projets.', true],
                ['📊', 'Rapports avancés', 'Statistiques détaillées sur vos projets et tâches.', true],
            ] as [$icon, $title, $desc, $available])
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <div class="text-3xl mb-3">{{ $icon }}</div>
                <h3 class="font-bold text-gray-800 mb-2">{{ $title }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
                <span class="inline-block mt-3 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                    ✓ Disponible
                </span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Fonctionnalités à venir --}}
<section class="py-12 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Bientôt disponible</h2>
        <p class="text-center text-gray-500 text-sm mb-8">Fonctionnalités en cours de développement</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['💬', 'Chat d\'équipe', 'Messagerie intégrée pour chaque projet.'],
                ['🤖', 'Automatisations', 'Automatisez les tâches répétitives.'],
                ['🌐', 'API publique', 'Intégrez SARCIS avec vos autres outils.'],
            ] as [$icon, $title, $desc])
            <div class="bg-white rounded-xl p-5 border border-dashed border-gray-200 opacity-75">
                <div class="text-2xl mb-2">{{ $icon }}</div>
                <h3 class="font-semibold text-gray-600 mb-1 text-sm">{{ $title }}</h3>
                <p class="text-gray-400 text-xs">{{ $desc }}</p>
                <span class="inline-block mt-2 text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">
                    En développement
                </span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
@guest
<section class="py-12 bg-white">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Prêt à essayer ?</h2>
        <p class="text-gray-500 mb-6 text-sm">Toutes ces fonctionnalités sont gratuites. Aucune carte bancaire requise.</p>
        <a href="{{ route('register') }}" class="inline-block bg-indigo-600 text-white font-semibold px-8 py-3 rounded-xl hover:bg-indigo-700 transition">
            Commencer gratuitement
        </a>
    </div>
</section>
@endguest

@endsection
