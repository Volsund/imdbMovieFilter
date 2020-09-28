<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Language;
use App\Models\Genre;

class FilterController extends Controller
{
    public function index()
    {
        $_POST = json_decode(file_get_contents("php://input"), true);
        $searchInputs = $_POST['data'];

        $genreSearch = strtolower(trim($searchInputs['genre'], ' '));
        $languageSearch = strtolower(trim($searchInputs['language'], ' '));
        $titleNameSearch = strtolower(trim($searchInputs['name'], ' '));

        //Returns collection of genres where name is like comedy
        $foundGenres = Genre::whereLike('name', $genreSearch)->get();
        $foundLanguages = Language::whereLike('name', $languageSearch)->get();
        $foundMovies = Movie::whereLike('name', $titleNameSearch)->get();

        $titlesFromGenreSearch = [];
        foreach ($foundGenres as $genre) {
            $genreMovies = $genre->movies()->get()->toArray();
            foreach ($genreMovies as $movie) {
                if (!in_array($movie['name'], $titlesFromGenreSearch)) {
                    array_push($titlesFromGenreSearch, $movie['name']);
                }
            }
        }

        $titlesFromLanguageSearch = [];
        foreach ($foundLanguages as $language) {
            $languageMovies = $language->movies()->get()->toArray();
            foreach ($languageMovies as $movie) {
                if (!in_array($movie['name'], $titlesFromLanguageSearch)) {
                    array_push($titlesFromLanguageSearch, $movie['name']);
                }
            }
        }

        $titlesFromTitleSearch  = [];
        foreach ($foundMovies as $movie) {
            $movieName =  $movie->toArray()['name'];
            if (!in_array($movieName, $titlesFromTitleSearch)) {
                array_push($titlesFromTitleSearch, $movieName);
            }
        }

        $filterResults =  array_intersect($titlesFromGenreSearch, $titlesFromLanguageSearch, $titlesFromTitleSearch);

        return $filterResults;
    }
}
