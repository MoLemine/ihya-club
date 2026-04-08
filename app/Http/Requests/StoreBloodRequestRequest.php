<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBloodRequestRequest extends FormRequest
{
    public static function mauritanianCities(): array
    {
        return [
            'Nouakchott',
            'Nouadhibou',
            'Rosso',
            'Kiffa',
            'Kaedi',
            'Atar',
            'Zouerat',
            'Selibaby',
            'Nema',
            'Akjoujt',
            'Tidjikja',
            'Aleg',
        ];
    }

    public static function mainHospitals(): array
    {
        return [
            'Centre Hospitalier National',
            'Hopital Cheikh Zayed',
            'Centre National d Oncologie',
            'Hopital Maternite de Nouakchott',
            'Hopital Regional de Nouadhibou',
            'Hopital Regional de Kiffa',
            'Hopital Regional de Kaedi',
            'Hopital Regional de Rosso',
            'Autre',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [$this->user() ? 'nullable' : 'required', 'string', 'max:255'],
            'phone' => [$this->user() ? 'nullable' : 'required', 'string', 'max:30'],
            'city' => ['required', Rule::in(self::mauritanianCities())],
            'last_donation_date' => ['nullable', 'date'],
            'needed_on_option' => ['nullable', Rule::in(['today', 'tomorrow', 'week'])],
            'patient_name' => ['nullable', 'string', 'max:255'],
            'hospital_name' => ['nullable', 'string', 'max:255'],
            'hospital_name_select' => ['nullable', Rule::in(self::mainHospitals())],
            'hospital_name_other' => ['nullable', 'string', 'max:255', 'required_if:hospital_name_select,Autre'],
            'urgency_level' => ['required', Rule::in(['normal', 'urgent'])],
            'required_units' => ['required', 'integer', 'min:1', 'max:50'],
            'description' => ['required', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                if (! $this->filled('hospital_name') && ! $this->filled('hospital_name_select')) {
                    $validator->errors()->add('hospital_name_select', __('validation.required', ['attribute' => 'hospital']));
                }
            },
        ];
    }
}
