<!DOCTYPE html>
<html>
	<head>
		<title>Live Player</title>
		<script src="https://cdn.jsdelivr.net/hls.js/latest/hls.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/screenfull.js/3.1.0/screenfull.min.js"></script>
		<link rel="stylesheet" href="/css/default.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" />
		<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
	</head>
	<body>
		<video id="video" src="/stream/{{ $stream->slug }}/{{ sha1(time()) }}"></video>
		<div id="metadata">
			<div id="title">{{ $stream->title }}</div>
			<div id="description">{{ $stream->description }}</div>
		</div>
		<div id="controls">
			<div id="play_pause">
				<a href="#" class="button" id="pause">&#xf04c;</a>
				<a href="#" class="button" id="play">&#xf04b;</a>
			</div>
			<div class="time" id="elapsed">00:00</div>
			<div class="time" id="separator"> / </div>
			<div class="time" id="duration">00:00</div>
			<div class="slider seek"><input type="range" id="seek" value="0" min="0" max="100"></div>
			<div class="speaker">
				<a href="#" class="button" id="unmuted">&#xf028;</a>
				<a href="#" class="button" id="muted">&#xf026;</a>
			</div>
			<div class="slider"><input type="range" id="volume" min="0" max="1" step="0.1" value="1"></div>
			<div id="toggle_fullscreen">
				<a href="#" class="button" id="fullscreen">&#xf065;</a>
				<a href="#" class="button" id="windowed">&#xf066;</a>
			</div>
		</div>
		<script src="/js/default.js"></script>
	</body>
</html>
