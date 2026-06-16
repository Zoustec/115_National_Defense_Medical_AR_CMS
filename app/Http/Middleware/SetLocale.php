<?php

namespace App\Http\Middleware;

use App\Constants\Locale;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = Session::get('locale', $request->header('Accept-Language', Locale::DEFAULT));

        if (! in_array($locale, Locale::ALLOWED, true)) {
            $locale = Locale::DEFAULT;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
