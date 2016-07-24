<?php
	require_once("../components.php");

	$statement = mysqli_stmt_init($mysqli);
	$query = "SELECT * FROM `wigle`";
	if(mysqli_stmt_prepare($statement, $query)){
		mysqli_execute($statement);
	}
?>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<title>Simple markers</title>
		<style>
			#map {
				height: 60%;
			}
		</style>
		<!-- Compiled and minified CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
	</head>
	<body>
		<div class="container">
			<!-- top navbar -->
			<nav>
				<div class="nav-wrapper">
					<a href="#" class="brand-logo">WiFi Mapper</a>
				</div>
			</nav>
			<!-- Options section -->
			<div class="row">
				<form class="col s12">
					<div class="input-field col s12 m4">
						<select multiple>
							<option disabled="disabled">Select Any:</option>
							<option value="1">WEP</option>
							<option value="2">WPA</option>
							<option value="3">WPS</option>
							<option value="4">Open</option>
						</select>
						<label>Security Type</label>
					</div>
					<div class="input-field col s12 m4">
						<select multiple>
							<option disabled="disabled">Select Any:</option>
							<option value="1">UPC</option>
							<option value="2">eircom</option>
						</select>
						<label>Known Residence Vulnerability</label>
					</div>
					<div class="input-field col s12 m4">
						<select multiple>
							<option value="1">WEP</option>
							<option value="2">WPA</option>
							<option value="3">Open</option>
						</select>
						<label>Security Type</label>
					</div>
				</form>
			</div>
			<!-- Map displayed here -->
			<div class="row" style="">
				<div id="map" class="z-depth-2"></div>
				<div id="capture"></div>
			</div>
			<script>
				function initMap() {
				<?php
					//centre the map
					$markerPos = generateMarkers($TABLE_1, false);
					$centrePoint = findCentreOfPoints($markerPos);
					echo "var centrePoint = {lat: " . $centrePoint["lat"] . ", lng: " . $centrePoint["long"] . "};";
				?>

				var map = new google.maps.Map(document.getElementById('map'), {
				  zoom: 14,
				  center: centrePoint
				});

				<?php
					foreach ($markerPos as $marker) {
						echo "
				var marker = new google.maps.Marker({
					position: {lat: " . $marker['lat'] . ", lng: " . $marker['long'] . "},
					map: map,
					title: '" . $marker['SSID'] . "'
				});";
					}
				?>

				marker.addListener('click', function() {
					map.setZoom(15);
					map.setCenter(marker.getPosition());
				});
			  }
			</script>
			<script async defer
				src="https://maps.googleapis.com/maps/api/js?key=<?php echo($_ENV['GOOGLE_API_KEY']) ?>&callback=initMap">
			</script>
		</div>
		<!-- jQuery and Materialize -->
		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
		<!-- Compiled and minified JavaScript -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('select').material_select();
			});
		</script>
	</body>
</html>