    <x-app-layout>
    <x-slot name="pageTitle">{{ $bridge->name }}</x-slot>

    {{-- Back button --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    {{-- =========================================================
         HERO: Photo + Rating + History Rating
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-0">

            {{-- Left: Bridge Photo --}}
            <div class="lg:col-span-3">
                @if($bridge->photo_url)
                    <img src="{{ asset('storage/' . $bridge->photo_url) }}"
                         alt="{{ $bridge->name }}"
                         class="w-full h-72 lg:h-full object-cover"
                         onerror="this.src='https://placehold.co/800x500/1e293b/ffffff?text=No+Photo'">
                @else
                    <div class="w-full h-72 lg:h-full bg-slate-800 flex items-center justify-center">
                        <span class="text-slate-400 text-sm">No Photo Available</span>
                    </div>
                @endif
            </div>

            {{-- Right: Rating + History --}}
            <div class="lg:col-span-2 flex flex-col p-6 gap-5">

                {{-- Rating Box --}}
                <div class="text-center border border-gray-100 rounded-xl p-5">
                    <p class="text-sm text-gray-500 font-medium mb-1">Rating</p>
                    @php $r = $bridge->rating ?? 0; @endphp
                    <p class="text-7xl font-black leading-none
                        {{ $r >= 8 ? 'text-green-500' : ($r >= 6 ? 'text-amber-500' : 'text-red-500') }}">
                        {{ $r }}
                    </p>
                    <p class="text-sm font-semibold mt-2
                        {{ str_contains(strtolower($bridge->maintenance_status ?? ''), 'good') ? 'text-green-600' :
                           (str_contains(strtolower($bridge->maintenance_status ?? ''), 'routine') ? 'text-blue-600' :
                           (str_contains(strtolower($bridge->maintenance_status ?? ''), 'corrective') ? 'text-red-600' : 'text-amber-600')) }}">
                        {{ $bridge->maintenance_status ?? 'â€”' }}
                    </p>
                </div>

                {{-- History Rating Table --}}
                <div class="flex-1 overflow-auto">
                    <p class="text-sm font-semibold text-gray-700 mb-2">History Rating</p>
                    <div class="overflow-auto rounded-lg border border-gray-100" style="max-height: 240px;">
                        <table class="w-full text-xs">
                            <thead class="sticky top-0 z-10">
                                <tr class="bg-blue-600 text-white">
                                    <th class="px-3 py-2 font-semibold text-center">No</th>
                                    <th class="px-3 py-2 font-semibold text-center">Avg Frequency</th>
                                    <th class="px-3 py-2 font-semibold text-center">Rating</th>
                                    <th class="px-3 py-2 font-semibold text-center">Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($historyRating as $i => $h)
                                <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50/50 transition-colors">
                                    <td class="px-3 py-1.5 text-center text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-3 py-1.5 text-center font-mono text-gray-700">{{ number_format($h['avg_frequency'], 5) }}</td>
                                    <td class="px-3 py-1.5 text-center">
                                        <span class="font-bold {{ $h['rating'] >= 8 ? 'text-green-600' : ($h['rating'] >= 6 ? 'text-amber-600' : 'text-red-600') }}">
                                            {{ $h['rating'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-1.5 text-center text-gray-400">
                                        {{ \Carbon\Carbon::parse($h['time'])->format('M j, Y H:i:s') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- =========================================================
         BRIDGE INFO + TECHNICAL DATA
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $bridge->name }}</h3>
        <p class="text-sm text-gray-500 mb-5">
            {{ optional($bridge->city)->name ?? 'â€”' }} &bull;
            {{ $bridge->latitude }}, {{ $bridge->longitude }}
        </p>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-3 text-sm">
            @php
            $fields = [
                'Box Beam Length (in)'                  => $bridge->box_beam_length,
                'Box Beam Inside Width (in)'            => $bridge->box_beam_inside_width,
                'Box Beam Outside Width (in)'           => $bridge->box_beam_outside_width,
                'Box Beam Inside Height (in)'           => $bridge->box_beam_inside_height,
                'Box Beam Outside Height (in)'          => $bridge->box_beam_outside_height,
                'Number of Box Beams'                   => $bridge->number_of_box_beams,
                'No. of Diaphragms per Box Beam'        => $bridge->number_of_diaphragms_per_box_beam,
                'Thickness at Diaphragms (in)'          => $bridge->thickness_at_diaphragms,
                'Thickness of Box Beam End (in)'        => $bridge->thickness_of_box_beam_end,
                'Thickness of Wearing Surface (in)'     => $bridge->thickness_of_wearing_surface,
                'Compressive Strength 28 Days (MPa)'    => $bridge->compressive_strength_28_days,
                'Unit Weight of Concrete (kN/mÂ³)'       => $bridge->unit_weight_of_concrete,
                'Support Type'                          => $bridge->support_type,
            ];
            @endphp
            @foreach($fields as $label => $value)
            <div class="border-b border-dashed border-gray-100 pb-2">
                <span class="text-xs text-gray-400 block">{{ $label }}</span>
                <span class="font-semibold text-gray-800">{{ $value ?? 'â€”' }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- =========================================================
         SENSOR LIST
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-5 pt-4 pb-3 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
                Sensor List
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 font-semibold text-left">No</th>
                        <th class="px-5 py-3 font-semibold text-left">Code</th>
                        <th class="px-5 py-3 font-semibold text-left">Sensor</th>
                        <th class="px-5 py-3 font-semibold text-left">Type</th>
                        <th class="px-5 py-3 font-semibold text-left">MAC Address</th>
                        <th class="px-5 py-3 font-semibold text-left">Last Triggered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sensors as $i => $sensor)
                    @php $latest = $sensor->sensorData->sortByDesc('recorded_at')->first(); @endphp
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50/40 transition-colors">
                        <td class="px-5 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-mono text-xs font-semibold text-blue-700">sensor{{ $i + 1 }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $sensor->name ?? $sensor->brand_model ?? 'â€”' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $sensor->type ?? 'â€”' }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-400">{{ $sensor->mac_address }}</td>
                        <td class="px-5 py-3 text-gray-500 text-xs">
                            @if($latest)
                                {{ $latest->recorded_at->format('d/m/Y, H:i:s') }}
                                <span class="ml-1.5 inline-block w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                            @else
                                <span class="text-gray-300">No data</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================================================
         FREQUENCY CHART + MODE SHAPE
    ========================================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Frequency Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Frequency, Hz
            </h3>
            <canvas id="freqChart" height="200"></canvas>
        </div>

        {{-- Mode Shape --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Mode Shape
            </h3>
            <canvas id="modeChart" height="200"></canvas>
        </div>

    </div>

    {{-- =========================================================
         FREQUENCY TABLE + AMPLITUDE TABLE
    ========================================================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">

        {{-- Fundamental Frequency of Field Bridge --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 pt-4 pb-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Fundamental Frequency of Field Bridge (Hz)</h3>
            </div>
            <div class="overflow-auto" style="max-height: 340px;">
                <table class="w-full text-xs">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-blue-600 text-white">
                            @foreach($sensors as $i => $s)
                            <th class="px-3 py-2 font-semibold text-center">{{ $i + 1 }}</th>
                            @endforeach
                            <th class="px-3 py-2 font-semibold text-center">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($freqTableData as $ri => $row)
                        <tr class="{{ $ri % 2 === 0 ? 'bg-white' : 'bg-blue-50/30' }}">
                            @foreach($row['values'] as $v)
                            <td class="px-2 py-1.5 text-center font-mono text-gray-700">
                                {{ is_numeric($v) ? number_format($v, 4) : $v }}
                            </td>
                            @endforeach
                            <td class="px-2 py-1.5 text-center text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row['time'])->format('M j, Y H:i:s') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Peak Amplitude of Field Bridge --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 pt-4 pb-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Peak Amplitude of Field Bridge (in/sec)</h3>
            </div>
            <div class="overflow-auto" style="max-height: 340px;">
                <table class="w-full text-xs">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-blue-600 text-white">
                            @foreach($sensors as $i => $s)
                            <th class="px-3 py-2 font-semibold text-center">{{ $i + 1 }}</th>
                            @endforeach
                            <th class="px-3 py-2 font-semibold text-center">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($ampTableData as $ri => $row)
                        <tr class="{{ $ri % 2 === 0 ? 'bg-white' : 'bg-blue-50/30' }}">
                            @foreach($row['values'] as $v)
                            <td class="px-2 py-1.5 text-center font-mono text-gray-700">
                                {{ is_numeric($v) ? number_format($v, 4) : $v }}
                            </td>
                            @endforeach
                            <td class="px-2 py-1.5 text-center text-gray-400 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($row['time'])->format('M j, Y H:i:s') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- =========================================================
         REPRESENTATIVE ACCELERATION â€” TIME & FREQUENCY DOMAIN
         One row of two charts per sensor (Time Domain | Frequency Domain)
    ========================================================= --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
            <h3 class="text-base font-bold text-gray-800">
                Representative Acceleration of Field Bridge
            </h3>
            <span class="text-xs text-gray-400 font-normal">Time &amp; Frequency Domain â€” per Sensor</span>
        </div>

        <div id="fft-sensor-grid" class="space-y-5">
            @foreach($perSensorTimeDomain as $s)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Sensor label header --}}
                <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 bg-gray-50/60">
                    <span class="font-mono text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full">
                        {{ $s['code'] }}
                    </span>
                    <span class="text-sm font-semibold text-gray-700">{{ $s['label'] }}</span>
                    <span class="ml-auto text-xs text-gray-400">{{ count($s['data']) }} data points</span>
                </div>

                {{-- Two charts side by side --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 divide-x divide-gray-100">

                    {{-- Time Domain --}}
                    <div class="p-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Time Domain
                        </p>
                        <canvas id="td-{{ $s['code'] }}" height="180"></canvas>
                        <p class="text-[10px] text-gray-400 text-center mt-2">Time, msec</p>
                    </div>

                    {{-- Frequency Domain --}}
                    <div class="p-5">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            Frequency Domain <span class="font-normal normal-case text-gray-400">(FFT)</span>
                        </p>
                        <canvas id="fd-{{ $s['code'] }}" height="180"></canvas>
                        <p class="text-[10px] text-gray-400 text-center mt-2">Frequency, Hz</p>
                    </div>
                </div>
            </div>
            @endforeach

            @if($perSensorTimeDomain->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 py-14 text-center text-gray-400">
                <svg class="w-10 h-10 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-sm font-medium">No sensor data available yet.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- =========================================================
         SENSOR HISTORY (streaming-style)
    ========================================================= --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                Sensor Histories
            </h3>
            <div class="relative">
                <input id="history-search"
                       type="text"
                       placeholder="Search sensor..."
                       class="pl-8 pr-3 py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 w-40 transition">
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                </svg>
            </div>
        </div>
        <div class="overflow-auto" style="max-height: 420px;">
            <table class="w-full text-sm">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-blue-600 text-white text-xs uppercase tracking-wider">
                        <th class="px-5 py-3 font-semibold text-left">No</th>
                        <th class="px-5 py-3 font-semibold text-left">Sensor</th>
                        <th class="px-5 py-3 font-semibold text-center">Frequency (Hz)</th>
                        <th class="px-5 py-3 font-semibold text-center">Amplitude (in/sec)</th>
                        <th class="px-5 py-3 font-semibold text-center">Recorded At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="history-tbody">
                    @foreach($sensorHistory as $i => $data)
                    @php
                        $sIdx = $sensors->search(fn($s) => $s->id === $data->accelerometer_wireless_sensor_id);
                        $sCode = $sIdx !== false ? 'sensor' . ($sIdx + 1) : ($data->sensor->name ?? 'â€”');
                    @endphp
                    <tr class="{{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50/40 transition-colors history-row"
                        data-sensor="{{ strtolower($sCode) }}">
                        <td class="px-5 py-2.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-5 py-2.5">
                            <span class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">
                                {{ $sCode }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 text-center font-mono text-gray-700">{{ number_format($data->frequency_hz, 5) }}</td>
                        <td class="px-5 py-2.5 text-center font-mono text-gray-700">{{ number_format($data->amplitude, 4) }}</td>
                        <td class="px-5 py-2.5 text-center text-xs text-gray-500">
                            {{ $data->recorded_at->format('d/m/Y, H:i:s') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-400">
            {{ $sensorHistory->count() }} total records
        </div>
    </div>


    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
    // â”€â”€â”€ Frequency Chart â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const freqRaw = @json($frequencyChartData);
    const freqCtx = document.getElementById('freqChart').getContext('2d');

    new Chart(freqCtx, {
        type: 'line',
        data: {
            labels: freqRaw.map(d => d.time),
            datasets: [{
                label: 'Frequency',
                data: freqRaw.map(d => d.frequency),
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.12)',
                borderWidth: 1.5,
                pointRadius: 0,
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom', labels: { font: { size: 10 } } },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    ticks: { maxTicksLimit: 6, font: { size: 9 }, maxRotation: 0 },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                y: {
                    title: { display: true, text: 'Frequency, Hz', font: { size: 10 } },
                    ticks: { font: { size: 9 } },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                }
            }
        }
    });

    // â”€â”€â”€ Mode Shape Chart â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const modeRaw  = @json($modeShapeData);
    const modeCtx  = document.getElementById('modeChart').getContext('2d');

    new Chart(modeCtx, {
        type: 'line',
        data: {
            labels: modeRaw.map(d => d.x),
            datasets: [{
                label: 'Amplitude, in',
                data: modeRaw.map(d => d.y),
                borderColor: '#64748b',
                backgroundColor: 'rgba(100,116,139,0.08)',
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: '#64748b',
                tension: 0.45,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'bottom', labels: { font: { size: 10 } } },
            },
            scales: {
                x: {
                    title: { display: true, text: 'Sensor Index', font: { size: 10 } },
                    ticks: { font: { size: 9 } },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                y: {
                    title: { display: true, text: 'Amplitude, in', font: { size: 10 } },
                    ticks: { font: { size: 9 } },
                    grid: { color: 'rgba(0,0,0,0.04)' }
                }
            }
        }
    });

    // â”€â”€â”€ Sensor History Search â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    document.getElementById('history-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.history-row').forEach(row => {
            row.style.display = row.dataset.sensor.includes(q) ? '' : 'none';
        });
    });

    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
    //  REPRESENTATIVE ACCELERATION â€” TIME & FREQUENCY DOMAIN CHARTS
    //  Using Fast Fourier Transform (Cooley-Tukey, 1965)
    // â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•

    // â”€â”€ Cooley-Tukey In-Place FFT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Input: Float64Array of signal values (length must be power of 2)
    // Returns: { re[], im[] } arrays of FFT coefficients
    function fft(signal) {
        const N = signal.length;
        const re = new Float64Array(N);
        const im = new Float64Array(N);

        // Bit-reversal permutation
        for (let i = 0; i < N; i++) {
            re[i] = signal[i];
            im[i] = 0;
        }
        let j = 0;
        for (let i = 1; i < N; i++) {
            let bit = N >> 1;
            for (; j & bit; bit >>= 1) j ^= bit;
            j ^= bit;
            if (i < j) {
                [re[i], re[j]] = [re[j], re[i]];
                [im[i], im[j]] = [im[j], im[i]];
            }
        }

        // Butterfly computations
        for (let len = 2; len <= N; len <<= 1) {
            const ang = -2 * Math.PI / len;
            const wRe = Math.cos(ang), wIm = Math.sin(ang);
            for (let i = 0; i < N; i += len) {
                let curRe = 1, curIm = 0;
                for (let k = 0; k < len / 2; k++) {
                    const uRe = re[i + k], uIm = im[i + k];
                    const vRe = re[i + k + len/2] * curRe - im[i + k + len/2] * curIm;
                    const vIm = re[i + k + len/2] * curIm + im[i + k + len/2] * curRe;
                    re[i + k]         = uRe + vRe;
                    im[i + k]         = uIm + vIm;
                    re[i + k + len/2] = uRe - vRe;
                    im[i + k + len/2] = uIm - vIm;
                    const newCurRe = curRe * wRe - curIm * wIm;
                    curIm = curRe * wIm + curIm * wRe;
                    curRe = newCurRe;
                }
            }
        }
        return { re, im };
    }

    // â”€â”€ Next power-of-2 helper â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function nextPow2(n) {
        let p = 1;
        while (p < n) p <<= 1;
        return p;
    }

    // â”€â”€ Shared academic chart style â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    Chart.defaults.font.family = "'Inter', 'Arial', sans-serif";

    // â”€â”€ Build charts for each sensor â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    const sensorData = @json($perSensorTimeDomain);

    sensorData.forEach(sensor => {
        const pts  = sensor.data;
        if (!pts || pts.length === 0) return;

        const code   = sensor.code;
        const sLabel = sensor.label;
        const sFs    = sensor.fs ?? 227.6;

        // â”€â”€ 1) TIME DOMAIN â€” dense smooth line chart (seismograph style) â”€â”€â”€â”€â”€â”€â”€
        const tdCanvas = document.getElementById('td-' + code);
        if (tdCanvas) {
            const tdCtx = tdCanvas.getContext('2d');

            // Gradient fill under the line
            const grad = tdCtx.createLinearGradient(0, 0, 0, tdCanvas.height * 0.9 || 200);
            grad.addColorStop(0,   'rgba(30,64,175,0.15)');
            grad.addColorStop(0.5, 'rgba(30,64,175,0.04)');
            grad.addColorStop(1,   'rgba(30,64,175,0)');

            new Chart(tdCtx, {
                type: 'line',
                data: {
                    // Use typed arrays for best performance with 2048 pts
                    datasets: [{
                        label: 'Acceleration, in/secÂ²',
                        data: pts.map(p => ({ x: p.t_ms, y: p.acc })),
                        borderColor: '#1e3a8a',
                        backgroundColor: grad,
                        borderWidth: 0.75,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        pointHoverBackgroundColor: '#1e3a8a',
                        tension: 0,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    animation: false,
                    parsing: false,         // data already in {x,y} â€” skip extra parsing
                    normalized: true,       // skip bounds-check for speed
                    // Decimation: show ~800 representative points visually,
                    // full data kept for hover/tooltip
                    plugins: {
                        decimation: {
                            enabled: true,
                            algorithm: 'lttb',  // Largest Triangle Three Buckets
                            samples: 800,
                            threshold: 800,
                        },
                        legend: { display: false },
                        title: {
                            display: true,
                            text: sLabel + '  â€”  Time Domain',
                            font: { size: 12, weight: '600' },
                            color: '#1f2937',
                            padding: { bottom: 6 }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            displayColors: false,
                            backgroundColor: 'rgba(15,23,42,0.88)',
                            titleColor: '#94a3b8',
                            bodyColor: '#f1f5f9',
                            borderColor: 'rgba(148,163,184,0.2)',
                            borderWidth: 1,
                            padding: { x: 10, y: 6 },
                            callbacks: {
                                title: items => 'T = ' + parseFloat(items[0].parsed.x).toFixed(1) + ' ms',
                                label: item  => 'a = ' + parseFloat(item.parsed.y).toFixed(3) + ' in/secÂ²'
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false,
                        axis: 'x'
                    },
                    scales: {
                        x: {
                            type: 'linear',
                            min: 0,
                            max: pts[pts.length - 1]?.t_ms ?? 9000,
                            title: {
                                display: true,
                                text: 'Time, msec',
                                font: { size: 10 },
                                color: '#374151'
                            },
                            ticks: {
                                font: { size: 9 },
                                maxTicksLimit: 10,
                                color: '#4b5563',
                                callback: v => Math.round(v).toLocaleString()
                            },
                            grid: { color: 'rgba(0,0,0,0.06)' },
                            border: { color: '#374151' }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Acceleration, in/secÂ²',
                                font: { size: 10 },
                                color: '#374151'
                            },
                            ticks: { font: { size: 9 }, color: '#4b5563' },
                            grid: { color: 'rgba(0,0,0,0.06)' },
                            border: { color: '#374151' }
                        }
                    }
                }
            });
        }

        // â”€â”€ 2) FREQUENCY DOMAIN via FFT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        const fdCanvas = document.getElementById('fd-' + code);
        if (!fdCanvas) return;

        // Pad signal to next power-of-2
        const rawAcc = pts.map(p => p.acc);
        const N      = nextPow2(rawAcc.length);
        const signal = new Float64Array(N);
        rawAcc.forEach((v, i) => { signal[i] = v; });

        // Sampling rate from controller
        let fs = sFs;
        if (pts.length >= 2) {
            const dtMs = (pts[pts.length - 1].t_ms - pts[0].t_ms) / (pts.length - 1);
            if (dtMs > 0) fs = 1000 / dtMs;
        }

        // Run FFT
        const { re, im } = fft(signal);

        // Single-sided amplitude spectrum 0 â€¦ 50 Hz
        const half      = Math.floor(N / 2);
        const freqArr   = [];
        const ampArr    = [];
        const maxDispHz = 50;

        for (let k = 0; k < half; k++) {
            const freqHz = (k * fs) / N;
            if (freqHz > maxDispHz) break;
            const mag = (2 / N) * Math.sqrt(re[k] * re[k] + im[k] * im[k]);
            freqArr.push(freqHz);
            ampArr.push(mag);
        }

        new Chart(fdCanvas.getContext('2d'), {
            type: 'line',
            data: {
                datasets: [{
                    label: 'Amplitude, in/secÂ²',
                    data: freqArr.map((f, i) => ({ x: f, y: ampArr[i] })),
                    borderColor: '#111827',
                    backgroundColor: 'rgba(17,24,39,0.04)',
                    borderWidth: 0.8,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: '#1e3a8a',
                    tension: 0,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                animation: false,
                parsing: false,
                normalized: true,
                plugins: {
                    decimation: {
                        enabled: true,
                        algorithm: 'lttb',
                        samples: 600,
                        threshold: 600,
                    },
                    legend: { display: false },
                    title: {
                        display: true,
                        text: sLabel + '  â€”  Frequency Domain (FFT)',
                        font: { size: 12, weight: '600' },
                        color: '#1f2937',
                        padding: { bottom: 6 }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        displayColors: false,
                        backgroundColor: 'rgba(15,23,42,0.88)',
                        titleColor: '#94a3b8',
                        bodyColor: '#f1f5f9',
                        borderColor: 'rgba(148,163,184,0.2)',
                        borderWidth: 1,
                        padding: { x: 10, y: 6 },
                        callbacks: {
                            title: items => 'f = ' + parseFloat(items[0].parsed.x).toFixed(2) + ' Hz',
                            label: item  => 'Amp = ' + parseFloat(item.parsed.y).toFixed(5) + ' in/secÂ²'
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                    axis: 'x'
                },
                scales: {
                    x: {
                        type: 'linear',
                        min: 0,
                        max: maxDispHz,
                        title: {
                            display: true,
                            text: 'Frequency, Hz',
                            font: { size: 10 },
                            color: '#374151'
                        },
                        ticks: {
                            font: { size: 9 },
                            color: '#4b5563',
                            stepSize: 5,
                            maxTicksLimit: 11,
                            callback: v => Number.isInteger(Math.round(v / 5) * 5) && Math.abs(v - Math.round(v / 5) * 5) < 0.5 ? Math.round(v) : null
                        },
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        border: { color: '#374151' }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Amplitude, in/secÂ²',
                            font: { size: 10 },
                            color: '#374151'
                        },
                        ticks: { font: { size: 9 }, color: '#4b5563' },
                        grid: { color: 'rgba(0,0,0,0.06)' },
                        border: { color: '#374151' },
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>

</x-app-layout>
