<?php
namespace App\Library\Services;

class MovieHelper
{
    public function isImdbId($string)
    {
        return (bool)preg_match("/^tt(\d+)$/",$string);
    }
}