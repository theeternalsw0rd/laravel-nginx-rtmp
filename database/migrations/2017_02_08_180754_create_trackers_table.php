<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trackers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('session_hash');
            $table->integer('stream_id');
            $table->string('playback_token');
            $table->integer('playback_time');
            $table->boolean('did_finish');
            $table->string('ip');
            $table->string('iso_code');
            $table->string('country');
            $table->string('city');
            $table->string('state');
            $table->string('state_name');
            $table->string('postal_code');
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->string('timezone');
            $table->string('continent');
            $table->string('currency');
            $table->boolean('default');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trackers');
    }
}
