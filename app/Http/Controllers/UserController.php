<?php

namespace App\Http\Controllers;

use App\Models\Bridge;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $authUser = auth()->user();

        $query = User::with(['company', 'bridge'])->orderByDesc('created_at');

        // Company role can only view PICs of their own company
        if ($authUser->isCompany()) {
            $query->where('company_id', $authUser->company_id)
                  ->where('role', 'pic');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        $users     = $query->paginate(10)->withQueryString();
        $bridges   = $authUser->isCompany()
            ? Bridge::where('company_id', $authUser->company_id)->orderBy('name')->get()
            : Bridge::orderBy('name')->get();
        $companies = $authUser->isCompany()
            ? Company::where('id', $authUser->company_id)->get()
            : Company::orderBy('name')->get();

        return view('manage.users.index', compact('users', 'bridges', 'companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:500',
            'role'              => 'required|in:superadmin,pic,viewer,company',
            'company_id'        => 'nullable|exists:companies,id',
            'bridge_id'         => 'nullable|exists:bridges,id',
            'password'          => ['required', 'confirmed', Password::min(8)],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('manage.users.index')
                         ->with('success', "User \"{$validated['name']}\" has been created.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:500',
            'role'       => 'required|in:superadmin,pic,viewer,company',
            'company_id' => 'nullable|exists:companies,id',
            'bridge_id'  => 'nullable|exists:bridges,id',
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['password_confirmation']);

        $user->update($validated);

        return redirect()->route('manage.users.index')
                         ->with('success', "User \"{$user->name}\" has been updated.");
    }

    public function destroy(User $user)
    {
        // Prevent deleting the currently logged-in user
        if ($user->id === auth()->id()) {
            return redirect()->route('manage.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('manage.users.index')
                         ->with('success', "User \"{$name}\" has been deleted.");
    }
}
