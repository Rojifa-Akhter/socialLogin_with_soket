<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    protected $guarded = ['id'];
    public function getReadByCountAttribute()
    {
        return count($this->read_by);  
    }

    // Relationship with Group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Relationship with Sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
