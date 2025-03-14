<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapHeadRoutes();
        $this->mapPartnerRoutes();
        $this->mapStoreRoutes();
        $this->mapShopRoutes();
        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapPartnerRoutes() {
        Route::prefix('partner')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/partner.php'));
    }

    protected function mapHeadRoutes() {
        Route::prefix('head')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/head.php'));
    }

    protected function mapStoreRoutes() {
        Route::prefix('store')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/store.php'));
    }

    protected function mapShopRoutes() {
        Route::prefix('shop')
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/shop.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
