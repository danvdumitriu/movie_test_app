<?php

namespace App\Http\Controllers;

use Tmdb\Repository\MovieRepository;
use App\Library\Services\DataParser;


class MovieController extends Controller
{
    private $movies;
    private $data_parser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MovieRepository $movies, DataParser $dataParser)
    {
        $this->movies = $movies;
        $this->data_parser = $dataParser;
    }


    public function index()
    {
        //echo $this->data_parser->doSomethingUseful();
        //$top_movies = $this->movies->getTopRated();

    }
}
