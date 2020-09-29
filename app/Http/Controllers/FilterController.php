<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Movie;
use App\Models\Language;
use App\Models\Genre;

class FilterController extends Controller
{
    public function index()
    {
        $_POST = json_decode(file_get_contents("php://input"), true);
        $searchInputs = $_POST['data'];

        //Getting search parameters from input boxes and formatting them for upcoming filter
        $genreSearch = strtolower(trim($searchInputs['genre'], ' '));
        $languageSearch = strtolower(trim($searchInputs['language'], ' '));
        $titleNameSearch = strtolower(trim($searchInputs['name'], ' '));

        //Ading search parameters to search history table.
        if ($genreSearch || $languageSearch || $titleNameSearch) {
            DB::table('history')->insert(
                [
                    'title' => $titleNameSearch,
                    'genre' => $genreSearch,
                    'language' => $languageSearch
                ]
            );
        }

        //Returns collection of genres where genre is like genre input
        $foundGenres = Genre::whereLike('name', $genreSearch)->get();

        //Returns collection of languages where language is like language input
        $foundLanguages = Language::whereLike('name', $languageSearch)->get();

        //Returns collection of movies where movie title is like title input
        $foundMovies = Movie::whereLike('name', $titleNameSearch)->get();

        //Making array of title strings from database where they match genre input.
        $titlesFromGenreSearch = [];
        foreach ($foundGenres as $genre) {
            $genreMovies = $genre->movies()->get()->toArray();
            foreach ($genreMovies as $movie) {
                if (!in_array($movie['name'], $titlesFromGenreSearch)) {
                    array_push($titlesFromGenreSearch, $movie['name']);
                }
            }
        }

        //Making array of title strings from database where they match language input.
        $titlesFromLanguageSearch = [];
        foreach ($foundLanguages as $language) {
            $languageMovies = $language->movies()->get()->toArray();
            foreach ($languageMovies as $movie) {
                if (!in_array($movie['name'], $titlesFromLanguageSearch)) {
                    array_push($titlesFromLanguageSearch, $movie['name']);
                }
            }
        }

        //Making array of title strings from database where they match title input.
        $titlesFromTitleSearch  = [];
        foreach ($foundMovies as $movie) {
            $movieName =  $movie->toArray()['name'];
            if (!in_array($movieName, $titlesFromTitleSearch)) {
                array_push($titlesFromTitleSearch, $movieName);
            }
        }

        //Making array of titles that are like are search paremeters 
        if (!$languageSearch && !$genreSearch) {
            $filterResults = $titlesFromTitleSearch;
        } else if (!$genreSearch && $languageSearch && $titleNameSearch) {
            $filterResults =  array_intersect($titlesFromLanguageSearch, $titlesFromTitleSearch);
        } else if (!$languageSearch && $genreSearch && $titleNameSearch) {
            $filterResults =  array_intersect($titlesFromGenreSearch, $titlesFromTitleSearch);
        } else if (!$languageSearch && !$titleNameSearch) {
            $filterResults = $titlesFromGenreSearch;
        } else if ($languageSearch && !$titleNameSearch && !$genreSearch) {
            $filterResults = $titlesFromLanguageSearch;
        } else {
            $filterResults =  array_intersect($titlesFromGenreSearch, $titlesFromLanguageSearch, $titlesFromTitleSearch);
        }

        //Return filtered title result array to Vue component
        return $filterResults;
    }
}
