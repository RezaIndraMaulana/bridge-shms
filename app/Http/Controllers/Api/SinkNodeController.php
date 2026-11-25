<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SinkNode;
use Illuminate\Http\Request;

class SinkNodeController extends Controller
{
    /** GET /api/sink-nodes */
    public function index()
    {
        $nodes = SinkNode::with('bridge:id,name')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sink nodes retrieved.',
            'data'    => $nodes,
        ]);
    }

    /** POST /api/sink-nodes */
    public function store(Request $request)
    {
        $data = $request->validate([
            'bridge_id'  => 'required|integer|exists:bridges,id',
            'ip_address' => 'nullable|ip',
            'status'     => 'nullable|string|in:active,inactive,maintenance',
        ]);

        $node = SinkNode::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sink node created.',
            'data'    => $node->load('bridge:id,name'),
        ], 201);
    }

    /** GET /api/sink-nodes/{id} */
    public function show(int $id)
    {
        $node = SinkNode::with(['bridge:id,name', 'cameras', 'accelerometerSensors'])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sink node detail.',
            'data'    => $node,
        ]);
    }

    /** PUT /api/sink-nodes/{id} */
    public function update(Request $request, int $id)
    {
        $node = SinkNode::findOrFail($id);

        $data = $request->validate([
            'bridge_id'  => 'sometimes|integer|exists:bridges,id',
            'ip_address' => 'nullable|ip',
            'status'     => 'nullable|string|in:active,inactive,maintenance',
        ]);

        $node->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sink node updated.',
            'data'    => $node->fresh('bridge:id,name'),
        ]);
    }

    /** DELETE /api/sink-nodes/{id} */
    public function destroy(int $id)
    {
        $node = SinkNode::findOrFail($id);
        $node->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sink node deleted.',
            'data'    => null,
        ]);
    }
}
