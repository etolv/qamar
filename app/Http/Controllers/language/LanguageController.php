<?php

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
  public function swap($locale)
  {
    if (in_array($locale, ['ar', 'en', 'fr', 'de'])) {
      if ($locale == 'ar') {
        config(['MIX_CONTENT_DIRECTION' => 'rtl']);
        session()->put('direction', 'rtl');
        session()->put('contentLayout', 'content-right-sidebar');
        session()->put('locale', $locale);
      } else {
        config(['MIX_CONTENT_DIRECTION' => 'ltr']);
        session()->put('direction', 'ltr');
        session()->put('contentLayout', 'content-left-sidebar');
        session()->put('locale', $locale);
      }
      App::setLocale($locale);
    }

    return redirect()->back();

    // if (!in_array($locale, ['en', 'fr', 'ar', 'de'])) {
    //   abort(400);
    // } else {
    //   session()->put('locale', $locale);
    // }

    // App::setLocale($locale);
    // return redirect()->back();
  }
}
