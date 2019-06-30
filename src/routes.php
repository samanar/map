<?php


Route::get('map/{user_id}/{province}/{state}/{city?}', 'Samanar\Map\MapController@index')->name('map.index');
