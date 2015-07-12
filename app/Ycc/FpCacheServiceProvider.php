<?php namespace App\Ycc;

use Illuminate\Support\ServiceProvider;
use App\Ycc\FpCache;
use App\Ycc\CacheCore;
use App\Ycc\ViewParser;
use App\Ycc\Component;
use Event;

class FpCacheServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Register view parts methods
                $this->app['Fpcache.viewparser']->injectBlade();

                if (!$this->app['Fpcache.component']->inUse()) {
                        return false;
                }

                // load startup
                $this->app['Fpcache']->start();
	}

	protected function setupConfig()
	{
		$source = realpath(__DIR__.'/config/fpcache.php');

		if (class_exists('Illuminate\Foundation\Application', false)) {
		    $this->publishes([$source => config_path('fpcache.php')]);
		}

		$this->mergeConfigFrom($source, 'fpcache');
	}


	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
                $this->app->bindIf('request', function () {
                        return Request::createFromGlobals();
                });

		$this->setupConfig();

                $this->app->bindIf('cache', function ($app) {
                        return new CacheManager($app);
                });


		$this->app->singleton("Fpcache",function($app) {
			return new FpCache($app);
		});

		$this->app->singleton('Fpcache.component', function ($app) {
                        return new Component($app);
                });

                $this->app->bind('Fpcache.viewparser', function ($app) {
                        return new ViewParser($app);
                });

                $this->app->singleton('Fpcache.cache', function ($app) {
                        return new CacheCore($app, $app['Fpcache']->computeKey());
                });
	}
}
