<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\StuffController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1/'], function () use ($router) {
    $router->group(['prefix' => 'stuff/'], function () use ($router) {
        $router->get('/', 'StuffController@index');
        $router->post('store', 'StuffController@store');
        $router->get('detail/{id}', 'StuffController@show');
        $router->patch('update/{id}', 'StuffController@update');
        $router->delete('delete/{id}', 'StuffController@destroy');
        $router->get('recycle-bin', 'StuffController@recycleBin');
        $router->get('restore/{id}', 'StuffController@restore');
        $router->get('force-delete/{id}', 'StuffController@forceDestroy');
    });
});
