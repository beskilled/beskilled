<?php

use Symfony\Component\Console\Input\ArgvInput;

require_once __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

/**
 * Load DotEnv based on environment
 */

if (php_sapi_name() == 'cli') {
    $input = new ArgvInput;

    if ($input->hasParameterOption('--env')) {
        $file = '.env.' . $input->getParameterOption('--env');
    }
}

if (empty($file)) {
    $file = '.env.' . env('APP_ENV');
}

if (! file_exists($file)) {
    $file = ".env";
}

try {
    (new Dotenv\Dotenv((__DIR__ . '/../'), $file))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app->withFacades(true, [
    Illuminate\Support\Facades\Mail::class    => 'Mail',
    Tymon\JWTAuth\Facades\JWTAuth::class      => 'JWTAuth',
    Tymon\JWTAuth\Facades\JWTFactory::class   => 'JWTFactory',
    Illuminate\Support\Facades\Hash::class    => 'Hash',
    Illuminate\Database\Eloquent\Model::class => 'Eloquent'
]);

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->routeMiddleware([
    'auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(Illuminate\Mail\MailServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\FormRequestServiceProvider::class);

$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

if ($app->environment() == 'local' || 'testing') {
    $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
}

$app->alias('cache', Illuminate\Cache\CacheManager::class);
$app->alias('auth', Illuminate\Auth\AuthManager::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
