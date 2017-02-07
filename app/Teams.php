<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $fillable = ['name','owner'];
    public function users()
    {
        return $this->belongsTo('App\User');
    }

    public function owners()
    {
        return $this->belongsTo('App\User', 'teams');
    }
}
