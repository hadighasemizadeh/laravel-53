<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventation extends Model
{
    protected $table = 'invitations';
    protected $fillable = array('code','email','expiration','active','used', 'team_id', 'created_by');
    public function createdBy()
    {
        return $this->belongsTo('App\User');
    }
    public function team()
    {
        return $this->belongsTo('App\Team');
    }
}
