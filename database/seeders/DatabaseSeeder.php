<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Bridge;
use App\Models\AccelerometerWirelessSensor;
use App\Models\AccelerometerSensorData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────
        // 1. COUNTRY
        // ─────────────────────────────────────────
        $indonesia = DB::table('countries')->insertGetId([
            'iso_code'   => 'ID',
            'name'       => 'Indonesia',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ─────────────────────────────────────────
        // 2. PROVINCES
        // ─────────────────────────────────────────
        $provinces = [
            ['name' => 'DKI Jakarta'],
            ['name' => 'Jawa Barat'],
            ['name' => 'Jawa Tengah'],
            ['name' => 'Jawa Timur'],
            ['name' => 'Banten'],
        ];

        $provinceIds = [];
        foreach ($provinces as $prov) {
            $provinceIds[$prov['name']] = DB::table('provinces')->insertGetId([
                'country_id' => $indonesia,
                'name'       => $prov['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ─────────────────────────────────────────
        // 3. CITIES
        // ─────────────────────────────────────────
        $cities = [
            ['name' => 'Jakarta Selatan', 'province' => 'DKI Jakarta'],
            ['name' => 'Jakarta Utara',   'province' => 'DKI Jakarta'],
            ['name' => 'Jakarta Timur',   'province' => 'DKI Jakarta'],
            ['name' => 'Bandung',         'province' => 'Jawa Barat'],
            ['name' => 'Bekasi',          'province' => 'Jawa Barat'],
            ['name' => 'Bogor',           'province' => 'Jawa Barat'],
            ['name' => 'Semarang',        'province' => 'Jawa Tengah'],
            ['name' => 'Solo',            'province' => 'Jawa Tengah'],
            ['name' => 'Surabaya',        'province' => 'Jawa Timur'],
            ['name' => 'Malang',          'province' => 'Jawa Timur'],
            ['name' => 'Tangerang',       'province' => 'Banten'],
        ];

        $cityIds = [];
        foreach ($cities as $city) {
            $cityIds[$city['name']] = DB::table('cities')->insertGetId([
                'province_id' => $provinceIds[$city['province']],
                'name'        => $city['name'],
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // ─────────────────────────────────────────
        // 4. COMPANIES
        // ─────────────────────────────────────────
        $company1 = Company::create([
            'name'          => 'PT Infrastruktur Jembatan Nusantara',
            'address'       => 'Jl. Gatot Subroto No. 45, Jakarta Selatan',
            'city_id'       => $cityIds['Jakarta Selatan'],
            'contact_email' => 'info@ijn.co.id',
        ]);

        $company2 = Company::create([
            'name'          => 'PT Karya Bangun Infrastruktur',
            'address'       => 'Jl. Soekarno Hatta No. 12, Bandung',
            'city_id'       => $cityIds['Bandung'],
            'contact_email' => 'contact@kbi.co.id',
        ]);

        $company3 = Company::create([
            'name'          => 'PT Surya Mitra Konstruksi',
            'address'       => 'Jl. Ahmad Yani No. 77, Surabaya',
            'city_id'       => $cityIds['Surabaya'],
            'contact_email' => 'support@smk.co.id',
        ]);

        // ─────────────────────────────────────────
        // 5. BRIDGES — 5 jembatan berbeda kota
        // ─────────────────────────────────────────
        $bridgeData = [
            // Bridge 0 — 3 sensors (jembatan kecil, kota Jakarta)
            [
                'city_id'                           => $cityIds['Jakarta Selatan'],
                'company_id'                        => $company1->id,
                'name'                              => 'Jembatan Semanggi',
                'latitude'                          => -6.22620000,
                'longitude'                         => 106.80970000,
                'rating'                            => 8,
                'maintenance_status'                => 'Good Condition',
                'support_type'                      => 'Box Girder',
                'box_beam_length'                   => 40.00,
                'box_beam_inside_width'             => 1.20,
                'box_beam_outside_width'            => 1.50,
                'box_beam_inside_height'            => 0.90,
                'box_beam_outside_height'           => 1.10,
                'number_of_box_beams'               => 6,
                'number_of_diaphragms_per_box_beam' => 4,
                'thickness_at_diaphragms'           => 0.20,
                'thickness_of_box_beam_end'         => 0.25,
                'thickness_of_wearing_surface'      => 0.08,
                'compressive_strength_28_days'      => 35.00,
                'unit_weight_of_concrete'           => 24.00,
            ],
            // Bridge 1 — 5 sensors
            [
                'city_id'                           => $cityIds['Bandung'],
                'company_id'                        => $company2->id,
                'name'                              => 'Jembatan Pasupati',
                'latitude'                          => -6.89460000,
                'longitude'                         => 107.60760000,
                'rating'                            => 9,
                'maintenance_status'                => 'Good Condition',
                'support_type'                      => 'Cable-stayed',
                'box_beam_length'                   => 50.00,
                'box_beam_inside_width'             => 1.50,
                'box_beam_outside_width'            => 1.80,
                'box_beam_inside_height'            => 1.00,
                'box_beam_outside_height'           => 1.25,
                'number_of_box_beams'               => 8,
                'number_of_diaphragms_per_box_beam' => 5,
                'thickness_at_diaphragms'           => 0.22,
                'thickness_of_box_beam_end'         => 0.28,
                'thickness_of_wearing_surface'      => 0.09,
                'compressive_strength_28_days'      => 40.00,
                'unit_weight_of_concrete'           => 25.00,
            ],
            // Bridge 2 — 7 sensors (jembatan besar, rawan, unassigned)
            [
                'city_id'                           => $cityIds['Semarang'],
                'company_id'                        => null,
                'name'                              => 'Jembatan Kaligawe',
                'latitude'                          => -6.96530000,
                'longitude'                         => 110.45120000,
                'rating'                            => 5,
                'maintenance_status'                => 'Major Repair Needed',
                'support_type'                      => 'Simply Supported',
                'box_beam_length'                   => 25.00,
                'box_beam_inside_width'             => 1.00,
                'box_beam_outside_width'            => 1.30,
                'box_beam_inside_height'            => 0.80,
                'box_beam_outside_height'           => 1.00,
                'number_of_box_beams'               => 4,
                'number_of_diaphragms_per_box_beam' => 3,
                'thickness_at_diaphragms'           => 0.16,
                'thickness_of_box_beam_end'         => 0.20,
                'thickness_of_wearing_surface'      => 0.06,
                'compressive_strength_28_days'      => 28.00,
                'unit_weight_of_concrete'           => 23.50,
            ],
            // Bridge 3 — 8 sensors (jembatan panjang lintas selat)
            [
                'city_id'                           => $cityIds['Surabaya'],
                'company_id'                        => $company3->id,
                'name'                              => 'Jembatan Suramadu',
                'latitude'                          => -7.17550000,
                'longitude'                         => 112.77880000,
                'rating'                            => 7,
                'maintenance_status'                => 'Preventive Maintenance',
                'support_type'                      => 'Suspension',
                'box_beam_length'                   => 60.00,
                'box_beam_inside_width'             => 1.80,
                'box_beam_outside_width'            => 2.10,
                'box_beam_inside_height'            => 1.20,
                'box_beam_outside_height'           => 1.50,
                'number_of_box_beams'               => 10,
                'number_of_diaphragms_per_box_beam' => 6,
                'thickness_at_diaphragms'           => 0.25,
                'thickness_of_box_beam_end'         => 0.30,
                'thickness_of_wearing_surface'      => 0.10,
                'compressive_strength_28_days'      => 45.00,
                'unit_weight_of_concrete'           => 25.50,
            ],
            // Bridge 4 — 10 sensors (jembatan flagship, unassigned)
            [
                'city_id'                           => $cityIds['Jakarta Utara'],
                'company_id'                        => null,
                'name'                              => 'Jembatan Ancol Baru',
                'latitude'                          => -6.12430000,
                'longitude'                         => 106.84560000,
                'rating'                            => 6,
                'maintenance_status'                => 'Preventive Maintenance',
                'support_type'                      => 'Continuous',
                'box_beam_length'                   => 35.00,
                'box_beam_inside_width'             => 1.10,
                'box_beam_outside_width'            => 1.40,
                'box_beam_inside_height'            => 0.85,
                'box_beam_outside_height'           => 1.05,
                'number_of_box_beams'               => 5,
                'number_of_diaphragms_per_box_beam' => 3,
                'thickness_at_diaphragms'           => 0.18,
                'thickness_of_box_beam_end'         => 0.22,
                'thickness_of_wearing_surface'      => 0.07,
                'compressive_strength_28_days'      => 32.00,
                'unit_weight_of_concrete'           => 24.00,
            ],
        ];

        $bridges = [];
        foreach ($bridgeData as $data) {
            $bridges[] = Bridge::create($data);
        }

        // ─────────────────────────────────────────
        // 6. SENSORS — jumlah berbeda per jembatan
        //    Bridge 0 → 3  sensors
        //    Bridge 1 → 5  sensors
        //    Bridge 2 → 7  sensors
        //    Bridge 3 → 8  sensors
        //    Bridge 4 → 10 sensors
        // ─────────────────────────────────────────
        $sensorCountPerBridge = [3, 5, 7, 8, 10];

        $brandPool  = ['Sunspot WSN v2', 'SensoNode Pro', 'VibraLink X3', 'SeismoEdge S4', 'AccuSense T7'];
        $namePrefix = ['Sunspot', 'SensoNode', 'VibraLink', 'SeismoEdge', 'AccuSense'];
        $typePool   = ['A12', 'A14', 'A16', 'B10', 'B12', 'C08', 'C10', 'D06', 'D08', 'E04'];

        // Per-sensor base characteristics (unique per sensor index across all bridges)
        // [baseFreq_hz, baseAmplitude, freqJitter, ampJitter]
        $sensorCharacteristics = [
            [2.45, 0.0032, 0.08, 0.0008],   // sensor type: calm/steady
            [3.12, 0.0051, 0.12, 0.0012],
            [1.87, 0.0028, 0.06, 0.0007],
            [4.03, 0.0074, 0.15, 0.0018],
            [2.78, 0.0043, 0.10, 0.0010],
            [3.55, 0.0061, 0.13, 0.0014],
            [1.62, 0.0022, 0.05, 0.0005],
            [5.10, 0.0092, 0.20, 0.0025],
            [2.20, 0.0038, 0.09, 0.0009],
            [3.80, 0.0065, 0.14, 0.0016],
            [4.50, 0.0081, 0.18, 0.0020],
            [2.95, 0.0048, 0.11, 0.0011],
            [1.40, 0.0018, 0.04, 0.0004],
            [3.30, 0.0057, 0.12, 0.0013],
            [2.65, 0.0041, 0.09, 0.0009],
            [4.80, 0.0088, 0.19, 0.0022],
            [1.95, 0.0030, 0.07, 0.0007],
            [3.70, 0.0063, 0.14, 0.0015],
            [5.40, 0.0098, 0.22, 0.0028],
            [2.35, 0.0035, 0.08, 0.0008],
            [4.20, 0.0077, 0.17, 0.0019],
            [1.75, 0.0025, 0.06, 0.0006],
            [3.45, 0.0059, 0.13, 0.0014],
            [2.55, 0.0039, 0.09, 0.0009],
            [5.70, 0.0105, 0.24, 0.0030],
            [2.10, 0.0033, 0.07, 0.0008],
            [4.60, 0.0083, 0.18, 0.0021],
            [1.50, 0.0020, 0.05, 0.0005],
            [3.90, 0.0067, 0.15, 0.0017],
            [6.00, 0.0110, 0.25, 0.0032],
            [2.80, 0.0045, 0.10, 0.0010],
            [4.35, 0.0079, 0.17, 0.0019],
            [1.30, 0.0016, 0.04, 0.0004],
        ];

        // Sensor index counter (global, untuk mac address & karakteristik unik)
        $globalSensorIdx = 0;
        $allSensors      = []; // [ [sensor, characteristics] ]

        foreach ($bridges as $bridgeIdx => $bridge) {
            $count = $sensorCountPerBridge[$bridgeIdx];

            for ($s = 0; $s < $count; $s++) {
                $charIdx = $globalSensorIdx % count($sensorCharacteristics);
                $char    = $sensorCharacteristics[$charIdx];

                $mac = sprintf(
                    '%02X:%02X:%02X:%02X:%02X:%02X',
                    $bridgeIdx + 1,
                    $s + 1,
                    mt_rand(10, 99),
                    mt_rand(10, 99),
                    mt_rand(10, 99),
                    mt_rand(10, 99)
                );

                $brandIdx = $s % count($brandPool);
                $sensor = AccelerometerWirelessSensor::create([
                    'bridge_id'   => $bridge->id,
                    'brand_model' => $brandPool[$brandIdx],
                    'mac_address' => $mac,
                    'name'        => $namePrefix[$brandIdx] . ' #' . ($s + 1),
                    'type'        => $typePool[$globalSensorIdx % count($typePool)],
                ]);

                $allSensors[] = ['sensor' => $sensor, 'char' => $char, 'bridge_idx' => $bridgeIdx];
                $globalSensorIdx++;
            }
        }

        // ─────────────────────────────────────────
        // 7. SENSOR DATA — ~500 readings per sensor
        //    Recording interval: setiap 30 menit
        //    Periode: 10 hari terakhir = 10 * 48 = 480 readings → ~500
        //    Pola realistis:
        //      - Siang (06:00–22:00): traffic tinggi → amplitudo & frekuensi lebih tinggi
        //      - Malam (22:00–06:00): quiet → amplitudo lebih rendah
        //      - Sesekali ada spike (truk berat / gempa kecil)
        //      - Setiap sensor punya baseline berbeda
        //      - Bridge rusak (Kaligawe) → nilai lebih tinggi & lebih jitter
        // ─────────────────────────────────────────

        // Bridge health factors (bridge idx → multiplier untuk amplitudo)
        // Bridge 2 (Kaligawe, rating 5) → lebih tinggi; Bridge 1 (Pasupati, rating 9) → lebih rendah
        $bridgeHealthFactor = [
            0 => 1.0,   // Semanggi, good
            1 => 0.8,   // Pasupati, very good
            2 => 1.6,   // Kaligawe, bad
            3 => 1.15,  // Suramadu, okay
            4 => 1.25,  // Ancol Baru, preventive
        ];

        $intervalMinutes = 30;
        $totalReadings   = 480; // 10 hari × 48 per hari

        foreach ($allSensors as $entry) {
            $sensor     = $entry['sensor'];
            $char       = $entry['char'];
            $bridgeIdx  = $entry['bridge_idx'];
            $healthMult = $bridgeHealthFactor[$bridgeIdx];

            [$baseFreq, $baseAmp, $freqJitter, $ampJitter] = $char;

            $batch = [];

            for ($i = $totalReadings; $i >= 1; $i--) {
                $recordedAt = Carbon::now()
                    ->subMinutes($i * $intervalMinutes)
                    ->second(0);

                $hour = (int) $recordedAt->format('H');

                // ── Waktu-of-day factor ──────────────────
                // Traffic peak: 07-09 (pagi), 12-14, 17-20 (sore)
                // Night quiet: 23-05
                if ($hour >= 7  && $hour <= 9)  $timeFactor = 1.45;  // rush morning
                elseif ($hour >= 12 && $hour <= 14) $timeFactor = 1.25;  // lunch traffic
                elseif ($hour >= 17 && $hour <= 20) $timeFactor = 1.55;  // rush evening
                elseif ($hour >= 23 || $hour <= 5)  $timeFactor = 0.55;  // night quiet
                elseif ($hour >= 6  && $hour < 7)   $timeFactor = 0.80;  // early morning
                else                                 $timeFactor = 1.10;  // normal daytime

                // ── Day-of-week factor ───────────────────
                $dow = (int) $recordedAt->dayOfWeek; // 0=Sun, 6=Sat
                $dayFactor = ($dow === 0 || $dow === 6) ? 0.75 : 1.0; // weekend quieter

                // ── Random noise ─────────────────────────
                $freqNoise = (mt_rand(-1000, 1000) / 1000) * $freqJitter;
                $ampNoise  = (mt_rand(-1000, 1000) / 1000) * $ampJitter;

                // ── Occasional spike (truk berat / event) ─
                // ~2% chance of spike
                $spikeFactor = 1.0;
                if (mt_rand(1, 100) <= 2) {
                    $spikeFactor = mt_rand(150, 280) / 100; // 1.5x – 2.8x
                }

                $freq = round($baseFreq + $freqNoise, 5);
                $amp  = round(
                    max(0.0001, ($baseAmp * $timeFactor * $dayFactor * $healthMult * $spikeFactor) + $ampNoise),
                    6
                );

                // Clamp freq to sensible range
                $freq = max(0.5, min(15.0, $freq));

                $batch[] = [
                    'accelerometer_wireless_sensor_id' => $sensor->id,
                    'recorded_at'                      => $recordedAt->format('Y-m-d H:i:s'),
                    'frequency_hz'                     => $freq,
                    'amplitude'                        => $amp,
                ];
            }

            // Insert in chunks of 100 untuk menghindari memory issue
            foreach (array_chunk($batch, 100) as $chunk) {
                DB::table('accelerometer_sensor_data')->insert($chunk);
            }
        }

        // ─────────────────────────────────────────
        // 8. USERS
        // ─────────────────────────────────────────

        // Superadmin
        User::create([
            'name'       => 'Admin SHMS',
            'email'      => 'admin@shms.com',
            'password'   => Hash::make('password'),
            'role'       => 'superadmin',
            'company_id' => null,
            'bridge_id'  => null,
            'phone'      => '+62-21-5550001',
            'address'    => 'Jl. Gatot Subroto No. 45, Jakarta Selatan',
        ]);

        // Company Accounts (Owners)
        $companyAccounts = [
            [
                'name'       => $company1->name,
                'email'      => $company1->contact_email,
                'password'   => Hash::make('password'),
                'role'       => 'company',
                'company_id' => $company1->id,
                'bridge_id'  => null,
                'phone'      => '+62-21-5550011',
                'address'    => $company1->address,
            ],
            [
                'name'       => $company2->name,
                'email'      => $company2->contact_email,
                'password'   => Hash::make('password'),
                'role'       => 'company',
                'company_id' => $company2->id,
                'bridge_id'  => null,
                'phone'      => '+62-22-7770022',
                'address'    => $company2->address,
            ],
            [
                'name'       => $company3->name,
                'email'      => $company3->contact_email,
                'password'   => Hash::make('password'),
                'role'       => 'company',
                'company_id' => $company3->id,
                'bridge_id'  => null,
                'phone'      => '+62-31-8880033',
                'address'    => $company3->address,
            ],
        ];

        foreach ($companyAccounts as $coAcc) {
            User::create($coAcc);
        }

        // PIC per jembatan
        $picData = [
            [
                'name'       => 'Budi Santoso',
                'email'      => 'budi.santoso@ijn.co.id',
                'role'       => 'pic',
                'company_id' => $company1->id,
                'bridge_id'  => $bridges[0]->id,
                'phone'      => '+62-811-1000001',
                'address'    => 'Jl. Tebet Raya No. 10, Jakarta Selatan',
            ],
            [
                'name'       => 'Dewi Rahayu',
                'email'      => 'dewi.rahayu@kbi.co.id',
                'role'       => 'pic',
                'company_id' => $company2->id,
                'bridge_id'  => $bridges[1]->id,
                'phone'      => '+62-811-2000001',
                'address'    => 'Jl. Dago No. 88, Bandung',
            ],
            [
                'name'       => 'Ahmad Fauzi',
                'email'      => 'ahmad.fauzi@pic.shms.com',
                'role'       => 'pic',
                'company_id' => null,
                'bridge_id'  => $bridges[2]->id,
                'phone'      => '+62-811-2000002',
                'address'    => 'Jl. Simpang Lima No. 3, Semarang',
            ],
            [
                'name'       => 'Siti Nurhaliza',
                'email'      => 'siti.nurhaliza@smk.co.id',
                'role'       => 'pic',
                'company_id' => $company3->id,
                'bridge_id'  => $bridges[3]->id,
                'phone'      => '+62-811-3000001',
                'address'    => 'Jl. Raya Darmo No. 20, Surabaya',
            ],
            [
                'name'       => 'Hendra Wijaya',
                'email'      => 'hendra.wijaya@pic.shms.com',
                'role'       => 'pic',
                'company_id' => null,
                'bridge_id'  => $bridges[4]->id,
                'phone'      => '+62-811-1000003',
                'address'    => 'Jl. Mangga Besar No. 5, Jakarta Utara',
            ],
        ];

        foreach ($picData as $pic) {
            User::create(array_merge($pic, ['password' => Hash::make('password')]));
        }
    }
}
