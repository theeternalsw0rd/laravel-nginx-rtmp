<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use App\Tracker;
use GeoIP;
use App\Stream;

class PlayerController extends BaseController
{
    public function player($streamName)
    {
        $stream = Stream::where('slug', '=', $streamName)->firstOrFail();
        return view('player', ['stream' => $stream]);
    }

    public function tracker(Request $request, $streamName, $playbackToken)
    {
        $stream = Stream::where('slug', '=', $streamName)->firstOrFail();
        $hash = hash('sha256', session('_token'));
        $tracker = Tracker::firstOrNew(['playback_token' => $playbackToken, 'session_hash' => $hash]);
        if($tracker->playback_time > 0) {
            abort(403);
        }
        $ip = $request->ip();
        $geoip = geoip($ip);
        $tracker->ip = $ip;
        $tracker->iso_code = $geoip["iso_code"];
        $tracker->country = $geoip["country"];
        $tracker->city = $geoip["city"];
        $tracker->state = $geoip["state"];
        $tracker->state_name = $geoip["state_name"];
        $tracker->postal_code = $geoip["postal_code"];
        $tracker->lat = $geoip["lat"];
        $tracker->lon = $geoip["lon"];
        $tracker->timezone = $geoip["timezone"];
        $tracker->continent = $geoip["continent"];
        $tracker->currency = $geoip["currency"];
        $tracker->default = $geoip["default"];
        $tracker->playback_time = 0;
        $tracker->did_finish = false;
        $stream->trackers()->save($tracker);
        return redirect('live/' . $streamName . '.m3u8');
    }

    protected function updatePlaybackTime($tracker, $streamName, $playbackTime)
    {
        if(!count($tracker->stream)) return abort(404);
        if($tracker->stream->slug != $streamName) return abort(404);
        $tracker->playback_time = $playbackTime;
        return $tracker;
    }

    public function updateTrackerPlayback(Request $request, $streamName, $playbackToken, $playbackTime)
    {
        $hash = hash('sha256', session('_token'));
        $tracker = Tracker::where('playback_token', '=', $playbackToken)->where('session_hash', '=', $hash)->firstOrFail();
        $tracker = $this->updatePlaybackTime($tracker, $streamName, $playbackTime);
        $tracker->save();
        return response("Successful.", 200);
    }

    public function updateTrackerFinished(Request $request, $streamName, $playbackToken, $playbackTime)
    {
        $hash = hash('sha256', session('_token'));
        $tracker = Tracker::where('playback_token', '=', $playbackToken)->where('session_hash', '=', $hash)->firstOrFail();
        $tracker = $this->updatePlaybackTime($tracker, $streamName, $playbackTime);
        $tracker->did_finish = true;
        $tracker->save();
        return response("Successful.", 200);
    }
}
