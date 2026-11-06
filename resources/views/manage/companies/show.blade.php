<x-app-layout>
    <x-slot name="pageTitle">{{ $company->name }}</x-slot>

    {{-- Flash --}}
    @if(session('success'))
    <div id="flash-ok" class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-ok').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">✕</button>
    </div>
    @endif

    {{-- Back breadcrumb --}}
    <div class="mb-4">
        <a href="{{ route('manage.companies.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to Company List
        </a>
    </div>

    {{-- Company Header Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Company Avatar --}}
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                            flex items-center justify-center text-white text-xl font-bold flex-shrink-0 shadow-md">
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $company->name }}</h1>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-sm text-gray-500">
                        @if($company->city)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $company->city->name }}
                        </span>
                        @endif
                        @if($company->contact_email)
                        <a href="mailto:{{ $company->contact_email }}" class="flex items-center gap-1 hover:text-blue-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $company->contact_email }}
                        </a>
                        @endif
                        @if($company->address)
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $company->address }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex items-center gap-4">
                <div class="text-center bg-blue-50 rounded-xl px-5 py-3">
                    <p class="text-2xl font-bold text-blue-600">{{ $company->bridges->count() }}</p>
                    <p class="text-xs text-blue-500 font-medium mt-0.5">Bridges</p>
                </div>
                <div class="text-center bg-purple-50 rounded-xl px-5 py-3">
                    <p class="text-2xl font-bold text-purple-600">{{ $company->users->count() }}</p>
                    <p class="text-xs text-purple-500 font-medium mt-0.5">Users</p>
                </div>
                <div class="text-center bg-emerald-50 rounded-xl px-5 py-3">
                    <p class="text-2xl font-bold text-emerald-600">{{ $company->bridges->sum(fn($b) => $b->pics->count()) }}</p>
                    <p class="text-xs text-emerald-500 font-medium mt-0.5">PICs</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ══════════ LEFT: BRIDGES LIST (2/3 width) ══════════ --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a9 9 0 0118 0"/></svg>
                        Bridges
                        <span class="text-xs font-normal text-gray-400">({{ $company->bridges->count() }} total)</span>
                    </h2>
                    {{-- Assign Bridge button (Superadmin only) --}}
                    @if(Auth::user()->isSuperAdmin() && $allBridges->isNotEmpty())
                    <button type="button" onclick="openAssignModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Assign Bridge
                    </button>
                    @endif
                </div>

                @if($company->bridges->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <p class="text-sm font-medium">No bridges assigned yet.</p>
                    <p class="text-xs mt-1">Click "Assign Bridge" to add bridges to this company.</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($company->bridges as $bridge)
                    <div class="border border-gray-100 rounded-xl p-4 hover:border-blue-200 hover:shadow-sm transition-all">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 flex-1 min-w-0">
                                {{-- Rating badge --}}
                                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold
                                    @if(($bridge->rating ?? 0) >= 4) bg-emerald-100 text-emerald-700
                                    @elseif(($bridge->rating ?? 0) >= 3) bg-yellow-100 text-yellow-700
                                    @elseif(($bridge->rating ?? 0) >= 1) bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-400 @endif">
                                    {{ $bridge->rating ?? '?' }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('dashboard.bridge.show', $bridge) }}"
                                       class="font-semibold text-gray-800 hover:text-blue-600 transition text-sm">
                                        {{ $bridge->name }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-400">
                                        @if($bridge->city)
                                        <span>📍 {{ $bridge->city->name }}</span>
                                        @endif
                                        @if($bridge->maintenance_status)
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium">{{ $bridge->maintenance_status }}</span>
                                        @endif
                                        <span>{{ $bridge->sensors->count() }} sensor(s)</span>
                                    </div>

                                    {{-- PICs for this bridge --}}
                                    @if($bridge->pics->isNotEmpty())
                                    <div class="mt-2.5 flex flex-wrap gap-2">
                                        @foreach($bridge->pics as $pic)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-medium border border-purple-100">
                                            <div class="w-4 h-4 rounded-full bg-purple-400 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">
                                                {{ strtoupper(substr($pic->name, 0, 1)) }}
                                            </div>
                                            {{ $pic->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                    @else
                                    <p class="text-xs text-gray-300 mt-1.5 italic">No PIC assigned</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Remove bridge from company (Superadmin only) --}}
                            @if(Auth::user()->isSuperAdmin())
                            <form method="POST" action="{{ route('manage.companies.bridges.remove', [$company, $bridge]) }}"
                                  onsubmit="return confirm('Remove \'{{ addslashes($bridge->name) }}\' from this company?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Remove from company"
                                        class="flex-shrink-0 text-gray-300 hover:text-red-500 transition p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- ══════════ RIGHT: USERS / STAFF (1/3 width) ══════════ --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-5">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Staff & Users
                    <span class="text-xs font-normal text-gray-400">({{ $company->users->count() }})</span>
                </h2>

                @if($company->users->isEmpty())
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <p class="text-sm font-medium">No users assigned.</p>
                    <p class="text-xs mt-1">Assign company to users in <a href="{{ route('manage.users.index') }}" class="text-blue-500 hover:underline">Manage User</a>.</p>
                </div>
                @else
                <div class="space-y-2.5">
                    @foreach($company->users as $user)
                    <div class="flex items-center gap-3 p-2.5 rounded-lg hover:bg-gray-50 transition">
                        {{-- Avatar --}}
                        <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center text-white text-xs font-bold
                            @if($user->role === 'superadmin') bg-gradient-to-br from-purple-500 to-indigo-600
                            @elseif($user->role === 'company') bg-gradient-to-br from-indigo-500 to-blue-600
                            @elseif($user->role === 'pic') bg-gradient-to-br from-blue-500 to-cyan-600
                            @else bg-gradient-to-br from-gray-400 to-gray-500 @endif">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                        {{-- Role badge --}}
                        @php $rc = match($user->role){ 'superadmin'=>'bg-purple-100 text-purple-700', 'company'=>'bg-indigo-100 text-indigo-700 border border-indigo-200', 'pic'=>'bg-blue-100 text-blue-700', default=>'bg-gray-100 text-gray-600' }; @endphp
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $rc }} flex-shrink-0">{{ $user->role === 'company' ? 'Company Owner' : ucfirst($user->role) }}</span>
                    </div>

                    {{-- If this user is a PIC, show which bridge --}}
                    @if($user->role === 'pic' && $user->bridge)
                    <div class="ml-11 -mt-1 mb-1">
                        <span class="text-[10px] text-blue-500 flex items-center gap-1">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                            PIC of: {{ $user->bridge->name }}
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Quick Info Card --}}
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-5 text-white">
                <h3 class="text-sm font-semibold mb-3 opacity-80">Bridge Overview</h3>
                <div class="space-y-2">
                    @forelse($company->bridges as $bridge)
                    <div class="flex items-center justify-between text-xs">
                        <span class="opacity-70 truncate max-w-[120px]">{{ $bridge->name }}</span>
                        <span class="flex items-center gap-1.5">
                            <span class="opacity-60">{{ $bridge->pics->count() }} PIC</span>
                            @if($bridge->rating)
                            <span class="px-1.5 py-0.5 rounded font-bold text-[10px]
                                @if($bridge->rating >= 4) bg-emerald-500
                                @elseif($bridge->rating >= 3) bg-yellow-500 text-gray-900
                                @else bg-red-500 @endif">
                                {{ $bridge->rating }}/5
                            </span>
                            @endif
                        </span>
                    </div>
                    @empty
                    <p class="text-xs opacity-40 italic">No bridges yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═════════════ ASSIGN BRIDGE MODAL (Superadmin only) ═════════════ --}}
    @if(Auth::user()->isSuperAdmin())
    <div id="modal-assign-bridge" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter:blur(2px);" onclick="closeAssignModal()"></div>
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl z-10 animate-modal">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-800">Assign Bridge</h3>
                <button type="button" onclick="closeAssignModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('manage.companies.bridges.assign', $company) }}">
                @csrf
                <div class="px-6 py-5">
                    <label for="assign_bridge_id" class="block text-sm font-medium text-gray-700 mb-1.5">Select Bridge</label>
                    <select name="bridge_id" id="assign_bridge_id" required
                            class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <option value="">— Choose bridge —</option>
                        @foreach($allBridges as $bridge)
                        <option value="{{ $bridge->id }}"
                            @if($bridge->company_id && $bridge->company_id !== $company->id) disabled @endif>
                            {{ $bridge->name }}
                            @if($bridge->company_id === $company->id) ✓ (already assigned)
                            @elseif($bridge->company_id) (assigned to other)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-2">Bridges already assigned to other companies are disabled.</p>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">Assign</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <style>
        .animate-modal { animation: modalIn .2s ease-out; }
        @keyframes modalIn { from { opacity:0; transform:scale(.95) translateY(-8px); } to { opacity:1; transform:scale(1) translateY(0); } }
    </style>
    <script>
        function openAssignModal()  { document.getElementById('modal-assign-bridge').classList.remove('hidden'); document.body.style.overflow='hidden'; }
        function closeAssignModal() { document.getElementById('modal-assign-bridge').classList.add('hidden'); document.body.style.overflow=''; }
        document.addEventListener('keydown', e => { if(e.key==='Escape') closeAssignModal(); });
    </script>
</x-app-layout>
