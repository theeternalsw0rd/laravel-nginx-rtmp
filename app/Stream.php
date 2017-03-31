<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    protected $appends = array('fbPageTitle');

    public function trackers()
    {
        return $this->hasMany('App\Tracker');
    }

    public function setTitleAttribute($value)
    {
        if(is_null($value))
        {
            $this->attributes['title'] = '';
        }
    }

    public function setBylineAttribute($value)
    {
        if(is_null($value))
        {
            $this->attributes['byline'] = '';
        }
    }

    public function getFbPageTitleAttribute()
    {
        $fbPageToken = $this->fbPageToken;
        $fbPageID = $this->fbPageID;
        if(empty($fbPageToken) || empty($fbPageID)) {
            return '';
        }
        $fb = app(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
        $fb->setDefaultAccessToken($fbPageToken);
        $response = $fb->get('/' . $fbPageID);
        $node = $response->getGraphObject();
        return $node->getProperty('name');
    }
}

