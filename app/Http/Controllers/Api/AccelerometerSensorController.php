<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccelerometerWirelessSensor;
use Illuminate\Http\Request;

class AccelerometerSensorController extends Controller
{
    /** GET /api/accelerometer-sensors */
    public function index()
    {
        $sensors = AccelerometerWirelessSensor::with('bridge:id,name')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Accelerometer sensors retrieved.',
            'data'    => $sensors,
        ]);
    }

    /** POST /api/accelerometer-sensors */
    public function store(Request $request)
    {
        $data = $request->validate([
            'bridge_id'   => 'required|integer|exists:bridges,id',
            'name'        => 'required|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'mac_address' => 'required|string|max:255|unique:accelerometer_wireless_sensors,mac_address',
            'type'        => 'nullable|string|max:255',
        ]);

        $sensor = AccelerometerWirelessSensor::create($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Accelerometer sensor created.',
            'data'    => $sensor->load('bridge:id,name'),
        ], 201);
    }

    /** GET /api/accelerometer-sensors/{id} */
    public function show(int $id)
    {
        $sensor = AccelerometerWirelessSensor::with(['bridge:id,name', 'latestData'])->findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sensor detail.',
            'data'    => $sensor,
        ]);
    }

    /** PUT /api/accelerometer-sensors/{id} */
    public function update(Request $request, int $id)
    {
        $sensor = AccelerometerWirelessSensor::findOrFail($id);

        $data = $request->validate([
            'bridge_id'   => 'sometimes|integer|exists:bridges,id',
            'name'        => 'sometimes|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'mac_address' => 'sometimes|string|max:255|unique:accelerometer_wireless_sensors,mac_address,' . $id,
            'type'        => 'nullable|string|max:255',
        ]);

        $sensor->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Sensor updated.',
            'data'    => $sensor->fresh('bridge:id,name'),
        ]);
    }

    /** DELETE /api/accelerometer-sensors/{id} */
    public function destroy(int $id)
    {
        $sensor = AccelerometerWirelessSensor::findOrFail($id);
        $sensor->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Sensor deleted.',
            'data'    => null,
        ]);
    }
}
