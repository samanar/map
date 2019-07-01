<?php

Route::namespace('Samanar\Map\Controllers')
    ->prefix('map')->as('map.')->group(function () {
        Route::post('', 'UserMapController@store')->name('store');
        Route::get('{user_id}/{province}/{state}/{city?}', 'MapController@index')->name('index');
    });
