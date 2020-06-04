<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{   
    //--------------- fillable fields
    protected $fillable = [
        'name', 'description' , 'status','group_id', 'position'
    ];
    //----------this represent relation onee to many btn group and task
    public function group(){
        return $this->belongsTo('App\Group');
    }
}
