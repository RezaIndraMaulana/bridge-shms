<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use App\Models\CameraData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CameraDataController extends Controller
{
    /**
     * GET /api/camera-data
     * Filters: camera_id, mac_address, truck_detected, per_page
     */
    public function index(Request $request)
    {
        $request->validate([
            'camera_id'      => 'nullable|integer|exists:cameras,id',
            'mac_address'    => 'nullable|string',
            'truck_detected' => 'nullable|boolean',
            'per_page'       => 'nullable|integer|min:1|max:200',
        ]);

        $query = CameraData::with('camera:id,mac_address,sink_node_id');

        if ($request->filled('camera_id')) {
            $query->where('camera_id', $request->camera_id);
        }

        if ($request->filled('mac_address')) {
            $cam = Camera::where('mac_address', $request->mac_address)->firstOrFail();
            $query->where('camera_id', $cam->id);
        }

        if ($request->filled('truck_detected')) {
            $query->where('truck_detected', filter_var($request->truck_detected, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = $request->integer('per_page', 50);
        $data    = $query->orderByDesc('recorded_at')->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera data retrieved.',
            'data'    => $data,
        ]);
    }

    /**
     * POST /api/camera-data
     * Accepts: camera_id OR mac_address, recorded_at (optional), truck_detected,
     *          image_url (string) OR image (file upload)
     */
    public function store(Request $request)
    {
        $request->validate([
            'camera_id'      => 'nullable|integer|exists:cameras,id',
            'mac_address'    => 'nullable|string|exists:cameras,mac_address',
            'recorded_at'    => 'nullable|date',
            'truck_detected' => 'required|boolean',
            'image_url'      => 'nullable|string',
            'image'          => 'nullable|file|image|max:10240',
        ]);

        // Resolve camera_id
        $cameraId = $request->camera_id;
        if (!$cameraId && $request->filled('mac_address')) {
            $cam      = Camera::where('mac_address', $request->mac_address)->firstOrFail();
            $cameraId = $cam->id;
        }

        if (!$cameraId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Provide camera_id or mac_address.',
                'data'    => null,
            ], 422);
        }

        $recordedAt = $request->recorded_at ?? now()->toDateTimeString();
        $imageUrl   = $request->image_url;

        // Handle file upload
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('camera_captures', 'public');
            $imageUrl = Storage::url($path);
        }

        $entry = CameraData::updateOrCreate(
            ['camera_id' => $cameraId, 'recorded_at' => $recordedAt],
            ['truck_detected' => $request->boolean('truck_detected'), 'image_url' => $imageUrl]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera data stored.',
            'data'    => $entry,
        ], 201);
    }

    /**
     * PUT /api/camera-data
     * Requires: camera_id & recorded_at as identifiers
     */
    public function update(Request $request)
    {
        $request->validate([
            'camera_id'      => 'required|integer|exists:cameras,id',
            'recorded_at'    => 'required|date',
            'truck_detected' => 'sometimes|boolean',
            'image_url'      => 'nullable|string',
            'image'          => 'nullable|file|image|max:10240',
        ]);

        $entry = CameraData::where('camera_id', $request->camera_id)
            ->where('recorded_at', $request->recorded_at)
            ->firstOrFail();

        $imageUrl = $request->image_url ?? $entry->image_url;

        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('camera_captures', 'public');
            $imageUrl = Storage::url($path);
        }

        $entry->fill([
            'truck_detected' => $request->has('truck_detected')
                ? $request->boolean('truck_detected')
                : $entry->truck_detected,
            'image_url' => $imageUrl,
        ])->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera data updated.',
            'data'    => $entry,
        ]);
    }

    /**
     * DELETE /api/camera-data
     * Requires: camera_id & recorded_at as identifiers
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'camera_id'   => 'required|integer|exists:cameras,id',
            'recorded_at' => 'required|date',
        ]);

        $entry = CameraData::where('camera_id', $request->camera_id)
            ->where('recorded_at', $request->recorded_at)
            ->firstOrFail();

        $entry->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Camera data deleted.',
            'data'    => null,
        ]);
    }
}
