<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = ['title','tmdb_id','imdb_id','poster','overview','date'];

    public function actor()
    {
        return $this->belongsToMany(Actor::class);
    }
}
