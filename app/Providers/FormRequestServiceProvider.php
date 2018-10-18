<?php

namespace App\Providers;

use App\Http\Requests\FormRequest;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(ValidatesWhenResolved::class, function (ValidatesWhenResolved $resolved) {
            $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function (FormRequest $request, $app) {
            $this->initializeRequest($request, $app['request']);
            $request->setContainer($app);
        });
    }

    /**
     * Initialize the form request with data from the given request.
     *
     * @param  \App\Http\Requests\FormRequest $form
     * @param Request $current
     *
     * @return void
     */
    protected function initializeRequest(FormRequest $form, Request $current)
    {
        $files = $current->files->all();
        $files = is_array($files) ? array_filter($files) : $files;
        $form->initialize(
            $current->query->all(),
            $current->request->all(),
            $current->attributes->all(),
            $current->cookies->all(),
            $files,
            $current->server->all(),
            $current->getContent()
        );
        $form->setUserResolver($current->getUserResolver());
        $form->setRouteResolver($current->getRouteResolver());
    }
}
