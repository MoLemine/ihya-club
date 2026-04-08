<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'city' => ['nullable', 'string', 'max:255'],
            'age_range' => ['nullable', 'in:18-25,26-55,56-69,70+'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
