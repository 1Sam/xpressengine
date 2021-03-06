<?php

namespace App\Http\Middleware;

use Auth;
use Route;
use XeLang;
use Closure;
use Xpressengine\Member\Rating;

class LangPreprocessor
{
    private $mapSeqName = [];
    private $mapSeqKey = [];
    private $mapSeqMultiLine = [];

    public function handle($request, Closure $next)
    {
        app('events')->listen('locale.changed', function($locale) {
            app('xe.translator')->setLocale($locale);
        });
        $locale = session()->get('locale') ?: app('xe.translator')->getLocale();
        app()->setLocale($locale);

        Route::matched(function($route, $request) use($locale) {
            $key = self::class.'://'.$request->method().'/'.$route->getPath().'/'.$locale;
            app('xe.translator')->setCurrentCacheKey($key);
        });

        if ($request->has('xe_use_request_preprocessor') && $this->available()) {
            $this->prepare($request);
        }

        $response = $next($request);

        if ($request->has('xe_use_request_preprocessor') && $this->available()) {
            $this->conduct($request);
        }

        return $response;
    }

    private function available()
    {
        return in_array(Auth::user()->getRating(), [Rating::SUPER, Rating::MANAGER]);
    }

    private function prepare($request)
    {
        $fields = $request->all();
        foreach ($fields as $key => $value) {
            if ($params = $this->parse($key)) {
                list($kSeq, $seq, $command) = $params;
                switch ( $command ) {
                    case 'name':
                        $this->mapSeqName[$seq] = $value;
                        break;

                    case 'key':
                        $this->mapSeqKey[$seq] = $value ?: XeLang::genUserKey();
                        break;

                    case 'multiline':
                        $this->mapSeqMultiLine[$seq] = $value;
                        break;

                    case 'locale':
                        $name = $this->mapSeqName[$seq];
                        $key = $this->mapSeqKey[$seq];
                        $request->merge([$name => $key]);
                        break;
                }
            }
        }
    }

    private function conduct($request)
    {
        $fields = $request->all();
        foreach ($fields as $key => $value) {
            if ($params = $this->parse($key)) {
                list($kSeq, $seq, $command) = $params;
                if ($command == 'locale') {
                    list($kSeq, $seq, $kLocale, $locale) = $params;
                    $key = $this->mapSeqKey[$seq];
                    $multiLine = isset($mapSeqMultiLine[$seq]);
                    XeLang::save($key, $locale, $value, $multiLine);
                }
            }
        }
    }

    private function parse($key)
    {
        $protocol = 'xe_lang_preprocessor://';
        $lenproto = strlen($protocol);

        if ( starts_with($key, $protocol) ) {
            $params = explode("/", substr($key, $lenproto));
            array_shift($params);
            return !empty($params) ? $params : null;
        }

        return null;
    }
}
