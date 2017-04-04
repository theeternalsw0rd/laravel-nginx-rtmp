<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Stream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'stream:facebook {stream} {--stop}';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Will generate a Facebook Live rtmp based on the stream.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
    {
        $this->fb = $fb;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $stream = \App\Stream::where('slug', '=', $this->argument('stream'))->firstOrFail();
        }
        catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e)
        {
            return;
        }
        if(empty($stream->fbPageToken) || empty($stream->fbPageID))
        {
            return;
        }
        $this->fb->setDefaultAccessToken($stream->fbPageToken);
        if($this->option('stop'))
        {
            if(!empty($stream->fbStreamURL)) {
                $videoID = preg_replace('%.*/([^?]*).*%', '${1}', $stream->fbStreamURL);
                $streamData = [
                    'end_live_video' => true
                ];
                try
                {
                    $response = $this->fb->post('/' . $videoID, $streamData);
                }
                catch(\Facebook\Exceptions\FacebookResponseException $e)
                {
                    return;
                }
                catch(\Facebook\Exceptions\FacebookSDKException $e)
                {
                    return;
                }
                $stream->fbStreamURL = '';
                $stream->save();
                $this->line($stream->fbStreamURL);
                return;
            }
        }
        $title = empty($stream->title) ? 'placeholder title' : $stream->title;
        $description = empty($stream->description) ? 'placeholder description' : $stream->description;
        if(!empty($stream->fbStreamURL)) {
            $this->line($stream->fbStreamURL);
            return;
        }
        $streamData = [
            'title' => $title,
            'description' => $title . ': ' . $description
        ];
        try
        {
            $response = $this->fb->post('/' . $stream->fbPageID . '/live_videos', $streamData, $stream->fbPageToken)->getGraphNode();
        }
        catch(\Facebook\Exceptions\FacebookResponseException $e)
        {
            return;
        }
        catch(\Facebook\Exceptions\FacebookSDKException $e)
        {
            return;
        }
        $this->line($response['stream_url']);
        $stream->fbStreamURL = $response['stream_url'];
        $stream->save();
    }
}
