<?php

namespace App\Http\Controllers;

use App\Models\Bridge;
use App\Models\City;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    // ── LIST ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = Company::with(['city', 'bridges.pics', 'users'])
                        ->withCount(['bridges', 'users'])
                        ->orderBy('name');

        // Company role can only view their own company
        if ($authUser->isCompany()) {
            $query->where('id', $authUser->company_id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhereHas('city', fn($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        $companies = $query->paginate(10)->withQueryString();
        $cities    = City::orderBy('name')->get();
        $bridges   = Bridge::with(['city', 'company', 'pics'])->orderBy('name')->get();
        $pics      = User::where('role', 'pic')->with(['company', 'bridge'])->orderBy('name')->get();

        return view('manage.companies.index', compact('companies', 'cities', 'bridges', 'pics'));
    }

    // ── SHOW (detail company: bridges + PICs) ─────────────────────────────────
    public function show(Company $company)
    {
        $authUser = auth()->user();

        // Company role cannot view other companies
        if ($authUser->isCompany() && $company->id !== $authUser->company_id) {
            abort(403, 'Unauthorized access to this company.');
        }

        $company->load([
            'city',
            'bridges' => fn($q) => $q->with(['city', 'pics', 'sensors']),
            'users'   => fn($q) => $q->orderBy('role')->orderBy('name'),
        ]);

        $allBridges  = Bridge::whereNull('company_id')
                             ->orWhere('company_id', $company->id)
                             ->orderBy('name')->get();
        $allUsers    = User::orderBy('name')->get();

        return view('manage.companies.show', compact('company', 'allBridges', 'allUsers'));
    }

    // ── STORE ─────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'address'          => 'nullable|string|max:500',
            'city_id'          => 'required|exists:cities,id',
            'contact_email'    => 'nullable|email|max:255',
            'bridge_ids'       => 'nullable|array',
            'bridge_ids.*'     => 'exists:bridges,id',
            'pic_ids'          => 'nullable|array',
            'pic_ids.*'        => 'exists:users,id',
            'account_email'    => 'nullable|email|max:255|unique:users,email',
            'account_password' => 'nullable|string|min:6',
        ]);

        $company = Company::create([
            'name'          => $validated['name'],
            'address'       => $validated['address'] ?? null,
            'city_id'       => $validated['city_id'],
            'contact_email' => $validated['contact_email'] ?? null,
        ]);

        // Assign selected bridges to this company
        $selectedBridgeIds = $request->input('bridge_ids', []);
        if (!empty($selectedBridgeIds)) {
            Bridge::whereIn('id', $selectedBridgeIds)
                  ->update(['company_id' => $company->id]);
        }

        // Assign selected PICs to this company
        $selectedPicIds = $request->input('pic_ids', []);
        if (!empty($selectedPicIds)) {
            User::whereIn('id', $selectedPicIds)
                ->where('role', 'pic')
                ->update(['company_id' => $company->id]);
        }

        // Determine user account email
        $userEmail = $request->input('account_email');
        if (empty($userEmail)) {
            if (!empty($validated['contact_email']) && !User::where('email', $validated['contact_email'])->exists()) {
                $userEmail = $validated['contact_email'];
            } else {
                $baseSlug = Str::slug($company->name, '.');
                if (empty($baseSlug)) {
                    $baseSlug = 'company.' . $company->id;
                }
                $userEmail = $baseSlug . '@company.shms.com';
                $counter = 1;
                while (User::where('email', $userEmail)->exists()) {
                    $userEmail = $baseSlug . '.' . $counter . '@company.shms.com';
                    $counter++;
                }
            }
        }

        $rawPassword = $request->filled('account_password') ? $request->input('account_password') : 'password';

        // Auto-create user account for company owner
        User::create([
            'name'       => $company->name,
            'email'      => $userEmail,
            'password'   => Hash::make($rawPassword),
            'role'       => 'company',
            'company_id' => $company->id,
            'bridge_id'  => null,
            'address'    => $company->address,
        ]);

        return redirect()->route('manage.companies.index')
                         ->with('success', "Company \"{$company->name}\" created successfully. Owner account: {$userEmail} (password: {$rawPassword}).");
    }

    // ── UPDATE ────────────────────────────────────────────────────────────────
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'nullable|string|max:500',
            'city_id'       => 'required|exists:cities,id',
            'contact_email' => 'nullable|email|max:255',
            'bridge_ids'    => 'nullable|array',
            'bridge_ids.*'  => 'exists:bridges,id',
            'pic_ids'       => 'nullable|array',
            'pic_ids.*'     => 'exists:users,id',
        ]);

        $company->update([
            'name'          => $validated['name'],
            'address'       => $validated['address'] ?? null,
            'city_id'       => $validated['city_id'],
            'contact_email' => $validated['contact_email'] ?? null,
        ]);

        // Sync bridges: Add or Remove bridges
        $selectedBridgeIds = $request->input('bridge_ids', []);

        // 1. Bridges unassigned from this company
        $removedBridges = Bridge::where('company_id', $company->id)
                                ->whereNotIn('id', $selectedBridgeIds)
                                ->get();
        if ($removedBridges->isNotEmpty()) {
            $removedBridgeIds = $removedBridges->pluck('id');
            Bridge::whereIn('id', $removedBridgeIds)->update(['company_id' => null]);
            // Also detach PICs of these removed bridges from this company
            User::where('company_id', $company->id)
                ->where('role', 'pic')
                ->whereIn('bridge_id', $removedBridgeIds)
                ->update(['company_id' => null]);
        }

        // 2. Bridges assigned to this company
        if (!empty($selectedBridgeIds)) {
            Bridge::whereIn('id', $selectedBridgeIds)
                  ->update(['company_id' => $company->id]);
        }

        // Sync PICs: Add or Remove PICs
        $selectedPicIds = $request->input('pic_ids', []);

        // 1. PICs removed from this company
        User::where('company_id', $company->id)
            ->where('role', 'pic')
            ->whereNotIn('id', $selectedPicIds)
            ->update(['company_id' => null]);

        // 2. PICs added to this company
        if (!empty($selectedPicIds)) {
            User::whereIn('id', $selectedPicIds)
                ->where('role', 'pic')
                ->update(['company_id' => $company->id]);
        }

        return redirect()->route('manage.companies.index')
                         ->with('success', "Company \"{$company->name}\" updated successfully.");
    }

    // ── ASSIGN BRIDGE ─────────────────────────────────────────────────────────
    public function assignBridge(Request $request, Company $company)
    {
        $request->validate(['bridge_id' => 'required|exists:bridges,id']);

        $bridge = Bridge::findOrFail($request->bridge_id);
        $bridge->update(['company_id' => $company->id]);

        // Link PIC of this bridge to company
        User::where('bridge_id', $bridge->id)->where('role', 'pic')->update(['company_id' => $company->id]);

        return back()->with('success', "Bridge \"{$bridge->name}\" has been assigned to the company.");
    }

    // ── REMOVE BRIDGE ─────────────────────────────────────────────────────────
    public function removeBridge(Company $company, Bridge $bridge)
    {
        if ($bridge->company_id === $company->id) {
            $bridge->update(['company_id' => null]);
            // Unlink PIC of this bridge from company too
            User::where('company_id', $company->id)->where('bridge_id', $bridge->id)->update(['company_id' => null]);
        }

        return back()->with('success', "Bridge \"{$bridge->name}\" removed from company.");
    }

    // ── DESTROY ───────────────────────────────────────────────────────────────
    public function destroy(Company $company)
    {
        $name = $company->name;

        // Detach bridges before deleting
        $company->bridges()->update(['company_id' => null]);

        // Delete dedicated company owner accounts
        User::where('company_id', $company->id)->where('role', 'company')->delete();

        // Detach other users (e.g. staff/PICs)
        User::where('company_id', $company->id)->update(['company_id' => null]);

        $company->delete();

        return redirect()->route('manage.companies.index')
                         ->with('success', "Company \"{$name}\" has been deleted.");
    }
}
