<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    const IMAGE_ENDPOINT_W200 = "https://image.tmdb.org/t/p/w200/";
    const ACTOR_MISSING_PHOTO = "missing_actor_photo.png";

    protected $fillable = ['name','character','photo'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    public static function processActors($data)
    {
        $processed = [];

        foreach($data as $actors) {
            foreach($actors as $actor) {
                $single_actor_data = [];
                $single_actor_data["name"] = $actor->getName();
                $single_actor_data["character"] = $actor->getCharacter();
                $single_actor_data["photo"] = self::evaluatePhoto($actor->getProfilePath());

                $processed[] = $single_actor_data;
            }
        }

        return $processed;
    }

    public static function evaluatePhoto($photo_url)
    {
        if(!empty($photo_url)) {
            return self::IMAGE_ENDPOINT_W200.$photo_url;
        } else {
            return self::ACTOR_MISSING_PHOTO;
        }
    }
}
