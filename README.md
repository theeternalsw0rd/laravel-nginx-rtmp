This is built on top of Laravel 5.4 and requires nginx, nginx-rtmp module, redis, php7, composer, and ffmpeg.

Install nginx and nginx-rtmp using the build documentation at https://github.com/arut/nginx-rtmp-module. Tested using nginx-1.11.3 and nginx-rtmp master branch commit 5150993accb5edefa61d71e1c81ad8c02f515428.

Use nginx.conf included from this repository to get you up and running quickly, updating paths and domains as necessary.

Install ffmpeg and ensure ffmpeg is in the system path. Any ffmpeg with aac and x264 codec support and rtmp support should work.
Tested with the following version and build config:
```
ffmpeg version 3.2.4 Copyright (c) 2000-2017 the FFmpeg developers
  built with gcc 6.3.1 (GCC) 20170109
  configuration: --prefix=/usr --disable-debug --disable-static --disable-stripping --enable-avisynth --enable-avresample --enable-fontconfig --enable-gmp --enable-gnutls --enable-gpl --enable-ladspa --enable-libass --enable-libbluray --enable-libfreetype --enable-libfribidi --enable-libgsm --enable-libiec61883 --enable-libmodplug --enable-libmp3lame --enable-libopencore_amrnb --enable-libopencore_amrwb --enable-libopenjpeg --enable-libopus --enable-libpulse --enable-libschroedinger --enable-libsoxr --enable-libspeex --enable-libssh --enable-libtheora --enable-libv4l2 --enable-libvidstab --enable-libvorbis --enable-libvpx --enable-libwebp --enable-libx264 --enable-libx265 --enable-libxvid --enable-netcdf --enable-shared --enable-version3 --enable-x11grab
```

Install composer https://getcomposer.org/.

Run `composer install` from within this repository to build the vendor directory.

You will need to create a .env file in the root of this repository (this file is ignored by git to protect the info within).

For database, you will need the following:
```
DB_HOST=localhost
DB_DATABASE=dbname
DB_USERNAME=user
DB_PASSWORD=password
```

If you need something other than mysql/mariadb please read https://laravel.com/docs/5.4/database for their instructions.

This setup requires Redis to be up and running. You can read https://laravel.com/docs/5.4/redis for instructions on setup.
As provided, the configuration is using predis which is installed via composer with a unix socket. I'm using the default configuration from Arch Linux (which only changes the `dir` directive from the Redis default) with the following changes:
```
port 0
unixsocket /var/run/redis/redis.sock
unixsocketperm 770
requirepass myredispassword
```
Normally `unixsocket`, `unixsocketperm`, and `requirepass` are commented out. Changing `port` to 0 will disable tcp requests which is important on vps systems which typically have tcp ports open by default.

For the .env file, you should have the following:
```
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_DRIVER=sync
REDIS_PASSWORD=myredispassword
```

Please see `config/database.php` for the full redis configuration. 

If you want to have password recovery functioning properly, you should have the following in your .env:
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=user
MAIL_PASSWORD=password
```

Run `php artisan migrate` after setting up database and updating the .env as appropriate.

If the setup is good, going to your site will ask you to create the first user. Everything except the PlayerController routes is auth protected.

I'm the sole developer. No unit tests are available at this time and most of the functionality cannot be tested via unit tests anyways.
