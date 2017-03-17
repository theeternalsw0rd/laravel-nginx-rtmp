<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    public function trackers() {
        return $this->hasMany('App\Tracker');
    }

    public function setTitleAttribute($value) {
        if(is_null($value)) {
            $this->attributes['title'] = '';
        }
    }

    public function setBylineAttribute($value) {
        if(is_null($value)) {
            $this->attributes['byline'] = '';
        }
    }
}

