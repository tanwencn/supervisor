<?php
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {

    Route::get('/resolvers', 'HomeController@resolvers')->name('supervisor.index');

    Route::get('/directoris/{$resolver}', 'HomeController@directoris')->name('supervisor.directoris');

    Route::get('/contents', 'HomeController@contents')->name('supervisor.contents');
});

// Catch-all Route...
Route::get('/{view?}', 'HomeController@index')->where('view', '(.*)')->name('supervisor.index');
