<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $guarded=['id'];

    protected $casts = [
        'read_by' => 'array',
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }
    public function sender(){
        return $this->belongsTo(User::class,'sender_id');
    }
}
