<?php


Route::post('map/', 'Samanar\Map\UserMapController@store')->name('map.store');
Route::get('map/{user_id}/{province}/{state}/{city?}', 'Samanar\Map\MapController@index')->name('map.index');
