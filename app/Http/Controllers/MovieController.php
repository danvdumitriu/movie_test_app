<?php

namespace App\Http\Controllers;

use Tmdb\Repository\MovieRepository;
use Tmdb\Model\Search\SearchQuery\MovieSearchQuery;
use App\Library\Services\MovieHelper;
use Tmdb\Repository\FindRepository;
use Tmdb\Repository\SearchRepository;
use App\Movie;

class MovieController extends Controller
{
    private $movies;
    private $helper;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MovieRepository $movies, MovieHelper $helper)
    {
        $this->movies = $movies;
        $this->helper = $helper;
    }


    public function index()
    {

    }

    public function searchByImdbId($id, $find_by_id)
    {
        if($data=Movie::findByImdbId($id)) {
            return $data;
        } else {
            $data = $find_by_id->findBy($id,["external_source"=>"imdb_id"])->getMovieResults();
            if(count($data)) {
                return Movie::storeData($data, $this->movies);
            }
        }
        return [];
    }

    public function searchByTitle($string, $query, $search)
    {
        if($data=Movie::findByTitle($string)) {
            return $data;
        } else {
            $data = $search->searchMovie($string, $query);
            if(count($data)) {
                return Movie::processMovieData($data);
            }
        }

        return [];
    }

    public function search($search_string, FindRepository $find_by_id, MovieSearchQuery $query, SearchRepository $search)
    {
        if($this->helper->isImdbId($search_string)) {
            return [
                "listing" => false,
                "data" => $this->searchByImdbId($search_string, $find_by_id)
            ];
        } else {
            return [
                "listing" => true,
                "data" => $this->searchByTitle($search_string, $query, $search)
            ];
        }
    }
}
