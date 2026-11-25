<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    /** GET /api/cameras */
    public function index()
    {
        $cameras = Camera::with('sinkNode:id,name,bridge_id')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Cameras retrieved.',
            'data'    => $cameras,
        ]);
    }

    /** POST /api/cameras */
    public function store(Request $request)
    {
        $data = $request->validate([
            'sink_node_id' => 'required|integer|exists:sink_nodes,id',
            'brand_model'  => 'nullable|string|max:255',
            'mac_address'  => 'required|string|max:255|unique:cameras,mac_address',
        ]);

        $camera = Camera::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera registered.',
            'data'    => $camera->load('sinkNode:id,name'),
        ], 201);
    }

    /** GET /api/cameras/{id} */
    public function show(int $id)
    {
        $camera = Camera::with(['sinkNode:id,name', 'latestData'])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera detail.',
            'data'    => $camera,
        ]);
    }

    /** PUT /api/cameras/{id} */
    public function update(Request $request, int $id)
    {
        $camera = Camera::findOrFail($id);

        $data = $request->validate([
            'sink_node_id' => 'sometimes|integer|exists:sink_nodes,id',
            'brand_model'  => 'nullable|string|max:255',
            'mac_address'  => 'sometimes|string|max:255|unique:cameras,mac_address,' . $id,
        ]);

        $camera->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera updated.',
            'data'    => $camera->fresh('sinkNode:id,name'),
        ]);
    }

    /** DELETE /api/cameras/{id} */
    public function destroy(int $id)
    {
        $camera = Camera::findOrFail($id);
        $camera->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera deleted.',
            'data'    => null,
        ]);
    }
}
