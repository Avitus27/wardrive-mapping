<?php
	require_once("../components.php");

	//see what filters we have

	$filters = array();
	if(!empty($_POST["security"])){
		$secFilter = array();
		foreach ($_POST['security'] as $sec) {
			$secFilter[] += $sec;
		}
		$filters[0] = $secFilter;
	}

	if(!empty($_POST["vulnerability"])) {
		$vulFilter = array();
		foreach ($_POST["vulnerability"] as $vul) {
			$vulFilter[] += $vul;
		}
		$filters[1] = $vulFilter;
	}
	
	$addOR = false;
	$query = "SELECT * FROM `wigle`";
	if( isset($filters[0]) ){
		$query .= " WHERE ";
		if (in_array(1, $filters[0])) {
			$query .= "AuthMode LIKE \"%WEP%\"";
			$addOR = true;
		}
		if (in_array(2, $filters[0])) {
			$query .= $addOR ? " OR " : "";
			$query .= "AuthMode LIKE \"%WPA%\"";
			$addOR = true;
		}
		if (in_array(3, $filters[0])) {
			$query .= $addOR ? " OR " : "";
			$query .= "AuthMode LIKE \"%WPS%\"";
			$addOR = true;
		}
		if (in_array(4, $filters[0])) {
			$query .= $addOR ? " OR " : "";
			$query .= "AuthMode IS NULL OR AuthMode = \"\"";
			$addOR = true;
		}
	}

	if (isset($filters[1])) {
		$UPCregex = "UPC[0-9]{7}";
		$eircomregex = "eircom[0-9]{4} [0-9]{4}";
		if (in_array(1, $filters[1])) {
			$query .= $addOR ? " OR " : " WHERE ";
			$query .= "SSID RLIKE '" . $UPCregex . "'";
			$addOR = true;
		}
		if (in_array(2, $filters[1])) {
			$query .= $addOR ? " OR " : " WHERE ";
			$query .= "SSID RLIKE '" . $eircomregex . "'";
			$addOR = true;
		}
	}

	$statement = mysqli_stmt_init($mysqli);
	if(mysqli_stmt_prepare($statement, $query)) {
		mysqli_stmt_execute($statement);
	}

	mysqli_stmt_close($statement);
	
	$TABLE_1 = array();
	if ($result = mysqli_query($mysqli, $query)) {
		while ($row = mysqli_fetch_row($result)) {
			array_push($TABLE_1, $row);
		}
		mysqli_free_result($result);
	}
	mysqli_close($mysqli);


?>
<html>
	<head>
		<meta name="viewport" content="initial-scale=1.0, user-scalable=no">
		<meta charset="utf-8">
		<title>WiFi Mapper</title>
		<style>
			#map {
				height: 60%;
			}
		</style>
		<!-- Compiled and minified CSS -->
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
		<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
				<form method="POST" class="col s12">
					<div class="input-field col s12 m12 l4">
						<select name="security[]" multiple>
							<option disabled="disabled">Select Any:</option>
							<option <?php
							if (isset($filters[0]))
								echo in_array(1, $filters[0]) == true ? "selected" : "";
							?> value="1">WEP</option>
							<option <?php
							if (isset($filters[0]))
								echo in_array(2, $filters[0]) == true ? "selected" : "";
							?> value="2">WPA</option>
							<option <?php
							if (isset($filters[0]))
								echo in_array(3, $filters[0]) == true ? "selected" : "";
							?> value="3">WPS</option>
							<option <?php
							if (isset($filters[0]))
								echo in_array(4, $filters[0]) == true ? "selected" : "";
							?> value="4">Open</option>
						</select>
						<label>Security Type</label>
					</div>
					<div class="input-field col s12 m12 l4">
						<select name="vulnerability[]" multiple>
							<option disabled="disabled">Select Any:</option>
							<?php
							if( !isset($filters[1]) )
								$filters[1] = array();
							$i = 1;
							foreach ($mappings as $mapping) {
								if (in_array($i, $filters[1])) {
									echo "<option selected value=\"{$i}\">{$mapping}</option>";
								} else {
									echo "<option value=\"{$i}\">{$mapping}</option>";
								}
								$i++;
							}

							?>
						</select>
						<label>Known Residence Vulnerability</label>
					</div>
					<button class="col s12 l3 right btn waves-effect waves-light" name="action" type="submit">Submit
							<i class="material-icons right">send</i>
					</button>
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
				src="//maps.googleapis.com/maps/api/js?key=<?php echo($_ENV['GOOGLE_API_KEY']) ?>&callback=initMap">
			</script>
		</div>
		<!-- jQuery and Materialize -->
		<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
		<!-- Compiled and minified JavaScript -->
		<script src="//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('select').material_select();
			});
		</script>
	</body>
</html>
