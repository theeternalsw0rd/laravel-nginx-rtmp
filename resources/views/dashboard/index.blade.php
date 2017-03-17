@extends('layouts.master')
@section('content')
	<h1>Streams</h1>
	<ul class="collection">
		@foreach($streams as $stream)
			<li class="item">
				<h2>Name: {{ $stream->name }}</h2>
				<p>Title: {{ $stream->title }}</p>
				<p>Byline: {{ $stream->byline }}</p>
				<p>RTMP Server: rtmp://{{ request()->server->get('SERVER_NAME') }}/live/</p>
				<p>RTMP Stream Key: {{ $stream->slug }}?key={{ $stream->key }}</p>
				<p>Player: <a href="/player/{{ $stream->slug }}">https://{{ request()->server->get('SERVER_NAME') }}/player/{{ $stream->slug }}</a></p>
				<p><a href="/stream/{{ $stream->id }}/edit">Edit</a> | <a class="delete" data-name="{{ $stream->name }}" href="/stream/{{ $stream->id }}/delete">Delete</a></p>
			</li>
		@endforeach
	</ul>
	<h3><a href="/stream/create">Create New Stream</a></h3>
@stop
@section('localScripts')
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
	<script type="text/javascript">
		$('a.delete').on('click', function(e) {
			e.preventDefault();
			var link = this.href;
			var name = $(this).data('name');
			bootbox.confirm({
				message: "Are you sure you want to delete the stream " + name + "?",
				callback: function(result){
					if(result) {
						window.location = link;
					}
				}
			});
		});
	</script>
@stop
