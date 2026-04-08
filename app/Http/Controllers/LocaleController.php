<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['ar', 'fr'], true), 404);

        $request->session()->put('locale', $locale);
        $request->user()?->update(['preferred_locale' => $locale]);

        return back();
    }
}
