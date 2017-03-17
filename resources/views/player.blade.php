<!DOCTYPE html>
<html>
	<head>
		<title>Live Player</title>
		<style>
			html, body {
				width: 100%;
				height: 100%;
				padding: 0;
				margin: 0;
				overflow: hidden;
				background: black;
			}
			video {
				width: 100%;
				height: 100%;
			}
		</style>
	</head>
	<body>
		<script src="https://cdn.jsdelivr.net/hls.js/latest/hls.min.js"></script>
		<video id="video" src="/stream/{{ $streamName }}/{{ sha1(time()) }}"></video>
		<script>
			if(Hls.isSupported()) {
				var video = document.getElementById('video');
				var hls = new Hls();
				hls.loadSource(video.src);
				hls.attachMedia(video);
				hls.on(Hls.Events.MANIFEST_PARSED,function() {
					video.play();
				});
			}
		</script>
	</body>
</html>
