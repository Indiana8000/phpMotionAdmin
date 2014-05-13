<?php require_once('core.php'); ?><!DOCTYPE html>
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
						<li><a href=".">Overview</a></li>
						<li class="active"><a href="settings.php">Settings</a></li>
						<li><a href="settings_cam.php">Cams</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="clearfix visible-xs">&nbsp;</div>
		<div class="container col-xs-12 col-sm-6 col-sm-offset-3">
			<div class="form-group">
				<label for="input_config_motionadminurl">Motion Administration URL</label>
				<input type="text" class="form-control" id="input_config_motionadminurl" placeholder="" value="<?=$GLOBALS['CONFIG']['AdminURL']?>" />
			</div>
			<div class="form-group">
				<label for="input_config_motionstreamurl">Motion Stream URL (With Port one lower)</label>
				<input type="text" class="form-control" id="input_config_motionstreamurl" placeholder="" value="<?=$GLOBALS['CONFIG']['StreamURL']?>" />
			</div>
			<div class="form-group">
				<label for="input_config_path2url">Local Path to URL convertion</label>
				<input type="text" class="form-control" id="input_config_path2url" placeholder="" value="<?=$GLOBALS['CONFIG']['PathToURL']?>" />
			</div>
			<button type="button" class="btn btn-primary pull-right" id="input_config_save"><span class="glyphicon glyphicon-save"></span> Save Settings</button>
		</div>
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				$('#input_config_save').click(function() {
					$.ajax({
						url: "ajax.php",
						type: "POST",
						data: {
							action: "config_save",
							motionadminurl: $('#input_config_motionadminurl').val(),
							motionstreamurl: $('#input_config_motionstreamurl').val(),
							path2url: $('#input_config_path2url').val()
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown);
						},
						success: function(data, textStatus, jqXHR) {
							if(data != 'OK') {
								alert(data);
							} else {
								alert('Settings saved successfull.');
							}
						}
					});
				});
			});
		</script>
	</body>
</html>