<?php

namespace Database\Seeders;

use App\Models\AccelerometerSensorData;
use App\Models\AccelerometerWirelessSensor;
use App\Models\Bridge;
use App\Models\Camera;
use App\Models\CameraData;
use App\Models\SinkNode;
use Illuminate\Database\Seeder;

class SimulationSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil bridge pertama yang sudah ada
        $bridge = Bridge::first();

        if (!$bridge) {
            $this->command->error('No bridge found. Run DatabaseSeeder first.');
            return;
        }

        // ─── Sink Node ────────────────────────────────────────────────────
        $sinkNode = SinkNode::firstOrCreate(
            ['ip_address' => '192.168.1.100'],
            [
                'bridge_id'  => $bridge->id,
                'ip_address' => '192.168.1.100',
                'status'     => 'active',
            ]
        );

        $this->command->info("✓ SinkNode ID={$sinkNode->id} (bridge: {$bridge->name})");

        // ─── Camera ───────────────────────────────────────────────────────
        $camera = Camera::firstOrCreate(
            ['mac_address' => 'AA:BB:CC:DD:EE:01'],
            [
                'sink_node_id' => $sinkNode->id,
                'brand_model'  => 'Hikvision DS-2CD2143G2-I',
                'mac_address'  => 'AA:BB:CC:DD:EE:01',
            ]
        );

        $this->command->info("✓ Camera ID={$camera->id} (mac: {$camera->mac_address})");

        // ─── Camera Data samples ──────────────────────────────────────────
        $cameraDataSamples = [
            ['truck_detected' => false, 'recorded_at' => now()->subMinutes(10)],
            ['truck_detected' => true,  'recorded_at' => now()->subMinutes(5)],
            ['truck_detected' => false, 'recorded_at' => now()->subMinutes(1)],
        ];

        foreach ($cameraDataSamples as $sample) {
            CameraData::firstOrCreate(
                ['camera_id' => $camera->id, 'recorded_at' => $sample['recorded_at']],
                ['truck_detected' => $sample['truck_detected'], 'image_url' => null]
            );
        }

        $this->command->info("✓ 3 CameraData samples inserted.");

        // ─── Accelerometer Sensor ─────────────────────────────────────────
        $sensor = AccelerometerWirelessSensor::firstOrCreate(
            ['mac_address' => 'FF:EE:DD:CC:BB:01'],
            [
                'bridge_id'   => $bridge->id,
                'name'        => 'Sensor Accelerometer A1',
                'brand_model' => 'ADXL345',
                'mac_address' => 'FF:EE:DD:CC:BB:01',
                'type'        => 'wireless',
            ]
        );

        $this->command->info("✓ AccelerometerSensor ID={$sensor->id}");

        // ─── Accelerometer Data samples ───────────────────────────────────
        $accelSamples = [
            ['frequency_hz' => 2.5,  'amplitude' => 0.012, 'recorded_at' => now()->subMinutes(15)],
            ['frequency_hz' => 3.1,  'amplitude' => 0.018, 'recorded_at' => now()->subMinutes(10)],
            ['frequency_hz' => 2.8,  'amplitude' => 0.015, 'recorded_at' => now()->subMinutes(5)],
            ['frequency_hz' => 10.4, 'amplitude' => 0.045, 'recorded_at' => now()->subMinutes(2)],
        ];

        foreach ($accelSamples as $sample) {
            AccelerometerSensorData::firstOrCreate(
                [
                    'accelerometer_wireless_sensor_id' => $sensor->id,
                    'recorded_at'                      => $sample['recorded_at'],
                ],
                [
                    'frequency_hz' => $sample['frequency_hz'],
                    'amplitude'    => $sample['amplitude'],
                ]
            );
        }

        $this->command->info("✓ 4 AccelerometerSensorData samples inserted.");
        $this->command->info('');
        $this->command->info('=== Simulation credentials for Postman ===');
        $this->command->info('POST /api/login → email: admin@shms.com / password: password');
        $this->command->info("Camera MAC : {$camera->mac_address}");
        $this->command->info("Sensor MAC : {$sensor->mac_address}");
        $this->command->info("SinkNode ID: {$sinkNode->id}");
    }
}
