<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SHMS') }} — {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="flex h-screen overflow-hidden">

        {{-- =========================================
             SIDEBAR
        ========================================= --}}
        <aside id="sidebar"
               class="flex flex-col w-64 flex-shrink-0 bg-slate-800 text-white transition-all duration-300">

            {{-- Logo --}}
            <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-700">
                <div class="flex items-center justify-center w-9 h-9 bg-blue-500 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944
                                 a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9
                                 c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03
                                 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-widest text-white">SHMS</span>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">

                {{-- Section: MENU --}}
                <div>
                    <span class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">MENU</span>
                    <div class="mt-2 space-y-1">
                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('dashboard*') && !request()->routeIs('manage.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1
                                         0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1
                                         0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                    </div>
                </div>

                {{-- Section: MASTER DATA (Superadmin Only) --}}
                @if(Auth::user()->isSuperAdmin())
                <div>
                    <span class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">MASTER DATA</span>
                    <div class="mt-2 space-y-1">
                        {{-- Manage Bridge --}}
                        <a href="{{ route('manage.bridges.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.bridges.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4 6h16M4 12h16M4 18h7"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3 9a9 9 0 0118 0"/>
                            </svg>
                            Manage Bridge
                        </a>

                        {{-- Manage Sensor --}}
                        <a href="{{ route('manage.sensors.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.sensors.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9
                                         3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0
                                         0h18"/>
                            </svg>
                            Manage Sensor
                        </a>

                        {{-- Manage User --}}
                        <a href="{{ route('manage.users.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                         0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3
                                         0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0
                                         0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6
                                         3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Manage User
                        </a>

                        {{-- Manage Company --}}
                        <a href="{{ route('manage.companies.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.companies.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                                         1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Manage Company
                        </a>
                    </div>
                </div>
                @elseif(Auth::user()->isCompany())
                <div>
                    <span class="px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">COMPANY DATA</span>
                    <div class="mt-2 space-y-1">
                        {{-- Manage User (PIC) --}}
                        <a href="{{ route('manage.users.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10
                                         0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3
                                         0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0
                                         0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6
                                         3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Manage User
                        </a>

                        {{-- Manage Company --}}
                        <a href="{{ route('manage.companies.index') }}"
                           class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                                  {{ request()->routeIs('manage.companies.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9
                                         0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1
                                         1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Manage Company
                        </a>
                    </div>
                </div>
                @endif

            </nav>

            {{-- Sidebar Footer --}}
            <div class="px-4 py-4 border-t border-slate-700 text-xs text-slate-500 text-center">
                SHMS &copy; {{ date('Y') }}
            </div>
        </aside>

        {{-- =========================================
             MAIN AREA (Header + Content)
        ========================================= --}}
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            {{-- HEADER / NAVBAR --}}
            <header class="flex items-center justify-between flex-shrink-0 bg-white shadow-sm px-6 py-3 z-10">

                {{-- Page Title Slot (optional) --}}
                <div class="text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    {{ $pageTitle ?? 'Dashboard' }}
                </div>

                {{-- User Info + Logout --}}
                <div class="flex items-center gap-4">

                    {{-- Avatar + Name --}}
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full {{ Auth::user()->isCompany() ? 'bg-indigo-600' : 'bg-blue-600' }} flex items-center justify-center
                                    text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-medium text-gray-800 leading-none">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                @if(Auth::user()->isCompany() && Auth::user()->company)
                                    <span class="text-indigo-600 font-medium">{{ Auth::user()->company->name }}</span>
                                @else
                                    <span class="capitalize">{{ Auth::user()->role }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="w-px h-6 bg-gray-200"></div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-red-600
                                       transition-colors duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0
                                         01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3
                                         3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 overflow-y-auto bg-gray-100 p-6">
                {{ $slot }}
            </main>

        </div>{{-- end main area --}}

    </div>{{-- end flex wrapper --}}

</body>
</html>
