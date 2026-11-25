<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /** GET /api/companies */
    public function index()
    {
        $companies = Company::with('city:id,name')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Companies retrieved.',
            'data'    => $companies,
        ]);
    }

    /** POST /api/companies */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'address'       => 'nullable|string',
            'city_id'       => 'required|integer|exists:cities,id',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $company = Company::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Company created.',
            'data'    => $company->load('city:id,name'),
        ], 201);
    }

    /** GET /api/companies/{id} */
    public function show(int $id)
    {
        $company = Company::with(['city:id,name', 'bridges:id,name,maintenance_status', 'users:id,name,email,role'])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Company detail.',
            'data'    => $company,
        ]);
    }

    /** PUT /api/companies/{id} */
    public function update(Request $request, int $id)
    {
        $company = Company::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'address'       => 'nullable|string',
            'city_id'       => 'sometimes|integer|exists:cities,id',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $company->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Company updated.',
            'data'    => $company->fresh('city:id,name'),
        ]);
    }

    /** DELETE /api/companies/{id} */
    public function destroy(int $id)
    {
        Company::findOrFail($id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Company deleted.',
            'data'    => null,
        ]);
    }
}
