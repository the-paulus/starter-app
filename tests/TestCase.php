<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, CreatesApplication;

    public function invokePrivateMethod(&$object, string $method, array $parameters = array()) {

        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($method);

        $method->setAccessible(TRUE);

        return $method->invokeArgs($object, $parameters);

    }

    /**
     * Performs the $method as $user to $endpoint URI.
     *
     * Returns a TestResponse if the value passed in as $method is valid, otherwise null is returned.
     *
     * @param Authenticatable     $user           User to perform the action as.
     * @param string              $method         HTTP verb: GET, HEAD, PATCH, POST, PUT, or DELETE.
     * @param string              $endpoint       URI to perform the action on. (e.g., api/user)
     * @param array               $data           Array that will be passed as the $data parameter of the $method function.
     * @param integer|null        $expectedStatus Expected status code to assert.
     * @param array               $headers        Array of headers to pass to the $method function.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|null
     */
    public function performActionAs(Authenticatable $user, string $method, string $endpoint, array $data = array(), $expectedStatus = null, $headers = array()) {

        $method = strtolower($method);
        $response = null;

        switch(strtolower($method)) {
            case 'get':
            case 'head':
                $response = $this->actingAs($user, 'api')->json('GET', $endpoint);
                break;
            case 'patch':
                $response = $this->actingAs($user, 'api')->patch($endpoint, $data, $headers);
                break;
            case 'post':
                $response = $this->actingAs($user, 'api')->post($endpoint, $data);
                break;
            case 'put':
                $response = $this->actingAs($user, 'api')->put($endpoint, $data, $headers);
                break;
            case 'delete':
                $response = $this->actingAs($user, 'api')->delete($endpoint, $data, $headers);
                break;
        }

        if( !is_null($response) && $expectedStatus ) {

            $this->assertEquals($expectedStatus, $response->getStatusCode(), $response->content());

        }

        return $response;

    }

    /**
     * Performs the $method as $user to the $endpoint URI using tokens.
     *
     * @param Authenticatable     $user                 User to perform the action as.
     * @param string              $method               HTTP verb: GET, HEAD, PATCH, POST, PUT, or DELETE.
     * @param string              $endpoint             URI to perform the action on. (e.g., api/user)
     * @param integer|null        $expectedStatus       Expected status code to assert.
     * @param array               $data                 Array that will be passed as the $data parameter of the $method function.
     * @param array               $additional_headers   Array of headers to pass to the $method function.
     *
     * @return \Illuminate\Foundation\Testing\TestResponse|null
     */
    public function performJWTActionAs(Authenticatable $user, string $method, string $endpoint, $expectedStatus = null, array $data = array(), $additional_headers = array()) {

        $token = JWTAuth::fromUser($user);
        $headers = ['HTTP_Authorization' => 'Bearer ' . $token];
        $headers = array_merge($headers, $additional_headers);

        $response = $this->call($method, $endpoint, $data, [], [], $headers, null);


        if( !is_null($expectedStatus) && $expectedStatus ) {

            $this->assertEquals($expectedStatus, $response->getStatusCode(), $response->json());
        }


        return $response;

    }

}
