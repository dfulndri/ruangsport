<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;

class ClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // hak akses dicek di controller lewat Gate/Policy
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama klub',
            'sport_id' => 'olahraga',
            'location_id' => 'lokasi',
            'address' => 'alamat',
            'description' => 'deskripsi',
        ];
    }
}
