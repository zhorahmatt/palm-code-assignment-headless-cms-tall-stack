<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CMS Portal</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center">
            <div class="text-center">
                <!-- Logo/Title -->
                <div class="mb-12">
                    <h1 class="text-6xl font-bold text-white mb-4">Palm CMS Portal</h1>
                    <p class="text-xl text-gray-300">Welcome to your content management system</p>
                </div>

                <!-- Authentication Buttons -->
                @if (Route::has('login'))
                    <div class="space-y-6">
                        @auth
                            <a href="{{ url('/admin/dashboard') }}"
                               class="inline-block w-80 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xl font-semibold rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                                Go to Dashboard
                            </a>
                        @else
                            <div class="space-y-4">
                                <a href="{{ route('login') }}"
                                   class="block w-80 mx-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-xl font-semibold rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                                    Login
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                       class="block w-80 mx-auto px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white text-xl font-semibold rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                                        Register
                                    </a>
                                @endif
                            </div>
                        @endauth
                    </div>
                @endif

                <!-- Footer -->
                <div class="mt-16">
                    <p class="text-gray-500 text-sm">Powered by Laravel & Livewire</p>
                </div>
            </div>
        </div>
    </body>
</html>
