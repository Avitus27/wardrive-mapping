<?php
	require_once("../components.php");
?>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<title>Simple markers</title>
		<style>
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}
			#map {
				height: 100%;
			}
		</style>
	</head>
	<body>
		<div id="map"></div>
		<div id="capture"></div>
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

		var marker = new google.maps.Marker({
		  position: centrePoint,
		  map: map,
		  title: 'Click to zoom'
		});

		marker.addListener('click', function() {
			map.setZoom(15);
			map.setCenter(marker.getPosition());
		});
	  }
	</script>
	<script asyn
		<script async defer
			src="https://maps.googleapis.com/maps/api/js?key=<?php echo($_ENV['GOOGLE_API_KEY']) ?>&callback=initMap">
		</script>
	</body>
</html>