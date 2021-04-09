<?php

namespace Orbtall\LaravelLocalization;

use Illuminate\Support\ServiceProvider;

class LaravelLocalizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('laravellocalization.php'),
        ], 'config');

        if (!class_exists('CreateLocalesTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/Migrations/create_locales_table.php.stub' => database_path('migrations/'.$timestamp.'_create_locales_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['modules.handler', 'modules'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $packageConfigFile = __DIR__.'/../../config/config.php';

        $this->mergeConfigFrom(
            $packageConfigFile, 'laravellocalization'
        );

        $this->registerBindings();

        $this->registerCommands();
    }

    /**
     * Registers app bindings and aliases.
     */
    protected function registerBindings()
    {
        $this->app->singleton(LaravelLocalization::class, function () {
            return new LaravelLocalization();
        });

        $this->app->alias(LaravelLocalization::class, 'laravellocalization');
    }

    /**
     * Registers route caching commands.
     */
    protected function registerCommands()
    {
        $this->app->singleton('laravellocalizationroutecache.cache', Commands\RouteTranslationsCacheCommand::class);
        $this->app->singleton('laravellocalizationroutecache.clear', Commands\RouteTranslationsClearCommand::class);
        $this->app->singleton('laravellocalizationroutecache.list', Commands\RouteTranslationsListCommand::class);

        $this->commands([
            'laravellocalizationroutecache.cache',
            'laravellocalizationroutecache.clear',
            'laravellocalizationroutecache.list',
        ]);
    }
}