<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'movies';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['movie_title','movie_overview','movie_url'];
}
