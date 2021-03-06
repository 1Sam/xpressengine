<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Xpressengine\Interception\AdvisorCollection;
use Xpressengine\Interception\InterceptionHandler;
use Xpressengine\Interception\Proxy\Loader\EvalLoader;
use Xpressengine\Interception\Proxy\Loader\FileLoader;
use Xpressengine\Interception\Proxy\Pass\ClassPass;
use Xpressengine\Interception\Proxy\Pass\MethodDefinitionPass;
use Xpressengine\Interception\Proxy\ProxyGenerator;

class InterceptionServiceProvider extends ServiceProvider
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
            'xe.interception',
            function ($app) {
                $advisorCollection = new AdvisorCollection();

                $loader = new FileLoader(storage_path('interception'), $app['config']->get('app.debug') === true);
                //$loader = new EvalLoader();
                $passes = [new ClassPass(), new MethodDefinitionPass()];

                $generator = new ProxyGenerator($loader, $passes);

                $interceptionHandler = new InterceptionHandler($advisorCollection, $generator);
                return $interceptionHandler;
            }
        );
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
}
