<?php

/** @var \Laravel\Lumen\Routing\Router $router */
$router->post('/login', 'Auth\LoginController@login');
$router->post('/register', 'Auth\RegisterController@register');
$router->group(['middleware' => 'auth:api'], function () use ($router) {
    $router->post('logout', 'Auth\LoginController@logout');
    $router->post('refresh', 'Auth\LoginController@refresh');
    $router->get('me', 'Auth\LoginController@me');
});
