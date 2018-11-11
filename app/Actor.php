<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $fillable = ['name','character','photo'];

    public function movie()
    {
        return $this->belongsToMany(Movie::class);
    }
}
