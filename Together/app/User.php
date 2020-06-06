<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable 
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email' , 'BirthDate', 'photo', 'gender', 'password', 'location'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    //------------ this represent many to many relation btn 
    public function groups(){
        return $this->belongsToMany('App\Group');
    }
    //-------------this represent many to many interests and group
    public function interests(){
        return $this->belongsToMany('App\Interest');
    }
    //-------------------this to represent relation btn user and other interests
    // public function others(){
    //     return $this->belongsToMany('App\Other');
    // }
    //------------------- this represent relation btn user and requests
    public function requests(){
        return $this->hasMany('App\UserRequest');
    }
    //-------------------- this for message
    public function messages(){
        return $this->hasMany('App\Message');
    }
}
