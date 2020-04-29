<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Freshbitsweb\LaravelCartManager\Traits\Cartable;

class Pizza extends Model
{
	use Cartable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'ingredients', 'price', 'image',
    ];
}
