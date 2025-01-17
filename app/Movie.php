<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tmdb\Repository\MovieRepository;

class Movie extends Model
{
    const IMAGE_ENDPOINT_W200 = "https://image.tmdb.org/t/p/w200/";
    const MOVIE_MISSING_PHOTO = "missing_movie_poster.png";
    const EXPIRE_TIME_TOP10 = 60*60*24; //one day, in seconds

    protected $fillable = ['title','tmdb_id','imdb_id','poster','overview'];

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }

    public function toprated()
    {
        return $this->hasOne(Toprated::class);
    }


    public static function findByImdbId($id)
    {
        return Movie::with("actors")->where("imdb_id", $id)->get()->toArray();
    }

    public static function findById($id)
    {
        $data = Movie::with("actors")->find($id);
        if(count($data)<1) return [];
        return [$data];
    }

    public static function findByTitle($string)
    {
        $data = Movie::whereRaw('LOWER(`title`) LIKE ?', array( "%".$string."%" ) )->get();

        if(count($data)<10) return [];

        return $data;
    }

    public static function findByTmdbId($id)
    {
        return Movie::where("tmdb_id", $id)->first();
    }

    public static function isCompleteData($movie, $actors=false)
    {
        if(!$actors) $actors = (isset($movie->actors) && count($movie->actors));
        return (isset($movie->imdb_id) || $actors);
    }

    public static function isDuplicate($movie)
    {
        $data = Movie::findByTmdbId($movie->tmdb_id);
        if(count($data)>0) return $data;
        return false;
    }

    public static function storeData($data, $top_save=false)
    {
        $out = [];
        $top_data = [];

        foreach($data as $key => $movie) {
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

            $top_data = array_merge($top_data, [[
                "top_number" => $key+1,
                "movie_id"   => $stored_movie->id,
                "created_at" => date("Y-m-d H:i:s")
            ]]);
            $out = array_merge($out, Movie::findById($stored_movie->id));
        }

        if($top_save) Toprated::insert($top_data);

        return $out;
    }

    public static function saveActors($movie, $actors)
    {
        foreach ($actors as $actor) {
            $movie->actors()->attach(Actor::create([
                "name" => $actor["name"],
                "character" => $actor["character"],
                "photo" => $actor["photo"]
            ]));
        }
    }

    public static function evaluatePhoto($photo_url) {
        if(!empty($photo_url)) {
            return self::IMAGE_ENDPOINT_W200.$photo_url;
        } else {
            return self::MOVIE_MISSING_PHOTO;
        }
    }

    public static function processMovieDataWithDetails($data, MovieRepository $movie_data)
    {
        $processed = [];

        foreach($data as $movie) {
            $single_movie_data = [];
            $id = method_exists($movie,"getId")?$movie->getId():$movie["tmdb_id"];
            $movie_details = $movie_data->load($id);

            $single_movie_data["title"] = $movie_details->getTitle();
            $single_movie_data["tmdb_id"] = $movie_details->getId();
            $single_movie_data["imdb_id"] = $movie_details->getImdbId();
            $single_movie_data["poster"] = self::evaluatePhoto($movie_details->getPosterPath());
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
            $single_movie_data["poster"] = self::evaluatePhoto($movie->getPosterPath());
            $single_movie_data["overview"] = $movie->getOverview();

            $processed[] = $single_movie_data;
        }

        return $processed;
    }

    public static function checkTop10Cache()
    {
        $top_data = Toprated::all()->first();
        if(count($top_data)<1) return;

        $created = strtotime($top_data->created_at->toDateTimeString());
        if(time() > $created + self::EXPIRE_TIME_TOP10) {
            Toprated::truncate();
        }
    }

    public static function getTop10()
    {
        self::checkTop10Cache();

        $data = Movie::with(["toprated" => function($query)
        {
            $query->where('top_number', '<=', '10');

        }])->get();

        foreach($data as $key => &$movie) {
            if(empty($movie["toprated"])) unset($data[$key]);
            $movie["top_rank"] = $movie["toprated"]["top_number"];
            unset($movie["toprated"]);
        }

        if(count($data)<1) return [];

        return $data;
    }
}
