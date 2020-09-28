<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('movies', function () {

    $x = DB::table('movies')->get();
    return $x;
});

Route::post('search', [SearchController::class, 'index']);

Route::post('filter', [FilterController::class, 'index']);
