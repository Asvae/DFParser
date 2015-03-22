<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Dfparser extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dfparser';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['file', 'item', 'json'];
}
