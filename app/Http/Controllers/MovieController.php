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

    /**
     * Searches by IMDB id
     *
     * @param $id received from API request
     * @param $find_by_id FindRepository
     * @return array
     */
    public function searchByImdbId($id, $find_by_id)
    {
        if($data=Movie::findByImdbId($id)) {
            return $data;
        } else {
            $data = $find_by_id->findBy($id,["external_source"=>"imdb_id"])->getMovieResults();
            if(count($data)) {
                $data = Movie::processMovieDataWithDetails($data, $this->movies);
                return Movie::storeData($data);
            }
        }
        return [];
    }

    /**
     * Searches by string in movie title
     *
     * @param $string received from API request
     * @param $query instance of MovieSearchQuery
     * @param $search instance of SearchRepository
     * @return array
     */
    public function searchByTitle($string, $query, $search)
    {
        if($data=Movie::findByTitle($string)) {
            return $data;
        } else {
            $data = $search->searchMovie($string, $query);
            if(count($data)) {
                $data = Movie::processMovieData($data);
                return Movie::storeData($data);
            }
        }

        return [];
    }

    /**
     * Movie search by name or IMDB id API endpoint.
     *
     * @param $search_string param reveived from API request
     * @param FindRepository $find_by_id
     * @param MovieSearchQuery $query
     * @param SearchRepository $search
     * @return array
     */
    public function search($search_string, FindRepository $find_by_id, MovieSearchQuery $query, SearchRepository $search)
    {
        if($this->helper->isImdbId($search_string)) {

            return $this->helper->setResponse(
                $this->searchByImdbId($search_string, $find_by_id)
            );

        } else {

            return $this->helper->setResponse(
                $this->searchByTitle($search_string, $query, $search),
                true
            );

        }
    }

    /**
     * API endpoint for getting movie by id
     *
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        if($data=Movie::findById($id)) {
            if(Movie::isCompleteData($data)) return $this->helper->setResponse($data);

            $data = Movie::processMovieDataWithDetails($data, $this->movies);

            return $this->helper->setResponse(
                Movie::storeData($data)
            );
        }

        return $this->helper->setResponse([]);
    }

    public function getTop10()
    {
        if($data=Movie::getTop10()) {

            return $this->helper->setResponse($data,true);

        } else {
            $data = $this->movies->getTopRated();

            if(count($data)) {
                $data = Movie::processMovieData($data);
                $data = Movie::storeData($data, true);

                foreach($data as $key => &$value) {
                    if($key>=10) unset($data[$key]);
                    else $value["top_rank"] = $key+1;
                }

                return $this->helper->setResponse($data, true);
            }

        }

        return $this->helper->setResponse([]);
    }
}
