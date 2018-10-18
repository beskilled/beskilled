<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function apiAs($user, $method, $uri, array $data = [], array $headers = [])
    {
        $headers = array_merge([
            'Authorization' => 'Bearer '.\JWTAuth::fromUser($user),
        ], $headers);

        return $this->api($method, $uri, $data, $headers);
    }


    protected function api($method, $uri, array $data = [], array $headers = [])
    {
        return $this->json($method, $uri, $data, $headers);
    }
}
