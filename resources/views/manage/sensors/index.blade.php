<x-app-layout>
    <x-slot name="pageTitle">Manage Sensor</x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
    <div id="flash-success"
         class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">✕</button>
    </div>
    @endif

    @if(session('error'))
    <div id="flash-error"
         class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="font-medium">{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400 hover:text-red-600">✕</button>
    </div>
    @endif

    {{-- Card Container --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        {{-- Header: Title, Search, and Add Sensor button --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">
                Sensor List
            </h2>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <form method="GET" action="{{ route('manage.sensors.index') }}" class="relative">
                    <input id="sensor-search"
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

                {{-- Add Sensor button --}}
                <button id="btn-open-add-modal"
                        onclick="openAddModal()"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Sensor
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 border-b border-gray-100">
                        <th class="py-3 px-3 w-16">
                            <span class="inline-flex items-center gap-1">
                                No
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Name</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Type</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Brand / Model</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">MAC Address</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Bridge</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 w-44">
                            <span class="inline-flex items-center gap-1">
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
                    @forelse($sensors as $sensor)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        {{-- No --}}
                        <td class="py-3.5 px-3 text-gray-500 text-sm">
                            {{ ($sensors->currentPage() - 1) * $sensors->perPage() + $loop->iteration }}
                        </td>

                        {{-- Name --}}
                        <td class="py-3.5 px-4 font-medium text-gray-800">
                            {{ $sensor->name ?? '—' }}
                        </td>

                        {{-- Type --}}
                        <td class="py-3.5 px-4">
                            @if($sensor->type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ $sensor->type }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Brand/Model --}}
                        <td class="py-3.5 px-4 text-gray-500 text-xs">
                            {{ $sensor->brand_model ?? '—' }}
                        </td>

                        {{-- MAC Address --}}
                        <td class="py-3.5 px-4 text-gray-400 text-xs font-mono">
                            {{ $sensor->mac_address }}
                        </td>

                        {{-- Bridge --}}
                        <td class="py-3.5 px-4 text-gray-600 text-xs">
                            @if($sensor->bridge)
                                <a href="{{ route('dashboard.bridge.show', $sensor->bridge) }}"
                                   class="hover:text-blue-600 transition">
                                    {{ $sensor->bridge->name }}
                                </a>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Created At --}}
                        <td class="py-3.5 px-4 text-gray-400 text-xs font-mono whitespace-nowrap">
                            {{ $sensor->created_at ? $sensor->created_at->format('M d, Y H:i:s') : '—' }}
                        </td>

                        {{-- Actions --}}
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Edit --}}
                                <button type="button"
                                        title="Edit Sensor"
                                        onclick="openEditModal({{ $sensor->id }}, {{ json_encode($sensor->name) }}, {{ json_encode($sensor->type) }}, {{ json_encode($sensor->brand_model) }}, {{ json_encode($sensor->mac_address) }}, {{ $sensor->bridge_id }})"
                                        class="text-emerald-500 hover:text-emerald-700 transition p-1.5 rounded hover:bg-emerald-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>

                                {{-- Delete --}}
                                <form method="POST" action="{{ route('manage.sensors.destroy', $sensor) }}"
                                      onsubmit="return confirm('Delete sensor \'{{ addslashes($sensor->name ?? $sensor->mac_address) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete Sensor"
                                            class="text-red-400 hover:text-red-600 transition p-1.5 rounded hover:bg-red-50">
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
                        <td colspan="8" class="py-16 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                            </svg>
                            <p class="font-medium">No sensors found.</p>
                            @if(request('search'))
                            <p class="text-xs mt-1">Try a different search term.</p>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 mt-4 border-t border-gray-100 text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 border border-gray-200 rounded-lg bg-gray-50 font-medium text-gray-700 flex items-center gap-1.5">
                    10
                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
                <span class="text-gray-400">Total: {{ $sensors->total() }} sensor(s)</span>
            </div>
            <div>
                {{ $sensors->links() }}
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         ADD SENSOR MODAL
    ═══════════════════════════════════════════════ --}}
    <div id="modal-add"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modal-add-title">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter: blur(2px);" onclick="closeAddModal()"></div>

        {{-- Modal Card --}}
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl z-10 overflow-hidden animate-modal">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 id="modal-add-title" class="text-base font-semibold text-gray-800">Add Sensor</h3>
                <button type="button" onclick="closeAddModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form method="POST" action="{{ route('manage.sensors.store') }}" id="form-add-sensor">
                @csrf
                <div class="px-6 py-5 space-y-4">

                    {{-- Name --}}
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text"
                               name="name"
                               id="add_name"
                               placeholder="e.g. Sunspot WSN"
                               required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Type --}}
                    <div>
                        <label for="add_type" class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <input type="text"
                               name="type"
                               id="add_type"
                               placeholder="e.g. A12"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Brand / Model --}}
                    <div>
                        <label for="add_brand_model" class="block text-sm font-medium text-gray-700 mb-1.5">Brand / Model</label>
                        <input type="text"
                               name="brand_model"
                               id="add_brand_model"
                               placeholder="e.g. Crossbow MICA2DOT"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- MAC Address --}}
                    <div>
                        <label for="add_mac" class="block text-sm font-medium text-gray-700 mb-1.5">MAC Address <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="mac_address"
                               id="add_mac"
                               placeholder="e.g. 00:1A:2B:3C:4D:5E"
                               required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono">
                    </div>

                    {{-- Bridge --}}
                    <div>
                        <label for="add_bridge_id" class="block text-sm font-medium text-gray-700 mb-1.5">Bridge <span class="text-red-500">*</span></label>
                        <select name="bridge_id"
                                id="add_bridge_id"
                                required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Choose Bridge</option>
                            @foreach($bridges as $bridge)
                                <option value="{{ $bridge->id }}">{{ $bridge->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         EDIT SENSOR MODAL
    ═══════════════════════════════════════════════ --}}
    <div id="modal-edit"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modal-edit-title">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter: blur(2px);" onclick="closeEditModal()"></div>

        {{-- Modal Card --}}
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl z-10 overflow-hidden animate-modal">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 id="modal-edit-title" class="text-base font-semibold text-gray-800">Edit Sensor</h3>
                <button type="button" onclick="closeEditModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal Form --}}
            <form method="POST" id="form-edit-sensor" action="">
                @csrf
                @method('PUT')
                <div class="px-6 py-5 space-y-4">

                    {{-- Name --}}
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name</label>
                        <input type="text"
                               name="name"
                               id="edit_name"
                               required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Type --}}
                    <div>
                        <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <input type="text"
                               name="type"
                               id="edit_type"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Brand / Model --}}
                    <div>
                        <label for="edit_brand_model" class="block text-sm font-medium text-gray-700 mb-1.5">Brand / Model</label>
                        <input type="text"
                               name="brand_model"
                               id="edit_brand_model"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- MAC Address --}}
                    <div>
                        <label for="edit_mac" class="block text-sm font-medium text-gray-700 mb-1.5">MAC Address <span class="text-red-500">*</span></label>
                        <input type="text"
                               name="mac_address"
                               id="edit_mac"
                               required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition font-mono">
                    </div>

                    {{-- Bridge --}}
                    <div>
                        <label for="edit_bridge_id" class="block text-sm font-medium text-gray-700 mb-1.5">Bridge <span class="text-red-500">*</span></label>
                        <select name="bridge_id"
                                id="edit_bridge_id"
                                required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Choose Bridge</option>
                            @foreach($bridges as $bridge)
                                <option value="{{ $bridge->id }}">{{ $bridge->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Validation errors (shown in modal context) --}}
    @if($errors->any())
    <script>
        // Re-open the relevant modal if there are validation errors
        document.addEventListener('DOMContentLoaded', function () {
            @if(old('_method') === 'PUT')
                openEditModal(
                    {{ old('_sensor_id', 0) }},
                    {{ json_encode(old('name')) }},
                    {{ json_encode(old('type')) }},
                    {{ json_encode(old('brand_model')) }},
                    {{ json_encode(old('mac_address')) }},
                    {{ old('bridge_id', 0) }}
                );
            @else
                openAddModal();
                document.getElementById('add_name').value = {{ json_encode(old('name')) }};
                document.getElementById('add_type').value = {{ json_encode(old('type')) }};
                document.getElementById('add_brand_model').value = {{ json_encode(old('brand_model')) }};
                document.getElementById('add_mac').value = {{ json_encode(old('mac_address')) }};
                document.getElementById('add_bridge_id').value = {{ old('bridge_id', '') }};
            @endif
        });
    </script>
    @endif

    <style>
        .animate-modal {
            animation: modalIn 0.2s ease-out;
        }
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(-8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>

    <script>
        // ── Modal helpers ─────────────────────────────────────────────────
        function openAddModal() {
            document.getElementById('modal-add').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('add_name').focus(), 100);
        }

        function closeAddModal() {
            document.getElementById('modal-add').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('form-add-sensor').reset();
        }

        function openEditModal(id, name, type, brandModel, mac, bridgeId) {
            // Set form action
            document.getElementById('form-edit-sensor').action =
                '/manage/sensors/' + id;

            // Populate fields
            document.getElementById('edit_name').value        = name       ?? '';
            document.getElementById('edit_type').value        = type       ?? '';
            document.getElementById('edit_brand_model').value = brandModel ?? '';
            document.getElementById('edit_mac').value         = mac        ?? '';
            document.getElementById('edit_bridge_id').value   = bridgeId   ?? '';

            document.getElementById('modal-edit').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('edit_name').focus(), 100);
        }

        function closeEditModal() {
            document.getElementById('modal-edit').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>

</x-app-layout>
