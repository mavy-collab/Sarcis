<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    protected array $supported = ['fr', 'en', 'ar'];

    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, $this->supported)) {
            abort(404);
        }

        Session::put('locale', $locale);

        return redirect()->back()->withInput();
    }
}
