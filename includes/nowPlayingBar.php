<?php 
$songQuery = mysqli_query($con, "SELECT id FROM Songs ORDER BY RAND() LIMIT 10");	//selects 10 songs at random
$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) { array_push($resultArray, $row['id']); }

$jsonArray = json_encode($resultArray);		//converts any php array into json, compatible w/ javascript
?>


<script>

	
	$(document).ready(() => {
		let newPlaylist = <?php echo $jsonArray; ?>;
		audioElement = new Audio();
		setTrack(newPlaylist[0], newPlaylist, false);
		updateVolumeProgressBar(audioElement.audio);



		$('#nowPlayingBarContainer').on('mousedown touchstart mousemove touchmove', function(e) {
			e.preventDefault();
		});



		//SONG TIME CONTROLS
		$('.playbackBar .progressBar').mousedown(() => {
			mouseDown = true;
		});

		$('.playbackBar .progressBar').mousemove((e) => {
			if(mouseDown) {
				timeFromOffset(e, this);
			}
		});

		$('.playbackBar .progressBar').mouseup((e) => {
			timeFromOffset(e, this);
		});


		//VOLUME CONTROLS
		$('.volumeBar .progressBar').mousedown(() => {
			mouseDown = true;
		});

		$('.volumeBar .progressBar').mousemove((e) => {
			if(mouseDown) {
				var percentage = e.offsetX / $('.volumeBar .progressBar').width();

				if (percentage >= 0 && percentage <= 1) {
					audioElement.audio.volume = percentage;
				}
			}
		});

		$('.volumeBar .progressBar').mouseup((e) => {
			var percentage = e.offsetX / $('.volumeBar .progressBar').width();

			if (percentage >= 0 && percentage <= 1) {
				audioElement.audio.volume = percentage;
			}
		});

		$(document).mouseup(() => {
			mouseDown = false;
		})

	});



	timeFromOffset = (mouse, progressBar) => {
		var percentage = mouse.offsetX / $('.progressBar').width();	//divide the x location of the mouse by the width
		var seconds = audioElement.audio.duration * percentage;
		audioElement.setTime(seconds);
	}



	prevSong = () => {
		if(audioElement.audio.currentTime >= 3 || currentIndex === 0) {
			audioElement.setTime(0);
		} else {
			currentIndex--;
			setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
		}
	}



	nextSong = () => {
		if(repeat) {
			audioElement.setTime(0);
			playSong();
			return;
		}

		if(currentIndex === currentPlaylist.length - 1) { currentIndex = 0;
		} else { currentIndex ++; }

		var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
		setTrack(trackToPlay, currentPlaylist, true);
	}


	setRepeat = () => {
		repeat = !repeat;
		let imageName = repeat ? 'repeat-active.png' : 'repeat.png';
		$('.controlButton.repeat img').attr('src', `assets/images/icons/${imageName}`);
	}


	setMute = () => {
		audioElement.audio.muted = !audioElement.audio.muted;
		let imageName = audioElement.audio.muted ? 'volume-mute.png' : 'volume.png';
		$('.controlButton.volume img').attr('src', `assets/images/icons/${imageName}`);
	}


	setShuffle = () => {
		shuffle = !shuffle;
		let imageName = shuffle ? 'shuffle-active.png' : 'shuffle.png';
		$('.controlButton.shuffle img').attr('src', `assets/images/icons/${imageName}`);

		if(shuffle) { 
			shuffleAr(shufflePlaylist);
			currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
		} else {
			currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
		}
	}


	shuffleAr = (ar) => {
		let j, x, i;
		for (i = ar.length; i; i--) {
			j = Math.floor(Math.random() * i);
			x = ar[i - 1];
			ar[i - 1] = ar[j];
			ar[j] = x;
		}
	}


	setTrack = (trackId, newPlaylist, play) => {

		if(newPlaylist !== currentPlaylist) {
			currentPlaylist = newPlaylist;
			shufflePlaylist = currentPlaylist.slice();
			shuffleAr(shufflePlaylist);
		}

		if(shuffle) {
			currentIndex = shufflePlaylist.indexOf(trackId);
		} else {
			currentIndex = currentPlaylist.indexOf(trackId);
		}

		pauseSong();

		$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, (data) => {

			var track = JSON.parse(data);
			$(".trackName span").text(track.title);


			$.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist }, (artistData) => { 
				var artist = JSON.parse(artistData);
				$(".trackInfo .artistName span").text(artist.name);
				$(".trackInfo .artistName span").attr('onclick', `openPage('artist.php?id=${artist.id}')`);
			});

			$.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album }, (albumData) => { 
				var album = JSON.parse(albumData);
				$(".content .albumLink img").attr('src', album.artworkPath);
				$(".content .albumLink img").attr('onclick', `openPage('album.php?id=${album.id}')`);
				$(".trackName span").attr('onclick', `openPage('album.php?id=${album.id}')`);
			});



			audioElement.setTrack(track);
			if(play) { playSong(); }
		});

	}


	playSong = () => { 
		if(audioElement.audio.currentTime === 0) {
			$.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });
		}

		$(".controlButton.play").hide();
		$(".controlButton.pause").show();
		audioElement.play(); 
	}


	pauseSong = () => { 
		$(".controlButton.play").show();
		$(".controlButton.pause").hide();
		audioElement.pause(); 
	}

</script>





<div id = 'nowPlayingBarContainer' >
	
	<div id = 'nowPlayingBar' >
		

		<div id = 'nowPlayingLeft' >
			<div class = 'content' >
				<span class = 'albumLink' >
					<img class = 'albumArtwork' src="" role = 'link' tabindex = '0' >
				</span>

				<div class = 'trackInfo' >
					<span class = 'trackName' >
						<span role = 'link' tabindex = '0' ></span>
					</span>
					<span class = 'artistName' >
						<span role = 'link' tabindex = '0' ></span>
					</span>
				</div>
			</div>
		</div>


		<div id = 'nowPlayingCenter' >
			<div class = 'content playerControls' >
							
				<div class = 'buttons' >
					<button class = 'controlButton shuffle' title = 'Shuffle button' onclick = 'setShuffle()' >
						<img src = "assets/images/icons/shuffle.png" alt = 'Shuffle'>
					</button>

					<button class = 'controlButton previous' title = 'Previous button' onclick = 'prevSong()' >
						<img src = "assets/images/icons/previous.png" alt = 'Shuffle'>
					</button>

					<button class = 'controlButton play' title = 'Play button' onclick = "playSong()" >
						<img src = "assets/images/icons/play.png" alt = 'Shuffle'>
					</button>

					<button class = 'controlButton pause' title = 'Pause button' style = 'display: none;' onclick = "pauseSong()" >
						<img src = "assets/images/icons/pause.png" alt = 'Shuffle'>
					</button>

					<button class = 'controlButton next' title = 'Next button' onclick = 'nextSong()' >
						<img src = "assets/images/icons/next.png" alt = 'Shuffle'>
					</button>

					<button class = 'controlButton repeat' title = 'Repeat button' onclick = 'setRepeat()' >
						<img src = "assets/images/icons/repeat.png" alt = 'Shuffle'>
					</button>
				</div>


				<div class = 'playbackBar' >

					<span class = 'progressTime current' >0.00</span>
					<div class = 'progressBar' >
						<div class = 'progressBarBg'>
							<div class = 'progress'></div>
						</div>
					</div>
					<span class = 'progressTime remaining' >0.00</span>
					
				</div>


			</div>

		</div>


		<div id = 'nowPlayingRight' >
			<div class = 'volumeBar' >
				
				<button class = 'controlButton volume' title = 'Volume button' onclick = 'setMute()' >
					<img src="assets/images/icons/volume.png" alt = 'Volume' >
				</button>

				<div class = 'progressBar' >
					<div class = 'progressBarBg'>
						<div class = 'progress'></div>
					</div>
				</div>

			</div>
		</div>


	</div>

</div>