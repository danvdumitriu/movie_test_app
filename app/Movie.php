<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tmdb\Repository\MovieRepository;

class Movie extends Model
{
    const IMAGE_ENDPOINT_W200 = "https://image.tmdb.org/t/p/w200/";
    protected $fillable = ['title','tmdb_id','imdb_id','poster','overview'];

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }

    public static function findByImdbId($id)
    {
        return Movie::with("actors")->where("imdb_id", $id)->get()->toArray();
    }

    public static function findById($id)
    {
        return [Movie::with("actors")->find($id)];
    }

    public static function findByTitle($string) {
        $data = Movie::whereRaw('LOWER(`title`) LIKE ?', array( "%".$string."%" ) )->get();

        if(count($data)<10) return [];

        return $data;
    }

    public static function findByTmdbId($id) {
        return Movie::where("tmdb_id", $id)->first();
    }

    public static function isCompleteData($movie, $actors=false) {
        if(!$actors) $actors = (isset($movie->actors) && count($movie->actors));
        return (isset($movie->imdb_id) || $actors);
    }

    public static function isDuplicate($movie) {
        $data = Movie::findByTmdbId($movie->tmdb_id);
        if(count($data)>0) return $data;
        return false;
    }

    public static function storeData($data)
    {
        $out = [];
        foreach($data as $movie) {
            $actors = [];
            if (!empty($movie["actors"])) {
                $actors = $movie["actors"];
                unset($movie["actors"]);
            }
                if($existing=Movie::isDuplicate((object)$movie)) {

                    if(Movie::isCompleteData((object)$movie,$actors) && !Movie::isCompleteData($existing)) {

                        $existing->imdb_id = $movie["imdb_id"];
                        $existing->save();

                        Movie::saveActors($existing, $actors);
                    }
                    $stored_movie = $existing;



                } else {
                    $stored_movie = Movie::create($movie);

                    Movie::saveActors($stored_movie, $actors);
                }

            $out = array_merge($out, Movie::findById($stored_movie->id));
        }
        return $out;
    }

    public static function saveActors($movie, $actors) {
        foreach ($actors as $actor) {
            $movie->actors()->attach(Actor::create([
                "name" => $actor["name"],
                "character" => $actor["character"],
                "photo" => $actor["photo"]
            ]));
        }
    }

    public static function processMovieDataWithDetails($data, MovieRepository $movie_data)
    {
        $processed = [];

        foreach($data as $movie) {
            $single_movie_data = [];
            $movie_details = $movie_data->load($movie->getId());

            $single_movie_data["title"] = $movie_details->getTitle();
            $single_movie_data["tmdb_id"] = $movie_details->getId();
            $single_movie_data["imdb_id"] = $movie_details->getImdbId();
            $single_movie_data["poster"] = self::IMAGE_ENDPOINT_W200.$movie_details->getPosterPath();
            $single_movie_data["overview"] = $movie_details->getOverview();
            $single_movie_data["date"] = date("Y-m-d", time());
            $single_movie_data["actors"] = Actor::processActors($movie_details->getCredits());

            $processed[] = $single_movie_data;
        }

        return $processed;
    }

    public static function processMovieData($data)
    {
        $processed = [];

        foreach($data as $movie) {
            $single_movie_data = [];

            $single_movie_data["title"] = $movie->getTitle();
            $single_movie_data["tmdb_id"] = $movie->getId();
            $single_movie_data["poster"] = self::IMAGE_ENDPOINT_W200.$movie->getPosterPath();
            $single_movie_data["overview"] = $movie->getOverview();

            $processed[] = $single_movie_data;
        }

        return $processed;
    }
}
