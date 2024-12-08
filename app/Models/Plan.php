<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    // protected $guarded = ['id'];
    protected $fillable = [
        'name', 'price', 'stripe_plan_id', 'interval', 'trial_period_days', 'lookup_key'
    ];
}
