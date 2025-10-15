<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    $availLocale = config("translatable.locales");
    // Locale is enabled and allowed to be change
    if (session()->has('locale') && in_array(session()->get('locale'), ['en', 'ar'])) {
      app()->setLocale(session()->get('locale'));
    }
    if ($request->header('accept-language') && in_array($request->header('accept-language'), $availLocale)) {
      // Set the Laravel locale 
      app()->setLocale($request->header('accept-language'));
    }

    if (session()->get('locale') == 'ar') {
      session()->put('direction', 'rtl');
    } else {
      session()->put('direction', 'ltr');
    }
    Config::set('custom.custom.myRTLMode', app()->getLocale() == "ar");
    Config::set('custom.custom.textDirection', app()->getLocale() == "ar");

    return $next($request);
  }
}
