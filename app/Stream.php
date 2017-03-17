<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    public function trackers() {
        return $this->hasMany('App\Tracker');
    }
}

