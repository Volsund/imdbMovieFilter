<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\FilterController;

//API route to get all movies from database
Route::get('movies', function () {
    $allMovies = DB::table('movies')->get();
    return $allMovies;
});

//API route to get all search history data from database
Route::get('history', function () {
    $allHistory = DB::table('history')->get()->toArray();
    return $allHistory;
});

//API route to pass new title search to laravel so that it is possible to find movie genres and languages
Route::post('search', [SearchController::class, 'index']);

//API route to filter movie titles from database depending on search parameters in input boxes
Route::post('filter', [FilterController::class, 'index']);
