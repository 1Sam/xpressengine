<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Xpressengine\Plugin\PluginRegister;
use Xpressengine\Support\Exceptions\AccessDeniedHttpException;
use Xpressengine\Theme\AbstractTheme;
use Xpressengine\Theme\ThemeHandler;
use Xpressengine\UIObjects\Theme\ThemeList;
use Xpressengine\UIObjects\Theme\ThemeSelect;

class ThemeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'xe.theme',
            function ($app) {

                /** @var PluginRegister $register */
                $register = $app['xe.pluginRegister'];

                $themeHandler = $app['xe.interception']->proxy(ThemeHandler::class, 'Theme');

                $blankThemeClass = $app['config']->get('xe.theme.blank');
                $themeHandler = new $themeHandler($register, $app['xe.config'], $blankThemeClass::getId());

                return $themeHandler;
            }
        );

        $this->app->bind(
            'Xpressengine\Theme\ThemeHandler',
            'xe.theme'
        );
    }

    public function boot()
    {
        // TODO: move code to valid location!!!
        // TODO: check permission!!
        $this->registerInterceptForThemePreview();

        $this->registerBlankTheme();

        $this->registerThemeListUIObject();

        $this->registerMobileResolver();

        $this->setThemeHandlerForTheme();

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

    /**
     * registerInterceptForThemePreview
     *
     * @return void
     */
    protected function registerInterceptForThemePreview()
    {
        $preview_theme = $this->app['request']->get('preview_theme', null);
        if ($preview_theme !== null) {
            intercept(
                'Theme@getSelectedTheme',
                'preview_theme',
                function ($target) use ($preview_theme) {
                    if (!auth()->user()->isAdmin()) {
                        throw new AccessDeniedHttpException();
                    }

                    /** @var ThemeHandler $themeHandler */
                    $themeHandler = $target->getTargetObject();
                    $themeHandler->selectTheme($preview_theme);
                    return $target();
                }
            );
        }
    }

    /**
     * registerBlankTheme
     *
     * @return void
     */
    protected function registerBlankTheme()
    {
        /** @var PluginRegister $registryManager */
        $registryManager = $this->app['xe.pluginRegister'];
        $blankThemeClass = $this->app['config']->get('xe.theme.blank');
        $registryManager->add($blankThemeClass);
    }

    private function registerThemeListUIObject()
    {
        /** @var PluginRegister $registryManager */
        $registryManager = $this->app['xe.pluginRegister'];
        $registryManager->add(ThemeList::class);
        $registryManager->add(ThemeSelect::class);
    }

    private function registerMobileResolver()
    {
        $this->app['xe.theme']->setMobileResolver(function(){
            return app('request')->isMobile();
        });
    }

    private function setThemeHandlerForTheme()
    {
        AbstractTheme::setHandler($this->app->make('xe.theme'));
    }
}
