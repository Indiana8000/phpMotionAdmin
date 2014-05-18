<?php require_once('core.php'); require_once('core_motion.php'); $_GET['cid'] = intval($_GET['cid']); ?><!DOCTYPE html>
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
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-6 col-md-offset-3">
<?php
	$motion_status = getMotionStatus();
	if($_GET['cid'] <= $motion_status['Cams']) {
		echo '<img width="100%" src="proxy_cam.php?id='.$_GET['cid'].'" />';
	} else {
		echo '<img width="100%" src="img/offline.gif" />';
	}
?>
				</div>
			</div>
		</div>
		<div class="container col-md-6 col-md-offset-3">
			<table class="table table-condensed table-striped table-hover" data-cid="<?=$_GET['cid']?>">
				<thead>
					<tr>
						<th colspan="5" class="text-center">Events</th>
					</tr>
				</thead>
				<tbody>
<?php
	$stmt2 = $GLOBALS['DB']->prepare("SELECT count(*) as count FROM events WHERE eCam = :cam AND eNum = :event AND eType = 'PICTURE'");
	$stmt2->bindValue(':cam', $_GET['cid'], PDO::PARAM_INT);
	$stmt2->bindParam(':event', $enum, PDO::PARAM_INT);

	$stmt3 = $GLOBALS['DB']->prepare("SELECT eFile FROM events WHERE eCam = :cam AND eNum = :event AND eType = 'MOVIE END'");
	$stmt3->bindValue(':cam', $_GET['cid'], PDO::PARAM_INT);
	$stmt3->bindParam(':event', $enum, PDO::PARAM_INT);

	$stmt = $GLOBALS['DB']->prepare("SELECT eNum, eDate, eData FROM events WHERE eCam = :cam AND eType = 'EVENT START' ORDER BY eNum DESC");
	$stmt->bindValue(':cam', $_GET['cid'], PDO::PARAM_INT);
	if($stmt->execute()) {
		while($row = $stmt->fetch()) {
			if($row['eData'] == 'remove') echo '<tr data-eid="0" class="text-warning">';
			else echo '<tr data-eid="'.$row['eNum'].'">';
				echo '<td class="text-right">'.$row['eNum'].'</td>';
				echo '<td class="text-center">'.$row['eDate'].'</td>';

				$enum = $row['eNum'];
				if($stmt2->execute() && ($row2 = $stmt2->fetch())) {
					echo '<td class="text-right">'.$row2['count'].'</td>';
				} else {
					echo '<td class="text-right text-danger">?</td>';
				}

				if($stmt3->execute() && ($row3 = $stmt3->fetch())) {
					echo '<td class="text-right"><span class="glyphicon glyphicon-film btn_cam_event_movie"></span></td>';
				} else {
					echo '<td>&nbsp;</td>';
				}
				if($row['eData'] == 'remove') echo '<td>&nbsp;</td>';
				else echo '<td class="text-right"><button class="btn btn-default btn-xs btn_cam_event_remove"><span class="glyphicon glyphicon-remove text-danger"></span></button></td>';

			echo '</tr>';
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
				$('tr').click(function(event) {
					event.preventDefault();
					if($(event.target).hasClass('text-center') && $(this).attr('data-eid') > 0)
						window.location.href = 'index_event_pictures.php' + window.location.search + '&eid=' + $(this).attr('data-eid');
				});
				$('.btn_cam_event_movie').click(function(event) {
					event.preventDefault();
					if($(this).closest('tr').attr('data-eid') > 0)
						window.location.href = 'index_event_movie.php' + window.location.search + '&eid=' + $(this).closest('tr').attr('data-eid');
				});
				$('.btn_cam_event_remove').click(function(event) {
					event.preventDefault();
					$this = $(this);
					if(confirm('Remove Event #'+$(this).closest('tr').attr('data-eid')+'?'))
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "cam_event_remove",
							cid: $(this).closest('table').attr('data-cid'),
							eid: $(this).closest('tr').attr('data-eid')
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								alert(data);
							} else {
								$this.closest('tr').addClass("text-warning");
								$this.closest('tr').attr('data-eid', 0);
								$this.remove();
							}
						}
					});
				});
			});
		</script>
	</body>
</html>