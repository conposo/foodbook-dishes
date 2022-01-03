<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type_id',
        'slug',
        'en_name',
        'bg_name',
        'order',
    ];

}