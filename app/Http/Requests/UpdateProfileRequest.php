<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'age_range' => ['nullable', 'in:18-25,26-55,56-69,70+'],
            'preferred_locale' => ['nullable', 'in:ar,fr'],
            'last_donation_date' => ['nullable', 'date'],
            'profile_locked' => ['nullable', 'boolean'],
        ];
    }
}
