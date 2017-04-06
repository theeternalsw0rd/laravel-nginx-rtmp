var toHHMMSS = function (value) {
	var sec_num = parseInt(value, 10); // don't forget the second param
	var hoursInt = Math.floor(sec_num / 3600);
	var minutes = Math.floor((sec_num - (hoursInt * 3600)) / 60);
	var seconds = sec_num - (hoursInt * 3600) - (minutes * 60);
	var hours;

	if (hoursInt < 10) { hours = "0" + hoursInt + ":"; }
	if (hoursInt < 1) { hours = ""; }
	if (minutes < 10) { minutes = "0" + minutes; }
	if (seconds < 10) { seconds = "0" + seconds; }
	var time = hours + minutes + ':' + seconds;
	return time;
};
// courtesy https://nathanielpaulus.wordpress.com/2016/09/04/finding-the-true-dimensions-of-an-html5-videos-active-area/
var videoDimensions = function(video) {
	// Ratio of the video's intrisic dimensions
	var videoRatio = video.videoWidth / video.videoHeight;
	// The width and height of the video element
	var width = video.offsetWidth, height = video.offsetHeight;
	// The ratio of the element's width to its height
	var elementRatio = width/height;
	// If the video element is short and wide
	if(elementRatio > videoRatio) width = height * videoRatio;
	// It must be tall and thin, or exactly equal to the original ratio
	else height = width / videoRatio;
	return {
		width: width,
		height: height
	};
};
var resizeOverlays = function() {
if(video.videoWidth == 0 || video.videoHeight == 0) {
	return;
	}
var dimensions = videoDimensions(video);
var frameWidth = dimensions.width;
var widthCss = "width: " + frameWidth + "px;";
var left = (video.offsetWidth - frameWidth) / 2;
var leftCss = "left: " + left + "px;";
var verticalOffset = (video.offsetHeight - dimensions.height) / 2;
var bottomCss = "bottom: " + verticalOffset + "px;";
var topCss = "top: " + verticalOffset + "px;";
metadata.style = widthCss + leftCss + topCss;
controls.style = widthCss + leftCss + bottomCss;
};
if(Hls.isSupported()) {
	var controls = document.getElementById('controls');
	var metadata = document.getElementById('metadata');
	var video = document.getElementById('video');
	var videoSrc = video.src;
	var play = document.getElementById('play');
	var pause = document.getElementById('pause');
	var elapsed = document.getElementById('elapsed');
	var separator = document.getElementById('separator');
	var duration = document.getElementById('duration');
	var seek = document.getElementById('seek');
	var muted = document.getElementById('muted');
	var unmuted = document.getElementById('unmuted');
	var volume = document.getElementById('volume');
	var fullscreenToggle = document.getElementById('toggle_fullscreen');
	var keyboard = false;
	var previousTime = 0;
	if(screenfull.enabled) {
		var fullscreen = document.getElementById('fullscreen');
		var windowed = document.getElementById('windowed');
		fullscreen.addEventListener('click', function(e) {
			e.preventDefault();
			fullscreenToggle.className = 'fullscreen';
			screenfull.request();
		});
		windowed.addEventListener('click', function(e) {
			e.preventDefault();
			fullscreenToggle.className = '';
			screenfull.exit();
		});
		screenfull.onchange(function(e) {
			if(screenfull.isFullscreen) {
				fullscreenToggle.className = '';
			}
			else {
				fullscreenToggle.className = 'fullscreen';
			}
		});
	}
	else {
		fullscreenToggle.parentNode.removeChild(fullscreenToggle);
		fullscreenToggle = undefined;
	}
	var hideOverlaysTimer = undefined;
	var setHideOverlaysTimer = function() { 
		hideOverlaysTimer = window.setTimeout(function() {
		if(video.paused) {
			return;
		}
		document.body.className = 'hideOverlays';
		}, 5000);
	};
	var clearHideOverlaysTimer = function() {
		if(typeof hideOverlaysTimer === 'number') {
		window.clearTimeout(hideOverlaysTimer);
		hideOverlaysTimer = undefined;
		}
	};
	var hls = new Hls({startPosition: 0});
	hls.loadSource(video.src);
	hls.attachMedia(video);
	var resetOverlays = function(e) {
		document.body.className = '';
		clearHideOverlaysTimer();
		setHideOverlaysTimer();
	};
	document.body.addEventListener('mousemove', resetOverlays);
	document.body.addEventListener('keyup', function(e) {
		switch(e.which) {
			case 32: { // space
				if(video.paused) {
					video.play();
				}
				else {
					keyboard = true;
					video.pause();
				}
				break;
			}
			case 27: { // esc
				if(screenfull.isFullscreen) {
					screenfull.exit();
				}
				break;
			}
			case 70: { // f
				screenfull.toggle();
				break;
			}
			case 77: { // m
				toggleMute(e);
				resetOverlays();
				break;
			}
			case 39: { // right arrow
				if(event.target.tagName.toUpperCase() === 'INPUT') break;
				e.preventDefault();
				var timeLeft = video.duration - video.currentTime;
				if(timeLeft > 10) {
					video.currentTime += 10;
				}
				else {
					video.currentTime = video.duration;
				}
				resetOverlays();
				break;
			}
			case 37: { // left arrow
				if(event.target.tagName.toUpperCase() === 'INPUT') break;
				e.preventDefault();
				if(video.currentTime > 10) {
					video.currentTime -= 10;
				}
				else {
					video.currentTime = 0;
				}
				resetOverlays();
				break;
			}
			case 38: { // up arrow
				if(event.target.tagName.toUpperCase() === 'INPUT') break;
				e.preventDefault();
				if(video.muted) {
					video.volume = 0.1;
					volume.value = 0.1;
					toggleMute(e);
					break;
				}
				// work around javascript floating point errors
				var fixedVolume = Math.floor(video.volume * 10);
				if(fixedVolume < 10) {
					fixedVolume += 1;
					video.volume = fixedVolume / 10;
					volume.value = fixedVolume / 10;
					resetOverlays();
				}
				break;
			}
			case 40: { // down arrow
				if(event.target.tagName.toUpperCase() === 'INPUT') break;
				e.preventDefault();
				// work around javascript floating point errors
				var fixedVolume = Math.floor(video.volume * 10);
				if(fixedVolume > 0) {
					fixedVolume -= 1;
					video.volume = fixedVolume / 10;
					volume.value = fixedVolume / 10;
					volume.value = video.volume;
					resetOverlays();
				}
				break;
			}
		}
	});
	play.addEventListener('click', function(e) {
		e.preventDefault();
		video.play();
	});
	pause.addEventListener('click', function(e) {
		e.preventDefault();
		video.pause();
		document.body.class = '';
	});
	window.addEventListener('resize', function(e) {
		resizeOverlays();
	});
	video.addEventListener('loadeddata', function(e) {
		resizeOverlays();
		video.play();
	});
	video.addEventListener('play', function(e) {
		play.style = "display: none;";
		pause.style = "display: block;";
		setHideOverlaysTimer();
		keyboard = false;
	});
	video.addEventListener('pause', function(e) {
		play.style = "display: block;";
		pause.style = "display: none;";
		document.body.className = '';
		if(keyboard) {
			play.focus();
		}
	});
	video.addEventListener('durationchange', function(e) {
		duration.innerHTML = toHHMMSS(video.duration);
		seek.max = video.duration;
	});
	video.addEventListener('timeupdate', function(e) {
		var currentTime = video.currentTime;
		elapsed.innerHTML = toHHMMSS(currentTime);
		seek.value = currentTime;
		var timeDifference = currentTime - previousTime;
		if(timeDifference > 10) {
			var xhr = new XMLHttpRequest();
			xhr.open('GET', videoSrc + '/' + Math.floor(currentTime));
			xhr.send();
			previousTime = currentTime;
		}
		if(video.duration - currentTime < 1) {
		}
	});
	video.addEventListener('click', function(e) {
		if(video.paused) {
			video.play();
		}
		else {
			video.pause();
		}
	});
	video.addEventListener('ended', function(e) {
		var xhr = new XMLHttpRequest();
		xhr.open('GET', videoSrc + '/' + Math.floor(video.duration) + '/finished');
		xhr.send();
	});
	var changeVolumeForIE = function(e) {
		video.volume = volume.value;
	};
	volume.addEventListener('change', changeVolumeForIE);
	volume.addEventListener('input', function(e) {
		seek.removeEventListener('change', changeVolumeForIE);
		video.volume = volume.value;
	});
	var toggleMute = function(e) {
		e.preventDefault();
		if(video.muted) {
			video.muted = false;
			muted.style = "display: none;";
			unmuted.style = "display: block;";
			volume.disabled = false;
		}
		else {
			video.muted = true;
			unmuted.style = "display: none;";
			muted.style = "display: block;";
			volume.disabled = true;
		}
	};
	unmuted.addEventListener('click', toggleMute);
	muted.addEventListener('click', toggleMute);
	var changeTimeForIE = function(e) {
		video.currentTime = seek.value;
	};
	seek.addEventListener('change', changeTimeForIE);
	seek.addEventListener('input', function(e) {
		seek.removeEventListener('change', changeTimeForIE);
		video.currentTime = seek.value;
	});
}
