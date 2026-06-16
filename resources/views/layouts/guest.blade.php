<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Learning Management System - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/interactive-reader.js') }}"></script>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-[#f3f2f1] min-h-screen flex items-center justify-center p-4"
<<<<<<< HEAD
          x-data="{
              highContrast: localStorage.getItem('highContrast') === 'true',
              dyslexicFont: localStorage.getItem('dyslexicFont') === 'true',
              toggleHighContrast() {
                  this.highContrast = !this.highContrast;
                  localStorage.setItem('highContrast', this.highContrast);
              },
              toggleDyslexicFont() {
                  this.dyslexicFont = !this.dyslexicFont;
                  localStorage.setItem('dyslexicFont', this.dyslexicFont);
              }
          }"
          :class="{ 'high-contrast': highContrast, 'dyslexic-font': dyslexicFont }">

=======
          x-data="{
              highContrast: localStorage.getItem('highContrast') === 'true',
              dyslexicFont: localStorage.getItem('dyslexicFont') === 'true',
              toggleHighContrast() {
                  this.highContrast = !this.highContrast;
                  localStorage.setItem('highContrast', this.highContrast);
              },
              toggleDyslexicFont() {
                  this.dyslexicFont = !this.dyslexicFont;
                  localStorage.setItem('dyslexicFont', this.dyslexicFont);
              }
          }"
          :class="{ 'high-contrast': highContrast, 'dyslexic-font': dyslexicFont }">

>>>>>>> origin/main
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-white focus:text-[#0067b8] focus:font-bold">Skip to main content</a>

        <!-- Accessibility Toggles for Guest Page -->
        <div class="absolute top-4 right-4 z-50">
            <div x-data="{ open: false }" class="relative">
<<<<<<< HEAD
                <button @click="open = !open"
=======
                <button @click="open = !open"
>>>>>>> origin/main
                    class="relative w-10 h-10 bg-white border border-gray-300 flex items-center justify-center rounded-sm text-slate-500 hover:text-indigo-600 hover:bg-slate-50 shadow-sm transition-all duration-200"
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
                    class="absolute right-0 mt-2 w-[calc(100vw-2rem)] sm:w-56 max-w-sm bg-white border border-slate-100 rounded-sm shadow-[0_8px_40px_rgba(0,0,0,0.12)] overflow-hidden py-2 z-50 origin-top-right">
                    <div class="px-4 pb-2 mb-1 border-b border-slate-100">
                        <p class="text-sm font-semibold text-slate-800">Accessibility Tools</p>
                    </div>
<<<<<<< HEAD

=======

>>>>>>> origin/main
                    <button @click="toggleHighContrast()" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            High Contrast
                        </span>
                        <div class="w-8 h-4 bg-slate-200 rounded-full relative" :class="{'bg-indigo-500': highContrast}">
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform" :class="{'translate-x-4': highContrast, 'translate-x-0': !highContrast}"></div>
                        </div>
                    </button>

                    <button @click="toggleDyslexicFont()" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="font-serif font-bold text-slate-400 text-base" aria-hidden="true">A</span>
                            Dyslexia Font
                        </span>
                        <div class="w-8 h-4 bg-slate-200 rounded-full relative" :class="{'bg-indigo-500': dyslexicFont}">
                            <div class="absolute w-4 h-4 bg-white rounded-full shadow-sm transform transition-transform" :class="{'translate-x-4': dyslexicFont, 'translate-x-0': !dyslexicFont}"></div>
                        </div>
                    </button>
<<<<<<< HEAD

=======

>>>>>>> origin/main
                    <button onclick="window.dispatchEvent(new CustomEvent('toggle-reader'))" class="w-full flex items-center justify-between px-4 py-3 sm:py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" /></svg>
                            Read Aloud Mode
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <main id="main-content" class="w-full max-w-[440px] relative z-10 bg-white border border-gray-200 shadow-[0_2px_6px_rgba(0,0,0,0.2)] p-6 sm:p-10 rounded-sm" role="main">
            <!-- Logo -->
            <div class="mb-6 text-left">
                <h1 class="text-[1.75rem] sm:text-[2rem] font-bold text-[#1b1b1b] tracking-tight">Learning<span class="text-[#0067b8]"> Management System</span></h1>
            </div>

            <!-- Login Content -->
            <div>
                {{ $slot }}
            </div>
        </main>
    </body>
</html>
