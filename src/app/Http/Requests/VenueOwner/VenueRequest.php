<?php

namespace App\Http\Requests\VenueOwner;

use Illuminate\Foundation\Http\FormRequest;

class VenueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:3000'],
            'sports' => ['nullable', 'array'],
            'sports.*' => ['integer', 'exists:sports,id'],
            'facilities' => ['nullable', 'string', 'max:2000'],      // satu fasilitas per baris
            'opening_hours' => ['nullable', 'string', 'max:1000'],   // satu baris per hari: "Senin: 08.00-22.00"
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama venue',
            'location_id' => 'lokasi',
            'address' => 'alamat',
            'sports' => 'olahraga',
            'facilities' => 'fasilitas',
            'opening_hours' => 'jam operasional',
        ];
    }
}
