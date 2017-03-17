<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use App\Tracker;
use GeoIP;
use Session;

class PlayerController extends BaseController
{
    public function player($streamName)
    {
        return view('player', ['streamName' => $streamName]);
    }

    public function tracker(Request $request, $streamName, $playbackToken)
    {
        $tracker = new Tracker();
        $ip = $request->ip();
        $geoip = geoip($ip);
        $hash = Hash::make(session('_token'));
        $tracker->sessionHash = $hash;
        $tracker->playbackToken = $playbackToken;
        $tracker->ip = $ip;
        $tracker->isoCode = $geoip["iso_code"];
        $tracker->country = $geoip["country"];
        $tracker->city = $geoip["city"];
        $tracker->state = $geoip["state"];
        $tracker->stateName = $geoip["state_name"];
        $tracker->postalCode = $geoip["postal_code"];
        $tracker->lat = $geoip["lat"];
        $tracker->lon = $geoip["lon"];
        $tracker->timezone = $geoip["timezone"];
        $tracker->continent = $geoip["continent"];
        $tracker->currency = $geoip["currency"];
        $tracker->default = $geoip["default"];
        return redirect('live/' . $streamName . '.m3u8');
    }
}
