<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    const IMAGE_ENDPOINT_W200 = "https://image.tmdb.org/t/p/w200/";
    protected $fillable = ['name','character','photo'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    public static function processActors($data) {
        $processed = [];

        foreach($data as $actors) {
            foreach($actors as $actor) {
                $single_actor_data = [];
                $single_actor_data["name"] = $actor->getName();
                $single_actor_data["character"] = $actor->getCharacter();
                $single_actor_data["photo"] = self::IMAGE_ENDPOINT_W200.$actor->getProfilePath();

                $processed[] = $single_actor_data;
            }
        }

        return $processed;
    }
}
