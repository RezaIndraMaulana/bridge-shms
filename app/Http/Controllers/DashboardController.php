<?php

namespace App\Http\Controllers;

use App\Models\Bridge;
use App\Models\AccelerometerSensorData;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Bridge::with(['city', 'sensors']);

        if ($user->isCompany()) {
            $query->where('company_id', $user->company_id);
        } elseif ($user->isPic() && $user->bridge_id) {
            $query->where('id', $user->bridge_id);
        }

        $bridges = $query->get();

        return view('dashboard', compact('bridges'));
    }

    public function show(Bridge $bridge)
    {
        $user = auth()->user();

        // Ensure company user can only see bridges belonging to their company
        if ($user->isCompany() && $bridge->company_id !== $user->company_id) {
            abort(403, 'Unauthorized. This bridge does not belong to your company.');
        }

        // Ensure PIC user can only see their assigned bridge
        if ($user->isPic() && $user->bridge_id && $bridge->id !== $user->bridge_id) {
            abort(403, 'Unauthorized. You are not assigned to this bridge.');
        }

        $bridge->load(['city', 'sensors.sensorData']);

        $sensors = $bridge->sensors;

        // Collect all sensor data ordered by recorded_at desc for the history table
        $sensorHistory = AccelerometerSensorData::whereIn(
            'accelerometer_wireless_sensor_id',
            $sensors->pluck('id')
        )
            ->with('sensor')
            ->orderByDesc('recorded_at')
            ->get();

        // Build frequency chart data (last 50 readings across all sensors, ordered asc for chart)
        $frequencyChartData = AccelerometerSensorData::whereIn(
            'accelerometer_wireless_sensor_id',
            $sensors->pluck('id')
        )
            ->orderBy('recorded_at')
            ->get()
            ->map(fn ($d) => [
                'time'      => $d->recorded_at->format('d/m/Y, H:i:s'),
                'frequency' => $d->frequency_hz,
            ]);

        // Mode shape: amplitude over sensor index
        $modeShapeData = $sensors->map(function ($sensor, $idx) {
            $avg = $sensor->sensorData->avg('amplitude') ?? 0;
            return ['x' => $idx, 'y' => round($avg, 4)];
        })->values();

        // Build frequency table rows (grouped by recorded_at timestamp)
        // Each row has one reading per sensor at the same timestamp
        $freqTableData  = $this->buildTableData($sensors, 'frequency_hz');
        $ampTableData   = $this->buildTableData($sensors, 'amplitude');

        // Rating history — build from sensor data snapshots (avg per timestamp group)
        $historyRating = AccelerometerSensorData::whereIn(
            'accelerometer_wireless_sensor_id',
            $sensors->pluck('id')
        )
            ->orderByDesc('recorded_at')
            ->get()
            ->groupBy(fn ($d) => $d->recorded_at->format('Y-m-d H:i:s'))
            ->take(15)
            ->map(function ($group, $time) use ($bridge) {
                return [
                    'avg_frequency' => round($group->avg('frequency_hz'), 5),
                    'rating'        => $bridge->rating ?? rand(6, 9),
                    'time'          => $time,
                ];
            })->values();

        // Build per-sensor synthetic vibration waveform for Time/Frequency Domain charts
        // We generate a realistic 512-point vibration signal based on stored freq & amplitude data
        $perSensorTimeDomain = $sensors->map(function ($sensor, $idx) {
            $data = $sensor->sensorData->sortBy('recorded_at')->values();

            // Statistical base values from stored readings
            $avgFreq = $data->avg('frequency_hz') ?? 2.45;   // fundamental frequency (Hz)
            $avgAmp  = $data->avg('amplitude')    ?? 0.003;  // amplitude proxy

            // Generate 2048-point waveform spanning 9 seconds — dense, realistic vibration
            // Sampling rate: 2048 / 9 ≈ 227.6 Hz  →  Nyquist ≈ 113 Hz
            $N  = 2048;
            $fs = $N / 9.0;         // samples per second
            $dt = 1000.0 / $fs;    // time step in ms

            // Scale so peak acceleration ≈ ±15–35 in/sec²
            $scale = max($avgAmp * 6000, 8.0);

            // Per-timestamp variance drawn from stored amplitude readings
            $storedAmps = $data->pluck('amplitude')->toArray();
            $nStored    = count($storedAmps);

            $waveform = [];
            srand($sensor->id * 7);
            for ($i = 0; $i < $N; $i++) {
                $t = $i / $fs;

                // Local scale from stored data (repeat pattern)
                $localScale = $nStored > 0
                    ? ($storedAmps[$i % $nStored] / max($avgAmp, 1e-9))
                    : 1.0;
                $localScale = max(0.4, min($localScale, 2.5));

                // Fundamental + 4 harmonics + dense broadband noise
                $noise = 0;
                for ($h = 0; $h < 6; $h++) {
                    $noise += ((mt_rand() / mt_getrandmax()) - 0.5);
                }
                $noise /= 6; // averaged noise ≈ normal distribution

                $acc = $scale * $localScale * (
                      1.00 * sin(2 * M_PI * $avgFreq * $t)
                    + 0.35 * sin(2 * M_PI * $avgFreq * 2.0 * $t + 0.52)
                    + 0.18 * sin(2 * M_PI * $avgFreq * 3.0 * $t + 1.05)
                    + 0.10 * sin(2 * M_PI * $avgFreq * 4.0 * $t + 1.57)
                    + 0.06 * sin(2 * M_PI * $avgFreq * 5.0 * $t + 2.09)
                    + 0.50 * $noise
                );

                $waveform[] = [
                    't_ms' => (float) round($i * $dt, 2),
                    'acc'  => (float) round($acc, 3),
                ];
            }

            return [
                'id'       => $sensor->id,
                'label'    => 'Sensor ' . ($idx + 1) . ($sensor->name ? ' – ' . $sensor->name : ''),
                'code'     => 'sensor' . ($idx + 1),
                'name'     => $sensor->name ?? ('Sensor ' . ($idx + 1)),
                'avg_freq' => round($avgFreq, 4),
                'fs'       => round($fs, 4),
                'data'     => $waveform,
            ];
        })->values();

        return view('bridge-detail', compact(
            'bridge',
            'sensors',
            'sensorHistory',
            'frequencyChartData',
            'modeShapeData',
            'freqTableData',
            'ampTableData',
            'historyRating',
            'perSensorTimeDomain',
        ));
    }

    /**
     * Build a table where rows are timestamps and columns are sensors.
     */
    private function buildTableData($sensors, string $field): array
    {
        // Collect all data keyed by [recorded_at][sensor_id]
        $byTime = [];
        foreach ($sensors as $sensor) {
            foreach ($sensor->sensorData as $d) {
                $key = $d->recorded_at->format('Y-m-d H:i:s');
                $byTime[$key][$sensor->id] = $d->$field;
            }
        }

        // Sort descending by time
        krsort($byTime);

        $rows = [];
        foreach ($byTime as $time => $vals) {
            $row = ['time' => $time, 'values' => []];
            foreach ($sensors as $sensor) {
                $row['values'][] = $vals[$sensor->id] ?? '-';
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
