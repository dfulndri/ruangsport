<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class TeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'sport_id' => ['required', 'integer', 'exists:sports,id'],
            'club_id' => ['nullable', 'integer', 'exists:clubs,id'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama tim', 'sport_id' => 'olahraga', 'club_id' => 'klub'];
    }
}
