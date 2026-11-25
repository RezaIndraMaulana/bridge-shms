<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /** GET /api/users */
    public function index(Request $request)
    {
        $query = User::with(['company:id,name', 'bridge:id,name']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('bridge_id')) {
            $query->where('bridge_id', $request->bridge_id);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Users retrieved.',
            'data'    => $query->get(),
        ]);
    }

    /** POST /api/users */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'nullable|string|in:superadmin,company,pic',
            'company_id' => 'nullable|integer|exists:companies,id',
            'bridge_id'  => 'nullable|integer|exists:bridges,id',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created.',
            'data'    => $user->load(['company:id,name', 'bridge:id,name']),
        ], 201);
    }

    /** GET /api/users/{id} */
    public function show(int $id)
    {
        $user = User::with(['company:id,name', 'bridge:id,name'])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'User detail.',
            'data'    => $user,
        ]);
    }

    /** PUT /api/users/{id} */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'email'      => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($id)],
            'password'   => 'nullable|string|min:8',
            'role'       => 'nullable|string|in:superadmin,company,pic',
            'company_id' => 'nullable|integer|exists:companies,id',
            'bridge_id'  => 'nullable|integer|exists:bridges,id',
            'phone'      => 'nullable|string|max:50',
            'address'    => 'nullable|string',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated.',
            'data'    => $user->fresh(['company:id,name', 'bridge:id,name']),
        ]);
    }

    /** DELETE /api/users/{id} */
    public function destroy(int $id)
    {
        User::findOrFail($id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User deleted.',
            'data'    => null,
        ]);
    }
}
