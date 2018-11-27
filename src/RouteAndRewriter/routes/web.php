<?php
Route::group(
    [
        'namespace' => '\Zento\RouteAndRewriter\Http\Controllers',
        'middleware' => ['web']
    ], function () {
    Route::get('/redirect', 
        [
            'uses'=>'RedirectController@redirect'
        ]
    );
});