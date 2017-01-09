<?php

namespace App;

use Validator;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'description', 'time', 'image'
    ];

    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public static $rules = array (
        'title'=>'required|min:3',
        'description'=> 'required|min:5',
        'time'=> 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    );

    public static function validate($data){
        return Validator::make($data,static::$rules);
    }
}
