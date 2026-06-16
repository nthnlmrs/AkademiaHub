<header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-slate-200/50 shadow-sm">
    <div class="flex items-center justify-between px-4 lg:px-8 h-16">
        <!-- Mobile menu -->
        <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <!-- Page title -->
        <div class="hidden lg:block">
            <h1 class="text-lg font-semibold text-slate-800">@yield('title', 'Dashboard')</h1>
        </div>

        <!-- Right side -->
        <div class="flex items-center gap-3">
            <span class="text-sm text-slate-400 hidden sm:block">{{ now()->format('l, d M Y') }}</span>

            <!-- Accessibility Settings Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="relative w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200"
                    aria-label="Accessibility Settings"
                    title="Accessibility Settings">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-[-4rem] sm:right-0 mt-2 w-[calc(100vw-2rem)] sm:w-56 max-w-sm bg-white border border-slate-100 rounded-2xl shadow-[0_8px_40px_rgba(0,0,0,0.12)] overflow-hidden py-2 z-50 origin-top-right">
                    <div class="px-4 pb-2 mb-1 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800">Accessibility Tools</p>
                    </div>

                    <button @click="toggleHighContrast()" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            High Contrast
                        </span>
                        <div class="w-8 h-4 bg-slate-200 rounded-full relative" :class="{'bg-indigo-500': $store.accessibility.highContrast}">
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform" :class="{'translate-x-4': $store.accessibility.highContrast, 'translate-x-0': !$store.accessibility.highContrast}"></div>
                        </div>
                    </button>

                    <button @click="toggleDyslexicFont()" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="font-serif font-bold text-slate-400 text-base" aria-hidden="true">A</span>
                            Dyslexia Font
                        </span>
                        <div class="w-8 h-4 bg-slate-200 rounded-full relative" :class="{'bg-indigo-500': $store.accessibility.dyslexicFont}">
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform" :class="{'translate-x-4': $store.accessibility.dyslexicFont, 'translate-x-0': !$store.accessibility.dyslexicFont}"></div>
                        </div>
                    </button>

                    <button @click="toggleReadAloud()" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                            Read Aloud Mode
                        </span>
                        <div class="w-8 h-4 bg-slate-200 rounded-full relative" :class="{'bg-indigo-500': $store.accessibility.readAloud}">
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform" :class="{'translate-x-4': $store.accessibility.readAloud, 'translate-x-0': !$store.accessibility.readAloud}"></div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Notifications -->
            <div x-data class="relative">
                <!-- Bell Button -->
                <button @click="$store.notifications.show()"
                    class="relative w-9 h-9 flex items-center justify-center rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-rose-600 text-[0.55rem] font-bold text-white shadow-sm ring-2 ring-white">
                            {{ Auth::user()->unreadNotifications->count() > 9 ? '9+' : Auth::user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
            </div>

            <!-- Profile dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 transition-all">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.away="open = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-52 bg-white border border-slate-100 rounded-2xl shadow-[0_8px_40px_rgba(0,0,0,0.12)] overflow-hidden py-2">
                    <!-- User info -->
                    <div class="px-4 pb-2 mb-1 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400">{{ Auth::user()->role_label }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
