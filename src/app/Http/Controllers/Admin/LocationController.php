<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        $cities = Location::whereNull('parent_id')->with('children')->orderBy('name')->get();

        return view('admin.locations.index', compact('cities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['kota', 'kabupaten', 'kecamatan'])],
            'parent_id' => ['nullable', 'integer', 'exists:locations,id'],
        ]);

        $exists = Location::where('name', $data['name'])->where('parent_id', $data['parent_id'] ?? null)->exists();

        if ($exists) {
            return back()->with('error', 'Lokasi tersebut sudah ada.');
        }

        Location::create($data);

        return back()->with('success', 'Lokasi ditambahkan.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->children()->exists()) {
            return back()->with('error', 'Hapus kecamatan di dalamnya terlebih dahulu.');
        }

        $location->delete();

        return back()->with('success', 'Lokasi dihapus.');
    }
}
