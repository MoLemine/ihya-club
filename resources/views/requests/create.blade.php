@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <section class="hero-panel">
            <p class="eyebrow">{{ __('messages.create_request') }}</p>
            <h2 class="mt-2 text-4xl font-semibold tracking-tight text-slate-900">{{ __('messages.create_request_title') }}</h2>
            <p class="mt-4 max-w-3xl text-base leading-8 text-slate-600">{{ __('messages.request_form_help') }}</p>
        </section>

        <section class="surface-panel rounded-[1.75rem] p-6">
            <form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data" class="grid gap-5 md:grid-cols-2">
                @csrf

                @auth
                    <input type="hidden" name="name" value="{{ $currentUser->name }}">
                    <div class="md:col-span-2 rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        {{ __('messages.request_as_user') }} <strong class="text-slate-900">{{ $currentUser->name }}</strong>
                    </div>
                @else
                    <div>
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input name="name" value="{{ old('name') }}" class="field">
                    </div>
                    <div>
                        <label class="form-label">{{ __('messages.phone') }}</label>
                        <input name="phone" value="{{ old('phone') }}" class="field">
                    </div>
                @endif

                <div>
                    <label class="form-label">{{ __('messages.patient_name') }}</label>
                    <input name="patient_name" value="{{ old('patient_name') }}" class="field">
                </div>

                <div>
                    <label class="form-label">{{ __('messages.required_units') }}</label>
                    <select name="required_units" class="field">
                        @foreach (range(1, 8) as $unit)
                            <option value="{{ $unit }}" @selected((int) old('required_units', 1) === $unit)>{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ __('messages.city') }}</label>
                    <select name="city" class="field">
                        <option value="">{{ __('messages.select_city') }}</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}" @selected(old('city') === $city)>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">{{ __('messages.urgency') }}</label>
                    <select name="urgency_level" class="field">
                        <option value="normal" @selected(old('urgency_level') === 'normal')>{{ __('messages.normal') }}</option>
                        <option value="urgent" @selected(old('urgency_level') === 'urgent')>{{ __('messages.urgent') }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ __('messages.hospital_name') }}</label>
                    <select id="hospital-select" name="hospital_name_select" class="field">
                        <option value="">{{ __('messages.select_hospital') }}</option>
                        @foreach ($hospitals as $hospital)
                            <option value="{{ $hospital }}" @selected(old('hospital_name_select') === $hospital)>{{ $hospital }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="hospital-other-wrapper" class="md:col-span-2 {{ old('hospital_name_select') === 'Autre' ? '' : 'hidden' }}">
                    <label class="form-label">{{ __('messages.other_hospital_name') }}</label>
                    <input name="hospital_name_other" value="{{ old('hospital_name_other') }}" class="field">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ __('messages.when_needed') }}</label>
                    <div class="grid gap-3 md:grid-cols-3">
                        <label class="choice-card"><input type="radio" name="needed_on_option" value="today" @checked(old('needed_on_option', 'today') === 'today')> <span>{{ __('messages.today') }}</span></label>
                        <label class="choice-card"><input type="radio" name="needed_on_option" value="tomorrow" @checked(old('needed_on_option') === 'tomorrow')> <span>{{ __('messages.tomorrow') }}</span></label>
                        <label class="choice-card"><input type="radio" name="needed_on_option" value="week" @checked(old('needed_on_option') === 'week')> <span>{{ __('messages.within_week') }}</span></label>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ __('messages.request_image') }}</label>
                    <input type="file" name="image" class="field">
                </div>

                <div class="md:col-span-2">
                    <label class="form-label">{{ __('messages.description') }}</label>
                    <textarea name="description" rows="5" class="field">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2 flex flex-col gap-3">
                    <button class="primary-pill w-full md:w-fit">{{ __('messages.submit_request') }}</button>
                    <p class="text-sm text-slate-500">{{ __('messages.request_visibility_notice') }}</p>
                </div>
            </form>
        </section>
    </div>
@endsection
