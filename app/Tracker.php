<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    public function stream() {
        return $this->belongsTo('App\Stream');
    }
}
