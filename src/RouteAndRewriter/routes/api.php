<?php
Route::group(
    [
        'prefix' => '/rest/v1',
        'namespace' => '\Zento\RouteAndRewriter\Http\Controllers\Api',
        'middleware' => ['web']
    ], function () {
    Route::get('/urlrewrite', 
        [
            'uses'=>'UrlRewriteController@getUrlRewriteTo'
        ]
    );
});