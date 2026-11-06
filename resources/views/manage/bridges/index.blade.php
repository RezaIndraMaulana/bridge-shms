<x-app-layout>
    <x-slot name="pageTitle">Manage Bridge</x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
    <div id="flash-success"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm shadow-sm transition">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">✕</button>
    </div>
    @endif

    {{-- Card Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        {{-- Header: Title, Search, and Add Bridge button --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">
                Bridge List
            </h2>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <form method="GET" action="{{ route('manage.bridges.index') }}" class="relative">
                    <input id="search-input"
                           name="search"
                           type="text"
                           value="{{ request('search') }}"
                           placeholder="Search"
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400 w-64 text-gray-700 placeholder-gray-400 transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </form>

                {{-- Add Bridge button --}}
                <a href="{{ route('manage.bridges.create') }}"
                   class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Bridge
                </a>
            </div>
        </div>

        {{-- Table matching media_1792867909161.png --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 border-b border-gray-100 pb-3">
                        <th class="py-3 px-3 w-16">
                            <span class="inline-flex items-center gap-1 cursor-pointer hover:text-gray-700">
                                No
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </th>
                        <th class="py-3 px-4 font-semibold text-gray-600 w-52">Name</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Location</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 w-48">
                            <span class="inline-flex items-center gap-1 cursor-pointer hover:text-gray-700">
                                Created At
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-center w-24">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bridges as $bridge)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        {{-- No --}}
                        <td class="py-4 px-3 text-gray-500 text-sm">
                            {{ ($bridges->currentPage() - 1) * $bridges->perPage() + $loop->iteration }}
                        </td>

                        {{-- Name --}}
                        <td class="py-4 px-4 font-medium text-gray-800">
                            <a href="{{ route('dashboard.bridge.show', $bridge) }}" class="hover:text-blue-600 transition">
                                {{ $bridge->name }}
                            </a>
                        </td>

                        {{-- Location (City + Coordinates) --}}
                        <td class="py-4 px-4 text-gray-600 text-xs leading-relaxed">
                            <span>{{ optional($bridge->city)->name ?? 'Indonesia' }}</span>
                            <span class="text-gray-400 font-mono text-[11px] ml-1.5">({{ number_format($bridge->latitude, 4) }}, {{ number_format($bridge->longitude, 4) }})</span>
                        </td>

                        {{-- Created At --}}
                        <td class="py-4 px-4 text-gray-500 text-xs font-mono">
                            {{ $bridge->created_at ? $bridge->created_at->format('d/m/Y, H:i:s') : '—' }}
                        </td>

                        {{-- Action --}}
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Edit (Green Pencil like in screenshot) --}}
                                <a href="{{ route('manage.bridges.edit', $bridge) }}"
                                   title="Edit Bridge"
                                   class="text-emerald-500 hover:text-emerald-700 transition p-1.5 rounded hover:bg-emerald-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('manage.bridges.destroy', $bridge) }}"
                                      onsubmit="return confirm('Delete bridge \'{{ addslashes($bridge->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Bridge"
                                            class="text-gray-400 hover:text-red-500 transition p-1.5 rounded hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-400 text-sm">
                            No bridge data found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer matching screenshot: 10 dropdown on left, pagination on right --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 mt-4 border-t border-gray-100 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 border border-gray-200 rounded-lg bg-gray-50 font-medium text-gray-700 flex items-center gap-1.5">
                    10
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                <span class="text-gray-400">Total: {{ $bridges->total() }} items</span>
            </div>

            <div>
                {{ $bridges->links() }}
            </div>
        </div>

    </div>

</x-app-layout>
