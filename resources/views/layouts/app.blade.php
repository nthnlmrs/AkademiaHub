<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="AkademiaHub - Modern Learning Management System">

        <title>{{ config('app.name', 'AkademiaHub') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/interactive-reader.js') }}"></script>
    </head>
    <body class="font-sans antialiased bg-slate-50 text-slate-900 min-h-screen {{ Auth::check() && Auth::user()->high_contrast ? 'high-contrast' : '' }} {{ Auth::check() && Auth::user()->dyslexia_font ? 'dyslexia-font' : '' }}">
        <!-- Skip to Content Link for Screen Readers -->
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 focus:z-[100] focus:p-4 focus:bg-indigo-600 focus:text-white focus:font-bold">
            Skip to main content
        </a>

        <div class="flex min-h-screen">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col ml-0 lg:ml-64">
                <!-- Top Bar -->
                @include('layouts.topbar')

                <!-- Page Content -->
                <main id="main-content" class="flex-1 p-4 lg:p-8">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert" aria-live="polite">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-error" role="alert" aria-live="assertive">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Notifications Sidebar (Off-canvas) -->
        @include('layouts.notifications-sidebar')

        <!-- Mobile sidebar overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('notifications', {
                    open: false,
                    toggle() { this.open = !this.open },
                    show()   { this.open = true },
                    close()  { this.open = false }
                });

                Alpine.store('accessibility', {
                    highContrast: {{ Auth::check() && Auth::user()->high_contrast ? 'true' : 'false' }},
                    dyslexicFont: {{ Auth::check() && Auth::user()->dyslexia_font ? 'true' : 'false' }},
                    readAloud:    {{ Auth::check() && Auth::user()->tts_enabled   ? 'true' : 'false' }},
                });
            });

            window.addEventListener('toggle-reader', () => toggleReadAloud());

            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            async function _toggleAccessibility(setting, bodyClass, storeKey) {
                try {
                    const res = await fetch('{{ route('profile.accessibility.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ setting }),
                    });
                    const data = await res.json();
                    if (bodyClass) document.body.classList.toggle(bodyClass, data.value);
                    if (window.Alpine) {
                        Alpine.store('accessibility')[storeKey] = data.value;
                    }
                } catch (e) {
                    console.error('Accessibility toggle failed', e);
                }
            }

            function toggleHighContrast()  { _toggleAccessibility('high_contrast', 'high-contrast', 'highContrast'); }
            function toggleDyslexicFont()  { _toggleAccessibility('dyslexia_font',  'dyslexia-font',  'dyslexicFont');  }
            function toggleReadAloud()     { _toggleAccessibility('tts_enabled',    null,             'readAloud');      }
        </script>
    </body>
</html>
