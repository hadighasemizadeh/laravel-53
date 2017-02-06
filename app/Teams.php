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
        return $this->belongsToMany('App\User', 'team_owners', 'team_id', 'user_id');
    }
}
