<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Symfony\Component\Process\Process;
use \Symfony\Component\Console\Output\BufferedOutput;
use \Symfony\Component\Console\Input\ArrayInput;

class TranscodeStream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stream:transcode {slug} {--stop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts transcoding livestream for HLS.';

    protected $process;
    protected $command = "";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function setCommand($withFacebook, $slug)
    {
        $facebookrtmp = "";
        if($withFacebook)
        {
            $output = new BufferedOutput;
            $instance = $this->getApplication()->find('stream:facebook');
            $arguments = [
                'slug' => $slug,
                '--stop' => false
            ];
            $instance->run(new ArrayInput($arguments), $output);
            $facebook = $output->fetch();
            $facebook = str_replace(array("\r", "\n"), '', $facebook);
            if(!empty($facebook)) $facebookrtmp = " -c copy -f flv '${facebook}'";
        }
        $command = "/usr/bin/ffmpeg -i 'rtmp://127.0.0.1/live/${slug}' -async 1 -vsync -1 ";
        $command = $command . "-c:v libx264 -c:a aac -b:v 256k -b:a 32k -vf 'scale=480:trunc(ow/a/2)*2' -tune zerolatency -preset veryfast ";
        $command = $command . "-crf 23 -f flv 'rtmp://127.0.0.1/show/${slug}_low' ";
        $command = $command . "-c:v libx264 -c:a aac -b:v 768k -b:a 96k -vf 'scale=720:trunc(ow/a/2)*2' -tune zerolatency -preset veryfast ";
        $command = $command . "-crf 23 -f flv 'rtmp://127.0.0.1/show/${slug}_mid' ";
        $command = $command . "-c:v libx264 -c:a aac -b:v 1024k -b:a 128k -vf 'scale=960:trunc(ow/a/2)*2' -tune zerolatency -preset veryfast ";
        $command = $command . "-crf 23 -f flv 'rtmp://127.0.0.1/show/${slug}_high'";
        $command = $command . "${facebookrtmp} -c copy -f flv 'rtmp://127.0.0.1/show/${slug}_src' 2>/HLS/live/${slug}-ffmpeg.log";
        $this->line($command);
        $this->command = $command;
    }


    protected function signal_handler($signal)
    {
        $this->process->stop(1, SIGINT);
        die;
    }

    protected function constructProcess()
    {
        $command = $this->command;
        $this->process = new Process('exec ' . $command);
        $this->process->setTimeout(3600);
        $this->process->start();
        pcntl_signal(SIGINT, function($signal) {
            $this->signal_handler($signal);
        });
        pcntl_signal(SIGTERM, function($signal) {
            $this->signal_handler($signal);
        });
        pcntl_signal(SIGHUP, function($signal) {
            $this->signal_handler($signal);
        });
        while($this->process->isRunning())
        {
            pcntl_signal_dispatch();
            usleep(1000);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slug = $this->argument('slug');
        if($this->option('stop'))
        {
            $this->call('stream:facebook', [
                'slug' => $slug,
                '--stop' => true
            ]);
            exec("rm /HLS/live/${slug}.running");
            sleep(5); // nginx-rtmp hasn't closed streams at this point give it some time to clean up otherwise our rewrite will be overwritten
            $files = explode("\n", shell_exec("find /HLS/live/ -name 'index.m3u8' | grep ${slug}"));
            foreach($files as $file)
            {
                if(!empty($file))
                {
                    exec("sh -c \"echo '#EXT-X-ENDLIST' >> " . $file . "\"");
                }
            }
        }
        else
        {
            exec("touch /HLS/live/${slug}.running");
            // clean up files. hls_cleanup off so stream will only expire on new stream
            $files = explode("\n", shell_exec("find /HLS/live/ -name 'index.m3u8' | grep ${slug}"));
            foreach($files as $file)
            {
                if(!empty($file))
                {
                    exec("rm ${file}");
                }
            }
            $files = explode("\n", shell_exec("find /HLS/live/ -name '*.ts' | grep ${slug}"));
            foreach($files as $file)
            {
                if(!empty($file))
                {
                    exec("rm ${file}");
                }
            }

            // start transcoding process. try with facebook first.
            $this->setCommand(true, $slug);
            $this->constructProcess();
            if(!$this->process->isSuccessful())
            {
                $this->error('Failed to start with Facebook Live enabled.');
                $this->setCommand(false, $slug);
                $this->constructProcess();
            }
        }
    }
}
