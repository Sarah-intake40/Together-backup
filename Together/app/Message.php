<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //--------------- this represent relation btn message and group
    public function group(){
        return $this->belongsTo('App\Group');
    }
    //------------- this represnt relation btn user and message
    public function user(){
        return $this->belongsTo('App\User');
    }
}
