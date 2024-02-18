<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

// Struktur pembuatan route
// $route->methodHttp('/path', 'NamaController@method');

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

    $router->group(['prefix' => 'user/'], function () use ($router) {
        $router->get('/', 'UserController@index');
        $router->post('store', 'UserController@store');
        $router->get('detail/{id}', 'UserController@show');
        $router->patch('update/{id}', 'UserController@update');
        $router->delete('delete/{id}', 'UserController@destroy');
        $router->get('recycle-bin', 'UserController@recycleBin');
        $router->get('restore/{id}', 'UserController@restore');
        $router->get('force-delete/{id}', 'UserController@forceDestroy');
    });

    $router->group(['prefix' => 'stuff-stock'], function () use ($router) {
        $router->get('/', 'StuffStockController@index');
        $router->post('store', 'StuffStockController@store');
        $router->get('detail/{id}', 'StuffStockController@show');
        $router->patch('update/{id}', 'StuffStockController@update');
        $router->delete('delete/{id}', 'StuffStockController@destroy');
        $router->get('recycle-bin', 'StuffStockController@recycleBin');
        $router->get('restore/{id}', 'StuffStockController@restore');
        $router->get('force-delete/{id}', 'StuffStockController@forceDestroy');
        $router->post('add-stock/{id}', 'StuffStockController@addStock');
        $router->post('sub-stock/{id}', 'StuffStockController@subStock');
    });

    $router->group(['prefix' => 'inbound-stuff'], function () use ($router) {
        $router->get('/', 'InboundStuffController@index');
        $router->post('store', 'InboundStuffController@store');
        $router->get('detail/{id}', 'InboundStuffController@show');
        $router->patch('update/{id}', 'InboundStuffController@update');
        $router->delete('delete/{id}', 'InboundStuffController@destroy');
        $router->get('recycle-bin', 'InboundStuffController@recycleBin');
        $router->get('restore/{id}', 'InboundStuffController@restore');
        $router->get('force-delete/{id}', 'InboundStuffController@forceDestroy');
    });

    $router->group(['prefix' => 'lending'], function () use ($router) {
        $router->get('/', 'LendingController@index');
        $router->post('store', 'LendingController@store');
        $router->get('detail/{id}', 'LendingController@show');
        $router->patch('update/{id}', 'LendingController@update');
        $router->delete('delete/{id}', 'LendingController@destroy');
        $router->get('recycle-bin', 'LendingController@recycleBin');
        $router->get('restore/{id}', 'LendingController@restore');
        $router->get('force-delete/{id}', 'LendingController@forceDestroy');
    });

    $router->group(['prefix' => 'restoration'], function () use ($router) {
        $router->get('/', 'RestorationController@index');
    });
});
