<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccelerometerSensorData;
use App\Models\AccelerometerWirelessSensor;
use Illuminate\Http\Request;

class AccelerometerDataController extends Controller
{
    /**
     * GET /api/accelerometer-data
     * Filters: sensor_id, mac_address, from, to, per_page
     */
    public function index(Request $request)
    {
        $request->validate([
            'sensor_id'   => 'nullable|integer|exists:accelerometer_wireless_sensors,id',
            'mac_address' => 'nullable|string',
            'from'        => 'nullable|date',
            'to'          => 'nullable|date|after_or_equal:from',
            'per_page'    => 'nullable|integer|min:1|max:500',
        ]);

        $query = AccelerometerSensorData::with('sensor:id,name,mac_address');

        if ($request->filled('sensor_id')) {
            $query->where('accelerometer_wireless_sensor_id', $request->sensor_id);
        }

        if ($request->filled('mac_address')) {
            $sensor = AccelerometerWirelessSensor::where('mac_address', $request->mac_address)->firstOrFail();
            $query->where('accelerometer_wireless_sensor_id', $sensor->id);
        }

        if ($request->filled('from')) {
            $query->where('recorded_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('recorded_at', '<=', $request->to);
        }

        $perPage = $request->integer('per_page', 100);
        $data    = $query->orderByDesc('recorded_at')->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Accelerometer data retrieved.',
            'data'    => $data,
        ]);
    }

    /**
     * POST /api/accelerometer-data
     * Supports single record or batch array.
     * Single: { sensor_id|mac_address, recorded_at?, frequency_hz, amplitude }
     * Batch:  { batch: [ {...}, {...} ] }
     */
    public function store(Request $request)
    {
        // --- BATCH MODE ---
        if ($request->has('batch')) {
            $request->validate([
                'batch'                  => 'required|array|min:1|max:500',
                'batch.*.sensor_id'      => 'nullable|integer|exists:accelerometer_wireless_sensors,id',
                'batch.*.mac_address'    => 'nullable|string',
                'batch.*.recorded_at'    => 'nullable|date',
                'batch.*.frequency_hz'   => 'required|numeric',
                'batch.*.amplitude'      => 'required|numeric',
            ]);

            $inserted = [];
            foreach ($request->batch as $item) {
                $sensorId = $item['sensor_id'] ?? null;
                if (!$sensorId && !empty($item['mac_address'])) {
                    $sensor   = AccelerometerWirelessSensor::where('mac_address', $item['mac_address'])->first();
                    $sensorId = $sensor?->id;
                }

                if (!$sensorId) continue;

                $recordedAt = $item['recorded_at'] ?? now()->toDateTimeString();

                $row = AccelerometerSensorData::updateOrCreate(
                    ['accelerometer_wireless_sensor_id' => $sensorId, 'recorded_at' => $recordedAt],
                    ['frequency_hz' => $item['frequency_hz'], 'amplitude' => $item['amplitude']]
                );

                $inserted[] = $row;
            }

            return response()->json([
                'status'  => 'success',
                'message' => count($inserted) . ' records stored (batch).',
                'data'    => $inserted,
            ], 201);
        }

        // --- SINGLE MODE ---
        $request->validate([
            'sensor_id'    => 'nullable|integer|exists:accelerometer_wireless_sensors,id',
            'mac_address'  => 'nullable|string',
            'recorded_at'  => 'nullable|date',
            'frequency_hz' => 'required|numeric',
            'amplitude'    => 'required|numeric',
        ]);

        $sensorId = $request->sensor_id;
        if (!$sensorId && $request->filled('mac_address')) {
            $sensor   = AccelerometerWirelessSensor::where('mac_address', $request->mac_address)->firstOrFail();
            $sensorId = $sensor->id;
        }

        if (!$sensorId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Provide sensor_id or mac_address.',
                'data'    => null,
            ], 422);
        }

        $recordedAt = $request->recorded_at ?? now()->toDateTimeString();

        $entry = AccelerometerSensorData::updateOrCreate(
            ['accelerometer_wireless_sensor_id' => $sensorId, 'recorded_at' => $recordedAt],
            ['frequency_hz' => $request->frequency_hz, 'amplitude' => $request->amplitude]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Accelerometer data stored.',
            'data'    => $entry,
        ], 201);
    }
}
