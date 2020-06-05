<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{    
    //---------------------- this to listt fill feilds of  group
    protected $fillable = [
        'name', 'description' , 'max_member_number', 'duration', 'current_number_of_members', 'status','level' , 'interest_id', 'other','admin_id','photo'
    ];
    //------------ this represent many to many relationship btn groups and users
    public function users(){
        return $this->belongsToMany('App\User');
    }
    //------------- this repreent one to many realtion btn group and interest
    public function interest(){
        return $this->belongsTo('App\Interest');
    }
    //----------------- this represent one to many realtion btn group and task 
    public function tasks(){
        return $this->hasMany('App\Task');
    }
    //---------------------- this repesent relation btn request and group
    public function requests(){
        return $this->hasMany('App\UserRequest');
    }
}
