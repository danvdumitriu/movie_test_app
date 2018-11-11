<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tmdb\Repository\MovieRepository;

class Movie extends Model
{
    const IMAGE_ENDPOINT_W200 = "https://image.tmdb.org/t/p/w200/";
    protected $fillable = ['title','tmdb_id','imdb_id','poster','overview','date'];

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

    }

    public static function storeData($data, MovieRepository $movie_data)
    {
        $processed = self::processMovieDataWithDetails($data, $movie_data);

        foreach($processed as $movie) {
            if(!empty($movie["actors"])) {
                $actors = $movie["actors"];
                unset($movie["actors"]);
            }
            $stored_movie = Movie::create($movie);

            foreach($actors as $actor) {
                $stored_movie->actors()->attach(Actor::create([
                    "name"=>$actor["name"],
                    "character"=>$actor["character"],
                    "photo"=>$actor["photo"]
                ]));
            }
        }
        return Movie::findById($stored_movie->id);
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
