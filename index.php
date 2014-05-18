<?php require_once('core.php'); require_once('core_motion.php'); ?><!DOCTYPE html>
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
<?php
	if($GLOBALS['CONFIG']['MasterSwitch'] == 'ON') {
		echo '<button data-ms="OFF" class="btn btn-block btn-default">Master Switch <span class="glyphicon glyphicon-ok text-success"></span></button>';
	} else if($GLOBALS['CONFIG']['MasterSwitch'] == 'OFF') {
		echo '<button data-ms="ON" class="btn btn-block btn-default">Master Switch <span class="glyphicon glyphicon-remove text-danger"></span></button>';
	} else {
		echo '<button data-ms="ON" class="btn btn-block btn-default">Master Switch <span class="glyphicon glyphicon-off text-warning"></span></button>';
	}
?>
			<table class="table table-condensed table-striped table-hover">
				<thead>
					<tr>
						<th colspan="3" class="text-center">Overview</th>
					</tr>
				</thead>
				<tbody>
<?php
	checkMotionStatus();
	$motion_status = getMotionStatus();
	$stmt2 = $GLOBALS['DB']->prepare("SELECT count(*) as count FROM events WHERE eCam = :cam AND eType = 'EVENT START' AND eData != 'remove'");
	$stmt2->bindParam(':cam', $cam, PDO::PARAM_INT);
	$stmt = $GLOBALS['DB']->prepare("SELECT tID, tName, tStatus FROM threats ORDER BY tID");
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			echo '<tr data-cid="'.$row['tID'].'">';
			if($row['tID'] <= $motion_status['Cams']) {
				echo '<td><img width="80" height="60" src="proxy_cam.php?id='.$row['tID'].'" /></td>';
			} else {
				echo '<td><img width="80" height="60" src="img/offline.gif" /></td>';
			}
			echo '<td>'.$row['tID'].'. '.$row['tName'].'<br/>';
			if($row['tStatus'] == 1) {
				echo 'Status: <span class="glyphicon glyphicon-ok text-success"></span></td>';
			} else if($row['tStatus'] == 2) {
				echo 'Status: <span class="glyphicon glyphicon-remove text-danger"></span></td>';
			} else {
				echo 'Status: <span class="glyphicon glyphicon-question-sign text-danger"></span></td>';
			}
			$cam = $row['tID'];
			if($stmt2->execute() && ($row2 = $stmt2->fetch())) {
				if($row2['count'] == 0)
					echo '<td><span class="badge alert-success pull-right">0</span></td>';
				else
					echo '<td><span class="badge alert-danger pull-right">'.$row2['count'].'</span></td>';
			} else {
				echo '<td><span class="badge alert-warning pull-right">???</span></td>';
			}
			echo '</tr>';
		}
	}
?>
				</tbody>
			</table>
		</div>
<?php
	if($GLOBALS['CONFIG']['CheckActive'] == "TRUE") echo '<div class="container col-md-6 col-md-offset-3 text-center text-primary">Event clean up is running.</div>';
?>
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				$('.btn-default').click(function(event) {
					event.preventDefault();
					$this = $(this).closest('button');
					$this.attr('disabled', true);
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "masterswitch",
							status: $(this).closest('button').attr('data-ms')
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								$this.attr('disabled', false);
								alert(data);
							} else {
								window.location.href = '.';
							}
						}
					});					
				});
				$('tr').click(function(event) {
					event.preventDefault();
					window.location.href = 'index_cam.php?cid=' + $(this).attr('data-cid');
				});
			});
		</script>
	</body>
</html>