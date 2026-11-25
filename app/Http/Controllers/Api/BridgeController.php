<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bridge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BridgeController extends Controller
{
    /** GET /api/bridges */
    public function index(Request $request)
    {
        $query = Bridge::with(['city:id,name', 'company:id,name']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        if ($request->filled('maintenance_status')) {
            $query->where('maintenance_status', $request->maintenance_status);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Bridges retrieved.',
            'data'    => $query->get(),
        ]);
    }

    /** POST /api/bridges */
    public function store(Request $request)
    {
        $data = $request->validate([
            'city_id'                            => 'required|integer|exists:cities,id',
            'company_id'                         => 'nullable|integer|exists:companies,id',
            'name'                               => 'required|string|max:255',
            'latitude'                           => 'required|numeric|between:-90,90',
            'longitude'                          => 'required|numeric|between:-180,180',
            'rating'                             => 'nullable|integer|min:1|max:5',
            'photo_url'                          => 'nullable|string',
            'photo'                              => 'nullable|file|image|max:10240',
            'maintenance_status'                 => 'nullable|string|max:255',
            'support_type'                       => 'nullable|string|max:255',
            'box_beam_length'                    => 'nullable|numeric',
            'box_beam_inside_width'              => 'nullable|numeric',
            'box_beam_outside_width'             => 'nullable|numeric',
            'box_beam_inside_height'             => 'nullable|numeric',
            'box_beam_outside_height'            => 'nullable|numeric',
            'number_of_box_beams'                => 'nullable|integer',
            'number_of_diaphragms_per_box_beam'  => 'nullable|integer',
            'thickness_at_diaphragms'            => 'nullable|numeric',
            'thickness_of_box_beam_end'          => 'nullable|numeric',
            'thickness_of_wearing_surface'       => 'nullable|numeric',
            'compressive_strength_28_days'       => 'nullable|numeric',
            'unit_weight_of_concrete'            => 'nullable|numeric',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_url'] = Storage::url($request->file('photo')->store('bridges', 'public'));
        }

        $bridge = Bridge::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bridge created.',
            'data'    => $bridge->load(['city:id,name', 'company:id,name']),
        ], 201);
    }

    /** GET /api/bridges/{id} */
    public function show(int $id)
    {
        $bridge = Bridge::with([
            'city:id,name',
            'company:id,name',
            'sensors',
            'pics:id,name,email,phone',
        ])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bridge detail.',
            'data'    => $bridge,
        ]);
    }

    /** PUT /api/bridges/{id} */
    public function update(Request $request, int $id)
    {
        $bridge = Bridge::findOrFail($id);

        $data = $request->validate([
            'city_id'                            => 'sometimes|integer|exists:cities,id',
            'company_id'                         => 'nullable|integer|exists:companies,id',
            'name'                               => 'sometimes|string|max:255',
            'latitude'                           => 'sometimes|numeric|between:-90,90',
            'longitude'                          => 'sometimes|numeric|between:-180,180',
            'rating'                             => 'nullable|integer|min:1|max:5',
            'photo_url'                          => 'nullable|string',
            'photo'                              => 'nullable|file|image|max:10240',
            'maintenance_status'                 => 'nullable|string|max:255',
            'support_type'                       => 'nullable|string|max:255',
            'box_beam_length'                    => 'nullable|numeric',
            'box_beam_inside_width'              => 'nullable|numeric',
            'box_beam_outside_width'             => 'nullable|numeric',
            'box_beam_inside_height'             => 'nullable|numeric',
            'box_beam_outside_height'            => 'nullable|numeric',
            'number_of_box_beams'                => 'nullable|integer',
            'number_of_diaphragms_per_box_beam'  => 'nullable|integer',
            'thickness_at_diaphragms'            => 'nullable|numeric',
            'thickness_of_box_beam_end'          => 'nullable|numeric',
            'thickness_of_wearing_surface'       => 'nullable|numeric',
            'compressive_strength_28_days'       => 'nullable|numeric',
            'unit_weight_of_concrete'            => 'nullable|numeric',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_url'] = Storage::url($request->file('photo')->store('bridges', 'public'));
        }

        $bridge->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Bridge updated.',
            'data'    => $bridge->fresh(['city:id,name', 'company:id,name']),
        ]);
    }

    /** DELETE /api/bridges/{id} */
    public function destroy(int $id)
    {
        Bridge::findOrFail($id)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Bridge deleted.',
            'data'    => null,
        ]);
    }
}
