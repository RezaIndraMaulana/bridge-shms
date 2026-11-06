<?php

namespace App\Http\Controllers;

use App\Models\AccelerometerWirelessSensor;
use App\Models\Bridge;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $query = AccelerometerWirelessSensor::with('bridge')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%")
                  ->orWhere('brand_model', 'like', "%{$search}%")
                  ->orWhere('mac_address', 'like', "%{$search}%")
                  ->orWhereHas('bridge', fn($bq) => $bq->where('name', 'like', "%{$search}%"));
            });
        }

        $sensors = $query->paginate(10)->withQueryString();
        $bridges  = Bridge::orderBy('name')->get();

        return view('manage.sensors.index', compact('sensors', 'bridges'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'nullable|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'mac_address' => 'required|string|max:255|unique:accelerometer_wireless_sensors,mac_address',
            'bridge_id'   => 'required|exists:bridges,id',
        ]);

        AccelerometerWirelessSensor::create($validated);

        return redirect()->route('manage.sensors.index')
                         ->with('success', "Sensor \"{$validated['name']}\" has been added.");
    }

    public function update(Request $request, AccelerometerWirelessSensor $sensor)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'nullable|string|max:255',
            'brand_model' => 'nullable|string|max:255',
            'mac_address' => 'required|string|max:255|unique:accelerometer_wireless_sensors,mac_address,' . $sensor->id,
            'bridge_id'   => 'required|exists:bridges,id',
        ]);

        $sensor->update($validated);

        return redirect()->route('manage.sensors.index')
                         ->with('success', "Sensor \"{$sensor->name}\" has been updated.");
    }

    public function destroy(AccelerometerWirelessSensor $sensor)
    {
        $name = $sensor->name;
        $sensor->delete();

        return redirect()->route('manage.sensors.index')
                         ->with('success', "Sensor \"{$name}\" has been deleted.");
    }
}
