<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ParticipationType;
use App\Http\Controllers\Controller;
use App\Models\Sport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SportController extends Controller
{
    public function index()
    {
        $sports = Sport::orderBy('name')->get();

        return view('admin.sports.index', compact('sports'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:sports,name'],
            'participation_type' => ['required', Rule::enum(ParticipationType::class)],
        ]);

        Sport::create($data + ['slug' => Str::slug($data['name'])]);

        return back()->with('success', 'Olahraga ditambahkan.');
    }

    public function destroy(Sport $sport): RedirectResponse
    {
        try {
            $sport->delete();
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Olahraga masih dipakai oleh klub, aktivitas, atau kompetisi dan tidak dapat dihapus.');
        }

        return back()->with('success', 'Olahraga dihapus.');
    }
}
