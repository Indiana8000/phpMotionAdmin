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
						<li><a href="settings.php">Settings</a></li>
						<li><a href="settings_cam.php">Cams</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="clearfix visible-xs">&nbsp;</div>
		<div class="container col-xs-12 col-sm-6 col-sm-offset-3">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">About / License</h3>
				</div>
				<div class="panel-body">
					<h3>phpMotionAdmin</h3>
						<p><a href="https://github.com/Indiana8000/phpMotionAdmin" target="_blank">https://github.com/Indiana8000/phpMotionAdmin</a></p>
					<h3>Used Libs</h3>
					<dl>
						<dt>jQuery 1.17</dt>
						<dd><a href="https://jquery.com/" target="_blank">https://jquery.com/</a></dd>
						<dt>Bootstrap 3.1.1</dt>
						<dd><a href="https://github.com/twbs/bootstrap" target="_blank">https://github.com/twbs/bootstrap</a></dd>
					</dl>
				</div>
			</div>
		</div>
		<script src="js/jquery-1.11.0.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</body>
</html>