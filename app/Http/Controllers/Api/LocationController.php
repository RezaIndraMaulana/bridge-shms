<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use App\Models\Country;
use Illuminate\Http\Request;

/** Handles Countries, Provinces, Cities as reference data */
class LocationController extends Controller
{
    // ─── COUNTRIES ───────────────────────────────────────────────────────────

    public function countriesIndex()
    {
        return response()->json([
            'status' => 'success', 'message' => 'Countries retrieved.',
            'data'   => Country::orderBy('name')->get(),
        ]);
    }

    public function countriesStore(Request $request)
    {
        $data = $request->validate([
            'iso_code' => 'required|string|size:2|unique:countries,iso_code',
            'name'     => 'required|string|max:255',
        ]);
        $country = Country::create($data);
        return response()->json(['status' => 'success', 'message' => 'Country created.', 'data' => $country], 201);
    }

    public function countriesUpdate(Request $request, int $id)
    {
        $country = Country::findOrFail($id);
        $data = $request->validate([
            'iso_code' => 'sometimes|string|size:2|unique:countries,iso_code,' . $id,
            'name'     => 'sometimes|string|max:255',
        ]);
        $country->update($data);
        return response()->json(['status' => 'success', 'message' => 'Country updated.', 'data' => $country]);
    }

    public function countriesDestroy(int $id)
    {
        Country::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Country deleted.', 'data' => null]);
    }

    // ─── PROVINCES ───────────────────────────────────────────────────────────

    public function provincesIndex(Request $request)
    {
        $query = Province::with('country:id,name,iso_code');
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        return response()->json(['status' => 'success', 'message' => 'Provinces retrieved.', 'data' => $query->get()]);
    }

    public function provincesStore(Request $request)
    {
        $data = $request->validate([
            'country_id' => 'required|integer|exists:countries,id',
            'name'       => 'required|string|max:255',
        ]);
        $province = Province::create($data);
        return response()->json(['status' => 'success', 'message' => 'Province created.', 'data' => $province->load('country:id,name')], 201);
    }

    public function provincesUpdate(Request $request, int $id)
    {
        $province = Province::findOrFail($id);
        $data = $request->validate([
            'country_id' => 'sometimes|integer|exists:countries,id',
            'name'       => 'sometimes|string|max:255',
        ]);
        $province->update($data);
        return response()->json(['status' => 'success', 'message' => 'Province updated.', 'data' => $province->fresh('country:id,name')]);
    }

    public function provincesDestroy(int $id)
    {
        Province::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Province deleted.', 'data' => null]);
    }

    // ─── CITIES ──────────────────────────────────────────────────────────────

    public function citiesIndex(Request $request)
    {
        $query = City::with('province:id,name');
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }
        return response()->json(['status' => 'success', 'message' => 'Cities retrieved.', 'data' => $query->get()]);
    }

    public function citiesStore(Request $request)
    {
        $data = $request->validate([
            'province_id' => 'required|integer|exists:provinces,id',
            'name'        => 'required|string|max:255',
        ]);
        $city = City::create($data);
        return response()->json(['status' => 'success', 'message' => 'City created.', 'data' => $city->load('province:id,name')], 201);
    }

    public function citiesUpdate(Request $request, int $id)
    {
        $city = City::findOrFail($id);
        $data = $request->validate([
            'province_id' => 'sometimes|integer|exists:provinces,id',
            'name'        => 'sometimes|string|max:255',
        ]);
        $city->update($data);
        return response()->json(['status' => 'success', 'message' => 'City updated.', 'data' => $city->fresh('province:id,name')]);
    }

    public function citiesDestroy(int $id)
    {
        City::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'City deleted.', 'data' => null]);
    }
}
