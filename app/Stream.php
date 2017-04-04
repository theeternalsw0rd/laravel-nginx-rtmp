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
        $this->attributes['title'] = is_null($value) ? '' : $value;
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = is_null($value) ? '' : $value;
    }

    public function setFbPageIDAttribute($value)
    {
        $this->attributes['fbPageID'] = is_null($value) ? '' : $value;
    }

    public function setFbPageTokenAttribute($value)
    {
        $this->attributes['fbPageToken'] = is_null($value) ? '' : $value;
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
        try
        {
            $response = $fb->get('/' . $fbPageID);
        }
        catch(\Facebook\Exceptions\FacebookResponseException $e)
        {
            return 'Error: ' . $e->getMessage();
        }
        catch(\Facebook\Exceptions\FacebookSDKException $e)
        {
            return 'Error: ' . $e->getMessage();
        }
        $node = $response->getGraphObject();
        return $node->getProperty('name');
    }
}

