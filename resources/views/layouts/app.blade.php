<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="h-screen flex overflow-hidden bg-gray-50">
            <!-- Static sidebar for desktop -->
            <div class="hidden lg:flex lg:shrink-0">
                <div class="flex flex-col w-64 border-r border-gray-200 pt-5 pb-4 bg-gray-100">
                    <div class="flex items-center shrink-0 px-6">
                        <a href="{{ route('dashboard.index') }}">

                        </a>
                    </div>
                    <!-- Sidebar component -->
                    <div class="h-0 flex-1 flex flex-col overflow-y-auto">
                        @livewire('navigation-menu')
                    </div>
                </div>
            </div>
            <!-- Main column -->
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <!-- Main content -->
                <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                    <!-- Page content -->
                    <div class="container mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
