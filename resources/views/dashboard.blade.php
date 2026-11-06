<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- Company Portal Banner --}}
    @if(Auth::user()->isCompany() && Auth::user()->company)
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-100 rounded-xl px-5 py-3.5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                {{ strtoupper(substr(Auth::user()->company->name, 0, 2)) }}
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-full mb-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                    Company Portal
                </span>
                <h1 class="text-base font-bold text-gray-900 leading-tight">{{ Auth::user()->company->name }}</h1>
            </div>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs bg-white text-indigo-700 font-semibold px-3 py-1.5 rounded-lg border border-indigo-100 shadow-xs">
                {{ $bridges->count() }} Bridge(s) Owned
            </span>
        </div>
    </div>
    @endif

    {{-- =========================================================
         MAPS OF BRIDGES
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="flex items-center justify-between px-5 pt-4 pb-3">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6
                             3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0
                             8V9m0 0L9 7"/>
                </svg>
                Maps of Bridges
            </h2>
            <span class="text-xs text-gray-400">{{ $bridges->count() }} bridge(s) registered</span>
        </div>

        <div id="map" class="w-full" style="height: 380px;"></div>
    </div>

    {{-- =========================================================
         BRIDGE LIST
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h8m-8 6h16"/>
                </svg>
                Bridge List
            </h2>

            {{-- Search --}}
            <div class="relative">
                <input id="bridge-search"
                       type="text"
                       placeholder="Search bridge..."
                       class="pl-9 pr-4 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 w-56 transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="bridge-table">
                <thead>
                    <tr class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                        <th class="px-5 py-3 font-semibold w-12">No</th>
                        <th class="px-5 py-3 font-semibold">Name</th>
                        <th class="px-5 py-3 font-semibold">Location</th>
                        <th class="px-5 py-3 font-semibold">Rating</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Created At</th>
                        <th class="px-5 py-3 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="bridge-tbody">
                    @foreach ($bridges as $idx => $bridge)
                    <tr class="hover:bg-blue-50/40 transition-colors bridge-row"
                        data-name="{{ strtolower($bridge->name) }}">
                        <td class="px-5 py-3.5 text-gray-500">{{ $idx + 1 }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-800">{{ $bridge->name }}</td>
                        <td class="px-5 py-3.5 text-gray-500">
                            {{ optional($bridge->city)->name ?? '—' }}
                            <span class="text-xs text-gray-400">
                                ({{ $bridge->latitude }}, {{ $bridge->longitude }})
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            @php $r = $bridge->rating ?? 0; @endphp
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $r >= 8 ? 'bg-green-100 text-green-700' : ($r >= 6 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $r }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                {{ str_contains(strtolower($bridge->maintenance_status ?? ''), 'good') ? 'bg-green-100 text-green-700' :
                                   (str_contains(strtolower($bridge->maintenance_status ?? ''), 'routine') ? 'bg-blue-100 text-blue-700' :
                                   (str_contains(strtolower($bridge->maintenance_status ?? ''), 'corrective') ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700')) }}">
                                {{ $bridge->maintenance_status ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">
                            {{ $bridge->created_at ? $bridge->created_at->format('d M Y H:i:s') : '—' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('dashboard.bridge.show', $bridge) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700
                                      text-white text-xs font-semibold rounded-lg transition-all shadow-sm hover:shadow-md">
                                View Detail
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($bridges->isEmpty())
        <div class="py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 9m0 8V9m0 0L9 7"/>
            </svg>
            <p class="text-sm">No bridges registered yet.</p>
        </div>
        @endif

        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400">
            Showing {{ $bridges->count() }} bridge(s)
        </div>
    </div>

    {{-- Leaflet CSS + JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // ─── Map ─────────────────────────────────────────────────────────────
        const map = L.map('map').setView([-2.5, 118.0], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(map);

        @php
        $bridgesMapData = $bridges->map(function($b) {
            return [
                'id'     => $b->id,
                'name'   => $b->name,
                'lat'    => $b->latitude,
                'lng'    => $b->longitude,
                'rating' => $b->rating,
                'status' => $b->maintenance_status,
                'url'    => route('dashboard.bridge.show', $b),
            ];
        });
        @endphp

        const bridges = @json($bridgesMapData);
        const markers = [];

        bridges.forEach(b => {
            const popup = `
                <div style="min-width:160px">
                    <p style="font-weight:700;font-size:13px;margin:0 0 4px">${b.name}</p>
                    <p style="font-size:11px;color:#555;margin:0 0 2px">Rating: <b>${b.rating ?? '—'}</b></p>
                    <p style="font-size:11px;color:#555;margin:0 0 8px">${b.status ?? ''}</p>
                    <a href="${b.url}" style="background:#2563eb;color:#fff;padding:4px 10px;border-radius:6px;font-size:11px;text-decoration:none">
                        View Detail →
                    </a>
                </div>`;
            const m = L.marker([b.lat, b.lng]).addTo(map).bindPopup(popup);
            markers.push(m);
        });

        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        }

        // ─── Search ───────────────────────────────────────────────────────────
        document.getElementById('bridge-search').addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.bridge-row').forEach(row => {
                row.style.display = row.dataset.name.includes(q) ? '' : 'none';
            });
        });
    </script>

</x-app-layout>
