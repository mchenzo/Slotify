<?php 
include('includes/includedFiles.php');

if(isset($_GET['term'])) {
	$term = urldecode($_GET['term']);

} else {
	$term = '';
}

?>



<div class = 'searchContainer' >

	<h4>Search for an artist, album, or song</h4>
	<input type = 'text' class = 'searchInput' value = "<?php echo $term; ?>" placeholder = "Start typing..." onfocus = "() => {this.value = this.value}" >

</div>


<script>

	$.fn.setCursorPosition = function(pos) {
		this.each(function(index, elem) {
		    if (elem.setSelectionRange) {
		      	elem.setSelectionRange(pos, pos);
		    } else if (elem.createTextRange) {
			    var range = elem.createTextRange();
			    range.collapse(true);
			    range.moveEnd('character', pos);
			    range.moveStart('character', pos);
			    range.select();
		    }
		});
		return this;
	};

	$('.searchInput').focus();
	$('.searchInput').setCursorPosition($('.searchInput').val().length);
	
	$(() => {

		$('.searchInput').keyup(() => {
			clearTimeout(timer);

			timer = setTimeout(() => {
				let val = $('.searchInput').val();
				openPage(`search.php?term=${val}`);
			}, 800);
		});

	});

</script>


<?php if($term == '') exit(); ?>			


<div class = 'tracklistContainer borderBottom' >
	<h2>SONGS</h2>
	<ul class = 'tracklist' >
		<?php 
		$songsQuery = mysqli_query($con, "SELECT id FROM Songs WHERE title LIKE '$term%' LIMIT 10");

		if(mysqli_num_rows($songsQuery) == 0) {
			echo "<span class = 'noResults' >No songs found matching \"" . $term . "\"</span>";
		}

		$songIdArray = array();

		$i = 1;
		while($row = mysqli_fetch_array($songsQuery)) {
			if($i > 15) { break; }

			array_push($songIdArray, $row['id']);

			$albumSong = new Song($con, $row['id']);
			$albumArtist = $albumSong->getArtist();

			echo "<li class = 'tracklistRow'>

					<div class = 'trackCount' >
						<img class = 'play' src = 'assets/images/icons/play-white.png' onclick = 'setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)' >
						<span class = 'trackNumber' >$i</span>
					</div>


					<div class = 'trackInfo' >
						<span class = 'trackName' >" . $albumSong->getTitle() . "</span>
						<span class = 'artistName' >" . $albumArtist->getName() . "</span>
					</div>


					<div class = 'trackOptions' > 
						<img class = 'optionsButton' src = 'assets/images/icons/more.png' >
					</div>


					<div class = 'trackDuration' >
						<span class = 'duration' >" . $albumSong->getDuration() . "</span>
					</div>

				</li>";


			$i++;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray) ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>



<div class = 'artistContainer borderBottom'>
	
	<h2>ARTISTS</h2>

	<?php 
		$artistsQuery = mysqli_query($con, "SELECT id FROM Artists WHERE name LIKE '$term%' LIMIT 10");
		if(mysqli_num_rows($artistsQuery) == 0) {
			echo "<span class = 'noResults' >No artists found matching \"" . $term . "\"</span>";
		}

		while($row = mysqli_fetch_array($artistsQuery)) {
			$artistFound = new Artist($con, $row['id']);

			echo "<div class = 'searchResultRow' >
					<div class = 'artistName' >
						<span role = 'link' tabindex = 0 onclick = 'openPage(\"artist.php?id=" . $artistFound->getId() . "\")' >
						" .
						$artistFound->getName()
						. "
						</span>
					</div>
				</div>";
		}
	?>

</div>


<div class = 'gridViewContainer'>

	<h2>ALBUMS</h2>
	
	<?php 
		$albumsQuery = mysqli_query($con, "SELECT * FROM Albums WHERE title LIKE '$term%' LIMIT 10");

		if(mysqli_num_rows($albumsQuery) == 0) {
			echo "<span class = 'noResults' >No albums found matching \"" . $term . "\"</span>";
		}

		while($row = mysqli_fetch_array($albumsQuery)) {

			echo "<div class = 'gridViewItem'>
					<span role = 'link' tabindex = '0' onclick = 'openPage(\"album.php?id=" . $row['id'] . "\")' >
						<img src = '" . $row['artworkPath'] . "' >

						<div class = 'gridViewInfo' >"
						. $row['title'] .
						"</div>
					</a>
				</div>";

		}
	?>

</div>

