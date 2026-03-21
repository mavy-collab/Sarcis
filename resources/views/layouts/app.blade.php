<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M8N2DW6G');</script>
<!-- End Google Tag Manager -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}?v=2">
    <title>@yield('title', 'SARCIS') - Gestion de Projets Gratuit en Ligne</title>
    <meta name="description" content="SARCIS est une plateforme gratuite de gestion de projets en ligne. Organisez vos tâches, collaborez avec votre équipe, suivez l'avancement en temps réel. Gratuit pour toujours.">
    <meta name="keywords" content="gestion de projet gratuit, outil gestion tâches, collaboration équipe, gestion projet en ligne, logiciel projet gratuit, kanban gratuit, suivi tâches, gestion projet Afrique, SARCIS">
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="kntLoBv2A8XXRTY4ZVgOuAM7U-fenKHk_POoVsL7mcY">
    <meta name="author" content="SARCIS">
    <link rel="canonical" href="https://sarcis.com{{ request()->getPathInfo() }}">

    {{-- Open Graph (réseaux sociaux) --}}
    <meta property="og:title" content="@yield('title', 'SARCIS') - Gestion de Projets Gratuit">
    <meta property="og:description" content="Plateforme gratuite de gestion de projets. Organisez vos tâches et collaborez avec votre équipe en temps réel.">
    <meta property="og:url" content="https://sarcis.com{{ request()->getPathInfo() }}">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/logos.png') }}">
    <meta property="og:site_name" content="SARCIS">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'SARCIS') - Gestion de Projets Gratuit">
    <meta name="twitter:description" content="Plateforme gratuite de gestion de projets en ligne.">
    <meta name="twitter:image" content="{{ asset('images/logos.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">



<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M8N2DW6G"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    {{-- Navbar --}}
    <nav class="bg-white shadow-md border-b border-gray-100" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 md:h-24 items-center">

                {{-- Logo + liens desktop --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="flex-shrink-0">
                        <img src="{{ asset('images/logos.png') }}" alt="Logo" class="h-10 md:h-20 w-auto">
                    </a>

                    <div class="hidden md:block w-px h-8 bg-gray-200 mx-3"></div>

                    {{-- Nav links desktop --}}
                    <div class="hidden md:flex items-center gap-1">
                        @auth
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition group
                                  {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 {{ request()->routeIs('dashboard') ? 'text-indigo-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('workspaces.index') }}"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition group
                                  {{ request()->routeIs('workspaces.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 {{ request()->routeIs('workspaces.*') ? 'text-indigo-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Espaces de travail
                        </a>

                        <a href="{{ route('projects.index') }}"
                           class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition group
                                  {{ request()->routeIs('projects.index') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 {{ request()->routeIs('projects.index') ? 'text-indigo-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Mes projets
                        </a>

                        @endauth
                    </div>
                </div>

                {{-- Droite --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    @auth
                        {{-- Cloche de notifications (polling automatique) --}}
                        @php $initUnread = \App\Models\UserNotification::where('user_id', Auth::id())->whereNull('read_at')->count(); @endphp
                        <div x-data="{ ...notifBell(), unreadCount: {{ $initUnread }} }" class="relative">
                            <button @click="open = !open"
                                    class="relative w-9 h-9 rounded-full flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span x-show="unreadCount > 0" x-cloak
                                      class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none"
                                      x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                            </button>

                            {{-- Dropdown notifications --}}
                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="fixed sm:absolute left-2 right-2 sm:left-auto sm:right-0 top-20 sm:top-12 sm:w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">

                                <div class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-bold text-indigo-700">Notifications</span>
                                        <span x-show="unreadCount > 0"
                                              x-text="unreadCount"
                                              class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full"></span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <template x-if="unreadCount > 0">
                                            <form method="POST" action="{{ route('notifications.readAll') }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-indigo-500 hover:text-indigo-700 font-semibold transition">
                                                    Tout marquer comme lu
                                                </button>
                                            </form>
                                        </template>
                                        <button @click="open = false" class="text-gray-400 hover:text-gray-600 sm:hidden">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="max-h-72 sm:max-h-80 overflow-y-auto">
                                    <template x-if="notifications.length === 0">
                                        <div class="px-4 py-8 text-center">
                                            <p class="text-2xl mb-2">🔔</p>
                                            <p class="text-sm text-gray-400">Aucune notification</p>
                                        </div>
                                    </template>
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition group"
                                             :class="notif.is_read ? 'bg-white' : 'bg-indigo-50/40'">
                                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm"
                                                 :class="{
                                                     'bg-indigo-100': notif.type === 'member_added',
                                                     'bg-rose-100':   notif.type === 'member_removed',
                                                     'bg-emerald-100':notif.type === 'task_assigned'
                                                 }"
                                                 x-text="notif.icon"></div>
                                            <div class="flex-1 min-w-0">
                                                <a :href="notif.read_url"
                                                   class="text-xs text-gray-700 leading-snug hover:text-indigo-600 transition block"
                                                   x-text="notif.message"></a>
                                                <p class="text-[10px] text-gray-400 mt-1" x-text="notif.time"></p>
                                            </div>
                                            <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
                                                <span x-show="!notif.is_read" class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                                <form :action="notif.delete_url" method="POST" @submit.prevent="deleteNotif(notif)">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-300 hover:text-rose-400 transition opacity-0 group-hover:opacity-100 text-xs">✕</button>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- Toast notification temps réel --}}
                            <template x-if="toast">
                                <div x-show="toast"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 translate-y-2"
                                     class="fixed bottom-5 right-5 z-50 max-w-sm bg-white border border-indigo-100 rounded-2xl shadow-2xl p-4 flex items-start gap-3 cursor-pointer"
                                     @click="open = true; dismissToast()">
                                    <span class="text-2xl flex-shrink-0" x-text="toast.icon"></span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800">Nouvelle notification</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="toast.message"></p>
                                    </div>
                                    <button @click.stop="dismissToast()" class="text-gray-300 hover:text-gray-500 flex-shrink-0 text-lg leading-none">✕</button>
                                </div>
                            </template>
                        </div>


                        {{-- Avatar avec dropdown --}}
                        @php
                            $avatarSrc = Auth::user()->avatar
                                ? (str_starts_with(Auth::user()->avatar, 'http') ? Auth::user()->avatar : asset('storage/' . Auth::user()->avatar))
                                : null;
                            $avatarInitial = strtoupper(substr(Auth::user()->name, 0, 1));
                        @endphp
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="w-9 h-9 rounded-full overflow-hidden shadow-md hover:shadow-indigo-300 hover:scale-105 transition-all duration-200 ring-2 ring-white flex-shrink-0">
                                @if($avatarSrc)
                                    <img src="{{ $avatarSrc }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 object-cover">
                                @else
                                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-sm font-bold">
                                        {{ $avatarInitial }}
                                    </div>
                                @endif
                            </button>

                            <div x-show="open" @click.outside="open = false" x-cloak
                                 class="absolute right-0 top-12 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">

                                <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-violet-50 border-b border-gray-100 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full overflow-hidden flex-shrink-0">
                                        @if($avatarSrc)
                                            <img src="{{ $avatarSrc }}" class="w-8 h-8 object-cover">
                                        @else
                                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-xs font-bold">
                                                {{ $avatarInitial }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-indigo-700 truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('profile.show') }}"
                                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition border-b border-gray-100">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Mon profil
                                </a>

                                @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2.5 px-4 py-3 text-sm text-rose-600 hover:bg-rose-50 transition font-semibold border-b border-gray-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Espace admin
                                </a>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Bouton hamburger mobile --}}
                        <button @click="mobileOpen = !mobileOpen"
                                class="md:hidden w-9 h-9 flex items-center justify-center rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                            <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-medium text-gray-600 hover:text-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-50 transition">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:shadow-md hover:shadow-indigo-200 transition-all duration-200">
                            S'inscrire
                        </a>
                    @endguest
                </div>

            </div>
        </div>

        {{-- Menu mobile (déplie sous la navbar) --}}
        @auth
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">

            <a href="{{ route('dashboard') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('workspaces.index') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs('workspaces.*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Espaces
            </a>

            <a href="{{ route('projects.index') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      {{ request()->routeIs('projects.index') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:text-indigo-600 hover:bg-indigo-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Mes projets
            </a>


            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="mobileOpen = false"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition
                      {{ request()->routeIs('admin.*') ? 'text-rose-600 bg-rose-50' : 'text-rose-500 hover:bg-rose-50' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Espace admin
            </a>
            @endif
        </div>
        @endauth
    </nav>

    {{-- Bannière de rafraîchissement automatique --}}
    @auth
    <div x-data="autoRefresh()"
         x-show="visible" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-0 inset-x-0 z-50 bg-indigo-600 text-white px-4 py-2.5 flex items-center justify-between gap-4 shadow-md">
        <div class="flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            <span>Des mises à jour sont disponibles. Rafraîchissement dans <strong x-text="countdown"></strong>s…</span>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <button @click="refreshNow()"
                    class="text-xs font-semibold bg-white text-indigo-600 px-3 py-1 rounded-lg hover:bg-indigo-50 transition">
                Rafraîchir maintenant
            </button>
            <button @click="dismiss()" class="text-indigo-200 hover:text-white text-lg leading-none">✕</button>
        </div>
    </div>
    @endauth

    {{-- Flash messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-4 right-4 left-4 sm:left-auto z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 transition max-w-sm sm:max-w-none sm:w-auto">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-4 right-4 left-4 sm:left-auto z-50 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg text-sm max-w-sm sm:max-w-none">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-4 right-4 left-4 sm:left-auto z-50 bg-blue-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 text-sm max-w-sm sm:max-w-none">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 mt-8">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-500">
            <span>© {{ date('Y') }} SARCIS. Tous droits réservés.</span>
            <div class="flex items-center gap-4 flex-wrap justify-center">
                <a href="{{ route('about') }}" class="text-gray-400 hover:text-indigo-600 transition">À propos</a>
                <a href="{{ route('features') }}" class="text-gray-400 hover:text-indigo-600 transition">Fonctionnalités</a>
                <a href="{{ route('contact.create') }}" class="text-gray-400 hover:text-indigo-600 transition">Nous contacter</a>
            </div>
        </div>
    </footer>

    {{-- Composants Alpine partagés --}}
    <script>
    document.addEventListener('alpine:init', () => {
        // Cochage rapide d'une tâche sans rechargement
        Alpine.data('quickTask', (url, initialStatus) => ({
            status: initialStatus,
            loading: false,
            get done() { return this.status === 'done'; },
            async toggle() {
                if (this.loading) return;
                this.loading = true;
                const newStatus = this.done ? 'in_progress' : 'done';
                try {
                    const res = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });
                    if (res.ok) this.status = newStatus;
                } finally {
                    this.loading = false;
                }
            }
        }));

        // Cloche de notifications (Server-Sent Events)
        Alpine.data('notifBell', () => ({
            open: false,
            unreadCount: 0,
            notifications: [],
            toast: null,
            toastTimer: null,
            es: null,
            init() {
                this.poll(); // chargement initial immédiat
                this.startSSE();
            },
            startSSE() {
                if (typeof EventSource === 'undefined') {
                    setInterval(() => this.poll(), 5000);
                    return;
                }
                const connect = () => {
                    this.es = new EventSource('{{ route('notifications.stream') }}');

                    const handle = (e) => {
                        try {
                            const data = JSON.parse(e.data);
                            const hadNew = data.has_new && !this.open;
                            this.unreadCount   = data.unread_count;
                            this.notifications = data.notifications;
                            if (hadNew && data.notifications.length > 0) {
                                this.showToast(data.notifications[0]);
                                window.dispatchEvent(new CustomEvent('page-activity'));
                            }
                        } catch(err) {}
                    };

                    this.es.addEventListener('new_notification', handle);
                    this.es.addEventListener('heartbeat', (e) => {
                        try {
                            const data = JSON.parse(e.data);
                            this.unreadCount   = data.unread_count;
                            this.notifications = data.notifications;
                        } catch(err) {}
                    });

                    this.es.onerror = () => {
                        this.es.close();
                        setTimeout(connect, 4000);
                    };
                };
                connect();
            },
            showToast(notif) {
                clearTimeout(this.toastTimer);
                this.toast = notif;
                this.toastTimer = setTimeout(() => { this.toast = null; }, 5000);
            },
            dismissToast() {
                clearTimeout(this.toastTimer);
                this.toast = null;
            },
            async poll() {
                try {
                    const res = await fetch('{{ route('notifications.poll') }}', {
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.unreadCount   = data.unread_count;
                    this.notifications = data.notifications;
                } catch(e) {}
            },
            async deleteNotif(notif) {
                try {
                    const body = new FormData();
                    body.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    body.append('_method', 'DELETE');
                    await fetch(notif.delete_url, { method: 'POST', body });
                    this.notifications = this.notifications.filter(n => n.id !== notif.id);
                    if (!notif.is_read && this.unreadCount > 0) this.unreadCount--;
                } catch(e) {}
            }
        }));

        // Rafraîchissement automatique
        Alpine.data('autoRefresh', () => ({
            visible: false,
            countdown: 10,
            timer: null,
            countdownTimer: null,
            lastActivity: Date.now(),
            idleMs: 5 * 60 * 1000, // 5 minutes d'inactivité
            init() {
                // Activité de page (SSE)
                window.addEventListener('page-activity', () => this.trigger());

                // Détection d'inactivité utilisateur
                ['mousemove','keydown','click','scroll','touchstart'].forEach(evt => {
                    document.addEventListener(evt, () => { this.lastActivity = Date.now(); }, { passive: true });
                });
                setInterval(() => {
                    if (!this.visible && (Date.now() - this.lastActivity) >= this.idleMs) {
                        this.triggerSilent();
                    }
                }, 30000);
            },
            trigger() {
                if (this.visible) return;
                this.visible   = true;
                this.countdown = 10;
                clearInterval(this.countdownTimer);
                this.countdownTimer = setInterval(() => {
                    this.countdown--;
                    if (this.countdown <= 0) {
                        clearInterval(this.countdownTimer);
                        window.location.reload();
                    }
                }, 1000);
            },
            triggerSilent() {
                window.location.reload();
            },
            refreshNow() {
                clearInterval(this.countdownTimer);
                window.location.reload();
            },
            dismiss() {
                clearInterval(this.countdownTimer);
                this.visible   = false;
                this.lastActivity = Date.now();
            }
        }));

        // Compteur animé
        Alpine.data('counter', (target) => ({
            value: 0,
            start() {
                if (target === 0) return;
                const duration = 800;
                const step = Math.ceil(target / (duration / 16));
                const interval = setInterval(() => {
                    this.value = Math.min(this.value + step, target);
                    if (this.value >= target) clearInterval(interval);
                }, 16);
            }
        }));
    });
    </script>

    @stack('scripts')
</body>
</html>
