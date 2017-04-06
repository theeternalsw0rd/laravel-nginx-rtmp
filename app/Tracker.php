<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $fillable = ['playback_token', 'session_hash'];
    public function stream() {
        return $this->belongsTo('App\Stream');
    }
}
