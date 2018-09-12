<?php
Route::group(
    [
        'namespace' => '\Zento\UrlRewriter\Http\Controllers',
        'middleware' => ['web']
    ], function () {
    Route::get('/redirect', 
        [
            'uses'=>'RedirectController@redirect'
        ]
    );
});