<x-app-layout>
    <x-slot name="pageTitle">Add Bridge</x-slot>

    {{-- Breadcrumb --}}
    <div class="mb-4 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('manage.bridges.index') }}" class="hover:text-blue-600 transition-colors">Manage Bridge</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-700 font-medium">Add Bridge</span>
    </div>

    <form method="POST" action="{{ route('manage.bridges.store') }}" id="bridge-form" enctype="multipart/form-data">
        @csrf

        {{-- Side-by-side: Form on left, Map on right (matching user screenshot) --}}
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            {{-- ── Left: Form (7/12 width) ─────────────────────────────── --}}
            <div class="w-full lg:w-7/12 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">
                    Add bridge
                </h2>

                @include('manage.bridges._form', ['bridge' => null, 'cities' => $cities])

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700
                                   text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Bridge
                    </button>
                    <a href="{{ route('manage.bridges.index') }}"
                       class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg
                              hover:bg-gray-50 hover:border-gray-300 transition-all">
                        Cancel
                    </a>
                </div>
            </div>

            {{-- ── Right: Map (5/12 width) ────────────────────────────── --}}
            <div class="w-full lg:w-5/12 sticky top-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    {{-- Search Location on Map --}}
                    <div class="relative mb-3">
                        <input type="text"
                               id="map-search-input"
                               placeholder="Enter a location"
                               class="w-full text-xs pl-8 pr-16 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                        <button type="button"
                                id="btn-search-location"
                                class="absolute right-1.5 top-1/2 -translate-y-1/2 px-3 py-1 text-xs bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                            Find
                        </button>
                    </div>

                    {{-- Map Container --}}
                    <div id="pick-map" class="w-full rounded-lg overflow-hidden border border-gray-100" style="height: 480px; z-index: 1;"></div>

                    {{-- Coordinate Readout --}}
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-1">Latitude</label>
                            <input type="text" id="lat-display" readonly
                                   value="-6.914744"
                                   class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 font-mono text-gray-700">
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-gray-500 mb-1">Longitude</label>
                            <input type="text" id="lng-display" readonly
                                   value="107.609810"
                                   class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5 font-mono text-gray-700">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const initialLat = -6.914744;
        const initialLng = 107.609810;

        const pickMap = L.map('pick-map').setView([initialLat, initialLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors', maxZoom: 18
        }).addTo(pickMap);

        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const latDisplay = document.getElementById('lat-display');
        const lngDisplay = document.getElementById('lng-display');

        let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(pickMap);

        function updateCoords(lat, lng) {
            latInput.value = lat.toFixed(8);
            lngInput.value = lng.toFixed(8);
            latDisplay.value = lat.toFixed(8);
            lngDisplay.value = lng.toFixed(8);
        }

        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        pickMap.on('click', function(e) {
            const { lat, lng } = e.latlng;
            marker.setLatLng([lat, lng]);
            updateCoords(lat, lng);
        });

        // Search Location using Nominatim
        document.getElementById('btn-search-location').addEventListener('click', searchLocation);
        document.getElementById('map-search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchLocation();
            }
        });

        function searchLocation() {
            const query = document.getElementById('map-search-input').value.trim();
            if (!query) return;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const first = data[0];
                        const lat = parseFloat(first.lat);
                        const lng = parseFloat(first.lon);
                        pickMap.setView([lat, lng], 14);
                        marker.setLatLng([lat, lng]);
                        updateCoords(lat, lng);
                    } else {
                        alert('Location not found. Please try another search term.');
                    }
                })
                .catch(() => alert('Failed to search location.'));
        }
    </script>
</x-app-layout>
