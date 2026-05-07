<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>AkademiaHub - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        </style>
    </head>
    <body class="antialiased bg-[#f3f2f1] min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-[440px] relative z-10 bg-white border border-gray-200 shadow-[0_2px_6px_rgba(0,0,0,0.2)] p-10 rounded-sm">
            <!-- Logo -->
            <div class="mb-6 text-left">
                <span class="text-[2rem] font-bold text-[#1b1b1b] tracking-tight">Akademia<span class="text-[#0067b8]">Hub</span></span>
            </div>

            <!-- Login Content -->
            <div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
