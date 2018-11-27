<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/debugiframe', ['as'=>'debugiframe.get', 'uses'=>'\Zento\ThemeManager\Http\Controllers\ThemeDebugController@index']);
    Route::post('/debugiframe', ['as'=>'debugiframe.post', 'uses'=>'\Zento\ThemeManager\Http\Controllers\ThemeDebugController@post']);
});