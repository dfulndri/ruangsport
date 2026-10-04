<?php

namespace App\Http\Requests\Organizer;

use App\Enums\ActivityStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id', function ($attribute, $value, $fail) {
                $user = $this->user();
                if ($value && ! $user->isAdmin() && ! $user->canManageClub((int) $value)) {
                    $fail('Anda bukan pengelola klub tersebut.');
                }
            }],
            'venue_id' => ['nullable', 'integer', 'exists:venues,id'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'quota' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'fee' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::enum(ActivityStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'sport_id' => 'olahraga',
            'club_id' => 'klub',
            'starts_at' => 'waktu mulai',
            'ends_at' => 'waktu selesai',
            'quota' => 'kuota',
            'fee' => 'biaya',
        ];
    }
}
