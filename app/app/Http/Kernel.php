<?php namespace MobileOptin\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
        'Illuminate\Cookie\Middleware\EncryptCookies',
        'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
        'Illuminate\Session\Middleware\StartSession',
        'Illuminate\View\Middleware\ShareErrorsFromSession',
        'MobileOptin\Http\Middleware\VerifyCsrfToken',
        'Clockwork\Support\Laravel\ClockworkMiddleware',
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => 'MobileOptin\Http\Middleware\Authenticate',
        'auth.basic' => 'Illuminate\Auth\Middleware\AuthenticateWithBasicAuth',
        'guest'      => 'MobileOptin\Http\Middleware\RedirectIfAuthenticated',
        'acl'        => 'MobileOptin\Http\Middleware\CheckPermission',
        'moduleAccess'        => 'MobileOptin\Http\Middleware\ModuleAccessMiddleWare'

    ];

}
