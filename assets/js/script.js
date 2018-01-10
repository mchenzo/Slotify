var currentPlaylist = [];
var shufflePlaylist = [];
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;


playFirstSong = () => { setTrack(tempPlaylist[0], tempPlaylist, true); }


createPlaylist = (error) => {
	let popup = prompt("Name of playlist:");

	if(popup != "") { 
		$.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn })
		.done((err) => {
			if(err) {
				alert(err);
			}

			openPage("yourMusic.php");
		});
	}
}


openPage = (url) => {
	if(timer != null) {
		clearTimeout(timer);
	}

	if(url.indexOf('?') === -1) { url = `${url}?`; }

	let encodedUrl = encodeURI(`${url}&userLoggedIn=${userLoggedIn}`);
	$('#mainContent').load(encodedUrl);
	$('body').scrollTop(0);
	history.pushState(null, null, url);
}


formatTime = (seconds) => {
	var time = Math.round(seconds);
	var minutes = Math.floor(time / 60);
	var seconds = time - minutes * 60;

	var extraZero = (seconds < 10) ? '0' : '';

	return minutes + ':' + extraZero + seconds;
}


updateTimeProgressBar = (audio) => {
	$('.progressTime.current').text(formatTime(audio.currentTime));
	$('.progressTime.remaining').text(formatTime(audio.duration - audio.currentTime));

	var progress = audio.currentTime / audio.duration * 100;
	$('.playbackBar .progress').css('width', progress + '%');
}


updateVolumeProgressBar = (audio) => {
	var volume = audio.volume * 100;
	$('.volumeBar .progress').css('width', volume + '%');
}



function Audio() {

	this.currentlyPlaying;
	this.audio = document.createElement('audio');

	this.audio.addEventListener('ended', function() {
		nextSong();
	})

	this.audio.addEventListener('canplay', function() {
		var duration = formatTime(this.duration);
		$('.progressTime.remaining').text(duration);
	});


	this.audio.addEventListener('timeupdate', function() {
		if(this.duration) {
			updateTimeProgressBar(this);
		}
	});


	this.audio.addEventListener('volumechange', function() {
		updateVolumeProgressBar(this);
	});


	this.setTrack = (track) => { 
		this.currentlyPlaying = track;
		this.audio.src = track.path; 
	}
	this.play = ()=> { this.audio.play(); }
	this.pause = ()=> { this.audio.pause(); }
	this.setTime = (sec) => { this.audio.currentTime = sec; }
}