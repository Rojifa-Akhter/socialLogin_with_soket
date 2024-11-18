<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded=['id'];

    public function members(){
        return $this->belongsToMany(User::class, 'group_member');
    }
    public function groupMessages(){
        return $this->hasMany(GroupMessage::class,'');
    }
}
