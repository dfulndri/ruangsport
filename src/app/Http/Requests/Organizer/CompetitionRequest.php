<?php

namespace App\Http\Requests\Organizer;

use App\Enums\CompetitionStatus;
use App\Enums\ParticipationType;
use App\Models\Sport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id', function ($attribute, $value, $fail) {
                $user = $this->user();
                if ($value && ! $user->isAdmin() && ! $user->canManageClub((int) $value)) {
                    $fail('Anda bukan pengelola klub tersebut.');
                }
            }],
            'venue_id' => ['nullable', 'integer', 'exists:venues,id'],
            'participant_type' => ['bail', 'required', Rule::in(['individual', 'team']), function ($attribute, $value, $fail) {
                $sport = Sport::find($this->input('sport_id'));
                if ($sport && ! $sport->allowsParticipantType(ParticipationType::from($value))) {
                    $fail('Olahraga ini tidak mendukung jenis peserta tersebut.');
                }
            }],
            'max_participants' => ['nullable', 'integer', 'min:2', 'max:128'],
            'registration_open_at' => ['nullable', 'date'],
            'registration_close_at' => ['nullable', 'date', 'after_or_equal:registration_open_at'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'rules' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::enum(CompetitionStatus::class)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama kompetisi',
            'sport_id' => 'olahraga',
            'participant_type' => 'jenis peserta',
            'max_participants' => 'maksimal peserta',
            'registration_open_at' => 'pembukaan pendaftaran',
            'registration_close_at' => 'penutupan pendaftaran',
            'starts_at' => 'tanggal mulai',
            'ends_at' => 'tanggal selesai',
        ];
    }
}
