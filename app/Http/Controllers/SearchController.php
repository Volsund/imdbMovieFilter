<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use App\Models\Language;
use App\Models\Genre;

class SearchController extends Controller
{
    public function index(): string
    {
        //Getting new movie title from title input box
        $_POST = json_decode(file_get_contents("php://input"), true);
        $movieArr = $_POST['data'];

        $this->addMoviesToDatabase($movieArr);

        return ('new title saved- ok');
    }

    // Method to add all new movies and their titles/languages to database
    // For each movie another API request is made because the first API request is providing all the movies associated with title
    // To get genres and languages for each movie different request is made where it is possible to get only 1 movie data back
    public function addMoviesToDatabase(array $movieArr): void
    {

        foreach ($movieArr as $movie) {

            $title = $movie['Title'];

            //Checking if title already exists in movies database
            $entryTitle = DB::table('movies')->where('name', $title)->exists();

            //If title does not exist in database new entry is made and saved.
            if (!$entryTitle) {
                $newMovie = new Movie;
                $newMovie->name = $title;
                $newMovie->save();

                $targetMovie = $newMovie;
            } else {
                $targetMovie = DB::table('movies')->where('name', $title)->first();
            }
            
            //Making API request where it is possible to see genres and languages
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
