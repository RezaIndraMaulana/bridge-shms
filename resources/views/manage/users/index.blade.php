<x-app-layout>
    <x-slot name="pageTitle">Manage User</x-slot>

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

        {{-- Header: Title, Search, Add User --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ Auth::user()->isCompany() ? 'PIC List' : 'User List' }}
                </h2>
                @if(Auth::user()->isCompany() && Auth::user()->company)
                <p class="text-xs text-gray-500 mt-0.5">Assigned PICs for {{ Auth::user()->company->name }}</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                {{-- Search --}}
                <form method="GET" action="{{ route('manage.users.index') }}" class="relative">
                    <input id="user-search"
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

                {{-- Add User button (Superadmin only) --}}
                @if(Auth::user()->isSuperAdmin())
                <button type="button"
                        id="btn-open-add-user"
                        onclick="openAddModal()"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full shadow-sm hover:shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 border-b border-gray-100">
                        <th class="py-3 px-3 w-16">
                            <span class="inline-flex items-center gap-1">No
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Name</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Email</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Phone</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Role</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Bridge / Company</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 w-44">
                            <span class="inline-flex items-center gap-1">Created At
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                </svg>
                            </span>
                        </th>
                        @if(Auth::user()->isSuperAdmin())
                        <th class="py-3 px-4 font-semibold text-gray-600 text-center w-24">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        {{-- No --}}
                        <td class="py-3.5 px-3 text-gray-500 text-sm">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>

                        {{-- Name --}}
                        <td class="py-3.5 px-4 font-medium text-gray-800">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br
                                    @if($user->role === 'superadmin') from-purple-500 to-indigo-600
                                    @elseif($user->role === 'company') from-indigo-500 to-blue-600
                                    @elseif($user->role === 'pic') from-blue-500 to-cyan-600
                                    @else from-gray-400 to-gray-500 @endif
                                    flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span>{{ $user->name }}</span>
                                @if($user->id === auth()->id())
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-600 font-medium">You</span>
                                @endif
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="py-3.5 px-4 text-gray-500 text-xs">{{ $user->email }}</td>

                        {{-- Phone --}}
                        <td class="py-3.5 px-4 text-gray-500 text-xs">{{ $user->phone ?? '—' }}</td>

                        {{-- Role Badge --}}
                        <td class="py-3.5 px-4">
                            @php
                                $roleClasses = match($user->role) {
                                    'superadmin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                    'company'    => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                    'pic'        => 'bg-blue-100 text-blue-700 border-blue-200',
                                    default      => 'bg-gray-100 text-gray-600 border-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $roleClasses }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        {{-- Bridge / Company --}}
                        <td class="py-3.5 px-4 text-xs text-gray-500">
                            @if($user->bridge)
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                                    </svg>
                                    {{ $user->bridge->name }}
                                </div>
                            @endif
                            @if($user->company)
                                <div class="flex items-center gap-1 {{ $user->bridge ? 'mt-0.5' : '' }}">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $user->company->name }}
                                </div>
                            @endif
                            @if(!$user->bridge && !$user->company)
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Created At --}}
                        <td class="py-3.5 px-4 text-gray-400 text-xs font-mono whitespace-nowrap">
                            {{ $user->created_at ? $user->created_at->format('M d, Y H:i:s') : '—' }}
                        </td>

                        {{-- Actions (Superadmin only) --}}
                        @if(Auth::user()->isSuperAdmin())
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Edit --}}
                                <button type="button"
                                        title="Edit User"
                                        onclick="openEditModal(
                                            {{ $user->id }},
                                            {{ json_encode($user->name) }},
                                            {{ json_encode($user->email) }},
                                            {{ json_encode($user->phone) }},
                                            {{ json_encode($user->address) }},
                                            {{ json_encode($user->role) }},
                                            {{ $user->company_id ?? 'null' }},
                                            {{ $user->bridge_id ?? 'null' }}
                                        )"
                                        class="text-emerald-500 hover:text-emerald-700 transition p-1.5 rounded hover:bg-emerald-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>

                                {{-- Delete (disabled for self) --}}
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('manage.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete user \'{{ addslashes($user->name) }}\'?\nThis action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Delete User"
                                            class="text-red-400 hover:text-red-600 transition p-1.5 rounded hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <span class="w-8 h-8 flex items-center justify-center text-gray-200 cursor-not-allowed" title="Cannot delete own account">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="font-medium">{{ Auth::user()->isCompany() ? 'No PICs assigned to your company bridges yet.' : 'No users found.' }}</p>
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
                <span class="text-gray-400">Total: {{ $users->total() }} user(s)</span>
            </div>
            <div>{{ $users->links() }}</div>
        </div>

    </div>

    {{-- ═══════════════════════════════════
         ADD USER MODAL (Superadmin only)
    ═══════════════════════════════════ --}}
    @if(Auth::user()->isSuperAdmin())
    <div id="modal-add-user"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modal-add-user-title">
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter: blur(2px);" onclick="closeAddModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl z-10 overflow-hidden max-h-[90vh] flex flex-col animate-modal">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 id="modal-add-user-title" class="text-base font-semibold text-gray-800">Add User</h3>
                <button type="button" onclick="closeAddModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('manage.users.store') }}" id="form-add-user" class="overflow-y-auto flex-1">
                @csrf
                <div class="px-6 py-5 space-y-4">

                    {{-- Name & Email (2 col) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="add_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="add_name" required placeholder="Full name"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="add_email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="add_email" required placeholder="user@example.com"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Phone & Role (2 col) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="add_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <input type="text" name="phone" id="add_phone" placeholder="e.g. 08123456789"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="add_role" class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="add_role" required onchange="toggleAddBridgeField(this.value)"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="pic">PIC (Bridge In-Charge)</option>
                                <option value="company">Company (Company Owner)</option>
                                <option value="viewer">Viewer</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="add_address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input type="text" name="address" id="add_address" placeholder="Full address"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Bridge Assignment (shown for PIC / viewer) --}}
                    <div id="add_bridge_section">
                        <label for="add_bridge_id" class="block text-sm font-medium text-gray-700 mb-1.5">Assigned Bridge</label>
                        <select name="bridge_id" id="add_bridge_id"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">— No bridge assigned —</option>
                            @foreach($bridges as $bridge)
                                <option value="{{ $bridge->id }}">{{ $bridge->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Company Assignment --}}
                    <div>
                        <label for="add_company_id" class="block text-sm font-medium text-gray-700 mb-1.5">Company</label>
                        <select name="company_id" id="add_company_id"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">— No company —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @if($companies->isEmpty())
                        <p class="text-xs text-amber-500 mt-1">No companies yet. Add a company first if needed.</p>
                        @endif
                    </div>

                    {{-- Password (2 col) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="add_password" class="block text-sm font-medium text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="add_password" required placeholder="Min. 8 characters"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="add_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm Password <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="add_password_confirmation" required placeholder="Repeat password"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         EDIT USER MODAL
    ═══════════════════════════════════ --}}
    <div id="modal-edit-user"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
         role="dialog" aria-modal="true" aria-labelledby="modal-edit-user-title">
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter: blur(2px);" onclick="closeEditModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl z-10 overflow-hidden max-h-[90vh] flex flex-col animate-modal">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 id="modal-edit-user-title" class="text-base font-semibold text-gray-800">Edit User</h3>
                <button type="button" onclick="closeEditModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form method="POST" id="form-edit-user" action="" class="overflow-y-auto flex-1">
                @csrf
                @method('PUT')
                <div class="px-6 py-5 space-y-4">

                    {{-- Name & Email (2 col) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="edit_name" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="edit_email" required
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                    </div>

                    {{-- Phone & Role (2 col) --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                            <input type="text" name="phone" id="edit_phone"
                                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="edit_role" required onchange="toggleEditBridgeField(this.value)"
                                    class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="pic">PIC (Bridge In-Charge)</option>
                                <option value="company">Company (Company Owner)</option>
                                <option value="viewer">Viewer</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="edit_address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input type="text" name="address" id="edit_address"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Bridge Assignment --}}
                    <div id="edit_bridge_section">
                        <label for="edit_bridge_id" class="block text-sm font-medium text-gray-700 mb-1.5">Assigned Bridge</label>
                        <select name="bridge_id" id="edit_bridge_id"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">— No bridge assigned —</option>
                            @foreach($bridges as $bridge)
                                <option value="{{ $bridge->id }}">{{ $bridge->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Company Assignment --}}
                    <div>
                        <label for="edit_company_id" class="block text-sm font-medium text-gray-700 mb-1.5">Company</label>
                        <select name="company_id" id="edit_company_id"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">— No company —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- New Password (optional) --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-3">
                        <p class="text-xs text-amber-600 font-medium mb-3 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Leave password fields empty to keep the existing password.
                        </p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1.5">New Password</label>
                                <input type="password" name="password" id="edit_password" placeholder="Min. 8 characters"
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation" placeholder="Repeat password"
                                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm hover:shadow transition">Update</button>
                </div>
            </form>
        </div>
    </div>
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
        // ── Add Modal ────────────────────────────────────────────────────
        function openAddModal() {
            document.getElementById('modal-add-user').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('add_name').focus(), 100);
        }

        function closeAddModal() {
            document.getElementById('modal-add-user').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('form-add-user').reset();
        }

        function toggleAddBridgeField(role) {
            const sec = document.getElementById('add_bridge_section');
            sec.style.display = (role === 'superadmin' || role === 'company') ? 'none' : '';
        }

        // ── Edit Modal ───────────────────────────────────────────────────
        function openEditModal(id, name, email, phone, address, role, companyId, bridgeId) {
            document.getElementById('form-edit-user').action = '/manage/users/' + id;

            document.getElementById('edit_name').value       = name    ?? '';
            document.getElementById('edit_email').value      = email   ?? '';
            document.getElementById('edit_phone').value      = phone   ?? '';
            document.getElementById('edit_address').value    = address ?? '';
            document.getElementById('edit_role').value       = role    ?? 'pic';
            document.getElementById('edit_company_id').value = companyId ?? '';
            document.getElementById('edit_bridge_id').value  = bridgeId  ?? '';
            document.getElementById('edit_password').value   = '';
            document.getElementById('edit_password_confirmation').value = '';

            toggleEditBridgeField(role);

            document.getElementById('modal-edit-user').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('edit_name').focus(), 100);
        }

        function closeEditModal() {
            document.getElementById('modal-edit-user').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function toggleEditBridgeField(role) {
            const sec = document.getElementById('edit_bridge_section');
            sec.style.display = (role === 'superadmin' || role === 'company') ? 'none' : '';
        }

        // ── Close on Escape ──────────────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });
    </script>

</x-app-layout>
