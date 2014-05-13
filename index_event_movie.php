<?php require_once('core.php'); $_GET['cid'] = intval($_GET['cid']); $_GET['eid'] = intval($_GET['eid']); ?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>phpMotionAdmin</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-default hidden-xs" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="about.php">phpMotionAdmin</a>
				</div>
				<div>
					<ul class="nav navbar-nav">
						<li class="active"><a href=".">Overview</a></li>
						<li><a href="settings.php">Settings</a></li>
						<li><a href="settings_cam.php">Cams</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="clearfix visible-xs">&nbsp;</div>
		<div class="container col-md-6 col-md-offset-3">
			<table class="table table-condensed">
				<thead>
					<tr>
						<th class="text-center">Movie</th>
					</tr>
				</thead>
				<tbody>
<?php
	$path2url = explode(" => ",$GLOBALS['CONFIG']['PathToURL']);
	$stmt = $GLOBALS['DB']->prepare("SELECT eFile, eData FROM events WHERE eCam = :cam AND eNum = :event AND eType = 'MOVIE END'");
	$stmt->bindValue(':cam', $_GET['cid'], PDO::PARAM_INT);
	$stmt->bindValue(':event', $_GET['eid'], PDO::PARAM_INT);
	if($stmt->execute()) {
		if($row = $stmt->fetch()) {
			echo '<tr><td>';
				$url = str_replace($path2url[0],$path2url[1],$row['eFile']);
				echo '<object width="640" height="480"><param name="movie" value="'.$url.'"><param name="LOOP" value="false"><embed src="'.$url.'" width="640" height="480" loop="false"></embed></object>';
			echo '</td></tr>';
		}
	}
?>
				</tbody>
			</table>
		</div>
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
<script>
$(document).ready(function() {
});
</script>
	</body>
</html>