<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login — {{ config('app.name', 'SHMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4">

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-md p-8 sm:p-10">

            {{-- Header --}}
            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-xl mb-4">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                                 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29
                                 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 leading-tight">Welcome Back!</h1>
                <p class="mt-1 text-sm text-gray-500">Structural Health Monitoring System</p>
            </div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900
                               placeholder-gray-400 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                               transition
                               @error('email') border-red-400 bg-red-50 @enderror"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900
                               placeholder-gray-400 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                               transition
                               @error('password') border-red-400 bg-red-50 @enderror"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button
                    type="submit"
                    class="w-full flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold py-2.5 px-4 rounded-lg shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                           transition-colors duration-200"
                >
                    Log In
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

        </div>

        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} SHMS. All rights reserved.
        </p>
    </div>

</body>
</html>
