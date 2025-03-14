<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->bind(
			Illuminate\Support\Facades\Redis::class,
			App\RedisInstance::class
		);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //
        Blade::directive('nl2br', function ($string) {
            return "<?php echo nl2br(htmlentities($string)); ?>";
        });
    }
}
