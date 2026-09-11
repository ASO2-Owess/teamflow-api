<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'TeamFlow API',
        'docs' => '/docs/openapi.yaml',
        'health' => '/up',
    ]);
});
