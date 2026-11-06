<?php

namespace App\Http\Controllers;

use App\Models\Bridge;
use App\Models\City;
use Illuminate\Http\Request;

class BridgeController extends Controller
{
    public function index(Request $request)
    {
        $query = Bridge::with('city')->orderByDesc('created_at');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('city', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $bridges = $query->paginate(10)->withQueryString();

        return view('manage.bridges.index', compact('bridges'));
    }

    public function create()
    {
        $cities = City::orderBy('name')->get();
        return view('manage.bridges.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                              => 'required|string|max:255',
            'city_id'                           => 'nullable|exists:cities,id',
            'latitude'                          => 'required|numeric',
            'longitude'                         => 'required|numeric',
            'rating'                            => 'nullable|integer|min:0|max:10',
            'photo'                             => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'maintenance_status'                => 'nullable|string|max:255',
            'support_type'                      => 'nullable|string|max:255',
            'box_beam_length'                   => 'nullable|numeric',
            'box_beam_inside_width'             => 'nullable|numeric',
            'box_beam_outside_width'            => 'nullable|numeric',
            'box_beam_inside_height'            => 'nullable|numeric',
            'box_beam_outside_height'           => 'nullable|numeric',
            'number_of_box_beams'               => 'nullable|integer',
            'number_of_diaphragms_per_box_beam' => 'nullable|integer',
            'thickness_at_diaphragms'           => 'nullable|numeric',
            'thickness_of_box_beam_end'         => 'nullable|numeric',
            'thickness_of_wearing_surface'      => 'nullable|numeric',
            'compressive_strength_28_days'      => 'nullable|numeric',
            'unit_weight_of_concrete'           => 'nullable|numeric',
        ]);

        if (empty($validated['city_id'])) {
            $validated['city_id'] = City::first()->id ?? 1;
        }

        if (empty($validated['rating'])) {
            $validated['rating'] = 8;
        }

        if (empty($validated['maintenance_status'])) {
            $validated['maintenance_status'] = 'Good Condition';
        }

        // Handle photo upload → convert to WebP
        if ($request->hasFile('photo')) {
            $validated['photo_url'] = $this->storeAsWebP($request->file('photo'));
        }

        unset($validated['photo']);
        Bridge::create($validated);

        return redirect()->route('manage.bridges.index')
                         ->with('success', "Bridge \"{$validated['name']}\" has been created successfully.");
    }

    public function edit(Bridge $bridge)
    {
        $cities = City::orderBy('name')->get();
        return view('manage.bridges.edit', compact('bridge', 'cities'));
    }

    public function update(Request $request, Bridge $bridge)
    {
        $validated = $request->validate([
            'name'                              => 'required|string|max:255',
            'city_id'                           => 'nullable|exists:cities,id',
            'latitude'                          => 'required|numeric',
            'longitude'                         => 'required|numeric',
            'rating'                            => 'nullable|integer|min:0|max:10',
            'photo'                             => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'remove_photo'                      => 'nullable|boolean',
            'maintenance_status'                => 'nullable|string|max:255',
            'support_type'                      => 'nullable|string|max:255',
            'box_beam_length'                   => 'nullable|numeric',
            'box_beam_inside_width'             => 'nullable|numeric',
            'box_beam_outside_width'            => 'nullable|numeric',
            'box_beam_inside_height'            => 'nullable|numeric',
            'box_beam_outside_height'           => 'nullable|numeric',
            'number_of_box_beams'               => 'nullable|integer',
            'number_of_diaphragms_per_box_beam' => 'nullable|integer',
            'thickness_at_diaphragms'           => 'nullable|numeric',
            'thickness_of_box_beam_end'         => 'nullable|numeric',
            'thickness_of_wearing_surface'      => 'nullable|numeric',
            'compressive_strength_28_days'      => 'nullable|numeric',
            'unit_weight_of_concrete'           => 'nullable|numeric',
        ]);

        if (empty($validated['city_id'])) {
            $validated['city_id'] = $bridge->city_id ?? City::first()->id ?? 1;
        }

        // Remove old photo if requested
        if ($request->boolean('remove_photo') && $bridge->photo_url) {
            $this->deletePhoto($bridge->photo_url);
            $validated['photo_url'] = null;
        }

        // Handle new photo upload → convert to WebP
        if ($request->hasFile('photo')) {
            if ($bridge->photo_url) {
                $this->deletePhoto($bridge->photo_url);
            }
            $validated['photo_url'] = $this->storeAsWebP($request->file('photo'));
        }

        unset($validated['photo'], $validated['remove_photo']);
        $bridge->update($validated);

        return redirect()->route('manage.bridges.index')
                         ->with('success', "Bridge \"{$bridge->name}\" has been updated successfully.");
    }

    public function destroy(Bridge $bridge)
    {
        $name = $bridge->name;

        // Delete photo from storage when bridge is deleted
        if ($bridge->photo_url) {
            $this->deletePhoto($bridge->photo_url);
        }

        $bridge->delete();

        return redirect()->route('manage.bridges.index')
                         ->with('success', "Bridge \"{$name}\" has been deleted.");
    }

    /**
     * Convert an uploaded image to WebP and store it in storage/app/public/bridges/.
     * Returns the relative storage path (e.g. "bridges/bridge_abc123.webp").
     */
    private function storeAsWebP(\Illuminate\Http\UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        // Create GD resource from the uploaded file
        $source = match (true) {
            str_contains($mime, 'png')          => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'jpeg'),
            str_contains($mime, 'jpg')          => imagecreatefromjpeg($file->getRealPath()),
            default                             => imagecreatefromjpeg($file->getRealPath()),
        };

        // Preserve transparency for PNG sources
        if (str_contains($mime, 'png')) {
            imagepalettetotruecolor($source);
            imagealphablending($source, true);
            imagesavealpha($source, true);
        }

        $filename    = 'bridges/' . uniqid('bridge_', true) . '.webp';
        $storagePath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        if (!is_dir(dirname($storagePath))) {
            mkdir(dirname($storagePath), 0755, true);
        }

        // Save as WebP with quality 82
        imagewebp($source, $storagePath, 82);
        imagedestroy($source);

        return $filename;
    }

    /**
     * Delete a stored photo file from disk.
     */
    private function deletePhoto(string $path): void
    {
        $fullPath = storage_path('app/public/' . $path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
