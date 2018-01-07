<?php


Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

Route::get('bucket/all', ['as' => 'getAllBuckets', 'uses' => 'BucketController@getAllBuckets']);

Route::post('/files', ['as' => 'getAllObjectsInBucket', 'uses' => 'KeyController@getAllKeysInPath']);

Route::get('settings', ['as' => 'settings', 'uses' => 'SettingsController@index']);
