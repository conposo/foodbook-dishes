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

$router->get('category/{category}', 'CategoryController@index');
$router->get('/{user_type_id}/category', 'CategoryController@get');
$router->get('/{user_type_id}/category', 'CategoryController@all');
$router->post('/{user_type_id}/category', 'CategoryController@store');
$router->delete('/{user_type_id}/category', 'CategoryController@destroy');

$router->get('dishbytype/{dish_type}/{dish_id_slug}[/{with_recipe}]', 'DishController@show');
$router->get('dish/{dish_id_slug}[/{with_recipe}]', 'DishController@index');


$router->post('dish/{dish_type}', 'DishController@store');
$router->delete('dish/{dish_type}/{dish_id}', 'DishController@destroy');
$router->patch('dish/{dish_type}/{dish_id}', 'DishController@update');

$router->get('{dish_type}/{owner_id}', 'DishController@showByOwner');

$router->get('getbyids', 'DishController@getbyids');

$router->post('adddishtorestaurantid', 'DishController@adddishtorestaurantid');
$router->delete('removedishtorestaurantid', 'DishController@removedishtorestaurantid');

/*
|--------------------------------------------------------------------------
| System Web Routes
|--------------------------------------------------------------------------
*/

$router->get('all/{dish_type}', 'SystemController@all');
$router->get('allrestaurantdishes', 'SystemController@allrestaurantdishes');