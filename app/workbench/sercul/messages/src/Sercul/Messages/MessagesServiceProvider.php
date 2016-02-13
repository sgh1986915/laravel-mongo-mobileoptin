<?php namespace Sercul\Messages;

use Illuminate\Support\ServiceProvider;

class MessagesServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
        public function boot()
        {
            
            if (! $this->app->routesAreCached()) {
                require __DIR__.'/../../routes.php';
            }
        }

	/**
	 * Register the service provider.
	 * php artisan vendor:publish 
	 * @return void
	 */
	public function register()
	{
           
                $this->app['hash'] = $this->app->share(function () {
                    return new \Illuminate\Hashing\BcryptHasher();
                });
		$this->app->make('Sercul\Messages\MessagesController');
                $this->loadViewsFrom(__DIR__.'/../../views', 'messages');
                $this->publishes([
                    __DIR__.'/../../views' => base_path('resources/views/sercul/messages'),
                    __DIR__.'/../../migrations' => base_path('database/migrations'),
                ]);
                
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [];
	}

}
