<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use App\Models\Language;
use App\Models\Genre;

class SearchController extends Controller
{
    public function index(): string
    {
        $_POST = json_decode(file_get_contents("php://input"), true);
        $movieArr = $_POST['data'];

        $this->addMoviesToDatabase($movieArr);

        return ('ok!');
    }

    public function addMoviesToDatabase(array $movieArr): void
    {

        foreach ($movieArr as $movie) {

            $title = $movie['Title'];

            $entryTitle = DB::table('movies')->where('name', $title)->exists();

            if (!$entryTitle) {
                $newMovie = new Movie;
                $newMovie->name = $title;
                $newMovie->save();

                $targetMovie = $newMovie;
            } else {
                $targetMovie = DB::table('movies')->where('name', $title)->first();
            }

            $titleReplaced = str_replace(' ', '+', $title);
            $url = "http://www.omdbapi.com/?t={$titleReplaced}&apikey=7c9b80bf";
            $contents = json_decode(file_get_contents($url));


            if ($contents !== false) {

                //Dealing with movie genres and saving them in database.
                $oneMovieGenres = explode(' ', $contents->Genre);
                foreach ($oneMovieGenres as $genre) {
                    $trimmedGenre = trim($genre, ',');
                    $genreEntry = DB::table('genres')->where('name', $trimmedGenre)->exists();

                    if (!$genreEntry) {
                        $newGenre = new Genre;
                        $newGenre->name = $trimmedGenre;
                        $newGenre->save();
                        $targetGenre = $newGenre;
                    } else {
                        $targetGenre = DB::table('genres')->where('name', $trimmedGenre)->first();
                    }

                    if (!DB::table('genre_movie')->where('movie_id', $targetMovie->id)->where('genre_id', $targetGenre->id)->exists()) {
                        $targetMovie->genres()->attach($targetGenre->id);
                    }
                }

                //Dealing with movie languages and saving them in database.
                $oneMovieLanguages = explode(' ', $contents->Language);
                foreach ($oneMovieLanguages as $language) {
                    $trimmedLanguage = trim($language, ',');
                    $languageEntry = DB::table('languages')->where('name', $trimmedLanguage)->exists();

                    if (!$languageEntry) {
                        $newLanguage = new Language;
                        $newLanguage->name = $trimmedLanguage;
                        $newLanguage->save();
                        $targetLanguage = $newLanguage;
                    } else {
                        $targetLanguage = DB::table('languages')->where('name', $trimmedLanguage)->first();
                    }

                    if (!DB::table('language_movie')->where('movie_id', $targetMovie->id)->where('language_id', $targetLanguage->id)->exists()) {
                        $targetMovie->languages()->attach($targetLanguage->id);
                    }
                }
            }
        }
    }
}
