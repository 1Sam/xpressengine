<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => env('APP_TIMEZONE', 'Asia/Seoul'),

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY', 'SomeRandomString'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log settings for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Settings: "single", "daily", "syslog", "errorlog"
    |
    */

    'log' => 'single',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */
    'providers' => [
        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Foundation\Providers\ArtisanServiceProvider::class,
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Routing\ControllerServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        Jenssegers\Agent\AgentServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\MobileServiceProvider::class,
        App\Providers\HttpServiceProvider::class,

        App\Providers\InterceptionServiceProvider::class,
        App\Providers\PluginServiceProvider::class,
        App\Providers\UIObjectServiceProvider::class,
        App\Providers\ThemeServiceProvider::class,
        App\Providers\SkinServiceProvider::class,
        App\Providers\SettingsServiceProvider::class,
        App\Providers\RegisterServiceProvider::class,

        App\Providers\RoutingServiceProvider::class,
        App\Providers\MenuServiceProvider::class,
        App\Providers\ModuleServiceProvider::class,
        App\Providers\ConfigServiceProvider::class,
        App\Providers\DocumentServiceProvider::class,
        App\Providers\DatabaseServiceProvider::class,
        App\Providers\DynamicFieldServiceProvider::class,
        App\Providers\KeygenServiceProvider::class,

        App\Providers\MemberServiceProvider::class,
        App\Providers\StorageServiceProvider::class,
        App\Providers\MediaServiceProvider::class,
        App\Providers\PermissionServiceProvider::class,
        App\Providers\CommentServiceProvider::class,

        App\Providers\PresenterServiceProvider::class,

        App\Providers\CategoryServiceProvider::class,
        App\Providers\CaptchaServiceProvider::class,
        App\Providers\WidgetServiceProvider::class,

        App\Providers\CounterServiceProvider::class,
        App\Providers\TagServiceProvider::class,

        App\Providers\ToggleMenuServiceProvider::class,
        App\Providers\TemporaryServiceProvider::class,

        App\Providers\TrashServiceProvider::class,

        App\Providers\TranslationServiceProvider::class,

        App\Providers\SeoServiceProvider::class,

        App\Providers\SiteServiceProvider::class,

        App\Providers\PurifierServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [
        'App'       => Illuminate\Support\Facades\App::class,
        'Artisan'   => Illuminate\Support\Facades\Artisan::class,
        'Auth'      => Illuminate\Support\Facades\Auth::class,
        'Blade'     => Illuminate\Support\Facades\Blade::class,
        'Bus'       => Illuminate\Support\Facades\Bus::class,
        'Cache'     => Illuminate\Support\Facades\Cache::class,
        'Config'    => Illuminate\Support\Facades\Config::class,
        'Cookie'    => Illuminate\Support\Facades\Cookie::class,
        'Crypt'     => Illuminate\Support\Facades\Crypt::class,
        'DB'        => Illuminate\Support\Facades\DB::class,
        'Eloquent'  => Illuminate\Database\Eloquent\Model::class,
        'Event'     => Illuminate\Support\Facades\Event::class,
        'File'      => Illuminate\Support\Facades\File::class,
        'Gate'      => Illuminate\Support\Facades\Gate::class,
        'Hash'      => Illuminate\Support\Facades\Hash::class,
        'Input'     => Illuminate\Support\Facades\Input::class,
        'Inspiring' => Illuminate\Foundation\Inspiring::class,
        'Lang'      => Illuminate\Support\Facades\Lang::class,
        'Log'       => Illuminate\Support\Facades\Log::class,
        'Mail'      => Illuminate\Support\Facades\Mail::class,
        'Password'  => Illuminate\Support\Facades\Password::class,
        'Queue'     => Illuminate\Support\Facades\Queue::class,
        'Redirect'  => Illuminate\Support\Facades\Redirect::class,
        'Redis'     => Illuminate\Support\Facades\Redis::class,
        'Request'   => Illuminate\Support\Facades\Request::class,
        'Response'  => Illuminate\Support\Facades\Response::class,
        'Route'     => Illuminate\Support\Facades\Route::class,
        'Schema'    => Illuminate\Support\Facades\Schema::class,
        'Session'   => Illuminate\Support\Facades\Session::class,
        //'Storage'   => Illuminate\Support\Facades\Storage::class,
        'URL'       => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View'      => Illuminate\Support\Facades\View::class,

        'Agent' => Jenssegers\Agent\Facades\Agent::class,

        'Member' => App\Facades\Member::class,
        'Group' => App\Facades\Group::class,
        'Plugin' => App\Facades\Plugin::class,
        'Interception' => App\Facades\Interception::class,
        'UI' => App\Facades\UI::class,
        'Theme' => App\Facades\Theme::class,
        'Skin' => App\Facades\Skin::class,
        'Register' => App\Facades\Register::class,
        'Settings' => App\Facades\Settings::class,
        'Menu' => App\Facades\Menu::class,
        'Module' => App\Facades\Module::class,
        'Cfg' => App\Facades\Config::class,
        'Storage' => App\Facades\Storage::class,
        'Site'      => App\Facades\Site::class,

        'Presenter' => App\Facades\Presenter::class,
        'Frontend' => App\Facades\Frontend::class,
        'DynamicField' => App\Facades\DynamicField::class,
        'XeDB' => App\Facades\XeDB::class,
        'Document' => App\Facades\Document::class,

        'Comment' => App\Facades\Comment::class,
        'Category' => App\Facades\Category::class,
        'Widget' => App\Facades\Widget::class,
        'Counter' => App\Facades\Counter::class,
        'Tag' => App\Facades\Tag::class,
        'ToggleMenu' => App\Facades\ToggleMenu::class,
        'Temporary' => App\Facades\Temporary::class,
        'Trash' => App\Facades\Trash::class,
        'Permission' => App\Facades\Permission::class,
        'Media' => App\Facades\Media::class,

        'XeLang' => App\Facades\Lang::class,
        'SEO' => App\Facades\SEO::class,
    ],
];
