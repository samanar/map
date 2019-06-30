<?php


Route::get('map/{user_id}/{province}/{state}/{city?}', 'Samanar\Map\MapController@index')->name('map.index');
Route::post('map', 'Samanar\Map\UserMapController@store')->name('map.store');
