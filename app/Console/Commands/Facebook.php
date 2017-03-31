<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Facebook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'facebook:delete {token} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
     protected $description = 'Will delete all facebook live videos given a token and id';

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

    public function delete()
    {
        try
        {
            $videos = $this->fb->get('/' . $this->argument('id') . '/live_videos')->getGraphEdge()->asArray();
            if(count($videos) < 1) return;
            foreach($videos as $video) {
                var_dump($this->fb->delete('/' . $video['id']));
            }
            $this->delete();
        }
        catch(Exception $e)
        {
            $this->error('Aborting due to exception ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fb->setDefaultAccessToken($this->argument('token'));
        $this->delete();
    }
}
