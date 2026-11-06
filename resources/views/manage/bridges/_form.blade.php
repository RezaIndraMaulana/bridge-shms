{{-- Shared Form for Create and Edit Bridge --}}
@php
    $b = $bridge ?? null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
    {{-- Left Column --}}
    <div class="space-y-4">
        {{-- Name --}}
        <div>
            <label for="name" class="block font-medium text-gray-700 mb-1">Name</label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name', $b?->name) }}"
                   placeholder="Enter bridge name"
                   required
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('name')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Box Beam Length --}}
        <div>
            <label for="box_beam_length" class="block font-medium text-gray-700 mb-1">Box Beam Length</label>
            <input type="number"
                   step="any"
                   name="box_beam_length"
                   id="box_beam_length"
                   value="{{ old('box_beam_length', $b?->box_beam_length) }}"
                   placeholder="Enter Box Beam Length"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('box_beam_length')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Box beam outside width --}}
        <div>
            <label for="box_beam_outside_width" class="block font-medium text-gray-700 mb-1">Box beam outside width</label>
            <input type="number"
                   step="any"
                   name="box_beam_outside_width"
                   id="box_beam_outside_width"
                   value="{{ old('box_beam_outside_width', $b?->box_beam_outside_width) }}"
                   placeholder="Enter Box beam outside width"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('box_beam_outside_width')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Box beam inside height (in) --}}
        <div>
            <label for="box_beam_inside_height" class="block font-medium text-gray-700 mb-1">Box beam inside height (in)</label>
            <input type="number"
                   step="any"
                   name="box_beam_inside_height"
                   id="box_beam_inside_height"
                   value="{{ old('box_beam_inside_height', $b?->box_beam_inside_height) }}"
                   placeholder="Enter Box beam inside height (in)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('box_beam_inside_height')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Number of diaphragms per box beam --}}
        <div>
            <label for="number_of_diaphragms_per_box_beam" class="block font-medium text-gray-700 mb-1">Number of diaphragms per box beam</label>
            <input type="number"
                   name="number_of_diaphragms_per_box_beam"
                   id="number_of_diaphragms_per_box_beam"
                   value="{{ old('number_of_diaphragms_per_box_beam', $b?->number_of_diaphragms_per_box_beam) }}"
                   placeholder="Enter Number of diaphragms per box beam"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('number_of_diaphragms_per_box_beam')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Thickness of box beam end (in) --}}
        <div>
            <label for="thickness_of_box_beam_end" class="block font-medium text-gray-700 mb-1">Thickness of box beam end (in)</label>
            <input type="number"
                   step="any"
                   name="thickness_of_box_beam_end"
                   id="thickness_of_box_beam_end"
                   value="{{ old('thickness_of_box_beam_end', $b?->thickness_of_box_beam_end) }}"
                   placeholder="Enter Thickness of box beam end (in)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('thickness_of_box_beam_end')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- 28-day compressive strength of concrete (psi) --}}
        <div>
            <label for="compressive_strength_28_days" class="block font-medium text-gray-700 mb-1">28-day compressive strength of concrete (psi)</label>
            <input type="number"
                   step="any"
                   name="compressive_strength_28_days"
                   id="compressive_strength_28_days"
                   value="{{ old('compressive_strength_28_days', $b?->compressive_strength_28_days) }}"
                   placeholder="Enter 28-day compressive strength of conc"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('compressive_strength_28_days')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- City (Foreign Key) --}}
        <div>
            <label for="city_id" class="block font-medium text-gray-700 mb-1">City / Region</label>
            <select name="city_id"
                    id="city_id"
                    required
                    class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm bg-white">
                <option value="">Choose City</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}" {{ old('city_id', $b?->city_id) == $city->id ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
            @error('city_id')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Right Column --}}
    <div class="space-y-4">
        {{-- Bridge end supports --}}
        <div>
            <label for="support_type" class="block font-medium text-gray-700 mb-1">Bridge end supports</label>
            <select name="support_type"
                    id="support_type"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm bg-white">
                <option value="">Choose</option>
                @php
                    $supports = ['Simply Supported', 'Continuous', 'Cantilever', 'Fixed', 'Roller', 'Suspension', 'Cable-stayed', 'Box Girder'];
                    $curSupport = old('support_type', $b?->support_type);
                @endphp
                @foreach($supports as $sup)
                    <option value="{{ $sup }}" {{ $curSupport == $sup ? 'selected' : '' }}>{{ $sup }}</option>
                @endforeach
            </select>
            @error('support_type')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Box Beam inside width --}}
        <div>
            <label for="box_beam_inside_width" class="block font-medium text-gray-700 mb-1">Box Beam inside width</label>
            <input type="number"
                   step="any"
                   name="box_beam_inside_width"
                   id="box_beam_inside_width"
                   value="{{ old('box_beam_inside_width', $b?->box_beam_inside_width) }}"
                   placeholder="Enter Box Beam inside width"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('box_beam_inside_width')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Box beam outside height (in) --}}
        <div>
            <label for="box_beam_outside_height" class="block font-medium text-gray-700 mb-1">Box beam outside height (in)</label>
            <input type="number"
                   step="any"
                   name="box_beam_outside_height"
                   id="box_beam_outside_height"
                   value="{{ old('box_beam_outside_height', $b?->box_beam_outside_height) }}"
                   placeholder="Enter Box beam outside height (in)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('box_beam_outside_height')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Number of box beams --}}
        <div>
            <label for="number_of_box_beams" class="block font-medium text-gray-700 mb-1">Number of box beams</label>
            <input type="number"
                   name="number_of_box_beams"
                   id="number_of_box_beams"
                   value="{{ old('number_of_box_beams', $b?->number_of_box_beams) }}"
                   placeholder="Enter Number of box beams"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('number_of_box_beams')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Thickness of diaphragms (in) --}}
        <div>
            <label for="thickness_at_diaphragms" class="block font-medium text-gray-700 mb-1">Thickness of diaphragms (in)</label>
            <input type="number"
                   step="any"
                   name="thickness_at_diaphragms"
                   id="thickness_at_diaphragms"
                   value="{{ old('thickness_at_diaphragms', $b?->thickness_at_diaphragms) }}"
                   placeholder="Enter Thickness of diaphragms (in)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('thickness_at_diaphragms')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Thickness of wearing surface (in) --}}
        <div>
            <label for="thickness_of_wearing_surface" class="block font-medium text-gray-700 mb-1">Thickness of wearing surface (in)</label>
            <input type="number"
                   step="any"
                   name="thickness_of_wearing_surface"
                   id="thickness_of_wearing_surface"
                   value="{{ old('thickness_of_wearing_surface', $b?->thickness_of_wearing_surface) }}"
                   placeholder="Enter Thickness of wearing surface (in)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('thickness_of_wearing_surface')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Unit weight of concrete (lb/ft) --}}
        <div>
            <label for="unit_weight_of_concrete" class="block font-medium text-gray-700 mb-1">Unit wight of concrete (lb/ft)</label>
            <input type="number"
                   step="any"
                   name="unit_weight_of_concrete"
                   id="unit_weight_of_concrete"
                   value="{{ old('unit_weight_of_concrete', $b?->unit_weight_of_concrete) }}"
                   placeholder="Enter Unit wight of concrete (lb/ft)"
                   class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            @error('unit_weight_of_concrete')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Maintenance Status & Rating in collapsed or compact row --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="rating" class="block font-medium text-gray-700 mb-1">Bridge Rating (1-10)</label>
                <input type="number"
                       min="1"
                       max="10"
                       name="rating"
                       id="rating"
                       value="{{ old('rating', $b?->rating ?? 8) }}"
                       placeholder="e.g. 8"
                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            </div>
            <div>
                <label for="maintenance_status" class="block font-medium text-gray-700 mb-1">Maintenance Status</label>
                <input type="text"
                       name="maintenance_status"
                       id="maintenance_status"
                       value="{{ old('maintenance_status', $b?->maintenance_status ?? 'Good Condition') }}"
                       placeholder="e.g. Good Condition"
                       class="w-full px-3.5 py-2.5 rounded-lg border border-gray-200 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition text-sm">
            </div>
        </div>
    </div>
</div>

{{-- ── Bridge Photo Upload ──────────────────────────────────────────── --}}
<div class="mt-5 pt-5 border-t border-gray-100">
    <label class="block font-medium text-gray-700 mb-2 text-sm">
        Bridge Photo
        <span class="text-gray-400 font-normal ml-1">(JPEG / PNG · max 5 MB · auto-converted to WebP)</span>
    </label>

    @php $existingPhoto = old('photo_url', $b?->photo_url); @endphp

    {{-- Drop Zone --}}
    <div id="photo-drop-zone"
         class="relative flex flex-col items-center justify-center w-full rounded-xl border-2 border-dashed border-gray-300
                bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition-all cursor-pointer group"
         style="min-height: 160px;"
         onclick="document.getElementById('photo-file-input').click()">

        {{-- Upload Prompt --}}
        <div id="photo-upload-prompt" class="{{ $existingPhoto ? 'hidden' : '' }} flex flex-col items-center gap-2 py-8 px-4 text-center pointer-events-none">
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-600">Click or drag & drop to upload photo</p>
            <p class="text-xs text-gray-400">JPEG, PNG — max 5 MB</p>
        </div>

        {{-- Preview (shown when photo is selected/existing) --}}
        <div id="photo-preview-wrap"
             class="{{ $existingPhoto ? '' : 'hidden' }} relative w-full flex items-center justify-center p-3">
            <img id="photo-preview-img"
                 src="{{ $existingPhoto ? asset('storage/' . $existingPhoto) : '' }}"
                 alt="Bridge photo preview"
                 class="max-h-52 rounded-lg object-contain shadow-sm"
                 onerror="this.parentElement.classList.add('hidden'); document.getElementById('photo-upload-prompt').classList.remove('hidden');">

            {{-- Overlay to re-pick --}}
            <div class="absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/30 transition-all rounded-xl opacity-0 hover:opacity-100">
                <span class="text-white text-xs font-semibold bg-black/50 px-3 py-1 rounded-full">Change Photo</span>
            </div>
        </div>

        {{-- Hidden file input --}}
        <input type="file"
               id="photo-file-input"
               name="photo"
               accept="image/jpeg,image/jpg,image/png"
               class="hidden"
               onchange="handlePhotoSelect(event)">
    </div>

    {{-- File Info Bar --}}
    <div id="photo-info-bar" class="{{ $existingPhoto ? '' : 'hidden' }} mt-2 flex items-center justify-between bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
        <div class="flex items-center gap-2 text-xs text-blue-700 font-medium min-w-0">
            <svg class="w-4 h-4 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span id="photo-info-name" class="truncate">
                @if($existingPhoto)
                    {{ basename($existingPhoto) }}
                @endif
            </span>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 ml-2">
            <span class="text-[10px] font-bold bg-blue-600 text-white px-1.5 py-0.5 rounded uppercase tracking-wide">WebP</span>
            <button type="button"
                    id="photo-remove-btn"
                    onclick="removePhoto(event)"
                    class="text-red-500 hover:text-red-700 transition ml-1"
                    title="Remove photo">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Validation error --}}
    @error('photo')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
    @enderror

    {{-- Hidden flag sent when user removes existing photo --}}
    <input type="hidden" name="remove_photo" id="remove-photo-flag" value="0">
</div>

{{-- Drag & Drop + Preview Script --}}
<script>
(function () {
    const dropZone   = document.getElementById('photo-drop-zone');
    const fileInput  = document.getElementById('photo-file-input');
    const prompt     = document.getElementById('photo-upload-prompt');
    const previewWrap= document.getElementById('photo-preview-wrap');
    const previewImg = document.getElementById('photo-preview-img');
    const infoBar    = document.getElementById('photo-info-bar');
    const infoName   = document.getElementById('photo-info-name');
    const removeFlag = document.getElementById('remove-photo-flag');

    // Drag & Drop
    ['dragenter','dragover'].forEach(ev => {
        dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.add('border-blue-500','bg-blue-50'); });
    });
    ['dragleave','drop'].forEach(ev => {
        dropZone.addEventListener(ev, e => { e.preventDefault(); dropZone.classList.remove('border-blue-500','bg-blue-50'); });
    });
    dropZone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length) {
            // Transfer to the real input
            const dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
            showPreview(files[0]);
        }
    });

    window.handlePhotoSelect = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        // Client-side size check (5 MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File is too large. Maximum size is 5 MB.');
            fileInput.value = '';
            return;
        }
        showPreview(file);
    };

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            previewWrap.classList.remove('hidden');
            prompt.classList.add('hidden');
            infoBar.classList.remove('hidden');
            infoName.textContent = file.name;
            removeFlag.value = '0';
        };
        reader.readAsDataURL(file);
    }

    window.removePhoto = function (event) {
        event.stopPropagation();
        fileInput.value   = '';
        previewImg.src    = '';
        previewWrap.classList.add('hidden');
        prompt.classList.remove('hidden');
        infoBar.classList.add('hidden');
        infoName.textContent = '';
        removeFlag.value  = '1';   // tell server to delete existing photo
    };
})();
</script>

{{-- Hidden Coordinates bound to map --}}
<input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude',  $b?->latitude  ?? -6.914744) }}">
<input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $b?->longitude ?? 107.609810) }}">
