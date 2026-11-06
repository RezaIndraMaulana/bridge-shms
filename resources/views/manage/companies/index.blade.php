<x-app-layout>
    <x-slot name="pageTitle">Manage Company</x-slot>

    {{-- Flash --}}
    @if(session('success'))
    <div id="flash-ok" class="mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-ok').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600">x</button>
    </div>
    @endif
    @if(session('error'))
    <div id="flash-err" class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm shadow-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="font-medium">{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-err').remove()" class="ml-auto text-red-400 hover:text-red-600">x</button>
    </div>
    @endif

    {{-- Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ Auth::user()->isCompany() ? 'My Company' : 'Company List' }}</h2>
                @if(Auth::user()->isCompany() && Auth::user()->company)
                <p class="text-xs text-gray-500 mt-0.5">Company Profile &amp; Assigned Bridges</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('manage.companies.index') }}" class="relative">
                    <input name="search" type="text" value="{{ request('search') }}" placeholder="Search"
                           class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-400 w-56 placeholder-gray-400 transition">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                </form>
                @if(Auth::user()->isSuperAdmin())
                <button type="button" onclick="openAddModal()"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-full shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add Company
                </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-500 border-b border-gray-100">
                        <th class="py-3 px-3 w-12">No</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Company Name</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">City</th>
                        <th class="py-3 px-4 font-semibold text-gray-600">Contact Email</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-center">Bridges</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-center">Users</th>
                        <th class="py-3 px-4 font-semibold text-gray-600 text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($companies as $company)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="py-3.5 px-3 text-gray-400 text-sm">{{ ($companies->currentPage()-1)*$companies->perPage()+$loop->iteration }}</td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('manage.companies.show', $company) }}" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline transition">{{ $company->name }}</a>
                            @if($company->address)
                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[180px]">{{ $company->address }}</p>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-gray-500 text-xs">{{ $company->city?->name ?? '---' }}</td>
                        <td class="py-3.5 px-4 text-gray-500 text-xs">
                            @if($company->contact_email)
                                <a href="mailto:{{ $company->contact_email }}" class="hover:text-blue-600 transition">{{ $company->contact_email }}</a>
                            @else
                                <span class="text-gray-300">---</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('manage.companies.show', $company) }}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold {{ $company->bridges_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }} hover:scale-110 transition-transform">
                                {{ $company->bridges_count }}
                            </a>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold {{ $company->users_count > 0 ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-400' }}">
                                {{ $company->users_count }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('manage.companies.show', $company) }}" title="View Detail"
                                   class="text-blue-500 hover:text-blue-700 transition p-1.5 rounded hover:bg-blue-50 flex items-center gap-1 text-xs font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @if(Auth::user()->isCompany()) View @endif
                                </a>
                                @if(Auth::user()->isSuperAdmin())
                                @php
                                    $cBridgeIds = $company->bridges->pluck('id')->toArray();
                                    $cPicIds    = $company->users->where('role','pic')->pluck('id')->toArray();
                                @endphp
                                <button type="button" title="Edit"
                                        onclick="openEditModal({{ $company->id }}, {{ json_encode($company->name) }}, {{ json_encode($company->address) }}, {{ $company->city_id ?? 'null' }}, {{ json_encode($company->contact_email) }}, {{ json_encode($cBridgeIds) }}, {{ json_encode($cPicIds) }})"
                                        class="text-emerald-500 hover:text-emerald-700 transition p-1.5 rounded hover:bg-emerald-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('manage.companies.destroy', $company) }}"
                                      onsubmit="return confirm('Delete company? Bridges will be unlinked.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Delete" class="text-red-400 hover:text-red-600 transition p-1.5 rounded hover:bg-red-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-gray-400 text-sm">
                            <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <p class="font-medium">No companies found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-5 mt-4 border-t border-gray-100 text-xs text-gray-500">
            <span>Total: {{ $companies->total() }} company</span>
            <div>{{ $companies->links() }}</div>
        </div>
    </div>

    {{-- MODALS (Superadmin only) --}}
    @if(Auth::user()->isSuperAdmin())

    {{-- ADD COMPANY MODAL --}}
    <div id="modal-add-company" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter:blur(2px);" onclick="closeAddModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl z-10 overflow-hidden animate-modal flex flex-col" style="max-height:90vh">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-base font-semibold text-gray-800">Add Company</h3>
                <button type="button" onclick="closeAddModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('manage.companies.store') }}" id="form-add-company" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label for="add_co_name" class="block text-sm font-medium text-gray-700 mb-1.5">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="add_co_name" required placeholder="e.g. PT Jasa Marga"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="add_co_email" class="block text-sm font-medium text-gray-700 mb-1.5">Contact Email</label>
                        <input type="email" name="contact_email" id="add_co_email" placeholder="contact@company.com"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="add_co_city" class="block text-sm font-medium text-gray-700 mb-1.5">City <span class="text-red-500">*</span></label>
                        <select name="city_id" id="add_co_city" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">--- Select city ---</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="add_co_address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input type="text" name="address" id="add_co_address" placeholder="Full address"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Assign Bridges --}}
                    <div class="pt-3 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign Bridges <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                        <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1 bg-gray-50/50">
                            @forelse($bridges as $bridge)
                            <label class="flex items-center gap-2.5 cursor-pointer hover:bg-white rounded-lg px-2 py-1 transition-colors">
                                <input type="checkbox" name="bridge_ids[]" value="{{ $bridge->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 flex-1">{{ $bridge->name }}</span>
                                @if($bridge->company_id)
                                <span class="text-xs text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded">already assigned</span>
                                @else
                                <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded">available</span>
                                @endif
                            </label>
                            @empty
                            <p class="text-xs text-gray-400 italic px-2 py-1">No bridges available.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Assign PICs --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign PIC Staff <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                        <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1 bg-gray-50/50">
                            @forelse($pics as $pic)
                            <label class="flex items-center gap-2.5 cursor-pointer hover:bg-white rounded-lg px-2 py-1 transition-colors">
                                <input type="checkbox" name="pic_ids[]" value="{{ $pic->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 flex-1">{{ $pic->name }}</span>
                                @if($pic->bridge)
                                <span class="text-xs text-blue-500">{{ $pic->bridge->name }}</span>
                                @endif
                                @if($pic->company_id)
                                <span class="text-xs text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded">has company</span>
                                @else
                                <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded">unassigned</span>
                                @endif
                            </label>
                            @empty
                            <p class="text-xs text-gray-400 italic px-2 py-1">No PICs available.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Owner Login Account --}}
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">Owner Login Account</span>
                            <span class="text-xs text-gray-400">Automatic / Optional custom</span>
                        </div>
                        <div class="space-y-3 bg-slate-50/80 p-3 rounded-xl border border-gray-100">
                            <div>
                                <label for="add_account_email" class="block text-xs font-medium text-gray-700 mb-1">Login Email <span class="text-gray-400 font-normal">(leave blank to use contact email)</span></label>
                                <input type="email" name="account_email" id="add_account_email" placeholder="e.g. owner@company.com"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            </div>
                            <div>
                                <label for="add_account_password" class="block text-xs font-medium text-gray-700 mb-1">Login Password <span class="text-gray-400 font-normal">(default: password)</span></label>
                                <input type="text" name="account_password" id="add_account_password" placeholder="Min. 6 chars (default: password)"
                                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-800 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">Save Company</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT COMPANY MODAL --}}
    <div id="modal-edit-company" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-800/25" style="backdrop-filter:blur(2px);" onclick="closeEditModal()"></div>
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl z-10 overflow-hidden animate-modal flex flex-col" style="max-height:90vh">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-base font-semibold text-gray-800">Edit Company</h3>
                <button type="button" onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" id="form-edit-company" action="" class="flex flex-col flex-1 overflow-hidden">
                @csrf @method('PUT')
                <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">
                    <div>
                        <label for="edit_co_name" class="block text-sm font-medium text-gray-700 mb-1.5">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_co_name" required
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="edit_co_email" class="block text-sm font-medium text-gray-700 mb-1.5">Contact Email</label>
                        <input type="email" name="contact_email" id="edit_co_email"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="edit_co_city" class="block text-sm font-medium text-gray-700 mb-1.5">City <span class="text-red-500">*</span></label>
                        <select name="city_id" id="edit_co_city" required
                                class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">--- Select city ---</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_co_address" class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                        <input type="text" name="address" id="edit_co_address"
                               class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>

                    {{-- Manage Bridges --}}
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-gray-700">Manage Bridges</label>
                            <span class="text-xs text-gray-400">Check to assign, uncheck to remove</span>
                        </div>
                        <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1 bg-gray-50/50">
                            @forelse($bridges as $bridge)
                            <label class="flex items-center gap-2.5 cursor-pointer hover:bg-white rounded-lg px-2 py-1 transition-colors">
                                <input type="checkbox" name="bridge_ids[]" value="{{ $bridge->id }}"
                                       class="edit-bridge-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 flex-1">{{ $bridge->name }}</span>
                                @if($bridge->city)
                                <span class="text-xs text-gray-400">{{ $bridge->city->name }}</span>
                                @endif
                                @if($bridge->company_id)
                                <span class="text-xs text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded">
                                    {{ $bridge->company?->name ?? 'other' }}
                                </span>
                                @else
                                <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded">available</span>
                                @endif
                            </label>
                            @empty
                            <p class="text-xs text-gray-400 italic px-2 py-1">No bridges in system.</p>
                            @endforelse
                        </div>
                        <p class="text-xs text-amber-600 mt-1">Unchecking a bridge will also unlink its PIC from this company.</p>
                    </div>

                    {{-- Manage PICs --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-medium text-gray-700">Manage PIC Staff</label>
                            <span class="text-xs text-gray-400">Check to assign, uncheck to remove</span>
                        </div>
                        <div class="max-h-36 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1 bg-gray-50/50">
                            @forelse($pics as $pic)
                            <label class="flex items-center gap-2.5 cursor-pointer hover:bg-white rounded-lg px-2 py-1 transition-colors">
                                <input type="checkbox" name="pic_ids[]" value="{{ $pic->id }}"
                                       class="edit-pic-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700 flex-1">{{ $pic->name }}</span>
                                @if($pic->bridge)
                                <span class="text-xs text-blue-500">{{ $pic->bridge->name }}</span>
                                @endif
                                @if($pic->company_id)
                                <span class="text-xs text-amber-600 font-medium bg-amber-50 px-1.5 py-0.5 rounded">has company</span>
                                @else
                                <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-1.5 py-0.5 rounded">unassigned</span>
                                @endif
                            </label>
                            @empty
                            <p class="text-xs text-gray-400 italic px-2 py-1">No PICs available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 flex-shrink-0">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-gray-600 font-medium rounded-lg border border-gray-200 hover:bg-gray-100 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">Update Company</button>
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
        function openAddModal() {
            document.getElementById('modal-add-company').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('add_co_name').focus(), 100);
        }
        function closeAddModal() {
            document.getElementById('modal-add-company').classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('form-add-company').reset();
        }

        function openEditModal(id, name, address, cityId, email, bridgeIds, picIds) {
            document.getElementById('form-edit-company').action = '/manage/companies/' + id;
            document.getElementById('edit_co_name').value    = name    ?? '';
            document.getElementById('edit_co_email').value   = email   ?? '';
            document.getElementById('edit_co_city').value    = cityId  ?? '';
            document.getElementById('edit_co_address').value = address ?? '';

            document.querySelectorAll('.edit-bridge-checkbox').forEach(cb => {
                cb.checked = Array.isArray(bridgeIds) && bridgeIds.includes(parseInt(cb.value));
            });
            document.querySelectorAll('.edit-pic-checkbox').forEach(cb => {
                cb.checked = Array.isArray(picIds) && picIds.includes(parseInt(cb.value));
            });

            document.getElementById('modal-edit-company').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('edit_co_name').focus(), 100);
        }
        function closeEditModal() {
            document.getElementById('modal-edit-company').classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closeAddModal(); closeEditModal(); }
        });
    </script>
</x-app-layout>
