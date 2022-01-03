<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    // protected $connection= 'sqlite';

    /**
     * The attributes that has a default value.
     *
     * @var array
     */
    protected $attributes = [
        'public' => false,
    ];

    public function getRecipeAttribute()
    {
        return $this->attributes['recipe'];
    }

    // protected $appends = ['recipe'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'public',
        'owner_id',
        'owner_type',
        'category',
        
        'slug',
        'en_name',
        'bg_name',
        
        'description',
        
        'recipe_id',
    ];

    /**
     * Get the tags for the dish.
     */
    public function tags()
    {
        return $this->hasMany('App\Tag');
    }

    /**
     * Get the restaurant_id for the dish.
     */
    public function restaurants()
    {
        return $this->hasMany('App\Restaurant');
    }

}
